<?php
require __DIR__ . '/vendor/autoload.php';

// Load all route files automatically
foreach (glob(__DIR__ . "/routes/*.php") as $route) {
    require $route;
}

// Default route
Flight::route('/', function() {
    echo 'FlightPHP is working!';
});

// Start FlightPHP
Flight::start();
