<?php
require_once __DIR__ . '/../services/TicketService.php';
require_once __DIR__ . '/../dao/EventDao.php';
require_once __DIR__ . '/../dao/TicketDao.php';

use OpenApi\Annotations as OA;

// Initialize DAOs
$ticketDao = new TicketDao();
$eventDao = new EventDao();
$ticketService = new TicketService($ticketDao, $eventDao);

// Get tickets for event
/**
* @OA\Get(
*     path="/api/events/{event_id}/tickets",
*     tags={"tickets"},
*     summary="Get tickets by event ID",
*     @OA\Parameter(
*         name="event_id",
*         in="path",
*         required=true,
*         description="Event ID",
*         @OA\Schema(type="integer", example=456)
*     ),
*     @OA\Response(
*         response=200,
*         description="List of tickets for specified event"
*     ),
*     @OA\Response(
*         response=404,
*         description="Event not found"
*     )
* )
*/
Flight::route('GET /api/events/@event_id/tickets', function($event_id) use ($ticketService) {
    $tickets = $ticketService->getTicketsByEventId($event_id);
    Flight::json($tickets);
});

// Create new ticket type for an event
/**
* @OA\Post(
*     path="/api/events/{event_id}/tickets",
*     tags={"tickets"},
*     summary="Create tickets for an event",
*     @OA\Parameter(
*         name="event_id",
*         in="path",
*         required=true,
*         description="Event ID",
*         @OA\Schema(type="integer", example=456)
*     ),
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"price", "total_tickets"},
*             @OA\Property(property="price", type="number", format="float", example=50.00),
*             @OA\Property(property="total_tickets", type="integer", example=100)
*         )
*     ),
*     @OA\Response(
*         response=201,
*         description="Tickets created successfully"
*     ),
*     @OA\Response(
*         response=400,
*         description="Invalid input"
*     )
* )
*/
Flight::route('POST /api/events/@event_id/tickets', function($event_id) use ($ticketService) {
    $data = Flight::request()->data->getData();
    try {
        $price = $data['price'];
        $total_tickets = $data['total_tickets'];
        $ticketId = $ticketService->createTicket($event_id, $price, $total_tickets);
        Flight::json(["id" => $ticketId], 201);
    } catch (Exception $e) {
        Flight::halt($e->getCode(), $e->getMessage());
    }
});

// Update remaining tickets (e.g., when tickets are sold)
/**
* @OA\Put(
*     path="/api/events/{event_id}/tickets/remaining",
*     tags={"tickets"},
*     summary="Update remaining tickets for an event",
*     @OA\Parameter(
*         name="event_id",
*         in="path",
*         required=true,
*         description="Event ID",
*         @OA\Schema(type="integer", example=456)
*     ),
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"tickets_sold"},
*             @OA\Property(property="tickets_sold", type="integer", example=25)
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Remaining tickets updated"
*     ),
*     @OA\Response(
*         response=400,
*         description="Invalid input"
*     )
* )
*/
Flight::route('PUT /api/events/@event_id/tickets/remaining', function($event_id) use ($ticketService) {
    $data = Flight::request()->data->getData();
    try {
        $tickets_sold = $data['tickets_sold'];
        $updatedTickets = $ticketService->updateRemainingTickets($event_id, $tickets_sold);
        Flight::json($updatedTickets);
    } catch (Exception $e) {
        Flight::halt($e->getCode(), $e->getMessage());
    }
});

// Delete all tickets for an event
/**
* @OA\Delete(
*     path="/api/events/{event_id}/tickets",
*     tags={"tickets"},
*     summary="Delete all tickets for an event",
*     @OA\Parameter(
*         name="event_id",
*         in="path",
*         required=true,
*         description="Event ID",
*         @OA\Schema(type="integer", example=456)
*     ),
*     @OA\Response(
*         response=200,
*         description="All tickets deleted"
*     ),
*     @OA\Response(
*         response=404,
*         description="Event not found"
*     )
* )
*/
Flight::route('DELETE /api/events/@event_id/tickets', function($event_id) use ($ticketService) {
    try {
        $result = $ticketService->deleteTicketsByEventId($event_id);
        Flight::json($result);
    } catch (Exception $e) {
        Flight::halt($e->getCode(), $e->getMessage());
    }
});