<?php
require_once 'BaseDao.php';

class EventDao extends BaseDao {
    public function __construct() {
        parent::__construct("events");
    }

    public function getByOrganizerId($organizer_id) {
        $stmt = $this->connection->prepare("SELECT * FROM events WHERE organizer_id = :organizer_id");
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->connection->prepare("SELECT * FROM events WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($event) {
        try {
            return parent::insert($event);
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function update($id, $data) {
        return parent::update($id, $data);
    }

    public function delete($id) {
        return parent::delete($id);
    }
}
?>
