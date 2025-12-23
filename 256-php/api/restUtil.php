<?php

header('Content-Type: application/json; charset=utf-8');

// ==========================================================
//  Build $requestPath from REQUEST_URI, relative to /api
// ==========================================================
$fullUri   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // e.g. /api/users/1
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');   // e.g. /api

// Remove the /api prefix if present
if ($scriptDir !== '/' && strpos($fullUri, $scriptDir) === 0) {
    $requestPath = substr($fullUri, strlen($scriptDir));       // -> /users/1
} else {
    $requestPath = $fullUri;                                   // fallback
}

// Normalise empty path to '/'
if ($requestPath === '' || $requestPath === false) {
    $requestPath = '/';
}

// ==========================================================
//  Route Attribute
// ==========================================================
#[\Attribute(\Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public string $path,
        public string $method = 'GET',
        public ?string $responseType = null
    ) {}
}

// ==========================================================
//  API Response
// ==========================================================
class ApiResponse
{
    public function __construct(
        public bool $success,
        public mixed $data = null,
        public ?string $message = null
    ) {}

    public function toJson(): string
    {
        return json_encode([
            'success' => $this->success,
            'data'    => $this->data,
            'message' => $this->message
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

// ==========================================================
//  Endpoint class
// ==========================================================
class Endpoint
{
    public function __construct(
        public string $path,
        public string $method,
        private mixed $handler,
        public array $variables = [],
        public ?string $responseType = null
    ) {}

    public function getHandler(): callable
    {
        return $this->handler;
    }

    public function extractVariables(string $requestPath): array
    {
        $vars = [];
        $pathParts = explode('/', trim($this->path, '/'));
        $reqParts  = explode('/', trim($requestPath, '/'));

        foreach ($pathParts as $i => $part) {
            if (preg_match('/^{(.+)}$/', $part, $m)) {
                $vars[$m[1]] = $reqParts[$i] ?? null;
            }
        }
        return $vars;
    }

    public function matches(string $method, string $path): bool
    {
        if ($method !== $this->method) return false;
        $pattern = '@^' . preg_replace('@\{[^}]+\}@', '([^/]+)', $this->path) . '$@';
        return preg_match($pattern, $path) === 1;
    }

    /**
     * Invoke the handler with automatic parameter resolution from:
     * - Path variables (e.g., /users/{id})
     * - Query parameters (e.g., ?q=ali&page=2)
     */
    public function invoke(string $requestPath): mixed
    {
        // Get path variables
        $pathVars = $this->extractVariables($requestPath);

        // Get query parameters
        $queryParams = $_GET;

        // Check if any query parameter tries to override a path parameter
        $conflicts = array_intersect_key($queryParams, $pathVars);
        if (!empty($conflicts)) {
            $conflictNames = implode(', ', array_keys($conflicts));
            throw new InvalidArgumentException(
                "Query parameters cannot override path parameters. Conflicting parameters: $conflictNames"
            );
        }

        // Combine path and query parameters (path vars take precedence)
        $allParams = array_merge($queryParams, $pathVars);

        // Use reflection to get handler parameters
        $reflection = $this->getReflection();
        $params = $reflection->getParameters();

        $args = [];
        foreach ($params as $param) {
            $paramName = $param->getName();
            $paramType = $param->getType();

            // Check if parameter value exists
            if (array_key_exists($paramName, $allParams)) {
                $value = $allParams[$paramName];

                // Treat empty strings as null for nullable parameters
                if ($paramType && $paramType->allowsNull() && $value === '') {
                    $value = null;
                }

                // Type conversion (only if value is not null or type doesn't allow null)
                if ($paramType && $value !== null) {
                    $typeName = $paramType instanceof ReflectionNamedType ? $paramType->getName() : null;

                    if ($typeName === 'int') {
                        $value = (int) $value;
                    } elseif ($typeName === 'float') {
                        $value = (float) $value;
                    } elseif ($typeName === 'bool') {
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    } elseif ($typeName === 'string') {
                        $value = (string) $value;
                    }
                }

                $args[] = $value;
            } elseif ($param->isDefaultValueAvailable()) {
                // Use default value (for optional parameters)
                $args[] = $param->getDefaultValue();
            } elseif ($paramType && $paramType->allowsNull()) {
                // Nullable parameter without value
                $args[] = null;
            } else {
                // Required parameter missing
                throw new InvalidArgumentException("Missing required parameter: $paramName");
            }
        }

        return call_user_func_array($this->handler, $args);
    }

    /**
     * Get reflection of the handler method
     */
    private function getReflection(): ReflectionFunctionAbstract
    {
        if (is_array($this->handler)) {
            return new ReflectionMethod($this->handler[0], $this->handler[1]);
        } else {
            return new ReflectionFunction($this->handler);
        }
    }
}

// ==========================================================
//  Endpoint Manager
// ==========================================================
class EndpointManager
{
    private array $endpoints = [];

    public function register(Endpoint $endpoint): void
    {
        $key = $endpoint->method . ' ' . $endpoint->path;
        $this->endpoints[$key] = $endpoint;
    }

    public function find(string $method, string $path): ?Endpoint
    {
        $key = "$method $path";
        if (isset($this->endpoints[$key])) return $this->endpoints[$key];

        foreach ($this->endpoints as $ep) {
            if ($ep->matches($method, $path)) return $ep;
        }
        return null;
    }
}

// ==========================================================
//  Auto-registration for a single controller
// ==========================================================
function registerRoutesFromController(EndpointManager $manager, object $controller): void
{
    $ref = new ReflectionClass($controller);
    foreach ($ref->getMethods() as $method) {
        $attrs = $method->getAttributes(Route::class);
        if (!$attrs) continue;

        $route = $attrs[0]->newInstance();
        $handler = [$controller, $method->getName()];

        $endpoint = new Endpoint(
            path: $route->path,
            method: $route->method,
            handler: $handler,
            responseType: $route->responseType
        );

        $manager->register($endpoint);
    }
}

// ==========================================================
//  Auto-registration for ALL controllers in a folder
//  Assumes: 
//    - each file in $dir ends with *Controller.php
//    - class name == file name (no namespace)
// ==========================================================
function registerControllersFromDir(EndpointManager $manager, string $dir): void
{
    foreach (glob($dir . '/*Controller.php') as $file) {
        require_once $file;

        $className = pathinfo($file, PATHINFO_FILENAME); // e.g. UserController
        if (!class_exists($className)) {
            continue;
        }

        $controller = new $className();
        registerRoutesFromController($manager, $controller);
    }
}
