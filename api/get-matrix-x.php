<?php
require_once '../api/auth-check.php';
require_once '../config/database.php';
header('Content-Type: application/json');

try {
    $pdo = Database::getConnection();

    // =========================================================================
    // LANGKAH 1: Ambil semua data kriteria untuk mapping ID ke Kode (C1, C2, dll)
    // =========================================================================
    $stmt = $pdo->prepare("SELECT id, code FROM criterias ORDER BY id ASC");
    $stmt->execute();
    $criterias_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($criterias_raw)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tidak ada data kriteria yang ditemukan.',
            'data' => null
        ]);
        exit;
    }

    // Buat peta dari criteria_id => criteria_code
    $criteriaMap = [];
    foreach ($criterias_raw as $row) {
        $criteriaMap[$row['id']] = $row['code'];
    }

    // =========================================================================
    // LANGKAH 2: Ambil semua data alternatif beserta skor mentahnya
    // =========================================================================
    $stmt = $pdo->prepare("
        SELECT 
            a.id as alternative_id, 
            a.code as alternative_code, 
            a.name as alternative_name,
            als.criteria_id,
            als.score_value
        FROM alternatives a
        JOIN scores als ON a.id = als.alternative_id
        ORDER BY a.id, als.criteria_id
    ");
    $stmt->execute();
    $rawScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rawScores)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tidak ada data penilaian (scores) yang ditemukan.',
            'data' => null
        ]);
        exit;
    }

    // =========================================================================
    // LANGKAH 3: Susun data menjadi format Matriks X
    // =========================================================================
    $matrixX_temp = [];
    foreach ($rawScores as $score) {
        $altId = $score['alternative_id'];
        $critId = $score['criteria_id'];
        $critCode = $criteriaMap[$critId] ?? 'UNKNOWN'; // Dapatkan kode C1, C2, dll.

        // Jika alternatif ini baru pertama kali, inisialisasi datanya
        if (!isset($matrixX_temp[$altId])) {
            $matrixX_temp[$altId] = [
                'id' => $altId,
                'code' => $score['alternative_code'],
                'name' => $score['alternative_name']
            ];
        }

        // Tambahkan skor ke dalam baris alternatif dengan key kode kriteria
        $matrixX_temp[$altId][$critCode] = (float)$score['score_value'];
    }

    // Ubah dari array asosiatif menjadi array numerik untuk output JSON
    $matrixX = array_values($matrixX_temp);

    // =========================================================================
    // LANGKAH 4: Kirim respon JSON berisi Matriks X
    // =========================================================================
    echo json_encode([
        'status' => 'success',
        'message' => 'Data Matriks X berhasil diambil',
        'data' => $matrixX
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage(),
        'data' => null
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'An unexpected error occurred: ' . $e->getMessage(),
        'data' => null
    ]);
}
