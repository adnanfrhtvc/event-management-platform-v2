<?php
require_once __DIR__ . '/../services/CategoryService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

use OpenApi\Annotations as OA;

// Initialize DAO and Service
$categoryDao = new CategoryDao();
$categoryService = new CategoryService($categoryDao);

// Get all categories
/**
* @OA\Get(
*      path="/api/categories",
*      tags={"categories"},
*      summary="Get all categories",
*      @OA\Response(
*           response=200,
*           description="Array of all categories in the database"
*      ),
*      @OA\Response(
*           response=500,
*           description="Server error"
*      )
* )
*/
Flight::route('GET /api/categories', function() use ($categoryService) {
    $categories = $categoryService->getAllCategories();
    Flight::json($categories);
});

// Get category by id
/**
* @OA\Get(
*     path="/api/categories/{id}",
*     tags={"categories"},
*     summary="Get category by ID",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="ID of the category",
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\Response(
*         response=200,
*         description="Returns the category with the given ID"
*     ),
*     @OA\Response(
*         response=404,
*         description="Category not found"
*     )
* )
*/
Flight::route('GET /api/categories/@id', function($id) use ($categoryService) {
    try {
        $category = $categoryService->getCategoryById($id);
        Flight::json($category);
    } catch (Exception $e) {
        Flight::json(["message" => $e->getMessage()], $e->getCode());
    }
});

// Post create category
/**
* @OA\Post(
*     path="/api/categories",
*     tags={"categories"},
*     summary="Add a new category",
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"name"},
*             @OA\Property(property="name", type="string", example="Technology"),
*             @OA\Property(property="description", type="string", example="Tech-related events")
*         )
*     ),
*     @OA\Response(
*         response=201,
*         description="Category created"
*     ),
*     @OA\Response(
*         response=400,
*         description="Invalid input"
*     )
* )
*/
Flight::route('POST /api/categories', function() use ($categoryService) {
    $data = Flight::request()->data->getData();
    try {
        $category = $categoryService->createCategory($data);
        Flight::json($category, 201);
    } catch (Exception $e) {
        Flight::json(["message" => $e->getMessage()], $e->getCode());
    }
});

// Update category
/**
* @OA\Put(
*     path="/api/categories/{id}",
*     tags={"categories"},
*     summary="Update an existing category by ID",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="Category ID",
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"name"},
*             @OA\Property(property="name", type="string", example="Updated Category"),
*             @OA\Property(property="description", type="string", example="Updated description")
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Category updated"
*     ),
*     @OA\Response(
*         response=404,
*         description="Category not found"
*     )
* )
*/
Flight::route('PUT /api/categories/@id', function($id) use ($categoryService) {
    $data = Flight::request()->data->getData();
    try {
        $category = $categoryService->updateCategory($id, $data);
        Flight::json($category);
    } catch (Exception $e) {
        Flight::json(["message" => $e->getMessage()], $e->getCode());
    }
});

// Delete category
/**
* @OA\Delete(
*     path="/api/categories/{id}",
*     tags={"categories"},
*     summary="Delete a category by ID",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="Category ID",
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\Response(
*         response=200,
*         description="Category deleted"
*     ),
*     @OA\Response(
*         response=404,
*         description="Category not found"
*     )
* )
*/
Flight::route('DELETE /api/categories/@id', function($id) use ($categoryService) {
    try {
        $categoryService->deleteCategory($id);
        Flight::json(["message" => "Category deleted"]);
    } catch (Exception $e) {
        Flight::json(["message" => $e->getMessage()], $e->getCode());
    }
});