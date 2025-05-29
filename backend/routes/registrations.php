<?php
require_once __DIR__ . '/../services/RegistrationService.php';
require_once __DIR__ . '/../dao/RegistrationDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/EventDao.php';

use OpenApi\Annotations as OA;

// Initialize DAOs
$registrationDao = new RegistrationDao();
$userDao = new UserDao();
$eventDao = new EventDao();
$registrationService = new RegistrationService($registrationDao, $userDao, $eventDao);

// Get all registrations for a user
/**
* @OA\Get(
*     path="/api/users/{user_id}/registrations",
*     tags={"registrations"},
*     summary="Get registrations by user ID",
*     @OA\Parameter(
*         name="user_id",
*         in="path",
*         required=true,
*         description="User ID",
*         @OA\Schema(type="integer", example=123)
*     ),
*     @OA\Response(
*         response=200,
*         description="List of user's event registrations"
*     ),
*     @OA\Response(
*         response=404,
*         description="User not found"
*     )
* )
*/
Flight::route('GET /api/users/@user_id/registrations', function($user_id) use ($registrationService) {
    try {
        $currentUser = Flight::get('user'); 
        
        if ($currentUser->role !== Roles::ADMIN && $currentUser->id != $user_id) {
            Flight::halt(403, json_encode(['error' => 'Forbidden: Access denied']));
        }

        $registrations = $registrationService->getRegistrationsByUserId($user_id);
        Flight::json($registrations);
    } catch (Exception $e) {
        Flight::halt($e->getCode(), $e->getMessage());
    }
});

// Get all registrations for an event
/**
* @OA\Get(
*     path="/api/events/{event_id}/registrations",
*     tags={"registrations"},
*     summary="Get registrations by event ID",
*     @OA\Parameter(
*         name="event_id",
*         in="path",
*         required=true,
*         description="Event ID",
*         @OA\Schema(type="integer", example=456)
*     ),
*     @OA\Response(
*         response=200,
*         description="List of event registrations"
*     ),
*     @OA\Response(
*         response=404,
*         description="Event not found"
*     )
* )
*/
Flight::route('GET /api/events/@event_id/registrations', function($event_id) use ($registrationService) {
    try {
        
        Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
        
        $registrations = $registrationService->getRegistrationsByEventId($event_id);
        Flight::json($registrations);
    } catch (Exception $e) {
        Flight::halt($e->getCode(), $e->getMessage());
    }
});

// Register user for event
/**
* @OA\Post(
*     path="/api/registrations",
*     tags={"registrations"},
*     summary="Create new registration",
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"user_id", "event_id"},
*             @OA\Property(property="user_id", type="integer", example=123),
*             @OA\Property(property="event_id", type="integer", example=456)
*         )
*     ),
*     @OA\Response(
*         response=201,
*         description="Registration created"
*     ),
*     @OA\Response(
*         response=400,
*         description="Invalid input"
*     ),
*     @OA\Response(
*         response=409,
*         description="Already registered"
*     )
* )
*/
Flight::route('POST /api/registrations', function() use ($registrationService) {
    try {
        $currentUser = Flight::get('user');
        $data = Flight::request()->data->getData();
        
        // If user is not admin, enforce self-registration
        if ($currentUser->role !== Roles::ADMIN && $currentUser->id != $data['user_id']) {
            Flight::halt(403, json_encode(['error' => 'Forbidden: You can only register yourself']));
        }

        $registrationId = $registrationService->registerUser($data['user_id'], $data['event_id']);
        Flight::json(["id" => $registrationId], 201);
    } catch (Exception $e) {
        Flight::halt($e->getCode(), $e->getMessage());
    }
});

// Unregister user from event
/**
* @OA\Delete(
*     path="/api/registrations",
*     tags={"registrations"},
*     summary="Delete a registration",
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"user_id", "event_id"},
*             @OA\Property(property="user_id", type="integer", example=123),
*             @OA\Property(property="event_id", type="integer", example=456)
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Registration deleted"
*     ),
*     @OA\Response(
*         response=404,
*         description="Registration not found"
*     )
* )
*/
Flight::route('DELETE /api/registrations', function() use ($registrationService) {
    try {
        $currentUser = Flight::get('user');
        $data = Flight::request()->data->getData();
        
        // If user is not admin, enforce self-unregistration
        if ($currentUser->role !== Roles::ADMIN && $currentUser->id != $data['user_id']) {
            Flight::halt(403, json_encode(['error' => 'Forbidden: You can only unregister yourself']));
        }

        $result = $registrationService->unregisterUser($data['user_id'], $data['event_id']);
        Flight::json($result);
    } catch (Exception $e) {
        Flight::halt($e->getCode(), $e->getMessage());
    }
});