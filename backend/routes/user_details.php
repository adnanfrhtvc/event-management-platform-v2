<?php
require_once __DIR__ . '/../services/UserDetailsService.php';

use OpenApi\Annotations as OA;

$userDetailsDao = new UserDetailsDao();
$userDetailsService = new UserDetailsService($userDetailsDao);

/**
 * @OA\Get(
 *     path="/api/user-details/{user_id}",
 *     tags={"user_details"},
 *     summary="Get user profile details",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="ID of the user",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User details retrieved"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User details not found"
 *     )
 * )
 */
Flight::route('GET /api/user-details/@user_id', function($user_id) use ($userDetailsService) {
    try {
        $user = Flight::get('user');
        if ($user->role !== Roles::ADMIN && $user->id != $user_id) {
            Flight::halt(403, json_encode(["error" => "Forbidden"]));
        }

        $details = $userDetailsService->getDetailsByUserId($user_id);
        Flight::json($details);
    } catch (Exception $e) {
        Flight::json(["message" => $e->getMessage()], $e->getCode());
    }
});

/**
 * @OA\Post(
 *     path="/api/user-details",
 *     tags={"user_details"},
 *     summary="Create user profile details",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "email"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Doe"),
 *             @OA\Property(property="phone_number", type="string", example="1234567890"),
 *             @OA\Property(property="address", type="string", example="123 Main St"),
 *             @OA\Property(property="profile_image", type="string", example="https://example.com/image.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User details created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input or user already has details"
 *     )
 * )
 */
Flight::route('POST /api/user-details', function() use ($userDetailsService) {
    try {
        $data = Flight::request()->data->getData();
        $user = Flight::get('user');

        // Only the user themselves or an admin can add their profile
        if ($user->role !== Roles::ADMIN && $user->id != $data['user_id']) {
            Flight::halt(403, json_encode(["error" => "Forbidden"]));
        }

        $result = $userDetailsService->createDetails($data);
        Flight::json($result, 201);
    } catch (Exception $e) {
        Flight::json(["message" => $e->getMessage()], $e->getCode());
    }
});

/**
 * @OA\Put(
 *     path="/api/user-details/{user_id}",
 *     tags={"user_details"},
 *     summary="Update user profile details",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="first_name", type="string"),
 *             @OA\Property(property="last_name", type="string"),
 *             @OA\Property(property="phone_number", type="string"),
 *             @OA\Property(property="address", type="string"),
 *             @OA\Property(property="profile_image", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User details updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Details not found"
 *     )
 * )
 */
Flight::route('PUT /api/user-details/@user_id', function($user_id) use ($userDetailsService) {
    try {
        $user = Flight::get('user');

        // Authorization check
        if ($user->role !== Roles::ADMIN && $user->id != $user_id) {
            Flight::halt(403, json_encode(["error" => "Forbidden"]));
        }

        $data = Flight::request()->data->getData();

        // Merge with user data (e.g., email from users table)
        $data['email'] = $user['email']; 

        // Create user_details if missing
        try {
            $existingDetails = $userDetailsService->getDetailsByUserId($user_id);
        } catch (Exception $e) {
            if ($e->getCode() === 404) {
                $userDetailsService->createDetails([
                    'user_id' => $user_id,
                    'first_name' => $user['name'], // Default to name from users
                    'last_name' => '',
                    ...$data // Include edited fields
                ]);
                $existingDetails = $userDetailsService->getDetailsByUserId($user_id);
            } else {
                throw $e;
            }
        }

        // Update details
        $result = $userDetailsService->updateDetails($user_id, $data);
        Flight::json($result);
    } catch (Exception $e) {
        Flight::json(["message" => $e->getMessage()], $e->getCode());
    }
});

Flight::route('GET /profile', function() {
    try {
        $user = Flight::get('user'); // Authenticated user from JWT

        // Fetch user details (if they exist)
        $userDetailsService = new UserDetailsService(new UserDetailsDao());
        $details = $userDetailsService->getDetailsByUserId($user['id']);
        
        // Fetch bookings
        $bookings = Flight::registration_service()->getRegistrationsByUser($user['id']);

        Flight::json([
            'user' => $user,
            'details' => $details, // Might be empty
            'bookings' => $bookings
        ]);
    } catch (Exception $e) {
        // If user_details don't exist, return only user data
        if ($e->getCode() === 404) {
            Flight::json([
                'user' => $user,
                'details' => null,
                'bookings' => []
            ]);
        } else {
            Flight::halt(500, json_encode(['error' => $e->getMessage()]));
        }
    }
});