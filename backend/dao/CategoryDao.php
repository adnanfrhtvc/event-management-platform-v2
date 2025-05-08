<?php
require_once 'BaseDao.php';

class CategoryDao extends BaseDao {
    public function __construct() {
        parent::__construct("categories");
    }

    public function getById($id) {
        $stmt = $this->connection->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    // Get by Name added per suggestion
    public function getByName($name) {
        $stmt = $this->connection->prepare("SELECT * FROM categories WHERE name = :name");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($category) {
        return parent::insert($category);
    }

    public function update($id, $category) {
        return parent::update($id, $category);
    }

    public function delete($id) {
        return parent::delete($id);
    }
}
?>
