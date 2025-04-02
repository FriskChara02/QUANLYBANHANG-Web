<?php
require_once 'app/config/database.php';
require_once 'app/models/CategoryModel.php';

class CategoryApiController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    public function index()
    {
        header('Content-Type: application/json');
        $categories = $this->categoryModel->getCategories();
        if ($categories) {
            echo json_encode($categories);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'No categories found']);
        }
    }

    public function show($id)
    {
        header('Content-Type: application/json');
        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            echo json_encode($category);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Category not found']);
        }
    }

    public function store()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['name'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing required field: name']);
            return;
        }

        $name = $data['name'];
        $description = $data['description'] ?? null;
        $result = $this->categoryModel->addCategory($name, $description);
        
        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Category created successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Category creation failed', 'errors' => $result]);
        }
    }

    public function update($id)
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['name'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing required field: name']);
            return;
        }

        $name = $data['name'];
        $description = $data['description'] ?? null;
        $result = $this->categoryModel->updateCategory($id, $name, $description);
        
        if ($result === true) {
            echo json_encode(['message' => 'Category updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Category update failed', 'errors' => $result]);
        }
    }

    public function destroy($id)
    {
        header('Content-Type: application/json');
        $result = $this->categoryModel->deleteCategory($id);
        
        if ($result === true) {
            echo json_encode(['message' => 'Category deleted successfully']);
        } else {
            if (isset($result['category_not_found'])) {
                http_response_code(404);
                echo json_encode(['message' => 'Category not found']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Category deletion failed', 'errors' => $result]);
            }
        }
    }
}
?>