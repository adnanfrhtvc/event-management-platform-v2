<?php
require_once __DIR__ . '/config.php';

class Database {
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=" . Config::DB_HOST() . 
                    ";port=" . Config::DB_PORT() . 
                    ";dbname=" . Config::DB_NAME() . 
                    ";charset=utf8",
                    Config::DB_USER(),
                    Config::DB_PASSWORD(),
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}