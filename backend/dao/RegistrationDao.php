<?php
require_once 'BaseDao.php';

class RegistrationDao extends BaseDao {
    public function __construct() {
        parent::__construct("registrations");
    }

    // Get all registrations by User ID
    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM registrations WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all registrations by Event ID
    public function getByEventId($event_id) {
        $stmt = $this->connection->prepare("SELECT * FROM registrations WHERE event_id = :event_id");
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registerUser($user_id, $event_id) {
        $stmt = $this->connection->prepare("
            INSERT INTO registrations (user_id, event_id, registered_at)
            VALUES (:user_id, :event_id, NOW()) 
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        return $this->connection->lastInsertId();
    }

    public function deleteRegistration($user_id, $event_id) {
        $stmt = $this->connection->prepare("DELETE FROM registrations WHERE user_id = :user_id AND event_id = :event_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $event_id);
        return $stmt->execute();
    }

    //PROVJERITI DA LI UBACITI GET ALL ZA SVE REGISTRACIJE I UPDATE
}
?>
