<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../dao/UserDao.php';
require __DIR__ . '/../dao/EventDao.php';
require __DIR__ . '/../dao/CategoryDao.php';
require __DIR__ . '/../dao/RegistrationDao.php';
require __DIR__ . '/../dao/TicketDao.php';

Flight::route('/api/test-db', function() {
    try {
        $db = Database::connect();
        $stmt = $db->query("SELECT 'Connected to MySQL' AS message");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        Flight::json($result); // ✅ Ensures only one response
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 500);
    }
});

// Test fetching all users
Flight::route('/api/test-users', function() {
    $userDao = new UserDao();
    $users = $userDao->getAll();
    Flight::json($users);
});



// Test inserting a new user (Prevent Duplicate Emails)
Flight::route('/api/test-insert-user', function() {
    $userDao = new UserDao();
    $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
    
    $email = "john.doe" . rand(1000, 9999) . "@example.com"; // ✅ Generate unique email

    $userDao->insert([
        'name' => 'John Doe',
        'email' => $email,
        'password' => $hashedPassword,
        'role' => 'user'
    ]);

    Flight::json(["message" => "User inserted successfully with email $email"]);
});

// Test updating a user
Flight::route('/api/test-update-user', function() {
    $userDao = new UserDao();

    // Fetch the first available user
    $users = $userDao->getAll();
    if (empty($users)) {
        Flight::json(["error" => "No users available to update"], 400);
        return;
    }

    $userId = $users[0]['id']; // Take first user
    $updatedData = ['name' => 'Updated Name'];

    $result = $userDao->update($userId, $updatedData);
    
    if ($result) {
        Flight::json(["message" => "User updated successfully", "user_id" => $userId]);
    } else {
        Flight::json(["error" => "User update failed"], 500);
    }
});

// Test deleting a user
Flight::route('/api/test-delete-user', function() {
    $userDao = new UserDao();

    // Fetch the first available user
    $users = $userDao->getAll();
    if (empty($users)) {
        Flight::json(["error" => "No users available to delete"], 400);
        return;
    }

    $userId = $users[0]['id']; // Take first user
    $result = $userDao->delete($userId);

    if ($result) {
        Flight::json(["message" => "User deleted successfully", "user_id" => $userId]);
    } else {
        Flight::json(["error" => "User deletion failed"], 500);
    }
});

// Test fetching all events
Flight::route('/api/test-events', function() {
    $eventDao = new EventDao();
    $events = $eventDao->getAll();
    Flight::json($events);
});

// Test inserting an event (With Required Category)
Flight::route('/api/test-insert-event', function() {
    $eventDao = new EventDao();
    $categoryDao = new CategoryDao();

    // Ensure there's at least one category
    $categories = $categoryDao->getAll();
    if (empty($categories)) {
        $categoryDao->insert(["name" => "Conference"]); // ✅ Insert category if none exist
        $categories = $categoryDao->getAll();
    }
    
    $categoryId = $categories[0]['id'];
    $organizerId = 25; // Change if necessary

    $eventDao->insert([
        'organizer_id' => $organizerId,
        'category_id' => $categoryId,
        'title' => 'Tech Conference',
        'description' => 'A great event for developers',
        'location' => 'New York',
        'event_date' => '2025-04-15 10:00:00'
    ]);

    Flight::json(["message" => "Event inserted successfully"]);
});

// Test fetching an event by ID
Flight::route('/api/test-get-event/@id', function($id) {
    $eventDao = new EventDao();
    $event = $eventDao->getById($id);
    Flight::json($event);
});

// Test updating an event
Flight::route('/api/test-update-event/@id', function($id) {
    $eventDao = new EventDao();

    $updatedData = ["title" => "Updated Event Title"];
    $eventDao->update($id, $updatedData);

    Flight::json(["message" => "Event updated successfully"]);
});

