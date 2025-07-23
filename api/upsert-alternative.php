<?php
require_once '../api/auth-check.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Ambil data dari POST request
$id = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$code = trim($_POST['code'] ?? '');
$scores = $_POST['scores'] ?? [];

// --- Validasi Input ---
if (empty($name) || empty($code)) {
    echo json_encode([
        "status" => "error",
        "message" => "Kode dan Nama Alternatif harus diisi."
    ]);
    exit;
}

if (empty($scores) || !is_array($scores)) {
    echo json_encode([
        "status" => "error",
        "message" => "Data nilai kriteria tidak boleh kosong."
    ]);
    exit;
}

// Validasi bahwa semua nilai kriteria telah diisi
foreach ($scores as $score_value) {
    if ($score_value === '' || $score_value === null) {
        echo json_encode([
            "status" => "error",
            "message" => "Semua nilai kriteria harus diisi."
        ]);
        exit;
    }
}

$pdo = Database::getConnection();

try {
    // Mulai transaksi untuk memastikan integritas data
    $pdo->beginTransaction();

    // --- LANGKAH 1: Upsert data ke tabel 'alternatives' ---
    if ($id) {
        // UPDATE jika ID ada
        $stmt = $pdo->prepare("UPDATE alternatives SET name = ?, code = ? WHERE id = ?");
        $stmt->execute([$name, $code, $id]);
        $message = "Data alternatif berhasil diupdate";
        $alternative_id = $id;
    } else {
        // INSERT jika ID tidak ada
        $stmt = $pdo->prepare("INSERT INTO alternatives (name, code) VALUES (?, ?)");
        $stmt->execute([$name, $code]);
        $alternative_id = $pdo->lastInsertId();
        $message = "Data alternatif berhasil ditambahkan";
    }

    // --- LANGKAH 2: Upsert data ke tabel 'scores' ---
    // Metode upsert yang paling andal adalah menghapus semua skor lama untuk alternatif ini,
    // lalu memasukkan kembali semua skor baru dari form.

    // Hapus skor lama
    $deleteStmt = $pdo->prepare("DELETE FROM scores WHERE alternative_id = ?");
    $deleteStmt->execute([$alternative_id]);

    // Masukkan skor baru
    $insertScoreStmt = $pdo->prepare(
        "INSERT INTO scores (alternative_id, criteria_id, score_value) VALUES (?, ?, ?)"
    );

    foreach ($scores as $criteria_id => $score_value) {
        // Lakukan sanitasi dasar untuk keamanan
        $clean_criteria_id = filter_var($criteria_id, FILTER_SANITIZE_NUMBER_INT);
        $clean_score_value = filter_var($score_value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $insertScoreStmt->execute([$alternative_id, $clean_criteria_id, $clean_score_value]);
    }

    // Jika semua query berhasil, commit transaksi
    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "message" => $message
    ]);
} catch (PDOException $e) {
    // Jika terjadi error di salah satu query, batalkan semua perubahan (rollback)
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    // Beri pesan error yang lebih spesifik jika terjadi duplikasi data
    if ($e->getCode() == 23000) {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menyimpan: Kode Alternatif sudah digunakan."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menyimpan data: " . $e->getMessage()
        ]);
    }
}
