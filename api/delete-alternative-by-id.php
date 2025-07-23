<?php
require_once '../api/auth-check.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode([
        "status" => "error",
        "message" => "ID alternatif tidak boleh kosong."
    ]);
    exit;
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("DELETE FROM alternatives WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Data alternatif berhasil dihapus secara permanen."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Data tidak ditemukan atau sudah dihapus."
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal menghapus data: " . $e->getMessage()
    ]);
}
