<?php
// api/update_order_status.php
require_once 'db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// Handle preflight CORS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $orderId = isset($input['order_id']) ? intval($input['order_id']) : 0;
    $orderStatus = isset($input['order_status']) ? trim($input['order_status']) : '';
    
    if ($orderId <= 0) {
        throw new Exception("Invalid order ID.");
    }
    
    $allowedStatuses = ['pending', 'preparing', 'completed', 'cancelled'];
    if (!in_array($orderStatus, $allowedStatuses)) {
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
    
    echo json_encode([
        'success' => true,
        'message' => 'Order status updated successfully.'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
