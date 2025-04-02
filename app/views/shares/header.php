<?php
$headerCSS = '/QUANLYBANHANG/public/css/header.css';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $headerCSS; ?>">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .product-image {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="/QUANLYBANHANG/api/product">Web Bán Hàng</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/QUANLYBANHANG/api/product">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/QUANLYBANHANG/api/product">Sản phẩm</a>
                </li>
                <li class="nav-item">
                    <?php if (isset($_SESSION['user'])): ?>
                        <a class="nav-link" href="#">Xin chào, <?php echo htmlspecialchars($_SESSION['user']->username, ENT_QUOTES, 'UTF-8'); ?></a>
                    <?php else: ?>
                        <a class="nav-link" href="/QUANLYBANHANG/account/login">Đăng nhập</a>
                    <?php endif; ?>
                </li>
                <li class="nav-item">
                    <?php if (isset($_SESSION['user'])): ?>
                        <a class="nav-link btn logout-btn text-white px-3" href="/QUANLYBANHANG/account/logout">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">