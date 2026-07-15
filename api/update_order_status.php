<?php
// api/update_order_status.php
require_once 'helpers.php';

header('Content-Type: application/json');
sendCorsHeaders('POST, OPTIONS');

requirePostMethod();

require_once 'db.php';

try {
    $input = readJsonRequestBody();
    
    $orderId = isset($input['order_id']) ? intval($input['order_id']) : 0;
    $orderStatus = isset($input['order_status']) ? trim($input['order_status']) : '';
    
    if ($orderId <= 0) {
        throw new Exception("Invalid order ID.");
    }
    
    if (!in_array($orderStatus, allowedOrderStatuses(), true)) {
        throw new Exception("Invalid order status value.");
    }
    
    // Check if order exists
    $checkStmt = $pdo->prepare("SELECT id FROM orders WHERE id = ?");
    $checkStmt->execute([$orderId]);
    if (!$checkStmt->fetch()) {
        throw new Exception("Order not found.");
    }
    
    // Update order status
    $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->execute([$orderStatus, $orderId]);
    
    sendJsonResponse([
        'success' => true,
        'message' => 'Order status updated successfully.'
    ]);
} catch (Exception $e) {
    sendJsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}
?>
