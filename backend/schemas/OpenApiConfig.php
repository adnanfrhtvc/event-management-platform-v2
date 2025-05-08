<?php
use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     info=@OA\Info(
 *         title="Event Management Platform API",
 *         version="1.0.0",
 *         description="API documentation for managing events, users, and registrations",
 *         @OA\Contact(email="support@example.com"),
 *         @OA\License(name="MIT")
 *     ),
 *     servers={
 *         @OA\Server(
 *             url="http://localhost/event-management-platform-v2/backend/api/v1",
 *             description="Development server"
 *         )
 *     },
 *     @OA\Components(
 *         securitySchemes={
 *             @OA\SecurityScheme(
 *                 securityScheme="bearerAuth",
 *                 type="http",
 *                 scheme="bearer",
 *                 bearerFormat="JWT"
 *             )
 *         }
 *     )
 * )
 */
class OpenApiConfig {}