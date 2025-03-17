<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';

Flight::route('/api/test', function() {
    $db = Database::connect();
    $stmt = $db->query("SELECT 'Connected to MySQL' AS message");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    Flight::json($result);
});
