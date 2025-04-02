<?php
class SessionHelper {
    // Khởi động session nếu chưa bắt đầu
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Kiểm tra người dùng đã đăng nhập chưa
    public static function isLoggedIn() {
        self::start(); // Đảm bảo session đã được khởi động
        return isset($_SESSION['username']);
    }

    // Kiểm tra người dùng có phải admin không
    public static function isAdmin() {
        self::start(); // Đảm bảo session đã được khởi động
        return isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    // Lấy vai trò của người dùng, mặc định là 'guest'
    public static function getRole() {
        self::start(); // Đảm bảo session đã được khởi động
        return $_SESSION['role'] ?? 'guest';
    }

    // Lấy tên người dùng
    public static function getUsername() {
        self::start(); // Đảm bảo session đã được khởi động
        return $_SESSION['username'] ?? 'guest'; // Trả về 'guest' nếu không có username trong session
    }

    // Đăng xuất và xóa session
    public static function logout() {
        self::start(); // Đảm bảo session đã được khởi động
        session_unset(); // Xóa tất cả session
        session_destroy(); // Hủy session
    }
}

?>
