<?php
require_once '../api/auth-check.php';
require_once '../config/database.php';
header('Content-Type: application/json');

try {
    $pdo = Database::getConnection();

    // LANGKAH 1: Ambil semua data kriteria dan susun ulang array
    $stmt = $pdo->prepare("SELECT id, code, weight, attribute FROM criterias ORDER BY id ASC");
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

    $criterias = [];
    foreach ($criterias_raw as $row) {
        $criterias[$row['id']] = $row;
    }

    // LANGKAH 2: Ambil semua data alternatif beserta skornya dalam format
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

    // LANGKAH 3: Ubah data "panjang" menjadi matriks [alternatif][kriteria] = skor
    $alternativesMatrix = [];
    foreach ($rawScores as $score) {
        $altId = $score['alternative_id'];
        $critId = $score['criteria_id'];

        if (!isset($alternativesMatrix[$altId])) {
            $alternativesMatrix[$altId] = [
                'id' => $altId,
                'code' => $score['alternative_code'],
                'name' => $score['alternative_name'],
                'scores' => []
            ];
        }
        $alternativesMatrix[$altId]['scores'][$critId] = (float)$score['score_value'];
    }

    // LANGKAH 4: Hitung nilai Min/Max untuk setiap kriteria
    $minMaxPerCriteria = [];
    foreach ($criterias as $critId => $critData) {
        $allScoresForCriteria = [];
        foreach ($alternativesMatrix as $altData) {
            if (isset($altData['scores'][$critId])) {
                $allScoresForCriteria[] = $altData['scores'][$critId];
            }
        }

        if (!empty($allScoresForCriteria)) {
            $minMaxPerCriteria[$critId] = [
                'min' => min($allScoresForCriteria),
                'max' => max($allScoresForCriteria)
            ];
        }
    }

    // LANGKAH 5: Lakukan Normalisasi
    $normalizedMatrix = [];
    foreach ($alternativesMatrix as $altId => $altData) {
        $normalizedMatrix[$altId] = [
            'code' => $altData['code'],
            'name' => $altData['name'],
            'normalized_scores' => []
        ];

        foreach ($criterias as $critId => $critData) {
            $score = $altData['scores'][$critId] ?? 0;
            $attribute = $critData['attribute'];
            $minMax = $minMaxPerCriteria[$critId] ?? ['min' => 0, 'max' => 0];

            $normalizedValue = 0;
            if ($attribute === 'benefit') {
                $max = $minMax['max'];
                $normalizedValue = ($max > 0) ? ($score / $max) : 0;
            } elseif ($attribute === 'cost') {
                $min = $minMax['min'];
                $normalizedValue = ($score > 0) ? ($min / $score) : 0;
            }
            $normalizedMatrix[$altId]['normalized_scores'][$critId] = $normalizedValue;
        }
    }

    // LANGKAH 6: Hitung nilai akhir (V) dengan mengalikan bobot
    $finalResults = [];
    foreach ($normalizedMatrix as $altId => $altData) {
        $totalScore = 0;
        foreach ($altData['normalized_scores'] as $critId => $normalizedScore) {
            $weight = $criterias[$critId]['weight'];
            $totalScore += $normalizedScore * $weight;
        }
        $finalResults[] = [
            'code' => $altData['code'],
            'name' => $altData['name'],
            'value' => round($totalScore, 4)
        ];
    }

    // LANGKAH 7: Urutkan hasil dan berikan ranking
    usort($finalResults, fn($a, $b) => $b['value'] <=> $a['value']);
    foreach ($finalResults as $i => &$r) {
        $r['rank'] = $i + 1;
    }
    unset($r);

    // LANGKAH 8: Kirim respon JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Perhitungan hasil akhir berhasil',
        'data' => $finalResults
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
