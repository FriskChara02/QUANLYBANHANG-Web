<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/utils/JWTHandler.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ProductApiController
{
    private $productModel;
    private $categoryModel;
    private $db;
    private $jwtHandler;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db); // Thêm CategoryModel
        $this->jwtHandler = new JWTHandler(); // Khởi tạo JWTHandler
    }

    private function authenticate()
{
    $headers = getallheaders();
    $jwt = null;

    // Kiểm tra header Authorization trước
    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        $arr = explode(" ", $authHeader);
        $jwt = $arr[1] ?? null;
    }

    // Nếu không có header, lấy token từ session
    if (!$jwt && isset($_SESSION['user']) && isset($_SESSION['user']->token)) {
        $jwt = $_SESSION['user']->token;
    }

    if ($jwt) {
        $decoded = $this->jwtHandler->decode($jwt);
        return $decoded !== null;
    }
    return false;
}

public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
            return;
        }

        if (!$this->authenticate()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized, please login']);
            return;
        }

        $searchTerm = $_GET['search'] ?? '';
        if ($searchTerm) {
            $products = $this->productModel->searchProducts($searchTerm);
        } else {
            $products = $this->productModel->getProducts();
        }
        $categories = $this->categoryModel->getCategories();

        // Ưu tiên JSON cho API, chỉ trả HTML nếu rõ ràng yêu cầu
        $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? 'application/json';
        if (strpos($acceptHeader, 'text/html') !== false && strpos($acceptHeader, 'application/json') === false) {
            require_once 'app/views/api/product_list.php';
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'products' => $products,
                'categories' => $categories
            ]);
        }
    }

    public function show($id)
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
            return;
        }

        $product = $this->productModel->getProductById($id);
        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Product not found']);
        }
    }

    public function store()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
            return;
        }

        if (!$this->authenticate()) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            return;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents("php://input"), true);
            $name = $input['name'] ?? '';
            $description = $input['description'] ?? '';
            $price = $input['price'] ?? '';
            $category_id = $input['category_id'] ?? '';
            $quantity = $input['quantity'] ?? '';
            $image = $input['image'] ?? '';
        } else {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $quantity = $_POST['quantity'] ?? '';
            $image = isset($_FILES['image']) && $_FILES['image']['error'] === 0 ? $this->uploadImage($_FILES['image']) : '';
        }

        $missingFields = [];
        if (empty($name)) $missingFields[] = 'name';
        if (empty($description)) $missingFields[] = 'description';
        if (empty($price)) $missingFields[] = 'price';
        if (empty($category_id)) $missingFields[] = 'category_id';
        if (empty($quantity)) $missingFields[] = 'quantity';
        if (empty($image)) $missingFields[] = 'image';

        if (!empty($missingFields)) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing required fields', 'fields' => $missingFields]);
            return;
        }

        $result = $this->productModel->addProduct($name, $description, $price, $category_id, $quantity, $image);

        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Product created successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product creation failed', 'errors' => $result]);
        }
    }

    public function update($id)
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
            return;
        }

        if (!$this->authenticate()) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            return;
        }

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            http_response_code(404);
            echo json_encode(['message' => 'Product not found']);
            return;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $rawData = file_get_contents("php://input");
        $data = [];
        $files = [];

        if (strpos($contentType, 'multipart/form-data') !== false && preg_match('/boundary=([^\s]+)/i', $contentType, $matches)) {
            $boundary = "--" . $matches[1];
            $parts = preg_split("/$boundary/", $rawData);
            foreach ($parts as $part) {
                if (trim($part) && strpos($part, 'Content-Disposition') !== false) {
                    preg_match('/name="([^"]+)"/', $part, $nameMatches);
                    if ($nameMatches) {
                        $name = $nameMatches[1];
                        $valueStart = strpos($part, "\r\n\r\n") + 4;
                        $valueEnd = strpos($part, "\r\n", $valueStart);
                        if ($valueEnd === false) $valueEnd = strlen($part);
                        $value = trim(substr($part, $valueStart, $valueEnd - $valueStart));
                        if (preg_match('/filename="([^"]+)"/', $part, $fileMatches)) {
                            $filename = $fileMatches[1];
                            $fileDataStart = strpos($part, "\r\n\r\n", $valueStart) + 4;
                            $fileDataEnd = strpos($part, "--", $fileDataStart);
                            if ($fileDataEnd === false) $fileDataEnd = strlen($part);
                            $fileData = substr($part, $fileDataStart, $fileDataEnd - $fileDataStart);
                            $tmpDir = '/Applications/XAMPP/xamppfiles/htdocs/QUANLYBANHANG/tmp';
                            if (!file_exists($tmpDir)) mkdir($tmpDir, 0777, true);
                            $tmpFile = tempnam($tmpDir, 'upload');
                            file_put_contents($tmpFile, $fileData);
                            $files[$name] = [
                                'name' => $filename,
                                'tmp_name' => $tmpFile,
                                'error' => 0,
                                'size' => strlen($fileData)
                            ];
                        } else {
                            if (!empty($value)) $data[$name] = $value;
                        }
                    }
                }
            }
        } else {
            $data = json_decode($rawData, true) ?? [];
        }

        $name = $data['name'] ?? $product['name'];
        $description = $data['description'] ?? $product['description'];
        $price = $data['price'] ?? $product['price'];
        $category_id = $data['category_id'] ?? $product['category_id'];
        $quantity = $data['quantity'] ?? $product['quantity'];
        $newImage = isset($files['image']) && $files['image']['error'] === 0 ? $this->uploadImage($files['image']) : false;
        $image = $newImage !== false ? $newImage : ($data['current_image'] ?? $product['image']);

        $debugFile = '/Applications/XAMPP/xamppfiles/htdocs/QUANLYBANHANG/debug.log';
        file_put_contents($debugFile, "PUT Data: " . print_r($data, true) . "\nFILES: " . print_r($files, true) . "\nImage: " . $image . "\n", FILE_APPEND);

        $missingFields = [];
        if (empty($name)) $missingFields[] = 'name';
        if (empty($description)) $missingFields[] = 'description';
        if (empty($price)) $missingFields[] = 'price';
        if (empty($category_id)) $missingFields[] = 'category_id';
        if (empty($quantity)) $missingFields[] = 'quantity';
        if (empty($image)) $missingFields[] = 'image';

        if (!empty($missingFields)) {
            http_response_code(400);
            echo json_encode([
                'message' => 'Missing required fields',
                'fields' => $missingFields,
                'debug' => [
                    'raw_data' => substr($rawData, 0, 500),
                    'content_type' => $contentType,
                    'data' => $data,
                    'files' => $files
                ]
            ]);
            return;
        }

        $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $quantity, $image);

        if ($result === true) {
            echo json_encode(['message' => 'Product updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode([
                'message' => 'Product update failed',
                'errors' => $result,
                'debug' => [
                    'data' => $data,
                    'files' => $files,
                    'sent_values' => compact('name', 'description', 'price', 'category_id', 'quantity', 'image'),
                    'original_product' => $product
                ]
            ]);
        }
    }

