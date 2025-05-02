<?php
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Ticket",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="event_id", type="integer", example=25),
 *     @OA\Property(property="price", type="number", format="float", example=50.99),
 *     @OA\Property(property="total_tickets", type="integer", example=100),
 *     @OA\Property(property="remaining_tickets", type="integer", example=95, description="Automatically managed by the system")
 * )
 */