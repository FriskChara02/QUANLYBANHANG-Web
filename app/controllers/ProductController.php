<?php
require_once 'app/config/session.php';
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';

class ProductController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    public function list()
    {
        $products = $this->productModel->getProducts();
        $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
        include 'app/views/product/list.php';
    }

    // Hiển thị chi tiết sản phẩm
    public function show($id)
    {
        $product = $this->productModel->getProductById($id);
        if ($product) {
            include 'app/views/product/show.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    // Thêm sản phẩm mới
    public function add()
{
    if (!isset($_SESSION['user']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
        $_SESSION['error_message'] = "Bạn không có quyền truy cập trang này!";
        header('Location: /QUANLYBANHANG/Product/pagemain');
        exit();
    }

    $categories = (new CategoryModel($this->db))->getCategories();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        header('Content-Type: application/json');
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? '';
        $category_id = $_POST['category_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;

        if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
            echo json_encode(['message' => 'Hình ảnh không được để trống hoặc lỗi upload']);
            exit;
        }

        $image = $this->uploadImage($_FILES['image']);
        if (!$image) {
            echo json_encode(['message' => 'Lỗi khi upload hình ảnh']);
            exit;
        }

        $result = $this->productModel->addProduct($name, $description, $price, $category_id, $quantity, $image);

        if ($result === true) {
            echo json_encode(['message' => 'Product created successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product creation failed', 'errors' => $result]);
        }
        exit;
    }

    include_once 'app/views/product/add.php';
}


    
    private function uploadImage($file)
    {
        $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/QUANLYBANHANG/public/images/";
        $targetFile = $targetDir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
        if (!getimagesize($file["tmp_name"])) {
            return null;  // Không phải là hình ảnh
        }
    
        if ($file["size"] > 500000) {
            return null;  // Quá lớn
        }
    
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return basename($file["name"]); // Chỉ trả về tên file
        }
    
        return null;
    }
    
    // Hiển thị form chỉnh sửa sản phẩm
    public function edit($id)
{
    if (!isset($_SESSION['user']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
        $_SESSION['error_message'] = "Bạn không có quyền truy cập trang này!";
        header('Location: /QUANLYBANHANG/Product/pagemain');
        exit();
    }

    $product = $this->productModel->getProductById($id);
    $categories = (new CategoryModel($this->db))->getCategories();

    if ($product) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $quantity = $_POST['quantity'] ?? '';
            $currentImage = $_POST['current_image'] ?? ''; // Ảnh hiện tại

            $image = $currentImage;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = $this->uploadImage($_FILES['image']);
                if (!$image) {
                    echo json_encode(['message' => 'Lỗi khi upload hình ảnh']);
                    exit;
                }
            }

            $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $quantity, $image);

            if ($result === true) {
                echo json_encode(['message' => 'Product updated successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Product update failed', 'errors' => $result]);
            }
            exit;
        } else {
            include 'app/views/product/edit.php';
        }
    } else {
        echo "Không thấy sản phẩm.";
    }
}

    // Xóa sản phẩm
    public function delete($id)
    {
        session_start();
        if (!isset($_SESSION['user']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
            $_SESSION['error_message'] = "Bạn không có quyền truy cập trang này!";
            header('Location: /QUANLYBANHANG/Product/pagemain');
            exit();
        }
        if ($this->productModel->deleteProduct($id)) {
            $_SESSION['success_message'] = "Sản phẩm đã được xóa thành công!";
        } else {
            $_SESSION['error_message'] = "Đã xảy ra lỗi khi xóa sản phẩm.";
        }
        header('Location: /QUANLYBANHANG/Product/list');
        exit();
    }

    // Trang chính của sản phẩm (Pagemain)
    public function pagemain()
    {
        // Lấy dữ liệu sản phẩm hoặc các dữ liệu khác cần thiết
        $products = $this->productModel->getProducts();

        // Bao gồm view trang chủ
        include 'app/views/product/pagemain.php';
    }

    public function confirm()
{

    
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_order'])) {
        // Lấy thông tin từ form
        $name = htmlspecialchars(strip_tags($_POST['fullname']));
        $phone = htmlspecialchars(strip_tags($_POST['phone']));
        $address = htmlspecialchars(strip_tags($_POST['address']));
        
        // Lấy giỏ hàng từ sessionStorage qua JavaScript gửi lên
        $cart = json_decode($_POST['cart_data'], true);

        // Thêm vào bảng orders
        $query = "INSERT INTO orders (name, phone, address) VALUES (:name, :phone, :address)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        
        if ($stmt->execute()) {
            $order_id = $this->db->lastInsertId();

            // Thêm chi tiết đơn hàng vào order_details
            foreach ($cart as $item) {
                $query = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                          VALUES (:order_id, :product_id, :quantity, :price)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':order_id', $order_id);
                $stmt->bindParam(':product_id', $item['id']);
                $quantity = 1; // Giả định mỗi sản phẩm mua 1 cái
                $stmt->bindParam(':quantity', $quantity);
                $stmt->bindParam(':price', $item['price']);
                $stmt->execute();
            }
        }
        
        include 'app/views/product/confirm.php';
    } else {
        include 'app/views/product/confirm.php';
    }
}

public function someProtectedFunction()
{
    // Lấy headers từ request
    $headers = getallheaders();
    
    if (!empty($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        echo "Token nhận được: " . $token;
        exit;
    } else {
        echo "Không nhận được token!";
        exit;
    }
}
}
?>