// Test deleting an event
Flight::route('/api/test-delete-event/@id', function($id) {
    $eventDao = new EventDao();
    $eventDao->delete($id);
    Flight::json(["message" => "Event deleted successfully"]);
});

// Test fetching tickets by event ID
Flight::route('/api/test-tickets/event/@id', function($id) {
    $ticketDao = new TicketDao();
    $tickets = $ticketDao->getByEventId($id);
    Flight::json($tickets);
});

// Test creating a new ticket record
Flight::route('/api/test-create-ticket/@event_id/@price/@total_tickets', function($event_id, $price, $total_tickets) {
    $ticketDao = new TicketDao();
    $ticketDao->createTicket($event_id, $price, $total_tickets);
    Flight::json(["message" => "Ticket created for event $event_id"]);
});

// Test updating remaining tickets after a purchase
Flight::route('/api/test-update-tickets/@event_id/@tickets_sold', function($event_id, $tickets_sold) {
    $ticketDao = new TicketDao();
    $updated = $ticketDao->updateRemainingTickets($event_id, $tickets_sold);
    if ($updated) {
        Flight::json(["message" => "Tickets updated for event $event_id"]);
    } else {
        Flight::json(["error" => "Not enough tickets available"], 400);
    }
});

// Test deleting a ticket record
Flight::route('/api/test-delete-ticket/@event_id', function($event_id) {
    $ticketDao = new TicketDao();
    $ticketDao->deleteTicket($event_id);
    Flight::json(["message" => "Ticket deleted for event $event_id"]);
});

// Test fetching all categories
Flight::route('/api/test-categories', function() {
    $categoryDao = new CategoryDao();
    $categories = $categoryDao->getAll();
    Flight::json($categories);
});

// Test inserting a new category
Flight::route('/api/test-insert-category', function() {
    $categoryDao = new CategoryDao();
    $categoryName = "Category " . rand(100, 999); // ✅ Unique name

    $categoryDao->insert([
        'name' => $categoryName
    ]);

    Flight::json(["message" => "Category inserted successfully with name: $categoryName"]);
});

// Test fetching a category by ID
Flight::route('/api/test-category/@id', function($id) {
    $categoryDao = new CategoryDao();
    $category = $categoryDao->getById($id);
    
    if ($category) {
        Flight::json($category);
    } else {
        Flight::json(["error" => "Category not found"], 404);
    }
});

// Test updating a category
Flight::route('/api/test-update-category/@id', function($id) {
    $categoryDao = new CategoryDao();

    $categoryDao->update($id, [
        'name' => 'Updated Category Name'
    ]);

    Flight::json(["message" => "Category updated successfully"]);
});

// Test deleting a category
Flight::route('/api/test-delete-category/@id', function($id) {
    $categoryDao = new CategoryDao();
    $categoryDao->delete($id);
    Flight::json(["message" => "Category deleted successfully"]);
});

// Test fetching registrations by user ID
Flight::route('/api/test-registrations/user/@id', function($id) {
    $registrationDao = new RegistrationDao();
    $registrations = $registrationDao->getByUserId($id);
    Flight::json($registrations);
});

// Test fetching registrations by event ID
Flight::route('/api/test-registrations/event/@id', function($id) {
    $registrationDao = new RegistrationDao();
    $registrations = $registrationDao->getByEventId($id);
    Flight::json($registrations);
});

// Test registering a user for an event
Flight::route('/api/test-register-user/@user_id/@event_id', function($user_id, $event_id) {
    $registrationDao = new RegistrationDao();
    $registrationDao->registerUser($user_id, $event_id);
    Flight::json(["message" => "User $user_id registered for event $event_id"]);
});

// Test unregistering a user from an event
Flight::route('/api/test-unregister-user/@user_id/@event_id', function($user_id, $event_id) {
    $registrationDao = new RegistrationDao();
    $registrationDao->deleteRegistration($user_id, $event_id);
    Flight::json(["message" => "User $user_id unregistered from event $event_id"]);
});

?>
