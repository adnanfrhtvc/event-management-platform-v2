<?php
require_once __DIR__ . '/../dao/CategoryDao.php';

class CategoryService {
    private $categoryDao;

    public function __construct(CategoryDao $categoryDao) {
        $this->categoryDao = $categoryDao;
    }

    public function getAllCategories() {
        return $this->categoryDao->getAll();
    }

    public function getCategoryById($id) {
        $category = $this->categoryDao->getById($id);
        if (!$category) {
            throw new Exception("Category not found", 404);
        }
        return $category;
    }

    public function createCategory($data) {
        // Validate required field
        if (empty($data['name'])) {
            throw new Exception("Category name is required", 400);
        }

        // Check for duplicate name
        $existing = $this->categoryDao->getByName($data['name']); // Add this method if needed
        if ($existing) {
            throw new Exception("Category name already exists", 409);
        }

        $id = $this->categoryDao->insert($data);  // Insert and get new ID
        return $this->categoryDao->getById($id);   // Return full created category
    }

    public function updateCategory($id, $data) {
        $this->getCategoryById($id); // Verify exists

        // Check for duplicate name
        if (isset($data['name'])) {
            $existing = $this->categoryDao->getByName($data['name']);
            if ($existing && $existing['id'] != $id) {
                throw new Exception("Category name already exists", 409);
            }
        }

        $this->categoryDao->update($id, $data);        // Update category
        return $this->categoryDao->getById($id);        // Return updated category
    }

    public function deleteCategory($id) {
        $this->getCategoryById($id); // Verify exists
        return $this->categoryDao->delete($id);
    }
}
