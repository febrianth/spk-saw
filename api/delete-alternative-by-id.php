<?php
require_once '../api/auth-check.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode([
        "status" => "error",
        "message" => "ID tidak ditemukan",
        "data" => null
    ]);
    exit;
}

try {
    $pdo = Database::getConnection();

    $pdo->beginTransaction();

    // 1. Soft delete alternatif
    $stmt = $pdo->prepare("UPDATE alternatives SET deleted_at = NOW() WHERE id = ?");
    $stmt->execute([$id]);

    // 2. Hard delete skor terkait
    $stmt = $pdo->prepare("DELETE FROM scores WHERE alternative_id = ?");
    $stmt->execute([$id]);

    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Data alternatif berhasil dihapus dan skor dihapus",
        "data" => null
    ]);
} catch (PDOException $e) {
    // Rollback jika error
    $pdo->rollBack();

    echo json_encode([
        "status" => "error",
        "message" => "Gagal menghapus data: " . $e->getMessage(),
        "data" => null
    ]);
}
