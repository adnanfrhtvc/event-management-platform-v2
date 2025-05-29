<?php
require_once __DIR__ . '/../dao/TicketDao.php';
require_once __DIR__ . '/../dao/EventDao.php';

class TicketService {
    private $ticketDao;
    private $eventDao;

    public function __construct(TicketDao $ticketDao, EventDao $eventDao) {
        $this->ticketDao = $ticketDao;
        $this->eventDao = $eventDao;
    }

    public function getTicketsByEventId($event_id) {
        return $this->ticketDao->getByEventId($event_id);
    }

    public function createTicket($event_id, $price, $total_tickets) {
        // Validate event exists
        $event = $this->eventDao->getById($event_id);
        if (!$event) {
            throw new Exception("Event not found", 404);
        }

        // Validate input
        if ($price <= 0) {
            throw new Exception("Price must be a positive number", 400);
        }
        if ($total_tickets <= 0 || !is_numeric($total_tickets)) {
            throw new Exception("Total tickets must be a positive integer", 400);
        }

        // Create ticket
        return $this->ticketDao->createTicket($event_id, $price, $total_tickets);
    }

    public function updateRemainingTickets($event_id, $tickets_sold) {
        if ($tickets_sold <= 0) {
            throw new Exception("Tickets sold must be a positive number", 400);
        }

        $affectedRows = $this->ticketDao->updateRemainingTickets($event_id, $tickets_sold);
        if ($affectedRows === 0) {
            throw new Exception("Not enough tickets remaining", 400);
        }

        return $this->ticketDao->getByEventId($event_id); // Return updated tickets
    }

    public function deleteTicketsByEventId($event_id) {
        $this->ticketDao->deleteTicket($event_id);
        return ["message" => "Tickets for event deleted"];
    }
}