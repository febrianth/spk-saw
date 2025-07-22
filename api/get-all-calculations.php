<?php
require_once '../api/auth-check.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

try {
    $pdo = Database::getConnection();

    // === LANGKAH 1: Ambil Kriteria & Bangun Matriks Keputusan (X) ===
    $stmt = $pdo->prepare("SELECT id, code, name, weight, attribute FROM criterias ORDER BY id ASC");
    $stmt->execute();
    $criterias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($criterias)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tidak ada data kriteria.'
        ]);
        exit;
    }

    $criteriaMap = [];
    foreach ($criterias as $c) {
        $criteriaMap[$c['id']] = $c;
    }

    $stmt = $pdo->prepare("
        SELECT a.id as alt_id, a.name as alt_name, als.criteria_id, als.score_value
        FROM alternatives a
        JOIN scores als 
          ON a.id = als.alternative_id
        WHERE a.deleted_at IS NULL ORDER BY a.id, als.criteria_id
    ");
    $stmt->execute();
    $rawScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $matrixX_temp = [];
    foreach ($rawScores as $score) {
        $altId = $score['alt_id'];
        if (!isset($matrixX_temp[$altId])) {
            $matrixX_temp[$altId] = ['name' => $score['alt_name']];
        }
        if (isset($criteriaMap[$score['criteria_id']])) {
            $critCode = $criteriaMap[$score['criteria_id']]['code'];
            $matrixX_temp[$altId][$critCode] = (float)$score['score_value'];
        }
    }
    $matrixX = array_values($matrixX_temp);

    // === LANGKAH 2: Hitung Min/Max & Buat Matriks Normalisasi (R) ===
    $minMax = [];
    foreach ($criterias as $crit) {
        $code = $crit['code'];
        $values = [];
        foreach ($matrixX as $row) {
            if (isset($row[$code])) {
                $values[] = $row[$code];
            }
        }

        if (!empty($values)) {
            $minMax[$code] = ['min' => min($values), 'max' => max($values)];
        } else {
            $minMax[$code] = ['min' => 0, 'max' => 0];
        }
    }

    $matrixR = [];
    foreach ($matrixX as $row) {
        $newRow = ['name' => $row['name']];
        foreach ($criterias as $crit) {
            $code = $crit['code'];
            // Gunakan null coalescing operator (??) untuk memberi nilai default 0 jika tidak ada
            $val = $row[$code] ?? 0;
            $extremes = $minMax[$code];
            $normVal = 0;
            if ($crit['attribute'] == 'benefit' && $extremes['max'] > 0) {
                $normVal = $val / $extremes['max'];
            } elseif ($crit['attribute'] == 'cost' && $val > 0) {
                $normVal = $extremes['min'] / $val;
            }
            $newRow[$code] = round($normVal, 4);
        }
        $matrixR[] = $newRow;
    }

    // === LANGKAH 3: Hitung Vektor V ===
    $matrixV = [];
    foreach ($matrixR as $row) {
        $newRow = ['name' => $row['name']];
        $total = 0;
        foreach ($criterias as $crit) {
            $code = $crit['code'];
            $weightedVal = $row[$code] * (float)$crit['weight'];
            $newRow[$code] = round($weightedVal, 4);
            $total += $weightedVal;
        }
        $newRow['total'] = round($total, 4);
        $matrixV[] = $newRow;
    }

    // === LANGKAH 4: Kirim Semua Hasil ===
    echo json_encode([
        "status" => "success",
        "data" => [
            "criterias" => $criterias,
            "matrix_x" => $matrixX,
            "matrix_r" => $matrixR,
            "matrix_v" => $matrixV
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database Error: " . $e->getMessage()
    ]);
}
