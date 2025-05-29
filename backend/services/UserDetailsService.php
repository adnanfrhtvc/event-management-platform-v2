<?php
require_once __DIR__ . '/../dao/UserDetailsDao.php';

class UserDetailsService {
    private $userDetailsDao;

    public function __construct(UserDetailsDao $userDetailsDao) {
        $this->userDetailsDao = $userDetailsDao;
    }

    public function getDetailsByUserId($user_id) {
        $details = $this->userDetailsDao->getByUserId($user_id);
        if (!$details) throw new Exception("User details not found", 404);
        return $details;
    }

    public function createDetails($data) {
        if (empty($data['user_id'])) throw new Exception("user_id is required", 400);
        return $this->userDetailsDao->insert($data);
    }

    public function updateDetails($user_id, $data) {
        $existing = $this->getDetailsByUserId($user_id);
        $this->userDetailsDao->updateByUserId($user_id, $data);
        return $this->getDetailsByUserId($user_id);
    }

    public function ensureUserDetailsExist($user_id, $user_name) {
        
        $details = $this->userDetailsDao->getByUserId($user_id);
        if (!$details) {
            
            $nameParts = explode(' ', $user_name, 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? 'Unknown';

            return $this->userDetailsDao->insert([
                'user_id' => $user_id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_number' => null, //  default
                'address' => null,
                'profile_image' => 'static/avatar.jpg' // default
            ]);
        }
        return $details; 
    }
}
?>
