<?php
// Require necessary files
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');

class CategoryController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        // Kết nối đến database
        $this->db = (new Database())->getConnection();
        
        // Khởi tạo đối tượng CategoryModel
        $this->categoryModel = new CategoryModel($this->db);
    }

    // Hiển thị danh sách các danh mục
    public function list()
    {
        // Lấy danh sách các danh mục từ CategoryModel
        $categories = $this->categoryModel->getCategories();
        
        // Bao gồm view hiển thị danh sách danh mục
        include 'app/views/category/list.php';
    }
}
?>
