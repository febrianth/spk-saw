<?php
require_once '../api/auth-check.php';
include('../api/public.php');
$pageTitle = 'results';

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Hasil - SPK SAW</title>
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<style>
    @media print {
        body * {
            visibility: hidden !important;
        }

        #rangkings,
        #rangkings * {
            visibility: visible !important;
        }

        #rangkings thead {
            background-color: white !important;
            color: black !important;
        }

        #rangkings th,
        #rangkings td {
            border: 1px solid black !important;
            padding: 8px !important;
            top: 10;
        }

        #rangkings thead {
            background-color: #ffffff !important;
            color: #000000 !important;
        }

        .sidebar,
        .navbar,
        .topbar,
        .footer {
            display: none !important;
        }
    }
</style>

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
                    <h1 class="h3 mb-4 text-gray-800" id="pageTitle">Hasil</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Hasil</h6>
                        </div>
                        <div class="card-body">
                            <button type="button" class="btn btn-primary mb-4" onclick="window.print()">
                                <i class="fas fa-print"></i> Cetak
                            </button>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="rangkings" width="100%" cellspacing="0">
                                    <thead class="bg-success text-light">
                                        <tr>
                                            <th class="text-center">Rangking</th>
                                            <th class="text-center">Kode</th>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
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
    $(document).ready(() => {
        getResults();
    });

    function showLoading() {
        $('#loading-overlay').removeClass('d-none').addClass('d-flex');
    }

    function hideLoading() {
        $('#loading-overlay').removeClass('d-flex').addClass('d-none');
    }

    function getResults() {
        showLoading();

        $.ajax({
            url: '../api/get-results.php',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                const $tbody = $('#rangkings').find('tbody');
                $tbody.empty();

                if (res.status === 'success' && res.data.length > 0) {
                    res.data.forEach(item => {
                        $tbody.append(`
                            <tr>
                                <td class="text-center text-dark">${item.rank}</td>
                                <td class="text-center text-dark">${item.code}</td>
                                <td class="text-dark">${item.name}</td>
                                <td class="text-center text-dark">${item.value}</td>
                            </tr>
                        `);
                    });
                } else {
                    $tbody.append(`
                        <tr>
                            <td colspan="4" class="text-center text-danger">Tidak ada data tersedia</td>
                        </tr>
                    `);
                }
            },
            error: function(err) {
                alert('Terjadi kesalahan saat mengambil data hasil.');
            },
            complete: function() {
                hideLoading();
            }
        });
    }
</script>

</html>