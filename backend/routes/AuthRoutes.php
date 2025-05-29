<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
Flight::group('/auth', function() {
   /**
    * @OA\Post(
    *     path="/auth/register",
    *     summary="Register new user.",
    *     description="Add a new user to the database.",
    *     tags={"auth"},
    *     security={
    *         {"ApiKey": {}}
    *     },
    *     @OA\RequestBody(
    *         description="Add new user",
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 required={"password", "email"},
    *                 @OA\Property(
    *                     property="password",
    *                     type="string",
    *                     example="some_password",
    *                     description="User password"
    *                 ),
    *                 @OA\Property(
    *                     property="email",
    *                     type="string",
    *                     example="demo@gmail.com",
    *                     description="User email"
    *                 )
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="User has been added."
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Internal server error."
    *     )
    * )
    */
   Flight::route("POST /register", function () {
    try {
        
        $data = json_decode(Flight::request()->getBody(), true);
        
        if (empty($data['email']) || empty($data['password']) || empty($data['name'])) {
            Flight::halt(400, json_encode(['error' => 'All fields are required']));
            return;
        }

        // Add default role and timestamp
        $data['role'] = 'user'; 
        $data['created_at'] = date('Y-m-d H:i:s'); 

        $response = Flight::auth_service()->register($data);
  
        if ($response['success']) {
            Flight::json($response['data'], 201);
        } else {
            Flight::halt(400, json_encode(['error' => $response['error']]));
        }
    } catch (Exception $e) {
        error_log('Registration error: ' . $e->getMessage()); // Log the error
        Flight::halt(500, json_encode(['error' => 'Server error']));
    }
});
   /**
    * @OA\Post(
    *      path="/auth/login",
    *      tags={"auth"},
    *      summary="Login to system using email and password",
    *      @OA\Response(
    *           response=200,
    *           description="Student data and JWT"
    *      ),
    *      @OA\RequestBody(
    *          description="Credentials",
    *          @OA\JsonContent(
    *              required={"email","password"},
    *              @OA\Property(property="email", type="string", example="demo@gmail.com", description="Student email address"),
    *              @OA\Property(property="password", type="string", example="some_password", description="Student password")
    *          )
    *      )
    * )
    */
   Flight::route('POST /login', function() {
       $data = Flight::request()->data->getData();


       $response = Flight::auth_service()->login($data);
  
       if ($response['success']) {
           Flight::json([
               'message' => 'User logged in successfully',
               'data' => $response['data']
           ]);
       } else {
           Flight::halt(500, $response['error']);
       }
   });
});
?>
