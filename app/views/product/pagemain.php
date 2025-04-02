<?php
require_once('app/models/ProductModel.php'); // Thêm dòng này
$pageMainCSS = '/QUANLYBANHANG/public/css/pagemain.css';
$isLoggedIn = isset($_SESSION['user']);
$db = (new Database())->getConnection(); // Giả sử bạn có class Database
$productModel = new ProductModel($db); // Khởi tạo ProductModel
$products = $productModel->getProducts(); // Lấy danh sách sản phẩm từ DB
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <!-- Liên kết tới file CSS của Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Thêm liên kết tới file CSS của Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Thêm Google Fonts cho Oswald -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&display=swap" rel="stylesheet">
    <!-- Liên kết tới file CSS của trang chủ -->
    <link type="text/css" rel="stylesheet" href="<?php echo $pageMainCSS; ?>">
    <!-- Liên kết tới file JavaScript của Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<!-- Thanh điều hướng -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="/QUANLYBANHANG/Product/pagemain">Trang chủ</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/QUANLYBANHANG/Product/list">Danh sách sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/QUANLYBANHANG/Product/add">Thêm sản phẩm mới</a>
                </li>
            </ul>
            <!-- Hiển thị thông tin người dùng nếu đã đăng nhập, và di chuyển sang góc phải -->
            <?php if (isset($_SESSION['user'])): ?>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <span class="nav-link text-white">Haloooooooooooooooooooo, <?php echo htmlspecialchars($_SESSION['user']->username); ?>!</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/QUANLYBANHANG/account/logout">Đăng xuất</a>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/QUANLYBANHANG/account/login">Đăng nhập</a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>



    <!-- Banner -->
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <!-- Hình ảnh đầu tiên -->
            <div class="carousel-item active" data-bs-interval="3000">
                <img src="/QUANLYBANHANG/public/images/Macbook01.jpg" alt="Banner 1">
            </div>
            <!-- Hình ảnh thứ hai -->
            <div class="carousel-item" data-bs-interval="3000">
                <img src="/QUANLYBANHANG/public/images/Iphone01.jpg" alt="Banner 2">
            </div>
             <!-- Hình ảnh thứ ba -->
            <div class="carousel-item" data-bs-interval="3000">
                <img src="/QUANLYBANHANG/public/images/Ipad01.jpg" alt="Banner 2">
            </div>
            <!-- Hình ảnh thứ tư -->
            <div class="carousel-item" data-bs-interval="3000">
                <img src="/QUANLYBANHANG/public/images/PC01.jpg" alt="Banner 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Nội dung trang chủ -->
    <div class="container">
        <h1>Chào mừng đến với Trang Quản lý Sản phẩm</h1>

        <div class="content">
            <h2>Giới thiệu về hệ thống</h2>
            <p>
                Đây là hệ thống quản lý sản phẩm đơn giản, dễ dàng thêm, sửa và xóa sản phẩm.
            </p>
            <p>
                Bạn có thể bắt đầu bằng cách thêm sản phẩm mới hoặc xem danh sách sản phẩm hiện có.
            </p>

            <!-- Hình ảnh giới thiệu hệ thống -->
            <img src="/QUANLYBANHANG/public/images/FirstPic.jpg" alt="Giới thiệu hệ thống">
            
            <!-- Button chuyển hướng -->
            <div>
                <a href="/QUANLYBANHANG/Product/list" class="btn">Xem Danh Sách Sản Phẩm</a>
                <a href="/QUANLYBANHANG/Product/add" class="btn">Thêm Sản Phẩm Mới</a> 
            </div>




            
            <div class="container mt-4">

<h2 class="text-center mb-4">Danh sách sản phẩm</h2>
<!-- Table to display the product list -->
<div class="row mb-3">
    <div class="col-12">
        <a href="/QUANLYBANHANG/Product/add" class="btn btn-success">Thêm sản phẩm mới</a>
    </div>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Hình ảnh</th>
            <th scope="col">Tên sản phẩm</th>
            <th scope="col">Giá</th>
            <th scope="col">Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <th scope="row"><?php echo $product['id']; ?></th>
                <td>
                    <?php if (!empty($product['image'])): ?>
                        <img src="/QUANLYBANHANG/public/images/<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" 
                             class="product-image" alt="Hình ảnh sản phẩm">
                    <?php else: ?>
                        <span>Chưa có ảnh</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></td>
                <td>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="/QUANLYBANHANG/Product/edit/<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                        <a href="/QUANLYBANHANG/Product/delete/<?php echo $product['id']; ?>" class="btn btn-danger btn-sm">Xóa</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
            



        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <p>© 2025 All Rights Reserved | LoiNguyen</p>
            </div>
            <div class="footer-right">
                <p><strong>Họ Tên: Nguyễn Bảo Lợi</strong></p>
                <p>SĐT: 0902621440</p>
                <p>Email: loi.nguyenbao02@gmail.com</p>
            </div>
        </div>
    </footer>

</body>
</html>