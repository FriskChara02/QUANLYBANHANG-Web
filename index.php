<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Lấy URL và xác định controller trước khi kiểm tra session
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Kiểm tra phần đầu tiên của URL để xác định controller
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'DefaultController';

// Kiểm tra nếu không có user trong session và không phải trang login/register thì chuyển hướng
if (!isset($_SESSION['user']) && $controllerName !== 'AccountController') {
    header('Location: /QUANLYBANHANG/account/login');
    exit;
}

require_once 'app/models/ProductModel.php';
require_once 'app/helpers/SessionHelper.php';

// Kiểm tra phần thứ hai của URL để xác định action
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// Định tuyến các yêu cầu API
if (isset($url[0]) && strtolower($url[0]) === 'api' && isset($url[1])) {
    $apiControllerName = ucfirst($url[1]) . 'ApiController'; // Ví dụ: ProductApiController
    $controllerPath = 'app/controllers/' . $apiControllerName . '.php';

    if (!file_exists($controllerPath)) {
        http_response_code(404);
        echo json_encode(['message' => 'API Controller not found']);
        exit;
    }

    require_once $controllerPath;
    $controller = new $apiControllerName();

    $method = $_SERVER['REQUEST_METHOD'];
    $id = $url[2] ?? null;

    // Xác định action dựa trên phương thức HTTP
    switch ($method) {
        case 'GET':
            $action = $id ? 'show' : 'index';
            break;
        case 'POST':
            $action = 'store';
            break;
        case 'PUT':
            $action = $id ? 'update' : null;
            break;
        case 'DELETE':
            $action = $id ? 'destroy' : null;
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
            exit;
    }

    if (!method_exists($controller, $action)) {
        http_response_code(404);
        echo json_encode(['message' => 'Action not found']);
        exit;
    }

    // Gọi action với tham số (nếu có)
    if ($id) {
        call_user_func_array([$controller, $action], [$id]);
    } else {
        call_user_func_array([$controller, $action], []);
    }
    exit;
}

// Định tuyến cho các controller thông thường
$controllerPath = 'app/controllers/' . $controllerName . '.php';
if (!file_exists($controllerPath)) {
    die('Controller not found');
}

require_once $controllerPath;
$controller = new $controllerName();

if (!method_exists($controller, $action)) {
    die('Action not found');
}

// Gọi action với các tham số còn lại (nếu có)
call_user_func_array([$controller, $action], array_slice($url, 2));