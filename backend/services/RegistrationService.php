<?php
require_once __DIR__ . '/../dao/RegistrationDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/EventDao.php';

class RegistrationService {
    private $registrationDao;
    private $userDao;
    private $eventDao;

    public function __construct(RegistrationDao $registrationDao, UserDao $userDao, EventDao $eventDao) {
        $this->registrationDao = $registrationDao;
        $this->userDao = $userDao;
        $this->eventDao = $eventDao;
    }

    // Get all registrations for a user
    public function getRegistrationsByUserId($user_id) {
        return $this->registrationDao->getByUserId($user_id);
    }

    // Get all registrations for an event
    public function getRegistrationsByEventId($event_id) {
        return $this->registrationDao->getByEventId($event_id);
    }

    // Register user for event
    public function registerUser($user_id, $event_id) {
        // Validate user exists
        if (!$this->userDao->getById($user_id)) {
            throw new Exception("User not found", 404);
        }

        // Validate event exists
        if (!$this->eventDao->getById($event_id)) {
            throw new Exception("Event not found", 404);
        }

        // Check for existing registration
        $existing = $this->registrationDao->getByUserId($user_id);
        foreach ($existing as $registration) {
            if ($registration['event_id'] == $event_id) {
                throw new Exception("User already registered for this event", 409);
            }
        }

        return $this->registrationDao->registerUser($user_id, $event_id);
    }

    // Unregister user from event
    public function unregisterUser($user_id, $event_id) {
        $success = $this->registrationDao->deleteRegistration($user_id, $event_id);
        if (!$success) {
            throw new Exception("Registration not found", 404);
        }
        return ["message" => "Unregistered successfully"];
    }

}