<?php
// api/helpers.php

function sendCorsHeaders(string $allowedMethods): void
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: ' . $allowedMethods);
}

function sendJsonResponse(array $payload, int $statusCode = 200): void
{
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function readJsonRequestBody(): array
{
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($input)) {
        throw new InvalidArgumentException('Invalid request payload. Content must be valid JSON.');
    }

    return $input;
}

function requirePostMethod(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
    }
}

function allowedOrderStatuses(): array
{
    return ['pending', 'preparing', 'completed', 'cancelled'];
}

function allowedPaymentStatuses(): array
{
    return ['unpaid', 'paid', 'failed'];
}

function allowedPaymentResults(): array
{
    return ['success', 'failed', null];
}

function maxCustomerNameLength(): int
{
    return 80;
}

function maxTableNumber(): int
{
    return 999;
}

function maxOrderItemQuantity(): int
{
    return 99;
}
?>
