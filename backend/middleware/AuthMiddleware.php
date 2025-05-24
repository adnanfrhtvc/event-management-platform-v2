<?php
require_once __DIR__ . '/../config/config.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    // Verify JWT token
    public function verifyToken($token) {
        if (!$token) {
            Flight::halt(401, json_encode(["error" => "Missing authentication header"]));
        }
        try {
            $decoded = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
            Flight::set('user', $decoded->user); // Store user data
            return true;
        } catch (Exception $e) {
            Flight::halt(401, json_encode(["error" => "Invalid token: " . $e->getMessage()]));
        }
    }

    // Check for specific role (e.g., admin)
    public function authorizeRole($requiredRole) {
        $user = Flight::get('user');
        if ($user->role !== $requiredRole) {
            Flight::halt(403, json_encode(["error" => "Access denied: insufficient privileges"]));
        }
    }

    // Check for multiple allowed roles
    public function authorizeRoles($allowedRoles) {
        $user = Flight::get('user');
        if (!in_array($user->role, $allowedRoles)) {
            Flight::halt(403, json_encode(["error" => "Forbidden: role not allowed"]));
        }
    }
}