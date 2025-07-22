<?php
require_once 'auth-check.php';
require_once '../config/database.php';
header('Content-Type: application/json');

try {
    $pdo = Database::getConnection();
    
    // Ambil kriteria yang tipenya 'option'
    $stmt = $pdo->prepare("SELECT id, name FROM criterias WHERE input_type = 'option' ORDER BY id ASC");
    $stmt->execute();
    $option_criterias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Untuk setiap kriteria, ambil sub-kriterianya
    foreach ($option_criterias as $key => $criteria) {
        $subStmt = $pdo->prepare("SELECT id, name, value FROM sub_criterias WHERE criteria_id = ? ORDER BY value DESC");
        $subStmt->execute([$criteria['id']]);
        $sub_criterias = $subStmt->fetchAll(PDO::FETCH_ASSOC);
        $option_criterias[$key]['sub_criterias'] = $sub_criterias;
    }

    echo json_encode(['status' => 'success', 'data' => $option_criterias]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
