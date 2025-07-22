<?php
require_once '../api/auth-check.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

try {
    $pdo = Database::getConnection();

    // =========================================================================
    // LANGKAH 1: Ambil semua kriteria untuk header tabel dan mapping
    // =========================================================================
    $stmt = $pdo->prepare("SELECT id, code, name FROM criterias ORDER BY id ASC");
    $stmt->execute();
    $criterias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($criterias)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tidak ada data kriteria ditemukan.'
        ]);
        exit;
    }

    // =========================================================================
    // LANGKAH 2: Ambil semua alternatif beserta skor dan nama sub-kriterianya
    // =========================================================================
    $stmt = $pdo->prepare("
        SELECT 
            a.id as alternative_id, 
            a.code as alternative_code, 
            a.name as alternative_name,
            als.criteria_id,
            als.score_value,
            sc.name as sub_criteria_name
        FROM alternatives a
        LEFT JOIN scores als 
               ON a.id = als.alternative_id
        LEFT JOIN sub_criterias sc 
               ON als.criteria_id = sc.criteria_id AND als.score_value = sc.value
        WHERE a.deleted_at IS NULL
        ORDER BY a.id, als.criteria_id
    ");
    $stmt->execute();
    $rawScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // =========================================================================
    // LANGKAH 3: Susun data menjadi format yang mudah digunakan di frontend
    // =========================================================================
    $alternativesData = [];
    foreach ($rawScores as $row) {
        $altId = $row['alternative_id'];

        // Jika alternatif ini baru, inisialisasi datanya
        if (!isset($alternativesData[$altId])) {
            $alternativesData[$altId] = [
                'id' => $altId,
                'code' => $row['alternative_code'],
                'name' => $row['alternative_name'],
                'scores' => [] // Siapkan untuk menampung skor
            ];
        }

        // Tentukan nilai yang akan ditampilkan: nama sub-kriteria jika ada, jika tidak, nilai skornya.
        $displayValue = $row['sub_criteria_name'] ?? $row['score_value'];

        // Simpan skor dengan key 'criteria_id'
        $alternativesData[$altId]['scores'][$row['criteria_id']] = $displayValue;
    }

    // =========================================================================
    // LANGKAH 4: Kirim data terstruktur sebagai JSON
    // =========================================================================
    echo json_encode([
        "status" => "success",
        "message" => "Data berhasil diambil",
        "data" => [
            "criterias" => $criterias,
            "alternatives" => array_values($alternativesData) // Reset keys agar menjadi array biasa
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil data: " . $e->getMessage()
    ]);
}
