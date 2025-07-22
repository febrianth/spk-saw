<?php
require_once '../api/auth-check.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode([
        "status" => "error",
        "message" => "ID alternatif tidak boleh kosong."
    ]);
    exit;
}

try {
    $pdo = Database::getConnection();

    // --- LANGKAH 1: Ambil data dasar alternatif ---
    $stmt = $pdo->prepare("SELECT id, code, name FROM alternatives WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$id]);
    $alternative = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alternative) {
        echo json_encode([
            "status" => "error",
            "message" => "Data alternatif dengan ID tersebut tidak ditemukan."
        ]);
        exit;
    }

    // --- LANGKAH 2: Ambil semua skor yang terkait dengan alternatif ini ---
    $scoreStmt = $pdo->prepare("SELECT criteria_id, score_value FROM scores WHERE alternative_id = ?");
    $scoreStmt->execute([$id]);
    $scoresRaw = $scoreStmt->fetchAll(PDO::FETCH_ASSOC);

    // --- LANGKAH 3: Ubah format skor menjadi [criteria_id => score_value] ---
    $scoresFormatted = [];
    foreach ($scoresRaw as $score) {
        $scoresFormatted[$score['criteria_id']] = $score['score_value'];
    }

    // Gabungkan data alternatif dengan skor yang sudah diformat
    $data = $alternative;
    $data['scores'] = $scoresFormatted;

    echo json_encode([
        "status" => "success",
        "message" => "Data alternatif berhasil diambil",
        "data" => $data
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil data: " . $e->getMessage()
    ]);
}
