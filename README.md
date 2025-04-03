# QUANLYBANHANG-Web 🛒✨

![PHP](https://img.shields.io/badge/PHP-8.2-blue?style=flat-square&logo=php) ![XAMPP](https://img.shields.io/badge/XAMPP-8.2.12-orange?style=flat-square&logo=xampp) ![MySQL](https://img.shields.io/badge/MySQL-8.0-blue?style=flat-square&logo=mysql) ![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple?style=flat-square&logo=bootstrap) ![jQuery](https://img.shields.io/badge/jQuery-3.6-green?style=flat-square&logo=jquery) ![Composer](https://img.shields.io/badge/Composer-2.7-red?style=flat-square&logo=composer)

Chào mừng đến với **QUANLYBANHANG-Web** - một ứng dụng quản lý bán hàng đơn giản nhưng mạnh mẽ, được xây dựng bằng PHP và chạy trên XAMPP. Dự án này cung cấp giao diện người dùng thân thiện, API mạnh mẽ, và hỗ trợ quản lý sản phẩm, danh mục, tài khoản cùng đơn hàng.

📌 **GitHub**: [https://github.com/FriskChara02/QUANLYBANHANG-Web](https://github.com/FriskChara02/QUANLYBANHANG-Web)

(Nguyễn Bảo Lợi - 2280618739 - 22DTHH1)

---

## Tính năng nổi bật 🚀

- **Quản lý tài khoản** 👤
  - Đăng nhập (`login`) và đăng ký (`register`) với JWT xác thực.
  - Phân quyền người dùng: `admin` và `user`.

- **Quản lý sản phẩm** 🛍️
  - Thêm, sửa, xóa sản phẩm qua giao diện API.
  - Tìm kiếm sản phẩm theo từ khóa.

- **Quản lý danh mục** 📋
  - Hiển thị và quản lý danh mục sản phẩm.

- **API mạnh mẽ** 🌐
  - Các endpoint RESTful cho `ProductApiController` và `CategoryApiController`.
  - Hỗ trợ GET, POST, PUT, DELETE với JSON response.

- **Giao diện hiện đại** 🎨
  - Sử dụng Bootstrap 5.3 và jQuery 3.6 cho trải nghiệm mượt mà.

- **Cơ sở dữ liệu** 🗄️
  - MySQL với schema đầy đủ trong `database.sql`.

---

## Công nghệ sử dụng 🛠️

| Công nghệ         | Mô tả                            | Icon                          |
|-------------------|----------------------------------|-------------------------------|
| **PHP**           | Ngôn ngữ chính của dự án         | ![PHP](https://img.shields.io/badge/-PHP-777BB4?logo=php) |
| **XAMPP**         | Môi trường phát triển cục bộ     | ![XAMPP](https://img.shields.io/badge/-XAMPP-FB7A24?logo=xampp) |
| **MySQL**         | Cơ sở dữ liệu quan hệ            | ![MySQL](https://img.shields.io/badge/-MySQL-4479A1?logo=mysql) |
| **Bootstrap**     | Framework CSS cho giao diện      | ![Bootstrap](https://img.shields.io/badge/-Bootstrap-7952B3?logo=bootstrap) |
| **jQuery**        | Thư viện JS hỗ trợ AJAX          | ![jQuery](https://img.shields.io/badge/-jQuery-0769AD?logo=jquery) |
| **Composer**      | Quản lý thư viện PHP (JWT, ...)  | ![Composer](https://img.shields.io/badge/-Composer-885630?logo=composer) |

---

## Cấu trúc dự án 📂

```
/QUANLYBANHANG-Web
├── app/
│   ├── controllers/      # Các controller: AccountController.php, ProductApiController.php, ...
│   ├── models/           # Các model: AccountModel.php, ProductModel.php, CategoryModel.php
│   ├── views/            # Giao diện: login.php, register.php, product_list.php
│   ├── utils/            # Công cụ hỗ trợ: JWTHandler.php, SessionHelper.php
│   └── config/           # Cấu hình: database.php, session.php
├── public/               # Tài nguyên tĩnh: CSS, JS, images
├── vendor/               # Thư viện Composer
├── database.sql          # Schema và dữ liệu mẫu MySQL
└── README.md             # File này
```

---

## Hướng dẫn cài đặt ⚙️

1. **Yêu cầu**:
   - XAMPP (Apache + MySQL) phiên bản 8.2.12 trở lên.
   - Composer cài đặt sẵn.

2. **Clone dự án**:
   ```bash
   git clone https://github.com/FriskChara02/QUANLYBANHANG-Web.git
   cd QUANLYBANHANG-Web
   ```

3. **Cài đặt thư viện**:
   ```bash
   composer install
   ```

4. **Cấu hình database**:
   - Tạo database `my_store` trong MySQL.
   - Import file `database.sql`:
     ```bash
     mysql -u root -p my_store < database.sql
     ```

5. **Chạy dự án**:
   - Copy thư mục `QUANLYBANHANG-Web` vào `/Applications/XAMPP/xamppfiles/htdocs/`.
   - Khởi động XAMPP (Apache + MySQL).
   - Truy cập: `http://localhost/QUANLYBANHANG-Web/account/login`.

6. **Tài khoản mặc định**:
   - Username: `user`
   - Password: `123`

---

## API Endpoints 🌐

| Method | Endpoint                     | Mô tả                        |
|--------|------------------------------|------------------------------|
| GET    | `/api/product`              | Lấy danh sách sản phẩm       |
| POST   | `/api/product`              | Thêm sản phẩm mới            |
| GET    | `/api/product/{id}`         | Lấy chi tiết sản phẩm        |
| PUT    | `/api/product/{id}`         | Cập nhật sản phẩm            |
| DELETE | `/api/product/{id}`         | Xóa sản phẩm                 |
| GET    | `/api/category`             | Lấy danh sách danh mục       |

---

## Sai sót đã gặp và cách sửa 🐛 (Dự án này không hoàn hảo T.T)

- **Vấn đề**: _Sau khi xong "Bảo Mật RestFul API với JWT" thì lúc postman ví dụ get thì không còn hiện category mà hiện full HTML._
  - **Nguyên nhân**: _Khó giải thích._
  - **Cách sửa**: _Thay đổi code._
- **Vấn đề**: _Ở "http://localhost/QUANLYBANHANG/api/product" Thêm, Sửa, Xoá đã ổn nhưng lúc sửa đổi hình ảnh thì độ phân giải của hình ảnh bị lỗi ví dụ ảnh gốc là 1200x400, lúc đổi thì còn 3..x3.._
  - **Nguyên nhân**: _Trong code hoặc chưa tìm ra được._
  - **Cách sửa**: _Chỉ nên add duy nhất hình ảnh lần đầu, không nên sửa đổi lại hình ảnh là ổn áp._
- **Vấn đề**: _Sau khi tạo Token thì Token được lưu vào Session rồi từ Session đó sẽ lấy Token ra._
  - **Nguyên nhân**: _Ở trong  "AccountController.php" ở "public function login()"._
  - **Cách sửa**: _Làm sao để không sử dụng Session là được, nên cho Token từ thẳng HTML ra._

---

## Đóng góp 🤝

Mọi đóng góp đều được hoan nghênh! Hãy:
1. Fork dự án.
2. Tạo branch mới (`git checkout -b feature/ten-tinh-nang`).
3. Commit thay đổi (`git commit -m "Mô tả thay đổi"`).
4. Push lên branch (`git push origin feature/ten-tinh-nang`).
5. Tạo Pull Request.

---

## Tác giả ✍️

- **FriskChara02** - [GitHub](https://github.com/FriskChara02)

---
