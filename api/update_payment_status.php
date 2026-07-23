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
        error_log("Payment update failed: Invalid order ID ($orderId)");
        throw new Exception("Invalid order ID.");
    }
    
    if (!in_array($paymentStatus, allowedPaymentStatuses(), true)) {
        error_log("Payment update failed: Invalid payment status ($paymentStatus)");
        throw new Exception("Invalid payment status value.");
    }
    
    if (!in_array($paymentResult, allowedPaymentResults(), true)) {
        error_log("Payment update failed: Invalid payment result ($paymentResult)");
        throw new Exception("Invalid payment result value.");
    }

    if ($paymentStatus === 'paid' && $paymentResult !== 'success') {
        error_log("Payment update failed: Paid order without success result (Order ID: $orderId)");
        throw new Exception("Paid orders must have a success payment result.");
    }

    if ($paymentStatus === 'failed' && $paymentResult !== 'failed') {
        error_log("Payment update failed: Failed status without failed result (Order ID: $orderId)");
        throw new Exception("Failed payments must have a failed payment result.");
    }

    if ($paymentStatus === 'unpaid' && $paymentResult !== null) {
        error_log("Payment update failed: Unpaid order with result (Order ID: $orderId)");
        throw new Exception("Unpaid orders cannot have a payment result.");
    }
    
    // Check if order exists
    $checkStmt = $pdo->prepare("SELECT id FROM orders WHERE id = ?");
    $checkStmt->execute([$orderId]);
    if (!$checkStmt->fetch()) {
        error_log("Payment update failed: Order not found (ID: $orderId)");
        throw new Exception("Order not found.");
    }
    
    // Update payment details
    $stmt = $pdo->prepare("UPDATE orders SET payment_status = ?, payment_result = ? WHERE id = ?");
    $stmt->execute([$paymentStatus, $paymentResult, $orderId]);
    
    error_log("Payment updated: Order ID $orderId - Status: '$paymentStatus', Result: '$paymentResult'");
    
    sendJsonResponse([
        'success' => true,
        'message' => 'Payment details updated successfully.'
    ]);
} catch (Exception $e) {
    error_log("Payment update exception: " . $e->getMessage());
    sendJsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}
?>
