<?php
require_once '../api/auth-check.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        SELECT 
            s.*,
            c.name AS criteria_name
        FROM sub_criterias AS s
        JOIN criterias AS c
          ON s.criteria_id = c.id");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "message" => "Data sub kriteria berhasil diambil",
        "data" => $data
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil data: " . $e->getMessage(),
        "data" => null
    ]);
}
