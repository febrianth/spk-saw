<?php
require_once '../api/auth-check.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? null;
$code = $_POST['code'] ?? null;
$attribute = $_POST['attribute'] ?? null;
$weight = $_POST['weight'] ?? null;
$input_type = $_POST['input_type'] ?? null;

if (
    !$weight
    || !$name
    || !$code
    || !$input_type
    || !$attribute
) {
    echo json_encode([
        "status" => "error",
        "message" => "semua field harus diisi",
        "data" => null
    ]);
    exit;
}

try {
    $pdo = Database::getConnection();

    if ($id) {
        // Ambil bobot lama dari kriteria yang akan diupdate
        $stmt = $pdo->prepare("SELECT weight FROM criterias WHERE id = ?");
        $stmt->execute([$id]);
        $oldWeight = $stmt->fetchColumn();

        if ($oldWeight === false) {
            echo json_encode([
                "status" => "error",
                "message" => "Kriteria tidak ditemukan",
                "data" => null
            ]);
            exit;
        }

        // Hitung total bobot saat ini
        $stmt = $pdo->prepare("SELECT SUM(weight) FROM criterias WHERE id != ?");
        $stmt->execute([$id]);
        $totalOtherWeights = $stmt->fetchColumn();

        // Hitung total bobot baru jika diupdate
        $newTotal = $totalOtherWeights + $weight;

        if ($newTotal > 1) {
            echo json_encode([
                "status" => "error",
                "message" => "Total bobot tidak boleh melebihi 1. Sisa bobot yang tersedia: " . round(1 - $totalOtherWeights, 4),
                "data" => null
            ]);
            exit;
        }
    } else {
        // Hitung total bobot saat ini
        $stmt = $pdo->prepare("SELECT SUM(weight) FROM criterias");
        $stmt->execute();
        $total = $stmt->fetchColumn();

        if ($total >= 1) {
            echo json_encode([
                "status" => "error",
                "message" => "Total Bobot sudah maksimal (MAX = 1)",
                "data" => null
            ]);
            exit;
        }
    }

    if ($id) {
        // update criterias
        $stmt = $pdo->prepare("UPDATE criterias SET name = ?, code = ?, weight = ?, attribute = ?, input_type = ? WHERE id = ?");
        $stmt->execute([$name, $code, $weight, $attribute, $input_type, $id]);
        $message = "Data kriteria berhasil diupdate";
    } else {
        // insert criterias
        $stmt = $pdo->prepare("INSERT INTO criterias (name, code, weight, attribute, input_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $code, $weight, $attribute, $input_type]);
        $message = "Data kriteria berhasil ditambahkan";
    }

    echo json_encode([
        "status" => "success",
        "message" => $message,
        "data" => null
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal menyimpan data: " . $e->getMessage(),
        "data" => null
    ]);
}
