<?php
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Event",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=16),
 *     @OA\Property(property="organizer_id", type="integer", example=25),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Tech Conference"),
 *     @OA\Property(property="description", type="string", example="A great event for developers"),
 *     @OA\Property(property="location", type="string", example="New York"),
 *     @OA\Property(property="event_date", type="string", format="date-time", example="2025-04-15 10:00:00"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-27 00:49:52")
 * )
 */