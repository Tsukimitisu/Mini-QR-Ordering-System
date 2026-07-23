<?php
// api/orders.php
require_once 'helpers.php';

header('Content-Type: application/json');
sendCorsHeaders('GET, POST, OPTIONS');
sendNoStoreHeaders();

// Handle preflight CORS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];

require_once 'db.php';

if ($method === 'GET') {
    // Admin request: fetch all orders (sorted newest first, with optional status filtering)
    try {
        $statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';

        if ($statusFilter !== '' && $statusFilter !== 'all' && !in_array($statusFilter, allowedOrderStatuses(), true)) {
            error_log("Orders fetch failed: Invalid status filter ($statusFilter)");
            sendJsonResponse([
                'success' => false,
                'message' => 'Invalid order status filter.'
            ], 400);
        }
        
        $sql = "SELECT * FROM orders";
        $params = [];
        
        if (!empty($statusFilter) && $statusFilter !== 'all') {
            $sql .= " WHERE order_status = ?";
            $params[] = $statusFilter;
        }
        
        $sql .= " ORDER BY id DESC"; // Newest orders first
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll();
        
        error_log("Orders fetched: " . count($orders) . " orders" . ($statusFilter ? " with status '$statusFilter'" : ""));
        
        // Hydrate each order with its item lines
        foreach ($orders as &$order) {
            $itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC");
            $itemStmt->execute([$order['id']]);
            $order['items'] = $itemStmt->fetchAll();
        }
        
        sendJsonResponse([
            'success' => true,
            'data' => $orders
        ]);
    } catch (\PDOException $e) {
        sendJsonResponse([
            'success' => false,
            'message' => 'Failed to fetch orders: ' . $e->getMessage()
        ], 500);
    }
} elseif ($method === 'POST') {
    // Customer request: submit a new order
    try {
        $input = readJsonRequestBody();
        
        $customerNameRaw = isset($input['customer_name']) ? trim($input['customer_name']) : '';
        $customerName = htmlspecialchars($customerNameRaw);
        $tableNumber = isset($input['table_number']) ? intval($input['table_number']) : 0;
        $items = isset($input['items']) ? $input['items'] : [];
        
        // 1. Basic Validations
        if (empty($customerName)) {
            throw new Exception("Customer name is required.");
        }
        if (strlen($customerNameRaw) > maxCustomerNameLength()) {
            throw new Exception("Customer name must be " . maxCustomerNameLength() . " characters or fewer.");
        }
        if ($tableNumber <= 0) {
            throw new Exception("Table number must be greater than 0.");
        }
        if ($tableNumber > maxTableNumber()) {
            throw new Exception("Table number must be " . maxTableNumber() . " or lower.");
        }
        if (empty($items) || !is_array($items)) {
            throw new Exception("Cart cannot be empty.");
        }
        
        // Begin Database Transaction to ensure atomicity
        $pdo->beginTransaction();
        
        $totalAmount = 0.00;
        $orderItemsToInsert = [];
        
        $mergedItems = [];
        foreach ($items as $item) {
            $productId = isset($item['product_id']) ? intval($item['product_id']) : 0;
            $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;

            if ($productId <= 0) {
                throw new Exception("Invalid product selection in cart.");
            }
            if ($quantity <= 0) {
                throw new Exception("Quantity must be greater than 0.");
            }
            if ($quantity > maxOrderItemQuantity()) {
                throw new Exception("Quantity cannot exceed " . maxOrderItemQuantity() . " per item.");
            }

            if (!isset($mergedItems[$productId])) {
                $mergedItems[$productId] = 0;
            }
            $mergedItems[$productId] += $quantity;
        }

        // 2. Process items and verify prices securely
        foreach ($mergedItems as $productId => $quantity) {
            // Query current item from the DB
            $prodStmt = $pdo->prepare("SELECT * FROM products WHERE id = ? FOR UPDATE");
            $prodStmt->execute([$productId]);
            $product = $prodStmt->fetch();
            
            if (!$product) {
                throw new Exception("Selected product does not exist.");
            }
            
            if (intval($product['availability_status']) !== 1) {
                throw new Exception("The item '" . $product['product_name'] . "' is currently out of stock.");
            }

            $stockQuantity = isset($product['stock_quantity']) ? intval($product['stock_quantity']) : 0;
            if ($stockQuantity < $quantity) {
                throw new Exception("Only " . $stockQuantity . " stock left for '" . $product['product_name'] . "'.");
            }
            
            $price = floatval($product['price']);
            $subtotal = $price * $quantity;
            $totalAmount += $subtotal;
            
            $orderItemsToInsert[] = [
                'product_id' => $product['id'],
                'product_name' => $product['product_name'],
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'remaining_stock' => $stockQuantity - $quantity
            ];
        }
        
        // 3. Create core order record
        $orderStmt = $pdo->prepare("
            INSERT INTO orders (customer_name, table_number, total_amount, order_status, payment_status, payment_result) 
            VALUES (?, ?, ?, 'pending', 'unpaid', NULL)
        ");
        $orderStmt->execute([$customerName, $tableNumber, $totalAmount]);
        $orderId = $pdo->lastInsertId();
        
        error_log("Order created: Order ID $orderId - Customer: $customerNameRaw, Table: $tableNumber, Total: $totalAmount, Items: " . count($orderItemsToInsert));
        
        // 4. Save items linking to order_id
        $itemInsertStmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, quantity, price, subtotal) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        foreach ($orderItemsToInsert as $oItem) {
            $itemInsertStmt->execute([
                $orderId,
                $oItem['product_id'],
                $oItem['product_name'],
                $oItem['quantity'],
                $oItem['price'],
                $oItem['subtotal']
            ]);

            $stockUpdateStmt = $pdo->prepare("
                UPDATE products 
                SET stock_quantity = ?,
                    availability_status = CASE WHEN ? <= 0 THEN 0 ELSE availability_status END
                WHERE id = ?
            ");
            $stockUpdateStmt->execute([
                $oItem['remaining_stock'],
                $oItem['remaining_stock'],
                $oItem['product_id'],
            ]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        error_log("Order finalized: Order ID $orderId with " . count($orderItemsToInsert) . " items committed successfully");
        
        sendJsonResponse([
            'success' => true,
            'message' => 'Order placed successfully.',
            'order_id' => $orderId,
            'total_amount' => $totalAmount
        ]);
        
    } catch (Exception $e) {
        // Rollback on failure to prevent orphaned rows
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
            error_log("Order transaction rolled back due to error: " . $e->getMessage());
        }
        
        error_log("Order submission failed: " . $e->getMessage());
        
        sendJsonResponse([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
} else {
    error_log("Invalid HTTP method attempted on orders endpoint: " . $_SERVER['REQUEST_METHOD']);
    sendJsonResponse([
        'success' => false,
        'message' => 'Method not allowed.'
    ], 405);
}
?>
