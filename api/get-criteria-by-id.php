<?php
require_once '../api/auth-check.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

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

    $stmt = $pdo->prepare("SELECT * FROM criterias WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => $data ? "success" : "error",
        "message" => $data ? "Data kriteria ditemukan" : "Data tidak ditemukan",
        "data" => $data ?: null
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil data: " . $e->getMessage(),
        "data" => null
    ]);
}