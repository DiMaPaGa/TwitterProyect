<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost:3306";
$user = "root";
$pass = "root";

$bd = "social_network";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$bd;charset=utf8mb4", $user, $pass);
} catch (PDOException $e) {
    echo "Error de conexión" . $e->getMessage();
}

?>
