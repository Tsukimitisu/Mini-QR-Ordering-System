<?php
/**
 * Configuration file for Mini Ordering System
 * Centralizes all configuration settings for the application
 */

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'mini_qr_ordering_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');
define('DB_TIMEOUT', 10); // seconds

// Application Settings
define('APP_NAME', 'Gourmet Express');
define('APP_VERSION', '1.0.0');
define('APP_DEBUG', getenv('APP_DEBUG') ?: false);
define('APP_ENVIRONMENT', getenv('APP_ENV') ?: 'development');

// API Settings
define('API_TIMEOUT', 30); // seconds
define('MAX_REQUEST_BODY_SIZE', 1048576); // 1MB
define('API_RATE_LIMIT', 100); // requests per minute
define('CACHE_EXPIRY', 3600); // 1 hour

// Security Settings
define('ENABLE_CSRF_PROTECTION', true);
define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);

// Logging Settings
define('LOG_LEVEL', getenv('LOG_LEVEL') ?: 'INFO');
define('LOG_FILE', getenv('LOG_FILE') ?: dirname(__DIR__) . '/logs/app.log');
define('LOG_MAX_SIZE', 10485760); // 10MB

// Feature Flags
define('ENABLE_STOCK_MANAGEMENT', true);
define('ENABLE_PAYMENT_SIMULATION', true);
define('ENABLE_QR_GENERATION', true);

// Validation Rules
define('MIN_TABLE_NUMBER', 1);
define('MAX_TABLE_NUMBER', 999);
define('MAX_ORDER_ITEMS', 100);
define('MAX_PRODUCT_NAME_LENGTH', 255);

return [
    'database' => [
        'host' => DB_HOST,
        'name' => DB_NAME,
        'user' => DB_USER,
        'pass' => DB_PASS,
        'charset' => DB_CHARSET,
        'timeout' => DB_TIMEOUT,
    ],
    'app' => [
        'name' => APP_NAME,
        'version' => APP_VERSION,
        'debug' => APP_DEBUG,
        'environment' => APP_ENVIRONMENT,
    ],
    'security' => [
        'csrf_protection' => ENABLE_CSRF_PROTECTION,
        'csrf_lifetime' => CSRF_TOKEN_LIFETIME,
        'session_timeout' => SESSION_TIMEOUT,
    ],
];
?>
