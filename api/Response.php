<?php
/**
 * Response - Standardized API response handler
 * Provides consistent response formatting across all API endpoints
 */

class Response
{
    /**
     * Send a successful response
     */
    public static function success(mixed $data = null, string $message = 'Success', int $statusCode = 200): void
    {
        self::send([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s'),
        ], $statusCode);
    }

    /**
     * Send an error response
     */
    public static function error(string $message = 'Error', int $statusCode = 400, array $errors = []): void
    {
        self::send([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s'),
        ], $statusCode);
    }

    /**
     * Send a validation error response
     */
    public static function validationError(array $errors, string $message = 'Validation failed'): void
    {
        self::send([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s'),
        ], 422);
    }

    /**
     * Send a not found response
     */
    public static function notFound(string $message = 'Resource not found'): void
    {
        self::send([
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
        ], 404);
    }

    /**
     * Send an unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized'): void
    {
        self::send([
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
        ], 401);
    }

    /**
     * Send a forbidden response
     */
    public static function forbidden(string $message = 'Forbidden'): void
    {
        self::send([
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
        ], 403);
    }

    /**
     * Send an internal server error response
     */
    public static function internalError(string $message = 'Internal server error'): void
    {
        self::send([
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
        ], 500);
    }

    /**
     * Send a service unavailable response
     */
    public static function serviceUnavailable(string $message = 'Service unavailable'): void
    {
        self::send([
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
        ], 503);
    }

    /**
     * Send paginated response
     */
    public static function paginated(array $data, int $total, int $page, int $pageSize, string $message = 'Success', int $statusCode = 200): void
    {
        self::send([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'pageSize' => $pageSize,
                'totalPages' => ceil($total / $pageSize),
            ],
            'timestamp' => date('Y-m-d H:i:s'),
        ], $statusCode);
    }

    /**
     * Send raw response
     */
    public static function raw(mixed $data, string $contentType = 'application/json', int $statusCode = 200): void
    {
        header('Content-Type: ' . $contentType . '; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        http_response_code($statusCode);
        
        if (is_array($data) || is_object($data)) {
            echo json_encode($data, JSON_UNESCAPED_SLASHES);
        } else {
            echo $data;
        }
        
        exit;
    }

    /**
     * Main response sending method
     */
    private static function send(array $payload, int $statusCode = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        http_response_code($statusCode);
        echo json_encode($payload, JSON_UNESCAPED_SLASHES);
        exit;
    }
}
?>
