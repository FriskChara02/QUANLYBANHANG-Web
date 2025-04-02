<?php include 'app/views/shares/header.php'; ?>

<section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-dark text-white" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">
                        <form action="/QUANLYBANHANG/account/login" method="post"> <!-- Sửa từ checklogin thành login -->                           
                             <div class="mb-md-5 mt-md-4 pb-5">
                                <!-- Tiêu đề đăng nhập -->
                                <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
                                <p class="text-white-50 mb-5">Please enter your login and password!</p>

                                <!-- Hiển thị thông báo lỗi nếu có -->
                                <?php if (isset($_SESSION['error_message'])): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $_SESSION['error_message']; ?>
                                    </div>
                                    <?php unset($_SESSION['error_message']); ?>
                                <?php endif; ?>

                                <!-- Form nhập thông tin đăng nhập -->
                                <div class="form-outline form-white mb-4">
                                    <input type="text" name="username" class="form-control form-control-lg" required />
                                    <label class="form-label" for="typeEmailX">UserName</label>
                                </div>

                                <div class="form-outline form-white mb-4">
                                    <input type="password" name="password" class="form-control form-control-lg" required />
                                    <label class="form-label" for="typePasswordX">Password</label>
                                </div>

                                <p class="small mb-5 pb-lg-2"><a class="text-white-50" href="#!">Forgot password?</a></p>

                                <button class="btn btn-outline-light btn-lg px-5" type="submit">Login</button>

                                <!-- Các nút mạng xã hội -->
                                <div class="d-flex justify-content-center text-center mt-4 pt-1">
                                    <a href="#!" class="text-white"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#!" class="text-white"><i class="fab fa-twitter fa-lg mx-4 px-2"></i></a>
                                    <a href="#!" class="text-white"><i class="fab fa-google"></i></a>
                                </div>
                            </div>

                            <!-- Liên kết đến trang đăng ký -->
                            <div>
                                <p class="mb-0">Don't have an account? <a href="/QUANLYBANHANG/account/register" class="text-white-50 fw-bold">Sign Up</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'app/views/shares/footer.php'; ?>
