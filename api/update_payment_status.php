<?php
// api/update_payment_status.php
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
    $paymentStatus = isset($input['payment_status']) ? trim($input['payment_status']) : '';
    $paymentResult = isset($input['payment_result']) && $input['payment_result'] !== '' ? trim($input['payment_result']) : null;
    
    if ($orderId <= 0) {
        throw new Exception("Invalid order ID.");
    }
    
    $allowedPaymentStatuses = ['unpaid', 'paid', 'failed'];
    if (!in_array($paymentStatus, $allowedPaymentStatuses)) {
        throw new Exception("Invalid payment status value.");
    }
    
    $allowedResults = ['success', 'failed', null];
    if (!in_array($paymentResult, $allowedResults, true)) {
        throw new Exception("Invalid payment result value.");
    }
    
    // Check if order exists
    $checkStmt = $pdo->prepare("SELECT id FROM orders WHERE id = ?");
    $checkStmt->execute([$orderId]);
    if (!$checkStmt->fetch()) {
        throw new Exception("Order not found.");
    }
    
    // Update payment details
    $stmt = $pdo->prepare("UPDATE orders SET payment_status = ?, payment_result = ? WHERE id = ?");
    $stmt->execute([$paymentStatus, $paymentResult, $orderId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Payment details updated successfully.'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
