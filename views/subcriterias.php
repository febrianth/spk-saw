<?php
require_once '../api/auth-check.php';
include('../api/public.php');
require_once __DIR__ . '/../config/database.php';
$pageTitle = 'subcriterias';
$option_criterias = [];
try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT id, name FROM criterias WHERE input_type = 'option' ORDER BY id ASC");
    $stmt->execute();
    $option_criterias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal, : " . $e->getMessage(),
        "data" => null
    ]);
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

    <title>Sub Kriteria - SPK SAW</title>
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
                    <h1 class="h3 mb-4 text-gray-800" id="pageTitle">Sub Kriteria</h1>

                    <?php if (empty($option_criterias)): ?>
                        <div class="alert alert-info">
                            Tidak ada kriteria yang diatur untuk menggunakan sub-kriteria (jenis input: option).
                            <br>
                            Anda dapat mengaturnya di halaman <a href="criterias.php" class="alert-link">Data Kriteria</a>.
                        </div>
                    <?php else: ?>
                        <!-- Loop melalui setiap kriteria 'option' dan buat card-nya masing-masing -->
                        <?php foreach ($option_criterias as $criteria): ?>
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        Daftar Sub Kriteria untuk: <?= htmlspecialchars($criteria['name']) ?>
                                    </h6>
                                    <!-- Tombol tambah data mengirimkan ID kriteria induknya -->
                                    <button type="button" class="btn btn-success btn-sm" onclick="showModal(<?= $criteria['id'] ?>, '<?= htmlspecialchars($criteria['name']) ?>')">
                                        <i class="fas fa-plus fa-sm"></i> Tambah Data
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <!-- Setiap tabel memiliki ID unik berdasarkan ID kriteria -->
                                        <table class="table table-bordered subcriteria-table" id="table-criteria-<?= $criteria['id'] ?>" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 5%;">#</th>
                                                    <th class="text-left">Nama Sub Kriteria</th>
                                                    <th class="text-center" style="width: 15%;">Nilai</th>
                                                    <th class="text-center" style="width: 15%;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Isi tabel akan dimuat oleh JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include('templates/footer.php'); ?>

        </div>
        <!-- End of Content Wrapper -->

        <!-- Modal untuk Tambah/Edit Data Sub Kriteria -->
        <div class="modal fade" id="modalSubcriteria" tabindex="-1" role="dialog" aria-labelledby="modalSubcriteriaLabel">
            <div class="modal-dialog" role="document">
                <form id="formSubcriteria" class="modal-content" onsubmit="handleSubmit(event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSubcriteriaLabel">Tambah Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="subcriteria_id">
                        <input type="hidden" name="criteria_id" id="criteria_id">

                        <div class="form-group">
                            <label>Untuk Kriteria</label>
                            <input type="text" class="form-control" id="criteria_name_display" readonly>
                        </div>
                        <div class="form-group">
                            <label for="subcriteria_name">Nama Sub Kriteria</label>
                            <input type="text" class="form-control" name="name" id="subcriteria_name" required>
                        </div>
                        <div class="form-group">
                            <label for="subcriteria_value">Nilai</label>
                            <input type="number" class="form-control" name="value" id="subcriteria_value" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
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
        loadAllSubCriterias();
    });

    function loadAllSubCriterias() {
        showLoading();
        $.ajax({
            url: '../api/get-grouped-subcriterias.php',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success' && res.data) {
                    res.data.forEach(criteriaGroup => {
                        const criteriaId = criteriaGroup.id;
                        const subCriterias = criteriaGroup.sub_criterias;
                        const $tbody = $(`#table-criteria-${criteriaId} tbody`);
                        $tbody.empty();

                        if (subCriterias.length > 0) {
                            subCriterias.forEach((sub, index) => {
                                $tbody.append(`
                                <tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td class="text-left">${sub.name}</td>
                                    <td class="text-center">${sub.value}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning" onclick="handleEdit(${sub.id})" title="Edit"><i class="fas fa-fw fa-pen"></i></button>
                                        <button class="btn btn-sm btn-danger" onclick="handleDelete(${sub.id})" title="Hapus"><i class="fas fa-fw fa-trash"></i></button>
                                    </td>
                                </tr>
                            `);
                            });
                        } else {
                            $tbody.html('<tr><td colspan="4" class="text-center">Belum ada data sub-kriteria.</td></tr>');
                        }
                    });
                }
            },
            error: function() {
                alert('Gagal memuat data sub-kriteria.');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function showModal(criteriaId, criteriaName) {
        $('#formSubcriteria')[0].reset();
        $('#modalSubcriteriaLabel').text('Tambah Sub Kriteria');
        $('#subcriteria_id').val('');
        $('#criteria_id').val(criteriaId);
        $('#criteria_name_display').val(criteriaName);
        $('#modalSubcriteria').modal('show');
    }

    function handleEdit(id) {
        showLoading();
        $.ajax({
            url: '../api/get-subcriteria-by-id.php',
            method: 'GET',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    const data = res.data;
                    $('#modalSubcriteriaLabel').text('Edit Sub Kriteria');
                    $('#subcriteria_id').val(data.id);
                    $('#criteria_id').val(data.criteria_id);
                    $('#criteria_name_display').val(data.criteria_name);
                    $('#subcriteria_name').val(data.name);
                    $('#subcriteria_value').val(data.value);
                    $('#modalSubcriteria').modal('show');
                } else {
                    alert('Gagal mengambil data: ' + res.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengambil data.');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function handleSubmit(event) {
        event.preventDefault();
        const formData = $('#formSubcriteria').serialize();
        showLoading();
        $.ajax({
            url: '../api/upsert-sub-criteria.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#modalSubcriteria').modal('hide');
                    alert(res.message);
                    loadAllSubCriterias();
                } else {
                    alert('Gagal menyimpan: ' + res.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan server saat menyimpan data.');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function handleDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            showLoading();
            $.ajax({
                url: '../api/delete-sub-criteria-by-id.php',
                method: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        alert(res.message);
                        loadAllSubCriterias();
                    } else {
                        alert('Gagal menghapus: ' + res.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus data.');
                },
                complete: function() {
                    hideLoading();
                }
            });
        }
    }

    function showLoading() {
        $('#loading-overlay').removeClass('d-none').addClass('d-flex');
    }

    function hideLoading() {
        $('#loading-overlay').removeClass('d-flex').addClass('d-none');
    }
</script>

</html>