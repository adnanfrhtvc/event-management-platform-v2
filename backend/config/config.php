<?php
class Config {
    
    public static function DB_HOST() {
        return "localhost";
    }

    public static function DB_PORT() {
        return "3307";
    }

    public static function DB_NAME() {
        return "event_management";
    }

    public static function DB_USER() {
        return "root";
    }

    public static function DB_PASSWORD() {
        return "";
    }

    // JWT Secret 
    public static function JWT_SECRET() {
        return "seceret"; 
    }
}