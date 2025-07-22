<?php
session_start();
header("Content-Type: application/json");

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($name) && !empty($password)) {
        $conn = Database::getConnection();

        $query = "SELECT id, name, password FROM users WHERE name = :name LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['password'] === $password) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            unset($_SESSION['error_message']);
            header("Location: ../views/dashboard.php");
            exit();
        } else {
            $_SESSION['error_message'] = 'name atau password salah';
            header("Location: ../views/login.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = 'name dan password wajib diisi';
        header("Location: ../views/login.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = 'Metode request tidak valid';
    header("Location: ../views/login.php");
    exit();
}