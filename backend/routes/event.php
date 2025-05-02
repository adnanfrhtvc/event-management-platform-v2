<?php
require_once __DIR__ . '/../services/EventService.php';
use OpenApi\Annotations as OA;

$eventService = new EventService();

// Get all events
/**
 * @OA\Get(
 *     path="/api/events",
 *     tags={"Events"},
 *     summary="Get all events",
 *     @OA\Response(response=200, description="Success"),
 *     @OA\Response(response=500, description="Server error")
 * )
 */
Flight::route('GET /api/events', function () use ($eventService) {
    Flight::json($eventService->getAllEvents());
});

// Get event by id
/**
 * @OA\Get(
 *     path="/api/events/{id}",
 *     tags={"Events"},
 *     summary="Get event by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Success"),
 *     @OA\Response(response=404, description="Not found"),
 *     @OA\Response(response=500, description="Server error")
 * )
 */
Flight::route('GET /api/events/@id', function ($id) use ($eventService) {
    try {
        $event = $eventService->getEventById($id);
        Flight::json($event);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], $e->getCode() ?: 500);
    }
});

// Create event
/**
 * @OA\Post(
 *     path="/api/events",
 *     tags={"Events"},
 *     summary="Create new event",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "date"},
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="date", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Created"),
 *     @OA\Response(response=400, description="Bad request"),
 *     @OA\Response(response=500, description="Server error")
 * )
 */
Flight::route('POST /api/events', function () use ($eventService) {
    $data = Flight::request()->data->getData();
    try {
        $newEvent = $eventService->createEvent($data);
        Flight::json($newEvent, 201);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], $e->getCode() ?: 400);
    }
});

//Update event
/**
 * @OA\Put(
 *     path="/api/events/{id}",
 *     tags={"Events"},
 *     summary="Update event by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="date", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Updated"),
 *     @OA\Response(response=400, description="Bad request"),
 *     @OA\Response(response=404, description="Not found"),
 *     @OA\Response(response=500, description="Server error")
 * )
 */
Flight::route('PUT /api/events/@id', function ($id) use ($eventService) {
    $data = Flight::request()->data->getData();
    try {
        $updated = $eventService->updateEvent($id, $data);
        Flight::json($updated);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], $e->getCode() ?: 400);
    }
});

//Delete event
/**
 * @OA\Delete(
 *     path="/api/events/{id}",
 *     tags={"Events"},
 *     summary="Delete event by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Deleted"),
 *     @OA\Response(response=404, description="Not found"),
 *     @OA\Response(response=500, description="Server error")
 * )
 */
Flight::route('DELETE /api/events/@id', function ($id) use ($eventService) {
    try {
        $eventService->deleteEvent($id);
        Flight::json(['message' => 'Event deleted']);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], $e->getCode() ?: 404);
    }
});
