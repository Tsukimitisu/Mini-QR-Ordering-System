<?php
// api/update_payment_status.php
require_once 'helpers.php';

header('Content-Type: application/json');
sendCorsHeaders('POST, OPTIONS');

requirePostMethod();

require_once 'db.php';

try {
    $input = readJsonRequestBody();
    
    $orderId = isset($input['order_id']) ? intval($input['order_id']) : 0;
    $paymentStatus = isset($input['payment_status']) ? trim($input['payment_status']) : '';
    $paymentResult = isset($input['payment_result']) && $input['payment_result'] !== '' ? trim($input['payment_result']) : null;
    
    if ($orderId <= 0) {
        throw new Exception("Invalid order ID.");
    }
    
    if (!in_array($paymentStatus, allowedPaymentStatuses(), true)) {
        throw new Exception("Invalid payment status value.");
    }
    
    if (!in_array($paymentResult, allowedPaymentResults(), true)) {
        throw new Exception("Invalid payment result value.");
    }

    if ($paymentStatus === 'paid' && $paymentResult !== 'success') {
        throw new Exception("Paid orders must have a success payment result.");
    }

    if ($paymentStatus === 'failed' && $paymentResult !== 'failed') {
        throw new Exception("Failed payments must have a failed payment result.");
    }

    if ($paymentStatus === 'unpaid' && $paymentResult !== null) {
        throw new Exception("Unpaid orders cannot have a payment result.");
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
    
    sendJsonResponse([
        'success' => true,
        'message' => 'Payment details updated successfully.'
    ]);
} catch (Exception $e) {
    sendJsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}
?>
