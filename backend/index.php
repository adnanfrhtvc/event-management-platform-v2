<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/config.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/dao/AuthDao.php';
require __DIR__ . '/services/AuthService.php';
require __DIR__ . '/middleware/AuthMiddleware.php'; 
require __DIR__ . '/data/roles.php'; 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Register services and middleware
Flight::register('auth_service', 'AuthService');
Flight::register('auth_middleware', 'AuthMiddleware'); 

// Global middleware for authentication
Flight::before('start', function() {
    $request = Flight::request();
    $path = $request->url;
    $method = $request->method;

    $isPublic = false;

    // Defining public routes
    if (
        ($method === 'POST' && in_array($path, ['/auth/login', '/auth/register'])) ||
        ($method === 'GET' && (
            preg_match('#^/api/events(/.*)?$#', $path) ||
            preg_match('#^/api/categories(/.*)?$#', $path) ||
            strpos($path, '/public/v1/docs') === 0
        ))
    ) {
        $isPublic = true;
    }

    // Enforce token check only if not public
    if (!$isPublic) {
        try {
            $token = $request->getHeader('Authentication');
            Flight::auth_middleware()->verifyToken($token);
        } catch (Exception $e) {
            Flight::halt(401, json_encode(['error' => $e->getMessage()]));
        }
    }
});


// Load all routes
foreach (glob(__DIR__ . "/routes/*.php") as $route) {
    require $route;
}

// Default route
Flight::route('/', function() {
    echo 'Event Management API';
});

Flight::start();