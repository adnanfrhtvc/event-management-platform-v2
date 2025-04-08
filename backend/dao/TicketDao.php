<?php
require_once 'BaseDao.php';

class TicketDao extends BaseDao {
    public function __construct() {
        parent::__construct("tickets");
    }

    public function getByEventId($event_id) {
        $stmt = $this->connection->prepare("SELECT * FROM tickets WHERE event_id = :event_id");
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTicket($event_id, $price, $total_tickets) {
        $stmt = $this->connection->prepare("
            INSERT INTO tickets (event_id, price, total_tickets, remaining_tickets)
            VALUES (:event_id, :price, :total_tickets, :remaining_tickets)
        ");
        $stmt->bindParam(':event_id', $event_id);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':total_tickets', $total_tickets);
        $stmt->bindParam(':remaining_tickets', $total_tickets);
        $stmt->execute();
        return $this->connection->lastInsertId();
    }

    public function updateRemainingTickets($event_id, $tickets_sold) {
        $stmt = $this->connection->prepare("
            UPDATE tickets 
            SET remaining_tickets = remaining_tickets - :tickets_sold 
            WHERE event_id = :event_id AND remaining_tickets >= :tickets_sold
        ");
        $stmt->bindParam(':tickets_sold', $tickets_sold);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        return $stmt->rowCount(); // Returns number of affected rows (0 if not enough tickets)
    }

    public function deleteTicket($event_id) {
        $stmt = $this->connection->prepare("DELETE FROM tickets WHERE event_id = :event_id");
        $stmt->bindParam(':event_id', $event_id);
        return $stmt->execute();
    }
}
?>
