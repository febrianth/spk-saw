<?php
require_once '../api/auth-check.php';
include('../api/public.php');
require_once __DIR__ . '/../config/database.php';
$pageTitle = 'criterias';

$pdo = Database::getConnection();

$sum_weight = $pdo->query("SELECT SUM(weight) FROM criterias")->fetch(PDO::FETCH_COLUMN);
$disabled = '';
$info_text = '';
if ($sum_weight == 1) {
    $disabled = 'disabled';
    $info_text = '<br><small class="text-muted">Tidak bisa tambah, Bobot sudah mencapai batas maksimal (Max = 1)</small>';
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

    <title>Kriteria - SPK SAW</title>
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
                    <h1 class="h3 mb-4 text-gray-800" id="pageTitle">Kriteria</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Kriteria</h6>
                        </div>
                        <div class="card-body">
                            <button type="button" class="btn btn-success" onclick="showModal()" <?= $disabled; ?>>
                                <i class="fas fa-plus"></i> Tambah Data
                            </button>
                            <?= $info_text; ?>
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Kode</th>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Atribut</th>
                                            <th class="text-center">Tipe</th>
                                            <th class="text-center">Bobot</th>
                                            <th class="text-center">Aksi</th>
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

        <div class="modal fade" id="modalAddData" tabindex="-1" role="dialog" aria-labelledby="modalAddDataLabel">
            <div class="modal-dialog modal-lg" role="document">
                <form id="formData" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAddDataLabel">Tambah Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" value="">

                        <div class="form-group mb-3">
                            <label for="code">Kode</label>
                            <input type="text" class="form-control" maxlength="10" name="code" id="code">
                        </div>

                        <div class="form-group mb-3">
                            <label for="name">Nama</label>
                            <input type="text" class="form-control" maxlength="50" name="name" id="name">
                        </div>

                        <div class="form-group mb-3">
                            <label for="attribute">Atribut</label>
                            <select class="form-control" name="attribute" id="attribute">
                                <option value="">--Pilih Atribut--</option>
                                <option value="benefit">Benefit</option>
                                <option value="cost">Cost</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="input_type">Tipe</label>
                            <select class="form-control" name="input_type" id="input_type">
                                <option value="">--Pilih Tipe--</option>
                                <option value="number">Angka Bulat</option>
                                <option value="option">Pilihan</option>
                                <option value="decimal">Desimal</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="weight">Bobot</label>
                            <input type="number" class="form-control" name="weight" id="weight" min="0.1" max="1.0" step="0.1" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" onclick="handleSubmit()">Simpan</button>
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
    $(document).ready(() => {
        getAllData()
    });

    function showLoading() {
        $('#loading-overlay').removeClass('d-none').addClass('d-flex');
    }

    function hideLoading() {
        $('#loading-overlay').removeClass('d-flex').addClass('d-none');
    }

    function getAllData() {
        showLoading();

        $.ajax({
            url: '../api/get-criterias.php',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    const $table = $('#dataTable');
                    const $tbody = $table.find('tbody');

                    // Hancurkan jika sudah ada DataTable
                    if ($.fn.DataTable.isDataTable($table)) {
                        $table.DataTable().clear().destroy();
                    }

                    $tbody.empty();

                    res.data.forEach((alt, index) => {
                        var tipe = '-';
                        if (alt.input_type == 'option') {
                            tipe = 'Pilihan';
                        } else if (alt.input_type == 'number') {
                            tipe = 'Angka Bulat';
                        } else {
                            tipe = 'Desimal';
                        }
                        $tbody.append(`
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td class="text-center">${alt.code}</td>
                            <td class="text-left">${alt.name}</td>
                            <td class="text-center">${alt.attribute}</td>
                            <td class="text-center">${tipe}</td>
                            <td class="text-right">${alt.weight}</td>
                            <td class="text-center">
                               <button class="btn btn-sm btn-warning" onclick="handleEdit(${alt.id})" title="Edit"><i class="fas fa-fw fa-pen"></i></button>
                               <button class="btn btn-sm btn-danger" onclick="handleDelete(${alt.id})" title="Hapus"><i class="fas fa-fw fa-trash"></i></button>
                            </td>
                        </tr>
                    `);
                    });

                    // Reinit
                    $table.DataTable();
                } else {
                    alert('Gagal mengambil data: ' + res.message);
                }
            },
            error: function(err) {
                alert('Terjadi kesalahan saat mengambil data kriteria.');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function handleEdit(id) {
        showLoading();

        $.ajax({
            url: '../api/get-criteria-by-id.php',
            method: 'GET',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                hideLoading();

                if (res.status === 'success') {
                    const data = res.data;

                    // Isi form
                    $('#id').val(data.id);
                    $('#code').val(data.code);
                    $('#name').val(data.name);
                    $('#attribute').val(data.attribute);
                    $('#weight').val(data.weight);
                    $('#input_type').val(data.input_type);

                    $('#modalAddDataLabel').text('Edit Data');
                    $('#modalAddData').modal('show');
                } else {
                    alert('Gagal: ' + res.message);
                }
            },
            error: function() {
                hideLoading();
                alert('Terjadi kesalahan saat mengambil data kriteria.');
            }
        });
    }

    function showModal() {
        $('#id').val('');
        $('#code').val('');
        $('#name').val('');
        $('#attribute').val('');
        $('#input_type').val('');
        $('#weight').val('');

        $('#modalAddDataLabel').text('Tambah Data');
        $('#modalAddData').modal('show');
    }

    function handleDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            showLoading();

            $.ajax({
                url: '../api/delete-criteria-by-id.php',
                method: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    hideLoading();

                    if (res.status === 'success') {
                        alert(res.message);
                        location.reload(); // Perbarui tabel
                    } else {
                        alert('Gagal: ' + res.message);
                    }
                },
                error: function() {
                    hideLoading();
                    alert('Terjadi kesalahan saat menghapus data.');
                }
            });
        }
    }

    function handleSubmit() {
        const $form = $('#formData');

        event.preventDefault();

        showLoading();

        $.ajax({
            url: '../api/upsert-criteria.php',
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                hideLoading();

                if (response.status === 'success') {
                    alert(response.message);
                    $('#modalAddData').modal('hide');
                    location.reload(); // reload data
                } else {
                    alert('Gagal: ' + response.message);
                }
            },
            error: function(xhr) {
                hideLoading();
                let message = 'Terjadi kesalahan saat menyimpan data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                alert(message);
            },
            complete: function() {
                hideLoading();
                $submitBtn.prop('disabled', false).html(originalBtnHTML);
            }
        });
    }
</script>

</html>