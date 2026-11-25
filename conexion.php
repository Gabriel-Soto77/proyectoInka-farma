<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set("America/Lima");

$servername = "sql111.infinityfree.com";
$username   = "if0_43115247";
$password   = "Ue54a0X2vVVZPu";
$dbname     = "if0_43115247_farmacia";

try {
    $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
    $conexion = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ]);
} catch (PDOException $e) {
    die("No se pudo conectar con la base de datos.");
}

function verificarSesion() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }
}
?>
