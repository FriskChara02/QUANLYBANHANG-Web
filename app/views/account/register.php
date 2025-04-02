<?php include 'app/views/shares/header.php'; ?>

<section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-dark text-white" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">
                        <h2 class="fw-bold mb-2 text-uppercase">Sign Up</h2>
                        <p class="text-white-50 mb-5">Create an account to get started</p>

                        <!-- Hiển thị thông báo lỗi nếu có -->
                        <?php if (isset($errors) && count($errors) > 0): ?>
                            <ul>
                                <?php foreach ($errors as $err): ?>
                                    <li class="text-danger"><?php echo $err; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <!-- Form đăng ký -->
                        <form class="user" action="/QUANLYBANHANG/account/save" method="post">
                            <div class="form-group row">
                                <div class="col-sm-12 mb-3 mb-sm-0">
                                    <input type="text" class="form-control form-control-user" id="username" name="username" placeholder="Username" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Password" required>
                                </div>
                                <div class="col-sm-6">
                                    <input type="password" class="form-control form-control-user" id="confirmpassword" name="confirmpassword" placeholder="Confirm Password" required>
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button class="btn btn-primary btn-icon-split p-3" type="submit">
                                    <span class="icon"><i class="fas fa-user-plus"></i></span> Register
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'app/views/shares/footer.php'; ?>
