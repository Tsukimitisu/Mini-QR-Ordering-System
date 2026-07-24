<?php
/**
 * Request - Request handling and validation utility
 * Provides methods for processing and validating HTTP requests
 */

require_once __DIR__ . '/Logger.php';
require_once __DIR__ . '/Validator.php';

class Request
{
    private $method;
    private $path;
    private $query;
    private $body;
    private $headers;
    private $files;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $this->query = $_GET ?? [];
        $this->headers = $this->parseHeaders();
        $this->files = $_FILES ?? [];
        $this->body = $this->parseBody();

        log_debug('Request received', [
            'method' => $this->method,
            'path' => $this->path,
            'query' => $this->query,
        ]);
    }

    /**
     * Get request method
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get request path
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get query parameter
     */
    public function getQuery(string $key = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? null;
    }

    /**
     * Get body parameter
     */
    public function getBody(string $key = null): mixed
    {
        if ($key === null) {
            return $this->body;
        }
        return $this->body[$key] ?? null;
    }

    /**
     * Get request header
     */
    public function getHeader(string $key = null): mixed
    {
        if ($key === null) {
            return $this->headers;
        }
        return $this->headers[$key] ?? null;
    }

    /**
     * Get uploaded file
     */
    public function getFile(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Check if request has body parameter
     */
    public function has(string $key): bool
    {
        return isset($this->body[$key]);
    }

    /**
     * Check if request is AJAX
     */
    public function isAjax(): bool
    {
        return strtolower($this->getHeader('X-Requested-With') ?? '') === 'xmlhttprequest';
    }

    /**
     * Check if request is JSON
     */
    public function isJson(): bool
    {
        $contentType = $this->getHeader('Content-Type') ?? '';
        return strpos($contentType, 'application/json') !== false;
    }

    /**
     * Get client IP address
     */
    public function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        return trim($ip);
    }

    /**
     * Get user agent
     */
    public function getUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Get referer
     */
    public function getReferer(): ?string
    {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }

    /**
     * Validate required fields
     */
    public function validateRequired(array $fields): bool
    {
        return Validator::validateRequiredKeys($this->body, $fields);
    }

    /**
     * Get all validated data
     */
    public function getValidated(array $rules): array
    {
        $validated = [];

        foreach ($rules as $field => $rule) {
            if (!isset($this->body[$field])) {
                continue;
            }

            $value = $this->body[$field];

            // Apply sanitization
            if (is_string($value)) {
                $value = Validator::sanitizeString($value);
            }

            $validated[$field] = $value;
        }

        return $validated;
    }

    /**
     * Parse request body (handles JSON and form data)
     */
    private function parseBody(): array
    {
        if ($this->method === 'GET') {
            return [];
        }

        $contentType = $this->getHeader('Content-Type') ?? '';

        // JSON request
        if (strpos($contentType, 'application/json') !== false) {
            $json = file_get_contents('php://input');
            return json_decode($json, true) ?? [];
        }

        // Form data
        return $_POST ?? [];
    }

    /**
     * Parse request headers
     */
    private function parseHeaders(): array
    {
        $headers = [];

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) === 'HTTP_') {
                    $key = substr($key, 5);
                    $key = str_replace('_', '-', $key);
                    $headers[$key] = $value;
                }
            }
        }

        return array_change_key_case($headers, CASE_LOWER);
    }
}
?>
