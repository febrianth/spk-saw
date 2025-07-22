<?php
require_once '../api/auth-check.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode([
        "status" => "error",
        "message" => "ID sub-kriteria tidak boleh kosong."
    ]);
    exit;
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        SELECT 
            sc.id, 
            sc.name, 
            sc.value, 
            sc.criteria_id, 
            c.name AS criteria_name
        FROM sub_criterias sc
        JOIN criterias c ON sc.criteria_id = c.id
        WHERE sc.id = ?
    ");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => $data ? "success" : "error",
        "message" => $data ? "Data sub-kriteria ditemukan" : "Data tidak ditemukan",
        "data" => $data ?: null
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil data: " . $e->getMessage()
    ]);
}
