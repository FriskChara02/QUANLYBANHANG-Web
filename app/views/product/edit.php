<?php
$editCSS = '/QUANLYBANHANG/public/css/edit.css';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
    <link type="text/css" rel="stylesheet" href="<?php echo $editCSS; ?>">
</head>
<body>
    <div class="navbar">
        <a href="/QUANLYBANHANG/Product/pagemain">Trang chủ</a>
        <a href="/QUANLYBANHANG/Product/list">Danh sách sản phẩm</a>
        <a href="/QUANLYBANHANG/Product/add">Thêm sản phẩm mới</a>
    </div>

    <h1>Sửa sản phẩm</h1>

    <div class="container">
    <form id="edit-product-form" onsubmit="return updateProduct(event);" enctype="multipart/form-data">
                <input type="hidden" id="id" name="id">
            <div class="form-group">
                <label for="name">Tên sản phẩm:</label>
                <input type="text" id="name" name="name" class="form-control" required minlength="10" maxlength="100">
            </div>
            <div class="form-group">
                <label for="description">Mô tả:</label>
                <textarea id="description" name="description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Giá:</label>
                <input type="number" id="price" name="price" class="form-control" required min="0.01" step="0.01">
            </div>
            <div class="form-group">
    <label for="category_id">Danh mục:</label>
    <select id="category_id" name="category_id" class="form-control" required>
        <option value="">Chọn danh mục</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
        <?php endforeach; ?>
    </select>
</div>
            <div class="form-group">
        <label for="quantity">Số lượng:</label>
        <input type="number" id="quantity" name="quantity" class="form-control" required min="1">
    </div>
    <div class="form-group">
        <label for="image">Hình ảnh:</label>
        <input type="file" id="image" name="image" class="form-control" accept="image/*">
        <input type="hidden" id="current_image" name="current_image">
    </div>
    <button type="submit">Lưu thay đổi</button>
</form>
        <a href="/QUANLYBANHANG/Product/list">Quay lại danh sách sản phẩm</a>
    </div>

    <footer>
        <p>© 2025 QUANLYBANHANG. All rights reserved.</p>
    </footer>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParts = window.location.pathname.split('/');
        const productId = urlParts[urlParts.length - 1];

        fetch(`/QUANLYBANHANG/api/product/${productId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('id').value = data.id;
                document.getElementById('name').value = data.name;
                document.getElementById('description').value = data.description;
                document.getElementById('price').value = data.price;
                document.getElementById('quantity').value = data.quantity;
                document.getElementById('category_id').value = data.category_id; // Chọn category hiện tại
                document.getElementById('current_image').value = data.image;
            })
            .catch(error => console.error('Lỗi tải dữ liệu sản phẩm:', error));
    });

    function updateProduct(event) {
        event.preventDefault();
        let name = document.getElementById('name').value.trim();
        let price = document.getElementById('price').value;
        let quantity = document.getElementById('quantity').value;
        let category_id = document.getElementById('category_id').value;
        let errors = [];

        if (name.length < 10 || name.length > 100) {
            errors.push('Tên sản phẩm phải có từ 10 đến 100 ký tự.');
        }
        if (price <= 0 || isNaN(price)) {
            errors.push('Giá phải là một số dương lớn hơn 0.');
        }
        if (quantity <= 0 || isNaN(quantity)) {
            errors.push('Số lượng phải là số dương.');
        }
        if (!category_id) {
            errors.push('Vui lòng chọn danh mục.');
        }
        if (errors.length > 0) {
            alert(errors.join('\n'));
            return false;
        }

        const formData = new FormData(document.getElementById('edit-product-form'));

        fetch(`/QUANLYBANHANG/Product/edit/${formData.get('id')}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'Product updated successfully') {
                alert('Cập nhật sản phẩm thành công');
                window.location.href = '/QUANLYBANHANG/Product/list';
            } else {
                alert('Cập nhật sản phẩm thất bại: ' + (data.errors ? JSON.stringify(data.errors) : data.message));
            }
        })
        .catch(error => {
            alert('Lỗi: ' + error.message);
        });
        return false;
    }
</script>
</body>
</html>