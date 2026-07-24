<?php
/**
 * ExceptionHandler - Global exception and error handling
 * Provides centralized exception handling and error logging
 */

require_once __DIR__ . '/Logger.php';

class ExceptionHandler
{
    /**
     * Register exception handlers
     */
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Handle uncaught exceptions
     */
    public static function handleException(\Throwable $exception): void
    {
        $code = $exception->getCode();
        $statusCode = ($code >= 400 && $code < 600) ? $code : 500;

        log_error('Uncaught exception', [
            'message' => $exception->getMessage(),
            'code' => $code,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        self::sendErrorResponse($exception->getMessage(), $statusCode);
    }

    /**
     * Handle PHP errors
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        // Ignore suppressed errors
        if (error_reporting() === 0) {
            return false;
        }

        $level = self::getErrorLevel($errno);

        log_error('PHP error', [
            'level' => $level,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
        ]);

        // Convert error to exception if critical
        if (in_array($errno, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        }

        return false;
    }

    /**
     * Handle fatal errors and shutdown
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error === null) {
            return;
        }

        if (!in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            return;
        }

        log_error('Fatal error', [
            'type' => self::getErrorLevel($error['type']),
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line'],
        ]);

        self::sendErrorResponse('Fatal error occurred', 500);
    }

    /**
     * Get human-readable error level
     */
    private static function getErrorLevel(int $errno): string
    {
        $levels = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];

        return $levels[$errno] ?? 'UNKNOWN_ERROR';
    }

    /**
     * Send error response
     */
    private static function sendErrorResponse(string $message, int $statusCode): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        http_response_code($statusCode);

        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        // Include debug information if in debug mode
        if (defined('APP_DEBUG') && APP_DEBUG) {
            $response['debug'] = [
                'php_version' => phpversion(),
                'memory_usage' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true),
            ];
        }

        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        exit;
    }
}

/**
 * Custom application exception
 */
class AppException extends \Exception
{
    protected $statusCode = 500;

    public function __construct(string $message = '', int $statusCode = 500, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}

/**
 * Validation exception
 */
class ValidationException extends AppException
{
    protected $statusCode = 422;
    protected $errors = [];

    public function __construct(string $message = '', array $errors = [])
    {
        parent::__construct($message, 422);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

/**
 * Authentication exception
 */
class AuthenticationException extends AppException
{
    protected $statusCode = 401;
}

/**
 * Authorization exception
 */
class AuthorizationException extends AppException
{
    protected $statusCode = 403;
}

/**
 * Not found exception
 */
class NotFoundException extends AppException
{
    protected $statusCode = 404;
}
?>
