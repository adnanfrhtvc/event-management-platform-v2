<?php
require_once __DIR__ . '/../dao/UserDao.php';

class UserService {
    private $userDao;

    public function __construct(UserDao $userDao) {
        $this->userDao = $userDao;
    }

    public function getAllUsers() {
        return $this->userDao->getAll();
    }

    public function getUserById($id) {
        $user = $this->userDao->getById($id);
        if (!$user) {
            throw new Exception("User not found", 404);
        }
        return $user;
    }

    public function createUser($data) {
        // Required fields: name, email, password
        $required = ['email', 'password', 'name'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field", 400);
            }
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format", 400);
        }

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            $id = $this->userDao->insert($data);
            return $this->getUserById($id); // Return full created user object
        } catch (Exception $e) {
            if (strpos($e->getMessage(), "already exists") !== false) {
                throw new Exception("Email already registered", 409);
            }
            throw new Exception("Service error: " . $e->getMessage(), 500);
        }
    }

    public function updateUser($id, $data) {
        $existingUser = $this->getUserById($id);

        // Check for email uniqueness
        if (isset($data['email']) && $data['email'] !== $existingUser['email']) {
            $existingWithEmail = $this->userDao->getByEmail($data['email']);
            if ($existingWithEmail && $existingWithEmail['id'] != $id) {
                throw new Exception("Email already in use", 409);
            }
        }

        // Update password if provided
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        try {
            $this->userDao->update($id, $data);
            return $this->getUserById($id); // Return full updated user object
        } catch (Exception $e) {
            throw new Exception("Service error: " . $e->getMessage(), 500);
        }
    }

    public function deleteUser($id) {
        $this->getUserById($id); // Verify exists
        $this->userDao->delete($id);
        return ["message" => "User deleted successfully"];
    }
}
