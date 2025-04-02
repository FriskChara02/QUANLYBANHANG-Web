<?php
require_once 'app/config/session.php'; // Quản lý session chung
require_once 'app/config/database.php';
require_once 'app/models/AccountModel.php';
require_once 'app/utils/JWTHandler.php';

class AccountController
{
    private $accountModel;
    private $db;
    private $jwtHandler;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
        $this->jwtHandler = new JWTHandler(); // Khởi tạo JWTHandler
    }

    // Hiển thị form đăng ký
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->save();
        } else {
            include_once 'app/views/account/register.php';
        }
    }

    // Xử lý lưu tài khoản khi đăng ký
    public function save()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmpassword'] ?? '';
        $role = $_POST['role'] ?? 'user';

        $errors = [];

        if (empty($username)) {
            $errors['username'] = "Vui lòng nhập username!";
        }
        if (empty($password)) {
            $errors['password'] = "Vui lòng nhập password!";
        }
        if ($password !== $confirmPassword) {
            $errors['confirmPass'] = "Mật khẩu và xác nhận chưa khớp!";
        }
        if (!in_array($role, ['admin', 'user'])) {
            $role = 'user'; // Đảm bảo role chỉ là admin hoặc user
        }
        if ($this->accountModel->getAccountByUsername($username)) {
            $errors['account'] = "Tài khoản này đã được đăng ký!";
        }

        if (count($errors) > 0) {
            include_once 'app/views/account/register.php';
        } else {
            $result = $this->accountModel->save($username, $password, $role);
            if ($result) {
                $_SESSION['success_message'] = "Đăng ký thành công! Vui lòng đăng nhập.";
                header('Location: /QUANLYBANHANG/account/login');
                exit;
            } else {
                $_SESSION['error_message'] = "Đăng ký thất bại!";
                include_once 'app/views/account/register.php';
            }
        }
    }

    // Hiển thị form đăng nhập và xử lý đăng nhập
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
    
            if (empty($username) || empty($password)) {
                $_SESSION['error_message'] = "Vui lòng điền đầy đủ thông tin đăng nhập!";
                include 'app/views/account/login.php';
                return;
            }
    
            $user = $this->accountModel->checkLogin($username, $password);
            if ($user) {
                // Tạo JWT token
                $token = $this->jwtHandler->encode([
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['user_role']
                ]);
                $_SESSION['user'] = (object) [
                    'username' => $user['username'],
                    'id' => $user['id'],
                    'token' => $token // Sử dụng token vừa tạo
                ];
                $_SESSION['role'] = $user['user_role'];
                header('Location: /QUANLYBANHANG/api/product');
                exit;
            } else {
                $_SESSION['error_message'] = "Sai tên đăng nhập hoặc mật khẩu!";
                include 'app/views/account/login.php';
            }
        } else {
            include 'app/views/account/login.php';
        }
    }

    // Đăng xuất
    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /QUANLYBANHANG/account/login');
        exit;
    }

    // Phương thức checklogin (có thể gộp vào login, nhưng giữ lại nếu bạn cần dùng riêng)
    public function checklogin()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['error_message'] = "Vui lòng điền đầy đủ thông tin đăng nhập!";
            header('Location: /QUANLYBANHANG/account/login');
            exit;
        }

        // Lấy thông tin tài khoản từ DB
        $account = $this->accountModel->getAccountByUsername($username);

        if ($account && password_verify($password, $account->password)) {
            // Tạo JWT token
            $tokenPayload = [
                'id' => $account->id,
                'username' => $account->username,
                'role' => $account->user_role,
                'iat' => time(), // Thời gian tạo token
                'exp' => time() + 3600 // Token hết hạn sau 1 giờ
            ];

            $token = $this->jwtHandler->encode($tokenPayload);

            // Lưu thông tin vào session
            $_SESSION['user'] = (object) [
                'id' => $account->id,
                'username' => $account->username,
                'role' => $account->user_role,
                'token' => $token
            ];
            $_SESSION['role'] = $account->user_role;

            echo "<pre>";
            print_r($_SESSION['user']);
            echo "</pre>";
            exit;

            // Nếu request là API, trả về JSON chứa token
            if ($_SERVER['HTTP_ACCEPT'] === 'application/json') {
                header('Content-Type: application/json');
                echo json_encode([
                    'message' => 'Đăng nhập thành công!',
                    'token' => $token
                ]);
                exit;
            }

            // Nếu không phải API, chuyển hướng đến trang chính
            header('Location: /QUANLYBANHANG/Product/pagemain');
            exit;
        } else {
            $_SESSION['error_message'] = "Sai tên đăng nhập hoặc mật khẩu!";
            header('Location: /QUANLYBANHANG/account/login');
            exit;
        }
    }
}
}
?>