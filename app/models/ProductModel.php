<?php
class ProductModel
{
    private $conn;
    private $table_name = "product";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy danh sách sản phẩm
    public function getProducts()
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.quantity, p.image, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy sản phẩm theo ID
    public function getProductById($id)
    {
        $query = "SELECT id, name, description, price, quantity, category_id, image 
                  FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Kiểm tra dữ liệu hợp lệ
    private function validateProductData($name, $description, $price, $category_id, $quantity)
    {
        $errors = [];
    
        if (empty($name)) {
            $errors['name'] = 'Tên sản phẩm không được để trống';
        }
        if (empty($description)) {
            $errors['description'] = 'Mô tả không được để trống';
        }
        if (!is_numeric($price) || $price <= 0) {
            $errors['price'] = 'Giá sản phẩm không hợp lệ';
        }
        if (empty($category_id)) {
            $errors['category_id'] = 'Danh mục sản phẩm không được để trống';
        }
        if (empty($quantity) || !is_numeric($quantity) || $quantity <= 0) {
            $errors['quantity'] = 'Số lượng sản phẩm không hợp lệ';
        }
    
        // Kiểm tra xem category_id có tồn tại trong bảng category không
        $query = "SELECT id FROM category WHERE id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
    
        if ($stmt->rowCount() == 0) {
            $errors['category_id'] = 'Danh mục sản phẩm không tồn tại';
        }
    
        return $errors;
    }

    // Thêm sản phẩm
    public function addProduct($name, $description, $price, $category_id, $quantity, $image)
    {
        $errors = [];
        if (empty($name) || strlen($name) < 10 || strlen($name) > 100) {
            $errors['name'] = 'Tên sản phẩm phải từ 10 đến 100 ký tự';
        }
        if (empty($description)) {
            $errors['description'] = 'Mô tả không được để trống';
        }
        if (!is_numeric($price) || floatval($price) <= 0) {
            $errors['price'] = 'Giá phải là số dương';
        }
        if (empty($category_id) || !is_numeric($category_id)) {
            $errors['category_id'] = 'Danh mục không hợp lệ';
        }
        if (!is_numeric($quantity) || intval($quantity) < 0) {
            $errors['quantity'] = 'Số lượng phải là số không âm';
        }
        if (empty($image)) {
            $errors['image'] = 'Hình ảnh không được để trống';
        }
    
        if (count($errors) > 0) {
            return $errors;
        }
    
        try {
            $query = "INSERT INTO " . $this->table_name . " (name, description, price, category_id, quantity, image) 
                      VALUES (:name, :description, :price, :category_id, :quantity, :image)";
            $stmt = $this->conn->prepare($query);
    
            $safeName = htmlspecialchars(strip_tags($name));
            $safeDescription = htmlspecialchars(strip_tags($description));
            $safePrice = floatval($price);
            $safeCategoryId = intval($category_id);
            $safeQuantity = intval($quantity);
            $safeImage = htmlspecialchars(strip_tags($image)); // Xử lý ảnh dưới dạng chuỗi tên file
    
            $stmt->bindParam(':name', $safeName);
            $stmt->bindParam(':description', $safeDescription);
            $stmt->bindParam(':price', $safePrice);
            $stmt->bindParam(':category_id', $safeCategoryId);
            $stmt->bindParam(':quantity', $safeQuantity);
            $stmt->bindParam(':image', $safeImage);
    
            return $stmt->execute();
        } catch (PDOException $e) {
            return ['database_error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()];
        }
    }

    // Cập nhật sản phẩm
    public function updateProduct($id, $name, $description, $price, $category_id, $quantity, $image)
    {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET name = :name, description = :description, price = :price, category_id = :category_id, quantity = :quantity, image = :image 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
    
            $safeId = intval($id);
            $safeName = htmlspecialchars(strip_tags($name));
            $safeDescription = htmlspecialchars(strip_tags($description));
            $safePrice = floatval($price);
            $safeCategoryId = intval($category_id);
            $safeQuantity = intval($quantity);
            $safeImage = basename($image);
    
            $stmt->bindParam(':id', $safeId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $safeName);
            $stmt->bindParam(':description', $safeDescription);
            $stmt->bindParam(':price', $safePrice);
            $stmt->bindParam(':category_id', $safeCategoryId, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $safeQuantity, PDO::PARAM_INT);
            $stmt->bindParam(':image', $safeImage);
    
            $stmt->execute();
            $rowCount = $stmt->rowCount();
            file_put_contents('/Applications/XAMPP/xamppfiles/htdocs/QUANLYBANHANG/debug.log', "Update Query: $query\nRow Count: $rowCount\n", FILE_APPEND);
            return $rowCount > 0;
        } catch (PDOException $e) {
            file_put_contents('/Applications/XAMPP/xamppfiles/htdocs/QUANLYBANHANG/debug.log', "DB Error: " . $e->getMessage() . "\n", FILE_APPEND);
            return ['database_error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()];
        }
    }

    // Xóa sản phẩm
    public function deleteProduct($id)
    {
        try {
            // Kiểm tra xem sản phẩm có tồn tại
            $product = $this->getProductById($id);
            if (!$product) {
                return ['product_not_found' => 'Sản phẩm không tồn tại'];
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

    // Tìm kiếm sản phẩm
    public function searchProducts($term)
    {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  JOIN category c ON p.category_id = c.id 
                  WHERE p.name LIKE :term";
        $stmt = $this->conn->prepare($query);
        $term = "%$term%";
        $stmt->bindParam(':term', $term);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
}
?>