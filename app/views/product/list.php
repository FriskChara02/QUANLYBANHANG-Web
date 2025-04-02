<?php
$listCSS = '/QUANLYBANHANG/public/css/list.css';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="<?php echo $listCSS; ?>">
</head>
<body>
    <div class="navbar">
        <a href="/QUANLYBANHANG/Product/pagemain">Trang chủ</a>
        <a href="/QUANLYBANHANG/Product/list">Danh sách sản phẩm</a>
        <a href="/QUANLYBANHANG/Product/add">Thêm sản phẩm mới</a>
        <div class="search-container">
            <input type="text" id="search-input" placeholder="Tìm kiếm sản phẩm...">
            <button onclick="searchProduct()">Tìm kiếm</button>
        </div>
        <a href="#" id="cart-icon" class="cart-icon">
            <i class="fas fa-shopping-cart"></i>
            Giỏ hàng (<span id="cart-count">0</span>)
        </a>
    </div>

    <h1>Danh sách sản phẩm</h1>

    <div class="banner-container" style="text-align: center; margin-bottom: 30px;">
        <img src="/QUANLYBANHANG/public/images/Macbook.jpg" alt="Banner" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    </div>

    <div class="container">
        <div class="account-container">
            <div class="balance">
                <i class="fas fa-coins"></i> Số tiền tài khoản của bạn: <span id="account-balance">100000</span> VNĐ
            </div>
            <div>
                <input type="number" id="add-amount" placeholder="Nhập số tiền" />
                <button onclick="addMoney()">Thêm tiền</button>
            </div>
            <a href="#" id="cart-icon" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                Giỏ hàng (<span id="cart-count">0</span>)
            </a>
        </div>

        <a href="/QUANLYBANHANG/Product/add" class="add-product-btn">Thêm sản phẩm mới</a>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="notification success">
                <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="notification error">
                <?php echo $_SESSION['error_message']; ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="row" id="product-list"></div>
        <button class="buy-all-btn" onclick="buyAllItems()">Mua tất cả hàng đặt</button>
    </div>

    <div id="cart-modal" class="cart-modal">
        <div class="cart-modal-content">
            <span class="close-btn" onclick="closeCart()">×</span>
            <h2>Giỏ hàng của bạn</h2>
            <ul id="cart-items-list"></ul>
            <div class="cart-total">
                Tổng cộng: <span id="cart-total">0</span> VNĐ
            </div>
            <button class="buy-all-btn" onclick="buyCartItems()">Thanh toán</button>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-left">
                <p>© 2025 All Rights Reserved | LoiNguyen</p>
                <p>Powered by LoiNguyen</p>
            </div>
            <div class="footer-right">
                <p><strong>Họ tên:</strong> Nguyễn Bảo Lợi</p>
                <p><strong>SĐT:</strong> 0902621440</p>
                <p><strong>Email:</strong> loi.nguyenbao02@gmail.com</p>
                <p><strong>MSSV:</strong> 2280618739</p>
            </div>
        </div>
    </footer>

    <script>
        const isAdmin = <?php echo json_encode($isAdmin); ?>;

        function refreshProductList() {
    fetch('/QUANLYBANHANG/api/product')
        .then(response => {
            if (!response.ok) throw new Error('Lỗi tải danh sách sản phẩm');
            return response.json();
        })
        .then(data => {
            const productList = document.getElementById('product-list');
            productList.innerHTML = ''; // Xóa danh sách cũ
            data.forEach(product => {
                const productItem = document.createElement('div');
                productItem.className = 'col-md-4 fade-in product-item';
                productItem.id = `product-${product.id}`;
                productItem.innerHTML = `
                <div class="product-card">
        <div class="image-container">
            <img src="/QUANLYBANHANG/public/images/${product.image}" alt="${product.name}" class="product-image">
        </div>
        <div class="product-info">
            <h2>${product.name}</h2>
            <p>${product.description}</p>
            <p class="price">${Number(product.price).toLocaleString('vi-VN')} VNĐ</p>
            <p class="quantity">Số lượng: ${product.quantity}</p>
            <p class="category">Danh mục: ${product.category_name}</p>
        </div>
                        <div class="product-actions">
                            ${isAdmin ? `
                                <button class="edit-btn" onclick="window.location.href='/QUANLYBANHANG/Product/edit/${product.id}'">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                                <button class="delete-btn" onclick="deleteProduct(${product.id})">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            ` : `
                                <button class="buy-btn" onclick="confirmBuy('${product.id}', ${product.price}, '${product.name}')">
                                    <i class="fas fa-shopping-cart"></i> Mua Hàng
                                </button>
                                <button class="order-btn" onclick="placeOrder('${product.id}')">
                                    <i class="fas fa-box"></i> Đặt hàng
                                </button>
                            `}
                        </div>
                    </div>
                `;
                productList.appendChild(productItem);
            });
        })
        .catch(error => console.error('Lỗi:', error));
}

