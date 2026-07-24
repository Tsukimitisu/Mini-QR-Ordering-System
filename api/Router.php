<?php
/**
 * Router - HTTP routing management
 * Handles route matching and dispatching requests to appropriate controllers
 */

require_once __DIR__ . '/Logger.php';

class Router
{
    private $routes = [];
    private $currentPath = '';
    private $currentMethod = '';
    private $notFoundCallback = null;
    private $middlewares = [];

    public function __construct()
    {
        $this->currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $this->currentMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Register a GET route
     */
    public function get(string $path, callable $callback): self
    {
        return $this->register('GET', $path, $callback);
    }

    /**
     * Register a POST route
     */
    public function post(string $path, callable $callback): self
    {
        return $this->register('POST', $path, $callback);
    }

    /**
     * Register a PUT route
     */
    public function put(string $path, callable $callback): self
    {
        return $this->register('PUT', $path, $callback);
    }

    /**
     * Register a DELETE route
     */
    public function delete(string $path, callable $callback): self
    {
        return $this->register('DELETE', $path, $callback);
    }

    /**
     * Register a PATCH route
     */
    public function patch(string $path, callable $callback): self
    {
        return $this->register('PATCH', $path, $callback);
    }

    /**
     * Register a route for all methods
     */
    public function any(string $path, callable $callback): self
    {
        foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
            $this->register($method, $path, $callback);
        }
        return $this;
    }

    /**
     * Register a route group with prefix
     */
    public function group(string $prefix, callable $callback): self
    {
        $previousPath = $this->currentPath;
        $this->currentPath = $prefix;

        call_user_func($callback, $this);

        $this->currentPath = $previousPath;
        return $this;
    }

    /**
     * Register a middleware
     */
    public function middleware(string $name, callable $callback): self
    {
        $this->middlewares[$name] = $callback;
        return $this;
    }

    /**
     * Register a 404 not found callback
     */
    public function notFound(callable $callback): self
    {
        $this->notFoundCallback = $callback;
        return $this;
    }

    /**
     * Dispatch the request
     */
    public function dispatch(): void
    {
        $route = $this->match();

        if ($route === null) {
            $this->handleNotFound();
            return;
        }

        log_info('Route matched', [
            'method' => $this->currentMethod,
            'path' => $this->currentPath,
        ]);

        // Execute route callback
        call_user_func($route['callback'], ...$route['params']);
    }

    /**
     * Match current request to a route
     */
    private function match(): ?array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $this->currentMethod) {
                continue;
            }

            $pattern = $this->pathToPattern($route['path']);

            if (preg_match($pattern, $this->currentPath, $matches)) {
                array_shift($matches); // Remove full match
                return [
                    'callback' => $route['callback'],
                    'params' => array_values($matches),
                ];
            }
        }

        return null;
    }

    /**
     * Convert path pattern to regex
     */
    private function pathToPattern(string $path): string
    {
        $pattern = preg_replace_callback('/\{(\w+)\}/', function ($matches) {
            return '(?P<' . $matches[1] . '>[^/]+)';
        }, $path);

        return '#^' . $pattern . '$#';
    }

    /**
     * Register a route
     */
    private function register(string $method, string $path, callable $callback): self
    {
        $path = $this->currentPath . $path;

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
        ];

        return $this;
    }

    /**
     * Handle 404 not found
     */
    private function handleNotFound(): void
    {
        log_warning('Route not found', [
            'method' => $this->currentMethod,
            'path' => $this->currentPath,
        ]);

        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            http_response_code(404);
            echo 'Not Found';
        }
    }
}
?>
