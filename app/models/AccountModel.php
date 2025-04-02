<?php
class AccountModel
{
    private $conn;
    private $table_name = "account";

    public function __construct($db)
    {
        $this->conn = $db; // Lưu kết nối database vào $conn
    }

    // Lấy tài khoản theo username
    public function getAccountByUsername($username)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Trả về mảng để thống nhất với checkLogin
    }

    // Kiểm tra đăng nhập
    public function checkLogin($username, $password)
    {
        $query = "SELECT id, username, password, role FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // Xóa password khỏi kết quả
            $user['user_role'] = $user['role']; // Thêm key 'user_role' để khớp với AccountController
            unset($user['role']); // Xóa key 'role' cũ
            return $user;
        }
        return false;
    }

    // Lưu tài khoản mới
    public function save($username, $password, $role)
    {
        $query = "INSERT INTO " . $this->table_name . " (username, password, role) 
                  VALUES (:username, :password, :role)";
        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Mã hóa mật khẩu
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role); // Đổi :user_role thành :role để khớp với database
        return $stmt->execute();
    }
}
?>