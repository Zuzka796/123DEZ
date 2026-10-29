<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$db   = 'denkyzmz_123dez';
$user = 'denkyzmz_123dez';
$pass = 'AS89996301051as**';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(200);
    echo json_encode(['success' => false, 'error' => 'Ошибка базы: ' . $e->getMessage()]);
    exit;
}