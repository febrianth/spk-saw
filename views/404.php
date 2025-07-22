<?php
require_once '../api/auth-check.php';
include('../api/public.php');
require_once __DIR__ . '/../config/database.php';
$pageTitle = 'alternatives';
$criterias = [];
try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT code, name FROM criterias ORDER BY id ASC");
    $stmt->execute();
    $criterias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal memuat data kriteria: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>404 - SPK SAW</title>
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <?php include('templates/sideBar.php'); ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php include('templates/header.php'); ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- 404 Error Text -->
                    <div class="text-center">
                        <div class="error mx-auto" data-text="404">404</div>
                        <p class="lead text-gray-800 mb-5">Page Not Found</p>
                        <p class="text-gray-500 mb-0">It looks like you found a glitch in the matrix...</p>
                        <a href="dashboard.php">&larr; Back to Dashboard</a>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include('templates/footer.php'); ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <?php include('templates/logoutModal.php'); ?>

    <div id="loading-overlay"
        class="position-fixed w-100 h-100 d-none justify-content-center align-items-center"
        style="top: 0; left: 0; background: rgba(255,255,255,0.7); z-index: 1050;">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

</body>
<script src="../assets/js/sb-admin-2.min.js"></script>
<script src="../assets/js/public.js"></script>

</html>