<?php
require_once '../api/auth-check.php';
include('../api/public.php');
require_once __DIR__ . '/../config/database.php';
$pageTitle = 'calculations';
$criterias = [];
try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT code, name FROM criterias ORDER BY id ASC");
    $stmt->execute();
    $criterias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal memuat data kriteria: " . $e->getMessage());
}
$criteriaCodes = array_column($criterias, 'code');
$colspan = count($criteriaCodes) + 1;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Perhitungan - SPK SAW</title>
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
                    <h1 class="h3 mb-4 text-gray-800" id="pageTitle">Perhitungan</h1>
                    <!-- Matrik Keputusan (X) -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Matrik Keputusan (X)</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="matrix-x" width="100%" cellspacing="0">
                                    <thead class="bg-primary text-light">
                                        <tr>
                                            <th class="text-center">Nama Alternatif</th>
                                            <?php foreach ($criterias as $c) echo "<th class='text-center'>" . htmlspecialchars($c['code']) . "</th>"; ?>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Nilai Bobot (W) -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Nilai Bobot (W)</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="criterias" width="100%" cellspacing="0">
                                    <thead class="bg-primary text-light">
                                        <tr>
                                            <?php foreach ($criterias as $c) echo "<th class='text-center'>" . htmlspecialchars($c['code']) . "</th>"; ?>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Matrik Normalisasi (R) -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Matrik Normalisasi (R)</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="matrix-r" width="100%" cellspacing="0">
                                    <thead class="bg-primary text-light">
                                        <tr>
                                            <th class="text-center">Nama Alternatif</th>
                                            <?php foreach ($criterias as $c) echo "<th class='text-center'>" . htmlspecialchars($c['code']) . "</th>"; ?>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Perhitungan Vektor (V) -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Perhitungan Vektor (V)</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="matrix-v" width="100%" cellspacing="0">
                                    <thead class="bg-primary text-light">
                                        <tr>
                                            <th class="text-center">Nama Alternatif</th>
                                            <?php foreach ($criterias as $c) echo "<th class='text-center'>" . htmlspecialchars($c['code']) . "</th>"; ?>
                                            <th class="text-center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
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
<script>
    $(document).ready(() => {
        loadAllCalculations();
    });

    function showLoading() {
        $('#loading-overlay').removeClass('d-none').addClass('d-flex');
    }

    function hideLoading() {
        $('#loading-overlay').removeClass('d-flex').addClass('d-none');
    }

    function loadAllCalculations() {
        showLoading();

        $.ajax({
            url: '../api/get-all-calculations.php',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status !== 'success' || !res.data) {
                    alert('Gagal memuat data perhitungan: ' + res.message);
                    return;
                }

                const {
                    criterias,
                    matrix_x,
                    matrix_r,
                    matrix_v
                } = res.data;
                const criteriaCodes = criterias.map(c => c.code);
                const colspan = criteriaCodes.length + 1;

                // 1. Populasi Tabel Bobot (W)
                const $tbodyW = $('#criterias tbody').empty();
                if (criterias.length > 0) {
                    let rowW = '<tr>';
                    criterias.forEach(c => {
                        rowW += `<td class="text-center">${c.weight}</td>`;
                    });
                    rowW += '</tr>';
                    $tbodyW.html(rowW);
                } else {
                    $tbodyW.html(`<tr><td colspan="${criteriaCodes.length}" class="text-center text-danger">Tidak ada data</td></tr>`);
                }

                // 2. Populasi Tabel Matriks X
                populateMatrixTable('#matrix-x', matrix_x, criteriaCodes, colspan);

                // 3. Populasi Tabel Matriks R
                populateMatrixTable('#matrix-r', matrix_r, criteriaCodes, colspan);

                // 4. Populasi Tabel Matriks V (dengan kolom total)
                populateMatrixTable('#matrix-v', matrix_v, criteriaCodes, colspan, true);

            },
            error: function(err) {
                alert('Terjadi kesalahan saat mengambil data perhitungan.');
                console.error(err);
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function populateMatrixTable(tableId, data, criteriaCodes, colspan, includeTotal = false) {
        const $tbody = $(tableId).find('tbody').empty();

        if (data && data.length > 0) {
            data.forEach(row => {
                let htmlRow = `<tr><td class="text-left">${row.name}</td>`;
                criteriaCodes.forEach(code => {
                    let value = row[code];
                    if (value) {
                        if (typeof value === 'number') {
                            // Jika BUKAN matriks X, gunakan toFixed(4).
                            // Jika matriks X, biarkan nilai apa adanya (tidak diformat).
                            if (tableId !== '#matrix-x') {
                                value = value.toFixed(4);
                            }
                        }
                    } else {
                        value = '-';
                    }
                    htmlRow += `<td class="text-right">${value}</td>`;
                });
                if (includeTotal) {
                    htmlRow += `<td class="text-right font-weight-bold">${row.total.toFixed(4)}</td>`;
                }
                htmlRow += '</tr>';
                $tbody.append(htmlRow);
            });
        } else {
            const finalColspan = includeTotal ? colspan + 1 : colspan;
            $tbody.html(`<tr><td colspan="${finalColspan}" class="text-center text-danger">Tidak ada data tersedia</td></tr>`);
        }
    }
</script>

</html>