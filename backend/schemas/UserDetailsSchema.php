<?php
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UserDetails",
 *     type="object",
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="phone_number", type="string", example="+123456789"),
 *     @OA\Property(property="address", type="string", example="123 Main St, NY"),
 *     @OA\Property(property="profile_image_url", type="string", example="https://example.com/avatar.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
