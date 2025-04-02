<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm - API</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/QUANLYBANHANG/public/css/api_product_list.css">
    <link rel="stylesheet" href="/QUANLYBANHANG/public/css/api_navbar_list.css">
    <link rel="stylesheet" href="/QUANLYBANHANG/public/css/api_footer_list.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/QUANLYBANHANG/api/product">Quản lý sản phẩm</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">


            <ul class="navbar-nav ms-auto">
    <li class="nav-item">
        <a class="nav-link" href="#">Trang chủ</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">Sản phẩm</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">Liên hệ</a>
    </li>

    <!-- Kiểm tra xem người dùng đã đăng nhập hay chưa -->
    <?php if (isset($_SESSION['user'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="#">Hellooooooooooooooo - <?php echo htmlspecialchars($_SESSION['user']->username); ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/QUANLYBANHANG/account/logout">Đăng xuất</a>
        </li>
    <?php else: ?>
        <li class="nav-item">
            <a class="nav-link" href="/QUANLYBANHANG/account/login">Đăng nhập</a>
        </li>
    <?php endif; ?>
</ul>


            </div>
        </div>
    </nav>
        <div class="container mt-4">
        <h1 class="text-center mb-4">Quản lý sản phẩm</h1>



        <!-- Thanh tìm kiếm -->
        <div class="search-container mb-4">
            <div class="input-group">
                <input type="text" id="search-input" class="form-control" placeholder="Tìm kiếm sản phẩm...">
                <button id="search-btn" class="btn btn-primary"><i class="fas fa-search"></i> Tìm kiếm</button>
            </div>
        </div>

        <!-- Form thêm/sửa sản phẩm -->
        <form id="product-form" class="card p-4 mb-4">
            <div class="row">
                <div class="col-md-6 form-group mb-3">
                    <label for="name">Tên sản phẩm:</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 form-group mb-3">
                    <label for="price">Giá:</label>
                    <input type="number" id="price" name="price" step="0.01" class="form-control" required>
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="description">Mô tả:</label>
                <textarea id="description" name="description" class="form-control" required></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 form-group mb-3">
                    <label for="category_id">Danh mục:</label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 form-group mb-3">
                    <label for="quantity">Số lượng:</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" required>
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="image">Hình ảnh:</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                <input type="hidden" id="current-image" name="current_image">
            </div>
            <div class="text-center">
                <button type="submit" id="submit-btn" class="btn btn-success">Thêm sản phẩm</button>
                <button type="button" id="update-btn" class="btn btn-warning" style="display: none;">Cập nhật sản phẩm</button>
            </div>
        </form>

        <!-- Danh sách sản phẩm -->
        <div class="product-list" id="product-list">
            <?php foreach ($products as $product): ?>
                <div class="product-item card mb-3">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="/QUANLYBANHANG/public/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid rounded-start">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text">Giá: <?php echo number_format($product['price'], 0, ',', '.') ?> VNĐ</p>
                                <p class="card-text">Số lượng: <?php echo $product['quantity']; ?></p>
                                <p class="card-text">Danh mục: <?php echo htmlspecialchars($product['category_name']); ?></p>
                                <button onclick="editProduct(<?php echo $product['id']; ?>)" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Sửa</button>
                                <button onclick="deleteProduct(<?php echo $product['id']; ?>)" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Xóa</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

                <!-- Hiển thị Token -->
                <div id="token-display" class="alert alert-info mt-3">
        Token: <span id="token-value"></span>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Quản lý bán hàng. Designed by <a href="#">Loi Nguyen</a>.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentProductId = null;

        const token = '<?php echo isset($_SESSION['user']) ? $_SESSION['user']->token : ''; ?>'; // Lấy token từ session

        // Hiển thị token lên màn hình
    document.getElementById('token-value').textContent = token;


        // Thêm hoặc cập nhật sản phẩm
        $('#product-form').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const fileInput = $(this).find('input[name="image"]')[0];
            
            console.log('FormData:', Array.from(formData.entries()));
            if (fileInput.files.length === 0 && currentProductId) {
                console.log('No file selected, keeping current image');
            } else if (fileInput.files.length > 0) {
                console.log('File selected:', fileInput.files[0].name, 'Size:', fileInput.files[0].size);
            }

            const url = currentProductId ? `/QUANLYBANHANG/api/product/${currentProductId}` : '/QUANLYBANHANG/api/product';
            const method = currentProductId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'Authorization': 'Bearer ' + token // Thêm header Authorization
        },
                success: function(response) {
                    if (response.message === 'Product created successfully' || response.message === 'Product updated successfully') {
                        alert(method === 'POST' ? 'Thêm sản phẩm thành công' : 'Cập nhật sản phẩm thành công');
                        location.reload();
                    } else {
                        alert('Thất bại: ' + JSON.stringify(response));
                    }
                },
                error: function(xhr) {
    if (xhr.status === 401) {
        alert('Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại!');
        window.location.href = '/QUANLYBANHANG/account/login';
    } else {
        alert('Lỗi: ' + xhr.responseText);
    }
}
            });
        });

        // Sửa sản phẩm
        function editProduct(id) {
    $.ajax({
        url: `/QUANLYBANHANG/api/product/${id}`,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token // Thêm header
        },
        success: function(product) {
            $('#name').val(product.name);
            $('#description').val(product.description);
            $('#price').val(product.price);
            $('#category_id').val(product.category_id);
            $('#quantity').val(product.quantity);
            $('#current-image').val(product.image);
            $('#image').val('');
            $('#submit-btn').hide();
            $('#update-btn').show();
            currentProductId = id;
        },
        error: function(xhr) {
    if (xhr.status === 401) {
        alert('Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại!');
        window.location.href = '/QUANLYBANHANG/account/login';
    } else {
        alert('Lỗi: ' + xhr.responseText);
    }
}
    });
}

        // Xóa sản phẩm
        function deleteProduct(id) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        $.ajax({
            url: `/QUANLYBANHANG/api/product/${id}`,
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token // Thêm header
            },
            success: function(response) {
                if (response.message === 'Product deleted successfully') {
                    alert('Xóa sản phẩm thành công');
                    location.reload();
                } else {
                    alert('Xóa thất bại: ' + JSON.stringify(response.errors));
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    alert('Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại!');
                    window.location.href = '/QUANLYBANHANG/account/login';
                } else {
                    alert('Lỗi: ' + xhr.responseText);
                }
            }
        });
    }
}

        // Tìm kiếm sản phẩm
        $('#search-btn').on('click', function() {
            const searchTerm = $('#search-input').val();
            window.location.href = `/QUANLYBANHANG/api/product?search=${encodeURIComponent(searchTerm)}`;
        });

        $('#search-input').on('keypress', function(e) {
            if (e.which === 13) {
                const searchTerm = $(this).val();
                window.location.href = `/QUANLYBANHANG/api/product?search=${encodeURIComponent(searchTerm)}`;
            }
        });

        // Bind sự kiện cho nút Cập nhật
        $('#update-btn').on('click', function() {
            $('#product-form').submit();
        });
    </script>
</body>
</html>