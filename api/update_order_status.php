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
        error_log("Order status update failed: Invalid order ID ($orderId)");
        throw new Exception("Invalid order ID.");
    }
    
    if (!in_array($orderStatus, allowedOrderStatuses(), true)) {
        error_log("Order status update failed: Invalid status value ($orderStatus)");
        throw new Exception("Invalid order status value.");
    }
    
    // Check if order exists
    $checkStmt = $pdo->prepare("SELECT id FROM orders WHERE id = ?");
    $checkStmt->execute([$orderId]);
    if (!$checkStmt->fetch()) {
        error_log("Order status update failed: Order not found (ID: $orderId)");
        throw new Exception("Order not found.");
    }
    
    // Update order status
    $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->execute([$orderStatus, $orderId]);
    
    error_log("Order status updated: Order ID $orderId changed to '$orderStatus'");
    
    sendJsonResponse([
        'success' => true,
        'message' => 'Order status updated successfully.'
    ]);
} catch (Exception $e) {
    error_log("Order status update exception: " . $e->getMessage());
    sendJsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}
?>
