<?php
require_once '../api/auth-check.php';
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

try {
    $pdo = Database::getConnection();

    // Ambil semua kriteria utama beserta jenis inputnya
    $stmt = $pdo->prepare("SELECT id, name, input_type FROM criterias ORDER BY id ASC");
    $stmt->execute();
    $criterias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($criterias as $key => $criteria) {
        $criterias[$key]['sub_criterias'] = [];
        // Hanya cari sub-kriteria jika jenis inputnya 'option'
        if ($criteria['input_type'] === 'option') {
            $subStmt = $pdo->prepare("SELECT name, value FROM sub_criterias WHERE criteria_id = ? ORDER BY value DESC");
            $subStmt->execute([$criteria['id']]);
            $criterias[$key]['sub_criterias'] = $subStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    echo json_encode([
        "status" => "success",
        "message" => "Struktur form berhasil diambil",
        "data" => $criterias
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil data: " . $e->getMessage()
    ]);
}
