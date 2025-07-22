<?php
require_once '../api/auth-check.php';
include('../api/public.php');
require_once __DIR__ . '/../config/database.php';
$pageTitle = 'dashboard';

$pdo = Database::getConnection();

// validasi jika bobot kurang dari 1
$sum_weight = $pdo->query("SELECT SUM(weight) FROM criterias")->fetch(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard - SPK SAW</title>
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
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

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800" id="pageTitle">Dashboard</h1>

                    <div class="container py-4">
                        <div class="row">

                            <?php if ($sum_weight != 1): ?>
                                <!-- Warning Card -->
                                <div class="col-12 mb-4">
                                    <div class="card border-danger shadow-sm">
                                        <div class="card-header bg-danger text-white font-weight-bold">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>Peringatan Bobot Kriteria
                                        </div>
                                        <div class="card-body bg-light d-flex align-items-start">
                                            <div class="display-4 text-danger mr-3">⚠️</div>
                                            <p class="mb-0 text-dark">
                                                Total bobot kriteria saat ini <strong>belum berjumlah 1</strong>. Hal ini dapat menyebabkan hasil peringkat menjadi tidak akurat.
                                                Silakan sesuaikan kembali nilai bobot di menu <strong class="text-danger">"Data Kriteria"</strong> agar perhitungan sistem pendukung keputusan dapat berjalan dengan optimal.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Welcome Card -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="bg-primary text-white rounded p-3 mr-3" style="font-size: 2rem;">
                                            👋
                                        </div>
                                        <div>
                                            <h5 class="card-title font-weight-bold">Selamat Datang, Admin!</h5>
                                            <p class="card-text text-muted">Semoga harimu menyenangkan dan produktif ✨</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Top 5 Suppliers Card -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title font-weight-bold mb-3"><span class="mr-2">🏆</span>Top 5 Supplier Terbaik Saat Ini</h5>
                                        <ul class="list-group list-group-flush" id="topSuppliersList">
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Decision Support System Card -->
                            <div class="col-12 mb-4">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title font-weight-bold mb-3"><span class="mr-2">📊</span>Sistem Pendukung Keputusan</h5>
                                        <p class="card-text text-muted">
                                            Sistem ini menggunakan metode <strong class="text-primary">Simple Additive Weighting (SAW)</strong> untuk menentukan Supplier Pakan Ternak terbaik berdasarkan 5 kriteria:
                                        </p>
                                        <div class="row text-center mt-4" id="kriteriaWeights">

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Usage Tips Card -->
                            <div class="col-12 mb-4">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title font-weight-bold mb-3"><span class="mr-2">💡</span>Tips Penggunaan</h5>
                                        <div class="d-flex align-items-center bg-warning rounded p-3">
                                            <div class="display-4 mr-3">🔍</div>
                                            <p class="mb-0 text-dark">
                                                Gunakan menu <strong class="text-primary">"Data Alternatif"</strong> untuk mengisi data Supplier, lalu lihat hasil penilaian di menu <strong class="text-primary">"Data Perhitungan dan Data Hasil"</strong>. Pastikan data lengkap agar hasil akurat.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
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
<script>
    $(document).ready(function() {
        loadTop5Suppliers();
        loadKriteriaWeights();
    });

    function loadTop5Suppliers() {
        $.ajax({
            url: '../api/get-results.php',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                const $list = $('#topSuppliersList');
                $list.empty();

                if (res.status === 'success' && res.data.length > 0) {
                    for (let i = 0; i < 5; i++) {
                        const item = res.data[i];
                        const content = item ?
                            `<span class="text-primary font-weight-bold mr-2">${i + 1}.</span> ${item.name} (${item.value})` :
                            `<span class="text-primary font-weight-bold mr-2">${i + 1}.</span> -`;
                        $list.append(`<li class="list-group-item d-flex align-items-center">${content}</li>`);
                    }
                } else {
                    for (let i = 0; i < 5; i++) {
                        $list.append(`<li class="list-group-item d-flex align-items-center">
                        <span class="text-primary font-weight-bold mr-2">${i + 1}.</span> -
                    </li>`);
                    }
                }
            },
            error: function(err) {
                alert('Gagal memuat Top 5 supplier.');
            }
        });
    }

    function loadKriteriaWeights() {
        $.ajax({
            url: '../api/get-criterias.php',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                const $container = $('#kriteriaWeights');
                $container.empty();

                if (res.status === 'success') {
                    const data = res.data;

                    if (data) {
                       data.forEach((val) => {
                            $container.append(`
                                <div class="col-sm-6 col-md-3 mb-3">
                                    <div class="bg-light border rounded py-3 px-2">
                                        <div class="font-weight-bold text-primary">${val.name}</div>
                                        <div class="h5 mt-2">${val.weight}</div>
                                    </div>
                                </div>
                            `);
                        });
                    }
                }
            },
            error: function() {
                alert('Gagal memuat data bobot kriteria.');
            }
        });
    }
</script>

</html>