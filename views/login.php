<?php
session_start();
include('../api/public.php');

?>
<!doctype html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - SPK SAW</title>
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">

</head>
<style>
    .bg-login-left {
        background-image: url('../assets/img/login-bg.jpg');
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .bg-login-left::before {
        content: "";
        position: absolute;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1;
    }

    .login-left-content {
        position: relative;
        z-index: 2;
        color: white;
    }

    .login-left-content h1,
    .login-left-content h2,
    .login-left-content p {
        margin-bottom: 1rem;
    }

    .login-left-footer {
        background-color: rgba(0, 0, 0, 0.3);
        padding: 1.5rem;
        border-radius: 0.5rem;
    }
</style>

<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row no-gutters min-vh-100">
            <!-- Left Branding Section with Image -->
            <div class="col-lg-7 col-md-6 d-none d-md-flex bg-login-left">
                <div class="d-flex flex-column justify-content-between p-5 w-100 login-left-content">
                    <div>
                        <p class="mb-3 font-weight-light">Sistem Pendukung Keputusan</p>
                        <h1 class="display-4 font-weight-bold mb-4">SIMPLE ADDITIVE<br>WEIGHTING</h1>
                        <h2 class="h1 font-weight-bold mb-3">PEMILIHAN<br>SUPPLIER<br>PAKAN<br>TERNAK</h2>
                        <p class="font-italic">Ver 1.0</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="login-left-footer">
                            <h3 class="h2 font-weight-bold mb-0">TOKO PAKAN<br>SELO</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Login Form Section -->
            <div class="col-lg-5 col-md-6 col-12 d-flex align-items-center justify-content-center">
                <div class="w-100 px-4" style="max-width: 400px;">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <p class="text-muted mb-2">Selamat datang !</p>
                                <h2 class="h3 font-weight-bold text-dark mb-2">Log in Admin</h2>
                                <p class="text-muted small">Silakan masukkan Nama Pengguna dan password Anda!</p>
                                <?php if (!empty($_SESSION['error_message'])) : ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?= $_SESSION['error_message'];
                                        unset($_SESSION['error_message']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <form action="../api/auth.php" method="POST">
                                <div class="form-group">
                                    <label for="name" class="font-weight-medium text-dark">Nama Pengguna</label>
                                    <input type="name" name="name" class="form-control form-control-lg" id="name" placeholder="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="font-weight-medium text-dark">Password</label>
                                    <input type="password" name="password" class="form-control form-control-lg" id="password" placeholder="Password" maxlength="32" required>
                                </div>
                                <button type="submit" class="btn btn-success btn-lg btn-block text-white font-weight-medium">Log in</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="../assets/js/sb-admin-2.min.js"></script>
</html>