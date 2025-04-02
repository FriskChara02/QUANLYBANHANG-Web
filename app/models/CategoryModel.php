<?php
class CategoryModel
{
    private $conn;
    private $table_name = "category";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy tất cả danh mục
    public function getCategories()
    {
        $query = "SELECT id, name, description FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh mục theo ID
    public function getCategoryById($id)
    {
        $query = "SELECT id, name, description FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm danh mục mới
    public function addCategory($name, $description)
    {
        $errors = [];
        
        if (empty($name)) {
            $errors['name'] = 'Tên danh mục không được để trống';
        }

        if (count($errors) > 0) {
            return $errors;
        }

        try {
            $query = "INSERT INTO " . $this->table_name . " (name, description) VALUES (:name, :description)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', htmlspecialchars(strip_tags($name)));
            $stmt->bindParam(':description', htmlspecialchars(strip_tags($description)));

            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            return ['database_error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()];
        }

        return false;
    }

    // Cập nhật danh mục
    public function updateCategory($id, $name, $description)
    {
        $errors = [];
        
        if (empty($name)) {
            $errors['name'] = 'Tên danh mục không được để trống';
        }

        if (count($errors) > 0) {
            return $errors;
        }

        try {
            $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', htmlspecialchars(strip_tags($name)));
            $stmt->bindParam(':description', htmlspecialchars(strip_tags($description)));

            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            return ['database_error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()];
        }

        return false;
    }

    // Xóa danh mục
    public function deleteCategory($id)
    {
        try {
            $category = $this->getCategoryById($id);
            if (!$category) {
                return ['category_not_found' => 'Danh mục không tồn tại'];
            }

            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            return ['database_error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()];
        }

        return false;
    }
}
?>