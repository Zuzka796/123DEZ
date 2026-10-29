<?php
session_start();
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

// «ащита: разрешаем всем загружать цены (GET), но мен€ть их может только админ
if ($method !== 'GET') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        die('ƒоступ запрещен: требуютс€ права администратора!');
    }
}

// получение актуального списка услуг и цен из базы MySQL
if ($method === 'GET') {
    // ... ваш текущий код со строки 12 ...
    $stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
    $services = $stmt->fetchAll();
    echo json_encode(['success' => true, 'services' => $services]);
    exit;
}

// —охранение отредактированной цены или названи€ в базу MySQL
if ($method === 'PATCH' || $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);
    $title = trim($input['title'] ?? '');
    $basePrice = floatval($input['basePrice'] ?? 0);
    $unitPrice = floatval($input['unitPrice'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ќе указан ID услуги']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE services SET title = ?, base_price = ?, price_per_unit = ? WHERE id = ?");
    $stmt->execute([$title, $basePrice, $unitPrice, $id]);

    echo json_encode(['success' => true]);
    exit;
}