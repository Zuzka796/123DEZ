<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

if ($action === 'register') {
    $name     = trim($input['name'] ?? '');
    $email    = trim($input['email'] ?? '');
    $phone    = trim($input['phone'] ?? '');
    $password = trim($input['password'] ?? '');

    if (empty($name) || empty($email) || empty($phone) || mb_strlen($password) < 6) {
        echo json_encode(['success' => false, 'error' => 'Заполните все поля (пароль от 6 символов)'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'user')");
       $stmt->execute([$name, $email, $phone, $hash]);
        
        $newId = $pdo->lastInsertId();
        
        // --- НОВЫЙ КОД: Привязка старых заявок к новому аккаунту ---
        // Ищем заказы по номеру телефона, где user_id еще пустой (или равен 0), и прописываем им ID нового клиента
        $linkStmt = $pdo->prepare("UPDATE orders SET user_id = ? WHERE client_phone = ? AND (user_id IS NULL OR user_id = 0)");
        $linkStmt->execute([$newId, $phone]);
        $_SESSION['user'] = [
            'id'    => $newId,
            'name'  => $name,
            'email' => $email,
            'phone' => $phone,
            'role'  => 'user'
        ];

        echo json_encode(['success' => true, 'status' => 'success', 'user' => $_SESSION['user']], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Ошибка БД: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

if ($action === 'login') {
    $login = trim($input['login'] ?? $input['email'] ?? $input['phone'] ?? '');
    $password = trim($input['password'] ?? '');

    if (empty($login) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Введите логин и пароль'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        // Ищем пользователя по email или точному номеру телефона
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();

        // Если не нашли с плюсом, пробуем найти без первого символа '+' или по частичному совпадению
        if (!$user) {
            $cleanLogin = ltrim($login, '+');
            $stmt = $pdo->prepare("SELECT * FROM users WHERE phone LIKE ? OR phone LIKE ? LIMIT 1");
            $stmt->execute(["%$cleanLogin", "%$login%"]);
            $user = $stmt->fetch();
        }

        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'Пользователь с таким логином не найден'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'    => $user['id'],
                'name'  => $user['full_name'] ?? $user['name'] ?? 'Пользователь',
                'email' => $user['email'],
                'phone' => $user['phone'],
                'role'  => $user['role'] ?? 'user'
            ];
            // ---> НОВЫЙ КОД: Собираем гостевые заявки при каждом входе в аккаунт
            $linkStmt = $pdo->prepare("UPDATE orders SET user_id = ? WHERE client_phone = ? AND (user_id IS NULL OR user_id = 0)");
            $linkStmt->execute([$user['id'], $user['phone']]);
            // ------------------------------------------------------------------
        
            echo json_encode([
                'success' => true,
                'status'  => 'success',
                'user'    => $_SESSION['user']
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'error' => 'Неверный логин или пароль'], JSON_UNESCAPED_UNICODE);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Ошибка базы: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

if ($action === 'check') {
    echo json_encode([
        'success' => true,
        'logged'  => isset($_SESSION['user']),
        'user'    => $_SESSION['user'] ?? null
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'logout') {
    unset($_SESSION['user']);
    session_destroy();
    echo json_encode(['success' => true, 'status' => 'success'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'count_users') {
    try {
        // Считаем всех пользователей в базе, у которых роль 'user' (клиент)
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
        $count = $stmt->fetchColumn();
        
        echo json_encode([
            'success' => true, 
            'total_users' => $count
        ], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Ошибка БД'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}