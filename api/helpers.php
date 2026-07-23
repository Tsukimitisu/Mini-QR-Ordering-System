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

/**
 * Sanitize user input by trimming and removing null bytes
 * @param string $input The input string to sanitize
 * @return string The sanitized string
 */
function sanitizeInput(string $input): string
{
    $sanitized = trim($input);
    $sanitized = str_replace("\x00", '', $sanitized);
    return $sanitized;
}
?>
