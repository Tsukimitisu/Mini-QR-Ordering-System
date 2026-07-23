<?php
// api/helpers.php

// Configuration Constants
const API_RESPONSE_TIMEOUT = 30; // seconds
const MAX_JSON_BODY_SIZE = 1048576; // 1MB
const CACHE_EXPIRY_SECONDS = 3600; // 1 hour
const REQUEST_RATE_LIMIT = 100; // requests per minute

/**
 * Send CORS headers for API requests
 * @param string $allowedMethods Comma-separated list of allowed HTTP methods
 */
function sendCorsHeaders(string $allowedMethods): void
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Methods: ' . $allowedMethods);
    header('Access-Control-Max-Age: 3600');
}

function sendNoStoreHeaders(): void
{
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Content-Encoding: gzip');
    header('Vary: Accept-Encoding');
}

function sendJsonResponse(array $payload, int $statusCode = 200): void
{
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Validate JSON request body size to prevent DOS attacks
 * @throws InvalidArgumentException if body size exceeds limit
 */
function validateRequestBodySize(): void
{
    $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? intval($_SERVER['CONTENT_LENGTH']) : 0;
    $maxSize = MAX_JSON_BODY_SIZE;
    
    if ($contentLength > $maxSize) {
        throw new InvalidArgumentException('Request body exceeds maximum allowed size of ' . ($maxSize / 1024 / 1024) . 'MB');
    }
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

/**
 * Validate order item quantity
 * @param int $quantity The quantity to validate
 * @return bool True if valid
 * @throws Exception if validation fails
 */
function validateOrderItemQuantity(int $quantity): bool
{
    if ($quantity <= 0) {
        throw new Exception("Quantity must be greater than 0.");
    }
    if ($quantity > maxOrderItemQuantity()) {
        throw new Exception("Quantity cannot exceed " . maxOrderItemQuantity() . " per item.");
    }
    return true;
}

/**
 * Validate customer name
 * @param string $name The customer name to validate
 * @return bool True if valid
 * @throws Exception if validation fails
 */
function validateCustomerName(string $name): bool
{
    if (empty($name)) {
        throw new Exception("Customer name is required.");
    }
    if (strlen($name) > maxCustomerNameLength()) {
        throw new Exception("Customer name must be " . maxCustomerNameLength() . " characters or fewer.");
    }
    return true;
}

/**
 * Validate table number
 * @param int $tableNumber The table number to validate
 * @return bool True if valid
 * @throws Exception if validation fails
 */
function validateTableNumber(int $tableNumber): bool
{
    if ($tableNumber <= 0) {
        throw new Exception("Table number must be greater than 0.");
    }
    if ($tableNumber > maxTableNumber()) {
        throw new Exception("Table number must be " . maxTableNumber() . " or lower.");
    }
    return true;
}

/**
 * Build a standardized success response with metadata
 * @param array $data The response data
 * @param string $message Optional success message
 * @return array The formatted response
 */
function buildSuccessResponse(array $data = [], string $message = 'Operation successful'): array
{
    return [
        'success' => true,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => $data
    ];
}

/**
 * Build a standardized error response with metadata
 * @param string $message The error message
 * @param string $code Optional error code
 * @return array The formatted response
 */
function buildErrorResponse(string $message, string $code = 'ERROR'): array
{
    return [
        'success' => false,
        'message' => $message,
        'code' => $code,
        'timestamp' => date('Y-m-d H:i:s')
    ];
}
?>
