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

    $stmt = $pdo->prepare("DELETE FROM criterias WHERE id = ?");
    $stmt->execute([$id]);

    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Data kriteria berhasil dihapus",
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