function deleteProduct(id) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        fetch(`/QUANLYBANHANG/api/product/${id}`, { method: 'DELETE' })
            .then(response => response.json())
            .then(data => {
                if (data.message === 'Product deleted successfully') {
                    document.getElementById(`product-${id}`).remove();
                    alert('Xóa sản phẩm thành công');
                    refreshProductList(); // Cập nhật lại danh sách sau khi xóa
                } else {
                    alert('Xóa sản phẩm thất bại');
                }
            });
    }
}

document.addEventListener("DOMContentLoaded", function() {
    refreshProductList(); // Load lần đầu

    var successMessage = document.querySelector('.notification.success');
    var errorMessage = document.querySelector('.notification.error');
    if (successMessage) {
        successMessage.style.display = 'block';
        setTimeout(() => successMessage.style.display = 'none', 5000);
    }
    if (errorMessage) {
        errorMessage.style.display = 'block';
        setTimeout(() => errorMessage.style.display = 'none', 5000);
    }
});


        function searchProduct() {
            var searchInput = document.getElementById('search-input').value.toLowerCase();
            var productItems = document.querySelectorAll('.product-item');
            productItems.forEach(item => {
                var productName = item.querySelector('h2').textContent.toLowerCase();
                item.style.display = productName.includes(searchInput) ? 'block' : 'none';
            });
        }

        function addMoney() {
            var addAmount = document.getElementById('add-amount').value;
            if (addAmount && !isNaN(addAmount)) {
                var currentBalance = parseInt(document.getElementById('account-balance').textContent);
                var newBalance = currentBalance + parseInt(addAmount);
                document.getElementById('account-balance').textContent = newBalance;
            } else {
                alert("Vui lòng nhập số tiền hợp lệ.");
            }
        }

        var cart = [];
        var cartCountElement = document.getElementById('cart-count');
        var cartModal = document.getElementById('cart-modal');
        var cartItemsList = document.getElementById('cart-items-list');
        var cartTotalElement = document.getElementById('cart-total');

        function addToCart(productID, productName, productPrice) {
            cart.push({ id: productID, name: productName, price: productPrice });
            updateCart();
        }

        function updateCart() {
            cartCountElement.textContent = cart.length;
            cartItemsList.innerHTML = '';
            let total = 0;
            cart.forEach(item => {
                total += item.price;
                var listItem = document.createElement('li');
                listItem.textContent = `${item.name} - ${item.price.toLocaleString('vi-VN')} VNĐ`;
                cartItemsList.appendChild(listItem);
            });
            cartTotalElement.textContent = total;
        }

        function placeOrder(productID) {
            var productName = document.querySelector(`#product-${productID} .product-info h2`).textContent;
            var productPrice = parseInt(document.querySelector(`#product-${productID} .price`).textContent.replace(/[^\d]/g, ''));
            addToCart(productID, productName, productPrice);
            alert(`Bạn đã đặt hàng sản phẩm ${productName} thành công!`);
            updateCart();
        }

        function confirmBuy(productID, productPrice, productName) {
            var balance = parseInt(document.getElementById('account-balance').textContent);
            if (balance >= productPrice) {
                balance -= productPrice;
                document.getElementById('account-balance').textContent = balance;
                alert(`Bạn đã mua sản phẩm ${productName} thành công!`);
            } else {
                alert("Bạn không đủ tiền để mua sản phẩm này.");
            }
        }

        function buyAllItems() {
            var balance = parseInt(document.getElementById('account-balance').textContent);
            var totalPrice = 0;
            cart.forEach(item => totalPrice += item.price);
            if (totalPrice > balance) {
                alert("Bạn không đủ tiền để mua tất cả sản phẩm trong giỏ.");
            } else if (confirm("Bạn có chắc chắn muốn mua tất cả sản phẩm này?")) {
                balance -= totalPrice;
                document.getElementById('account-balance').textContent = balance;
                cart = [];
                updateCart();
                alert("Bạn đã mua tất cả sản phẩm trong giỏ hàng thành công!");
            }
        }

        function buyCartItems() {
            var totalPrice = parseInt(cartTotalElement.textContent);
            var balance = parseInt(document.getElementById('account-balance').textContent);
            if (cart.length === 0) {
                alert("Giỏ hàng của bạn đang trống!");
                return;
            }
            if (balance >= totalPrice) {
                balance -= totalPrice;
                document.getElementById('account-balance').textContent = balance;
                sessionStorage.setItem('cart', JSON.stringify(cart));
                cart = [];
                updateCart();
                window.location.href = '/QUANLYBANHANG/Product/confirm';
            } else {
                alert("Bạn không đủ tiền để mua tất cả sản phẩm.");
            }
        }

        document.getElementById('cart-icon').onclick = function() {
            cartModal.classList.add('show');
        };

        function closeCart() {
            cartModal.classList.remove('show');
        }

        window.addEventListener('scroll', function() {
            var elements = document.querySelectorAll('.fade-in');
            elements.forEach(element => {
                var position = element.getBoundingClientRect();
                if (position.top < window.innerHeight) {
                    element.classList.add('visible');
                }
            });
        });
    </script>
</body>
</html>