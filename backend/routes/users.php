<?php
require_once __DIR__ . '/../services/UserService.php';

use OpenApi\Annotations as OA;

$userDao = new UserDao();
$userService = new UserService($userDao);

// Get all users
/**
* @OA\Get(
*     path="/api/users",
*     tags={"users"},
*     summary="Get all users",
*     @OA\Response(
*         response=200,
*         description="List of all users"
*     ),
*     @OA\Response(
*         response=500,
*         description="Server error"
*     )
* )
*/
Flight::route('GET /api/users', function() use ($userService) {
    $users = $userService->getAllUsers();
    Flight::json($users);
});

// Get user by ID
/**
* @OA\Get(
*     path="/api/users/{id}",
*     tags={"users"},
*     summary="Get user by ID",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="User ID",
*         @OA\Schema(type="integer", example=123)
*     ),
*     @OA\Response(
*         response=200,
*         description="User details"
*     ),
*     @OA\Response(
*         response=404,
*         description="User not found"
*     )
* )
*/
Flight::route('GET /api/users/@id', function($id) use ($userService) {
    try {
        $user = $userService->getUserById($id);
        Flight::json($user);
    } catch (Exception $e) {
        Flight::json(["message" => $e->getMessage()], $e->getCode());
    }
});

// Create user
/**
* @OA\Post(
*     path="/api/users",
*     tags={"users"},
*     summary="Create new user",
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"name", "email"},
*             @OA\Property(property="name", type="string", example="John Doe"),
*             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
*             @OA\Property(property="role", type="string", example="user")
*         )
*     ),
*     @OA\Response(
*         response=201,
*         description="User created"
*     ),
*     @OA\Response(
*         response=400,
*         description="Invalid input"
*     )
* )
*/
Flight::route('POST /api/users', function() use ($userService) {
    $data = Flight::request()->data->getData();
    try {
        $user = $userService->createUser($data);
        Flight::json($user, 201);
    } catch (Exception $e) {
        Flight::json(["message" => $e->getMessage()], $e->getCode());
    }
});

// Update user
/**
* @OA\Put(
*     path="/api/users/{id}",
*     tags={"users"},
*     summary="Update user by ID",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="User ID",
*         @OA\Schema(type="integer", example=123)
*     ),
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"name", "email"},
*             @OA\Property(property="name", type="string", example="Updated Name"),
*             @OA\Property(property="email", type="string", format="email", example="updated@example.com"),
*             @OA\Property(property="role", type="string", example="admin")
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="User updated"
*     ),
*     @OA\Response(
*         response=404,
*         description="User not found"
*     )
* )
*/
Flight::route('PUT /api/users/@id', function($id) use ($userService) {
    $data = Flight::request()->data->getData();
    try {
        $user = $userService->updateUser($id, $data);
        Flight::json($user);
    } catch (Exception $e) {
        Flight::json(["message" => $e->getMessage()], $e->getCode());
    }
});

// Delete user
/**
* @OA\Delete(
*     path="/api/users/{id}",
*     tags={"users"},
*     summary="Delete user by ID",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="User ID",
*         @OA\Schema(type="integer", example=123)
*     ),
*     @OA\Response(
*         response=200,
*         description="User deleted"
*     ),
*     @OA\Response(
*         response=404,
*         description="User not found"
*     )
* )
*/
Flight::route('DELETE /api/users/@id', function($id) use ($userService) {
    try {
        $userService->deleteUser($id);
        Flight::json(["message" => "User deleted successfully"], 200);
    } catch (Exception $e) {
        Flight::json(["message" => $e->getMessage()], $e->getCode());
    }
});
