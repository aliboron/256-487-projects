<?php

require __DIR__ . '/restUtil.php';
require_once __DIR__ . '/db.php';

// Create endpoint manager
$manager = new EndpointManager();

// Register all controllers from /controllers (inside this folder)
registerControllersFromDir($manager, __DIR__ . '/controllers');

// Get HTTP method and path
$method = $_SERVER['REQUEST_METHOD'];

// $requestPath comes from restUtil.php
global $requestPath;

$endpoint = $manager->find($method, $requestPath);

if (!$endpoint) {
    http_response_code(404);
    echo (new ApiResponse(false, null, 'Not Found'))->toJson();
    exit;
}

try {
    $vars     = $endpoint->extractVariables($requestPath);
    $handler  = $endpoint->getHandler();
    $response = $handler(...array_values($vars));

    http_response_code($response->success ? 200 : 400);
    echo $response->toJson();
} catch (Throwable $e) {
    http_response_code(500);
    echo (new ApiResponse(false, null, "Server Error: " . $e->getMessage()))->toJson();
}
