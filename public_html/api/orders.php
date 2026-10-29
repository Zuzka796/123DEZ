<?php
session_start();
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

// 1. ЗАЩИТА: разрешаем обычным пользователям получать свои заявки (GET) и создавать новые (POST).
// PATCH и DELETE (редактирование и удаление) останутся жестко защищенными ниже.
if ($method !== 'POST' && $method !== 'GET') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        die('Доступ запрещен: требуются права администратора!');
    }
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $name = trim($input['name'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $address = trim($input['address'] ?? '');
    $items = trim($input['items'] ?? '');
    $total = floatval($input['total'] ?? 0);
    $userId = $_SESSION['user']['id'] ?? null;

    // Очищаем номер от скобок, пробелов и тире, оставляем только цифры и знак плюс
    $cleanPhone = preg_replace('/[^\d+]/', '', $phone);

    if (!$name || !$cleanPhone || !$address) {
        echo json_encode(['success' => false, 'error' => 'Заполните имя, телефон и адрес']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, client_name, client_phone, address, items_desc, total_price, status) VALUES (?, ?, ?, ?, ?, ?, 'новый')");
    $stmt->execute([$userId, $name, $cleanPhone, $address, $items, $total]);

    echo json_encode(['success' => true, 'order_id' => $pdo->lastInsertId()]);
    exit;
}

if ($method === 'GET') {
    // Если вообще не авторизован — отдаем пустой список
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => true, 'orders' => []]);
        exit;
    }

    // Если это АДМИН — показываем всю базу заказов
    if ($_SESSION['user']['role'] === 'admin') {
        $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
    // Если это ОБЫЧНЫЙ КЛИЕНТ — показываем ТОЛЬКО ЕГО заявки
    else {
        $userId = $_SESSION['user']['id'];
        
        // 1. Оставляем от номера телефона ТОЛЬКО цифры (удаляем пробелы, скобки, тире, плюсы)
        $onlyDigits = preg_replace('/[^\d]/', '', $_SESSION['user']['phone']);
        // 2. Берем только последние 10 цифр (отбрасываем +7 или 8 в начале)
        $shortPhone = substr($onlyDigits, -10); 
        
        // Защита: ищем по номеру, только если он корректной длины
        if (strlen($shortPhone) >= 10) {
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? OR client_phone LIKE ? ORDER BY id DESC");
            $stmt->execute([$userId, "%$shortPhone"]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
            $stmt->execute([$userId]);
        }
        
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['success' => true, 'orders' => $orders]);
    exit;
}

if ($method === 'PATCH') {
    // Смена статуса заказа из админ-панели (Двойная проверка на админа)
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403); exit;
    }
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);
    $status = $input['status'] ?? 'новый';

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo json_encode(['success' => true]);
    exit;
}

if ($method === 'DELETE') {
    // Проверка, что удаляет администратор (Двойная проверка на админа)
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403); exit;
    }
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);

    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Не указан ID заявки']);
    }
    exit;
}