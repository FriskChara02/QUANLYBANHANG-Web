<?php
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán</title>
    <link type="text/css" rel="stylesheet" href="/QUANLYBANHANG/public/css/confirm.css">
</head>
<body>

    <!-- Header -->
    <div class="navbar">
        <a href="/QUANLYBANHANG/Product/pagemain">Trang chủ</a>
        <a href="/QUANLYBANHANG/Product/list">Danh sách sản phẩm</a>
        <a href="/QUANLYBANHANG/Product/add">Thêm sản phẩm mới</a>
    </div>

    <?php if (!isset($_POST['confirm_order'])): ?>
        <!-- Form thanh toán -->
        <div class="container">
            <h1>Thanh Toán</h1>
            <form id="checkout-form" method="POST" action="/QUANLYBANHANG/Product/confirm">
                <input type="hidden" name="cart_data" id="cart-data">
                <div class="form-group">
                    <label for="fullname">Họ tên:</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="address">Địa chỉ:</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                <div class="buttons">
                    <button type="submit" name="confirm_order">Thanh Toán</button>
                    <button type="button" onclick="window.location.href='/QUANLYBANHANG/Product/list'">Quay lại giỏ hàng</button>
                </div>
            </form>
        </div>
        <script>
            // Lấy giỏ hàng từ sessionStorage và gửi cùng form
            document.getElementById('checkout-form').onsubmit = function() {
                var cart = sessionStorage.getItem('cart');
                document.getElementById('cart-data').value = cart;
            };
        </script>
    <?php else: ?>
        <!-- Trang xác nhận -->
        <div class="container success-message">
            <h1>Xác nhận đơn hàng</h1>
            <p>Cảm ơn bạn đã đặt hàng. Đơn hàng của bạn đã được xử lý thành công.</p>
            <button onclick="window.location.href='/QUANLYBANHANG/Product/list'">Tiếp tục mua sắm</button>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <p>© 2025 All Rights Reserved | LoiNguyen</p>
            <p>Powered by LoiNguyen</p>
        </div>
    </footer>

</body>
</html>