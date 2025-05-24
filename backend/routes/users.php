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
*     security={{"Authentication": {}}},
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
    try {
        Flight::auth_middleware()->authorizeRole(Roles::ADMIN); 
        $users = $userService->getAllUsers();
        Flight::json($users);
    } catch (Exception $e) {
        Flight::json(["message" => $e->getMessage()], $e->getCode());
    }
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
        $currentUser = Flight::get('user');
        
        if ($currentUser->role !== Roles::ADMIN && $currentUser->id != $id) {
            Flight::halt(403, json_encode(["error" => "Forbidden"]));
        }

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
*     security={{"Authentication": {}}}, 
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
        Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
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
        $currentUser = Flight::get('user');
        
        // Check if user is admin or updating themselves
        if ($currentUser->role !== Roles::ADMIN && $currentUser->id != $id) {
            Flight::halt(403, json_encode(["error" => "Forbidden"]));
        }

        // Prevent non-admins from changing roles
        if ($currentUser->role !== Roles::ADMIN && isset($data['role'])) {
            unset($data['role']); // Remove role from data if non-admin
        }

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
*     security={{"Authentication": {}}},
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
        Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
        $userService->deleteUser($id);
        Flight::json(["message" => "User deleted successfully"], 200);
    } catch (Exception $e) {
        Flight::json(["message" => $e->getMessage()], $e->getCode());
    }
});
