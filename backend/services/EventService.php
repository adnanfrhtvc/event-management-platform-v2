<?php
require_once __DIR__ . '/../dao/EventDao.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class EventService {
    private $eventDao;
    private $categoryDao;
    private $userDao;

    public function __construct() {
        $this->eventDao = new EventDao();
        $this->categoryDao = new CategoryDao();
        $this->userDao = new UserDao();
    }

    public function getAllEvents() {
        return $this->eventDao->getAll();
    }

    public function getEventById($id) {
        $event = $this->eventDao->getById($id);
        if (!$event) {
            throw new Exception("Event not found", 404);
        }
        return $event;
    }

    public function getEventsByOrganizer($organizer_id) {
        return $this->eventDao->getByOrganizerId($organizer_id);
    }

    public function createEvent($data) {
        // Ensure required fields exist
        $required = ['title', 'event_date', 'organizer_id', 'category_id'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field", 400);
            }
        }
    
        // Validate organizer exists
        if (!$this->userDao->getById($data['organizer_id'])) {
            throw new Exception("Organizer does not exist", 400);
        }
    
        // Validate category exists
        if (!$this->categoryDao->getById($data['category_id'])) {
            throw new Exception("Category does not exist", 400);
        }
    
        // Date validation
        if (strtotime($data['event_date']) < time()) {
            throw new Exception("Event date must be in the future", 400);
        }
    
        // Ensure field names match database columns
        $sanitizedData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'location' => $data['location'] ?? null,
            'event_date' => $data['event_date'],
            'organizer_id' => $data['organizer_id'],
            'category_id' => $data['category_id']
        ];
    
        $eventId = $this->eventDao->insert($sanitizedData);
        
        // Fetch the event to return all its data, including the ID
        return $this->getEventById($eventId);  // Assuming you have a method to fetch an event by ID.
    }

    public function updateEvent($id, $data) {
        $existing = $this->eventDao->getById($id);
        if (!$existing) {
            throw new Exception("Event not found", 404);
        }

        // Only allow certain fields to be updated
        $allowedFields = ['title', 'description', 'location', 'event_date', 'category_id'];
        $filteredData = array_intersect_key($data, array_flip($allowedFields));

        if (isset($filteredData['category_id']) && !$this->categoryDao->getById($filteredData['category_id'])) {
            throw new Exception("Category does not exist", 400);
        }

        $updated = $this->eventDao->update($id, $filteredData);

    // Ensure that the update was successful (check the result)
    if ($updated) {
        return $this->getEventById($id);  // Fetch the updated event data
    } else {
        throw new Exception("Failed to update event", 500);
    }
    }

    public function deleteEvent($id) {
        $existing = $this->eventDao->getById($id);
        if (!$existing) {
            throw new Exception("Event not found", 404);
        }

        return $this->eventDao->delete($id);
    }
}