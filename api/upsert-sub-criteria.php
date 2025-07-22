<?php
require_once '../api/auth-check.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Ambil data dari POST request
$id = $_POST['id'] ?? null;
$criteria_id = $_POST['criteria_id'] ?? null;
$name = trim($_POST['name'] ?? '');
$value = $_POST['value'] ?? '';

// Validasi input
if (empty($name) || empty($criteria_id) || $value === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Semua field harus diisi."
    ]);
    exit;
}

try {
    $pdo = Database::getConnection();

    if ($id) {
        // UPDATE jika ID ada
        $stmt = $pdo->prepare("UPDATE sub_criterias SET name = ?, value = ?, criteria_id = ? WHERE id = ?");
        $stmt->execute([$name, $value, $criteria_id, $id]);
        $message = "Data sub-kriteria berhasil diupdate";
    } else {
        // INSERT jika ID tidak ada
        $stmt = $pdo->prepare("INSERT INTO sub_criterias (name, value, criteria_id) VALUES (?, ?, ?)");
        $stmt->execute([$name, $value, $criteria_id]);
        $message = "Data sub-kriteria berhasil ditambahkan";
    }

    echo json_encode([
        "status" => "success",
        "message" => $message
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal menyimpan data: " . $e->getMessage()
    ]);
}
