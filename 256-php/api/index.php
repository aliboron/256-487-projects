<?php

require __DIR__ . '/restUtil.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/authHelper.php';

ensureSession();

$manager = new EndpointManager();

registerControllersFromDir($manager, __DIR__ . '/controllers');

$method = $_SERVER['REQUEST_METHOD'];

global $requestPath;

$endpoint = $manager->find($method, $requestPath);

if (!$endpoint) {
    http_response_code(404);
    echo (new ApiResponse(false, null, 'Not Found'))->toJson();
    exit;
}

try {
    $result = $endpoint->invoke($requestPath);
    echo $result->toJson();
} catch (Throwable $e) {
    http_response_code(500);
    echo (new ApiResponse(false, null, "Server Error: " . $e->getMessage()))->toJson();
}
