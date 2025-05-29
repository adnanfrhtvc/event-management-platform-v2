<?php
require_once __DIR__ . '/../dao/AuthDao.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService {
    private $auth_dao;

    public function __construct() {
        $this->auth_dao = new AuthDao(); 
    }

    public function register($data) {
    try {
        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email format'];
        }

        // Check email existence
        $existing = $this->auth_dao->get_user_by_email($data['email']);
        if ($existing) {
            return ['success' => false, 'error' => 'Email already registered'];
        }

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // Insert into database
        $userId = $this->auth_dao->insert($data);

        return ['success' => true, 'data' => [
            'id' => $userId,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role']
        ]];
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return ['success' => false, 'error' => 'Database operation failed'];
    }
}

    public function login($data) {  
        if (empty($data['email']) || empty($data['password'])) {
            return ['success' => false, 'error' => 'Email and password are required.'];
        }

        // Fetch user by email
        $user = $this->auth_dao->get_user_by_email($data['email']);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            return ['success' => false, 'error' => 'Invalid email or password.'];
        }

        // Create JWT payload (include user role)
        $jwt_payload = [
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'] // From your `users` table
            ],
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) // 24 hours
        ];

        // Generate JWT token
        $token = JWT::encode(
            $jwt_payload,
            Config::JWT_SECRET(),
            'HS256'
        );

        // Return user data (without password) + token
        unset($user['password']);
        return ['success' => true, 'data' => array_merge($user, ['token' => $token])];
    }
}