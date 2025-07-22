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

    <title>Alternatif - SPK SAW</title>
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
                    <h1 class="h3 mb-4 text-gray-800" id="pageTitle">Alternatif</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Alternatif</h6>
                        </div>
                        <div class="card-body">
                            <button type="button" class="btn btn-success mb-4" onclick="openAddModal()">
                                <i class="fas fa-plus"></i> Tambah Data
                            </button>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Kode</th>
                                            <th class="text-center">Nama</th>
                                            <?php
                                            if (!empty($criterias)) {
                                                foreach ($criterias as $criteria) {
                                                    $displayName = htmlspecialchars($criteria['name']);
                                                    $displayCode = htmlspecialchars($criteria['code']);
                                                    echo "<th class='text-center'>{$displayCode}<br><small class='text-muted'>{$displayName}</small></th>";
                                                }
                                            }
                                            ?>
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

    </div>
    <!-- End of Page Wrapper -->

    <!-- Modal -->
    <div class="modal fade" id="modalAddData" tabindex="-1" role="dialog" aria-labelledby="modalAddDataLabel">
        <div class="modal-dialog modal-lg" role="document">
            <form id="formData" class="modal-content" onsubmit="handleSubmit(event)">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddDataLabel">Tambah Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">

                    <div class="form-group">
                        <label for="code">Kode Alternatif</label>
                        <input type="text" class="form-control" name="code" maxlength="10" id="code" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Nama Alternatif</label>
                        <input type="text" class="form-control" name="name" maxlength="50" id="name" required>
                    </div>

                    <hr>
                    <h5>Nilai Kriteria</h5>
                    <div id="dynamic-form-fields"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

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
        getAllData();
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
            url: '../api/get-alternatives.php',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success' && res.data) {
                    const $table = $('#dataTable');
                    const $tbody = $table.find('tbody');

                    if ($.fn.DataTable.isDataTable($table)) {
                        $table.DataTable().clear().destroy();
                    }

                    $tbody.empty();

                    const criterias = res.data.criterias; // Array kriteria untuk referensi urutan
                    const alternatives = res.data.alternatives; // Array alternatif

                    // Loop melalui setiap alternatif
                    alternatives.forEach((alt, index) => {
                        // Mulai membangun baris (tr)
                        let row = `<tr>
                        <td class="text-center">${index + 1}</td>
                        <td class="text-center">${alt.code}</td>
                        <td class="text-left">${alt.name}</td>`;

                        // Loop melalui setiap kriteria (dari header) untuk memastikan urutan kolom benar
                        criterias.forEach(crit => {
                            // Cari skor untuk kriteria saat ini di dalam data alternatif
                            // 'crit.id' adalah ID kriteria dari header
                            // 'alt.scores' adalah objek skor dari alternatif saat ini
                            const score = alt.scores[crit.id] ?? '-';

                            // Cek apakah skor adalah numerik untuk perataan teks
                            const textAlign = isNaN(score) ? 'text-left' : 'text-right';

                            // Tambahkan sel (td) ke baris
                            row += `<td class="${textAlign}">${score}</td>`;
                        });

                        // Tambahkan kolom Aksi
                        row += `<td class="text-center">
                               <button class="btn btn-sm btn-warning" onclick="handleEdit(${alt.id})" title="Edit"><i class="fas fa-fw fa-pen"></i></button>
                               <button class="btn btn-sm btn-danger" onclick="handleDelete(${alt.id})" title="Hapus"><i class="fas fa-fw fa-trash"></i></button>
                            </td>`;

                        // Tutup baris
                        row += `</tr>`;
                        $tbody.append(row);
                    });

                    $table.DataTable();

                } else {
                    alert('Gagal mengambil data: ' + (res.message || 'Format respons tidak sesuai.'));
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Terjadi kesalahan saat mengambil data: ' + textStatus);
                console.error(errorThrown);
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function openAddModal() {
        // Reset form
        $('#formData')[0].reset();
        $('#modalAddDataLabel').text('Tambah Data');
        $('#id').val('');

        const $formFieldsContainer = $('#dynamic-form-fields');
        $formFieldsContainer.html('<p>Memuat data kriteria...</p>');
        $.ajax({
                url: '../api/get-form-structure.php',
                method: 'GET',
                dataType: 'json'
            })
            .done(function(res) {
                if (res.status === 'success' && res.data) {
                    $formFieldsContainer.empty(); // Kosongkan container

                    // Loop melalui setiap kriteria dari API
                    res.data.forEach(crit => {
                        let fieldHtml = '<div class="form-group">';
                        fieldHtml += `<label for="crit_${crit.id}">${crit.name}</label>`;

                        // Cek apakah kriteria punya sub-kriteria
                        if (crit.input_type == 'option') {
                            // JIKA YA: Buat <select>
                            fieldHtml += `<select class="form-control" name="scores[${crit.id}]" id="crit_${crit.id}" required>`;
                            fieldHtml += `<option value="">-- Pilih --</option>`;
                            if (crit.sub_criterias && crit.sub_criterias.length > 0) {
                                crit.sub_criterias.forEach(sub => {
                                    fieldHtml += `<option value="${sub.value}">${sub.name}</option>`;
                                });
                            }
                            fieldHtml += `</select>`;
                        } else {
                            // JIKA TIDAK: Buat <input type="number">
                            fieldHtml += `<input type="number" class="form-control" name="scores[${crit.id}]" id="crit_${crit.id}" min="1" required>`;
                        }

                        fieldHtml += '</div>';
                        $formFieldsContainer.append(fieldHtml);
                    });

                    $('#modalAddData').modal('show');
                } else {
                    alert('Gagal memuat struktur form: ' + res.message);
                }
            })
            .fail(function() {
                alert('Terjadi kesalahan saat menghubungi server.');
                $formFieldsContainer.html('<p class="text-danger">Gagal memuat form.</p>');
            });
    }

    function handleDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            showLoading();

            $.ajax({
                url: '../api/delete-alternative-by-id.php',
                method: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    hideLoading();

                    if (res.status === 'success') {
                        alert(res.message);
                        getAllData(); // Perbarui tabel
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

    function handleEdit(id) {
        showLoading();

        // Reset form dan siapkan modal
        $('#formData')[0].reset();
        $('#modalAddDataLabel').text('Edit Data');
        const $formFieldsContainer = $('#dynamic-form-fields');
        $formFieldsContainer.html('<p>Memuat data...</p>');

        // --- LANGKAH 1: Bangun struktur form terlebih dahulu ---
        $.ajax({
            url: '../api/get-form-structure.php',
            method: 'GET',
            dataType: 'json'
        }).done(function(formStructureRes) {
            if (formStructureRes.status !== 'success') {
                hideLoading();
                alert('Gagal memuat struktur form: ' + formStructureRes.message);
                return;
            }

            // Bangun HTML form dari struktur
            $formFieldsContainer.empty();
            formStructureRes.data.forEach(crit => {
                let fieldHtml = `<div class="form-group">
                                <label for="crit_${crit.id}">${crit.name}</label>`;
                if (crit.input_type == 'option') {
                    fieldHtml += `<select class="form-control" name="scores[${crit.id}]" id="crit_${crit.id}" required>
                                <option value="">-- Pilih --</option>`;
                    if (crit.sub_criterias && crit.sub_criterias.length > 0) {
                        crit.sub_criterias.forEach(sub => {
                            fieldHtml += `<option value="${sub.value}">${sub.name}</option>`;
                        });
                    }
                    fieldHtml += `</select>`;
                } else {
                    fieldHtml += `<input type="number" class="form-control" name="scores[${crit.id}]" id="crit_${crit.id}" min="1" required>`;
                }
                fieldHtml += '</div>';
                $formFieldsContainer.append(fieldHtml);
            });

            // --- LANGKAH 2: Setelah form dibuat, ambil data spesifik untuk diisi ---
            $.ajax({
                url: '../api/get-alternative-by-id.php', // API baru kita
                method: 'GET',
                data: {
                    id: id
                },
                dataType: 'json'
            }).done(function(dataRes) {
                if (dataRes.status === 'success') {
                    const data = dataRes.data;

                    // Isi field dasar
                    $('#id').val(data.id);
                    $('#code').val(data.code);
                    $('#name').val(data.name);

                    // Isi field dinamis (skor)
                    if (data.scores) {
                        for (const criteria_id in data.scores) {
                            const score_value = data.scores[criteria_id];
                            // Cari elemen form berdasarkan ID kriteria dan isi nilainya
                            $(`#crit_${criteria_id}`).val(score_value);
                        }
                    }

                    $('#modalAddData').modal('show');
                } else {
                    alert('Gagal mengambil data alternatif: ' + dataRes.message);
                }
            }).fail(function() {
                alert('Terjadi kesalahan saat mengambil data alternatif.');
            }).always(function() {
                hideLoading();
            });

        }).fail(function() {
            hideLoading();
            alert('Terjadi kesalahan saat memuat struktur form.');
        });
    }

    function handleSubmit(event) {
        event.preventDefault();

        const formData = $('#formData').serialize(); // Mengambil semua data form

        $.ajax({
            url: '../api/upsert-alternative.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    alert('Data berhasil disimpan!');
                    $('#modalAddData').modal('hide');
                    getAllData(); // Muat ulang data tabel
                } else {
                    alert('Gagal menyimpan: ' + res.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan server saat menyimpan data.');
            }
        });
    }
</script>

</html>