private function uploadImage($file)
{
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/QUANLYBANHANG/public/images/";
    $targetFile = $targetDir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $debugFile = '/Applications/XAMPP/xamppfiles/htdocs/QUANLYBANHANG/debug.log';

    file_put_contents($debugFile, "Upload Attempt: $targetFile\n", FILE_APPEND);

    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
        file_put_contents($debugFile, "Created directory: $targetDir\n", FILE_APPEND);
    }
    if (!is_writable($targetDir)) {
        file_put_contents($debugFile, "Directory not writable: $targetDir\n", FILE_APPEND);
        return false;
    }
    if (!file_exists($file["tmp_name"]) || !is_readable($file["tmp_name"])) {
        file_put_contents($debugFile, "Cannot read tmp file: " . $file["tmp_name"] . "\n", FILE_APPEND);
        return false;
    }
    // Đọc nội dung file tạm
    $fileContent = file_get_contents($file["tmp_name"]);
    $mime = isset($file["type"]) && !empty($file["type"]) ? $file["type"] : 'unknown';
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    // Tìm vị trí bắt đầu của dữ liệu ảnh (sau \r\n\r\n)
    $imageStart = strpos($fileContent, "\r\n\r\n");
    if ($imageStart !== false) {
        $imageStart += 4; // Bỏ qua \r\n\r\n
        $imageData = substr($fileContent, $imageStart);
        file_put_contents($debugFile, "Image data extracted at offset: $imageStart\n", FILE_APPEND);
    } else {
        $imageData = $fileContent; // Nếu không tìm thấy, dùng nguyên nội dung (rủi ro thấp)
    }

    // Kiểm tra phần mở rộng và header
    if (!in_array($imageFileType, $allowedExtensions)) {
        file_put_contents($debugFile, "Invalid extension: $imageFileType - MIME: $mime\n", FILE_APPEND);
        return false;
    }
    if (strpos($imageData, "\xFF\xD8") === 0) {
        file_put_contents($debugFile, "Recognized as JPEG by content - MIME: $mime\n", FILE_APPEND);
    } elseif (strpos($imageData, "\x89PNG") === 0) {
        file_put_contents($debugFile, "Recognized as PNG by content - MIME: $mime\n", FILE_APPEND);
    } else {
        file_put_contents($debugFile, "Not an image or invalid: " . $file["tmp_name"] . " - MIME: $mime\n", FILE_APPEND);
        $debugTmpFile = "/Applications/XAMPP/xamppfiles/htdocs/QUANLYBANHANG/tmp/debug_" . basename($file["tmp_name"]);
        file_put_contents($debugTmpFile, $imageData);
        file_put_contents($debugFile, "Saved tmp file for debug: $debugTmpFile\n", FILE_APPEND);
        return false;
    }
    if ($file["size"] > 500000) {
        file_put_contents($debugFile, "File too large: " . $file["size"] . "\n", FILE_APPEND);
        return false;
    }
    // Chỉ dùng file_put_contents để lưu dữ liệu ảnh
    if (file_put_contents($targetFile, $imageData) !== false) {
        file_put_contents($debugFile, "Upload successful: $targetFile\n", FILE_APPEND);
        return basename($file["name"]);
    }
    file_put_contents($debugFile, "Upload failed: " . $file["tmp_name"] . " to $targetFile\n", FILE_APPEND);
    return false;
}
    public function destroy($id)
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
            return;
        }

        if (!$this->authenticate()) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            return;
        }

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            http_response_code(404);
            echo json_encode(['message' => 'Product not found']);
            return;
        }

        $result = $this->productModel->deleteProduct($id);

        if ($result === true) {
            echo json_encode(['message' => 'Product deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product deletion failed', 'errors' => $result]);
        }
    }
}
?>