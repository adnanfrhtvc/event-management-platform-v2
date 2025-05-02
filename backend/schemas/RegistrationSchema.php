<?php
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Registration",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=25),
 *     @OA\Property(property="event_id", type="integer", example=10),
 *     @OA\Property(property="registered_at", type="string", format="date-time", example="2025-04-30 14:30:00")
 * )
 */