<?php
include 'app/views/shares/header.php';
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>

<div class="banner-container" style="text-align: center; margin-bottom: 30px;">
    <img src="/QUANLYBANHANG/public/images/IphoneIpadMacbook.jpg" alt="Banner" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
</div>

<h1 class="text-center my-4">Thêm sản phẩm mới</h1>

<div class="container">
    <form id="add-product-form" onsubmit="return addProduct(event);" enctype="multipart/form-data">
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
            <input type="number" id="price" name="price" class="form-control" step="0.01" required min="0.01">
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
            <input type="file" id="image" name="image" class="form-control" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Thêm sản phẩm</button>
    </form>
    <a href="/QUANLYBANHANG/Product/list" class="btn btn-secondary mt-3 btn-block">Quay lại danh sách sản phẩm</a>
</div>

<?php include 'app/views/shares/footer.php'; ?>

<script>
    function addProduct(event) {
        event.preventDefault();
        let name = document.getElementById('name').value.trim();
        let price = document.getElementById('price').value;
        let quantity = document.getElementById('quantity').value;
        let image = document.getElementById('image').files[0];
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
        if (!image) {
            errors.push('Hình ảnh không được để trống.');
        }
        if (errors.length > 0) {
            alert(errors.join('\n'));
            return false;
        }

        const formData = new FormData(document.getElementById('add-product-form'));

        fetch('/QUANLYBANHANG/Product/add', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status); // Debug trạng thái
            if (!response.ok) {
                return response.text().then(text => { throw new Error('Lỗi server: ' + response.status + ' - ' + text); });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Debug dữ liệu trả về
            if (data.message === 'Product created successfully') {
                alert('Thêm sản phẩm thành công');
                window.location.href = '/QUANLYBANHANG/Product/list';
            } else {
                alert('Thêm sản phẩm thất bại: ' + (data.errors ? JSON.stringify(data.errors) : data.message));
            }
        })
        .catch(error => {
            console.error('Lỗi fetch:', error); // Debug lỗi fetch
            alert('Lỗi: ' + error.message);
        });
        return false;
    }
</script>