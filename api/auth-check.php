<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../views/login.php");
    exit();
}