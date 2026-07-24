# Mini Ordering System - Class Library Reference

## Overview
This document provides a reference guide for all utility classes in the Mini Ordering System API.

## Table of Contents

### Core Classes
1. [Config](#config) - Application configuration
2. [Database](#database) - Database operations
3. [Router](#router) - HTTP routing
4. [Request](#request) - HTTP request handling
5. [Response](#response) - Standardized API responses

### Security & Authentication
6. [Auth](#auth) - User authentication and authorization
7. [CsrfToken](#csrftoken) - CSRF protection
8. [Security](#security) - Encryption and security utilities
9. [Validator](#validator) - Input validation

### Session & Caching
10. [Session](#session) - Session management
11. [Cache](#cache) - File-based caching

### Utility Classes
12. [Logger](#logger) - Application logging
13. [ExceptionHandler](#exceptionhandler) - Error handling
14. [Pagination](#pagination) - Result pagination
15. [Upload](#upload) - File upload handling
16. [Mailer](#mailer) - Email sending
17. [RateLimiter](#ratelimiter) - API rate limiting
18. [Util](#util) - General utilities

---

## Core Classes

### Config
Location: `config.php`

Centralized configuration file containing:
- Database connection settings
- Application settings
- Security settings
- API configuration
- Feature flags
- Validation rules

**Usage:**
```php
require_once 'config.php';

// Access constants
echo DB_HOST;
echo APP_VERSION;
```

### Database
Location: `api/Database.php`

Singleton class for database operations.

**Methods:**
```php
Database::getInstance()->getPdo()           // Get PDO instance
Database::getInstance()->query($sql, $params) // Execute prepared statement
Database::getInstance()->fetchOne($sql, $params) // Fetch single row
Database::getInstance()->fetchAll($sql, $params) // Fetch all rows
Database::getInstance()->insert($table, $data) // Insert record
Database::getInstance()->update($table, $data, $where) // Update record
Database::getInstance()->delete($table, $where) // Delete record
Database::getInstance()->beginTransaction() // Start transaction
Database::getInstance()->commit() // Commit transaction
Database::getInstance()->rollback() // Rollback transaction
```

### Router
Location: `api/Router.php`

HTTP routing system for handling requests.

**Methods:**
```php
$router = new Router();
$router->get('/path', $callback)
$router->post('/path', $callback)
$router->put('/path', $callback)
$router->delete('/path', $callback)
$router->group('/prefix', function($router) { ... })
$router->dispatch()
```

### Request
Location: `api/Request.php`

HTTP request handling and validation.

**Methods:**
```php
$request = new Request();
$request->getMethod()      // GET, POST, etc.
$request->getPath()        // Request path
$request->getQuery($key)   // Query parameter
$request->getBody($key)    // Body parameter
$request->getHeader($key)  // Request header
$request->isAjax()         // Check if AJAX
$request->isJson()         // Check if JSON
$request->getClientIp()    // Client IP
```

### Response
Location: `api/Response.php`

Standardized API response formatting.

**Methods:**
```php
Response::success($data, $message, $statusCode)
Response::error($message, $statusCode, $errors)
Response::validationError($errors)
Response::notFound($message)
Response::unauthorized($message)
Response::forbidden($message)
Response::internalError($message)
Response::paginated($data, $total, $page, $pageSize)
```

---

## Security & Authentication

### Auth
Location: `api/Auth.php`

User authentication and authorization.

**Methods:**
```php
Auth::init()              // Initialize authentication
Auth::authenticateAdmin($username, $password) // Authenticate admin
Auth::isAuthenticated()   // Check if user is authenticated
Auth::isAdmin()          // Check if user is admin
Auth::getUser()          // Get current user
Auth::logout()           // Logout user
Auth::requireAuth()      // Require authentication
Auth::requireAdmin()     // Require admin role
```

### CsrfToken
Location: `api/CsrfToken.php`

CSRF token generation and validation.

**Methods:**
```php
CsrfToken::generate()     // Generate CSRF token
CsrfToken::validate($token) // Validate token
CsrfToken::refresh()      // Refresh token
CsrfToken::getTokenFromRequest() // Get token from request
CsrfToken::getHiddenInput() // Get form input HTML
CsrfToken::getMetaTag()   // Get meta tag HTML
```

### Security
Location: `api/Security.php`

Encryption, hashing, and security utilities.

**Methods:**
```php
Security::generateToken($length)           // Generate token
Security::generateRandomString($length)    // Generate random string
Security::hash($value)                     // SHA256 hash
Security::hashPassword($password)          // Bcrypt password hash
Security::verifyPassword($password, $hash) // Verify password
Security::encrypt($data, $key)             // Encrypt data
Security::decrypt($data, $key)             // Decrypt data
Security::escape($value)                   // HTML escape
Security::sanitizeHtml($html)              // Sanitize HTML
```

### Validator
Location: `api/Validator.php`

Input validation and sanitization.

**Methods:**
```php
Validator::validateEmail($email)
Validator::validateInteger($value, $min, $max)
Validator::validateFloat($value, $min, $max)
Validator::validateStringLength($value, $minLength, $maxLength)
Validator::validateTableNumber($tableNumber)
Validator::validateQuantity($quantity)
Validator::sanitizeString($value)
Validator::sanitizeEmail($email)
Validator::sanitizeArray($data, $sanitizer)
```

---

## Session & Caching

### Session
Location: `api/Session.php`

Session management and flash data.

**Methods:**
```php
Session::init($options)      // Initialize session
Session::set($key, $value)   // Set session value
Session::get($key, $default) // Get session value
Session::has($key)           // Check if key exists
Session::delete($key)        // Delete session value
Session::flash($key, $value) // Set flash data
Session::getFlash($key)      // Get flash data
Session::flush()             // Flush all session data
Session::regenerate()        // Regenerate session ID
Session::destroy()           // Destroy session
```

### Cache
Location: `api/Cache.php`

File-based caching system.

**Methods:**
```php
Cache::set($key, $value, $ttl)      // Store in cache
Cache::get($key, $default)          // Retrieve from cache
Cache::has($key)                    // Check if key exists
Cache::delete($key)                 // Delete cache entry
Cache::flush()                      // Clear all cache
Cache::remember($key, $callback, $ttl) // Get or set cache
Cache::cleanup()                    // Remove expired entries
Cache::stats()                      // Get cache statistics
```

---

## Utility Classes

### Logger
Location: `api/Logger.php`

Application logging with multiple levels.

**Methods:**
```php
Logger::getInstance()->debug($message, $context)
Logger::getInstance()->info($message, $context)
Logger::getInstance()->warning($message, $context)
Logger::getInstance()->error($message, $context)

// Global helper functions
log_debug($message, $context)
log_info($message, $context)
log_warning($message, $context)
log_error($message, $context)
```

### ExceptionHandler
Location: `api/ExceptionHandler.php`

Global exception and error handling.

**Methods:**
```php
ExceptionHandler::register()
ExceptionHandler::handleException($exception)
ExceptionHandler::handleError($errno, $errstr, $errfile, $errline)
```

### Pagination
Location: `api/Pagination.php`

Result pagination helper.

**Methods:**
```php
$pagination = new Pagination($total, $perPage, $currentPage);
$pagination->getTotal()
$pagination->getPerPage()
$pagination->getTotalPages()
$pagination->getOffset()
$pagination->getLimit()
$pagination->getNextPage()
$pagination->getPreviousPage()
$pagination->getPageNumbers()
$pagination->getMetadata()
```

### Upload
Location: `api/Upload.php`

File upload handling and validation.

**Methods:**
```php
$upload = new Upload($_FILES['file']);
$upload->validate($maxSize)
$upload->store($directory, $filename)
$upload->getError()
Upload::delete($filepath)
```

### Mailer
Location: `api/Mailer.php`

Email composition and delivery.

**Methods:**
```php
Mailer::create()
    ->to($email, $name)
    ->cc($email, $name)
    ->bcc($email, $name)
    ->subject($subject)
    ->body($body)
    ->htmlBody($htmlBody)
    ->attach($filepath, $filename)
    ->send()

Mailer::sendSimple($to, $subject, $body)
```

### RateLimiter
Location: `api/RateLimiter.php`

API request rate limiting.

**Methods:**
```php
$limiter = new RateLimiter($clientId, $limit, $window);
$limiter->isAllowed()
$limiter->getRemaining()
$limiter->getResetTime()
$limiter->reset()
RateLimiter::middleware($limit, $window)
```

### Util
Location: `api/Util.php`

General utility functions.

**Methods:**
```php
Util::formatBytes($bytes, $precision)
Util::formatNumber($number, $decimals)
Util::formatPrice($price, $currency)
Util::formatDate($date, $format)
Util::formatRelativeTime($date)
Util::truncate($string, $length)
Util::slug($string)
Util::capitalize($string)
Util::generateUuid()
Util::arrayToCsv($data)
Util::csvToArray($csv)
Util::isHttps()
Util::getCurrentUrl()
Util::getClientIp()
```

---

## Best Practices

### Database Operations
- Always use prepared statements
- Use transactions for multiple related operations
- Handle exceptions properly
- Log important operations

### Security
- Always sanitize user input
- Validate all request data
- Use CSRF tokens for state-changing requests
- Hash passwords using bcrypt
- Set secure headers

### API Responses
- Always use Response class for consistent formatting
- Include proper HTTP status codes
- Provide helpful error messages
- Include pagination metadata when applicable

### File Operations
- Validate file uploads
- Store files outside web root when possible
- Use unique filenames
- Log file operations

---

## Error Handling

The application uses custom exception classes:

```php
AppException - General application exception
ValidationException - Validation errors (422)
AuthenticationException - Auth failures (401)
AuthorizationException - Permission denied (403)
NotFoundException - Resource not found (404)
```

All exceptions should extend `AppException` and provide:
- Clear error message
- Appropriate HTTP status code
- Context data for logging

---

## Configuration

Application configuration is centralized in `config.php`. Key environment variables:

```
DB_HOST=localhost
DB_NAME=mini_qr_ordering_db
DB_USER=root
DB_PASS=

APP_DEBUG=false
APP_ENV=development

ADMIN_USERNAME=admin
ADMIN_PASSWORD=admin123

LOG_LEVEL=INFO
```

Copy `.env.example` to `.env` and update values for your environment.
