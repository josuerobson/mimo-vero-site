<?php
/**
 * Admin Login Handler
 * SuaNet Fibra
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/site.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos']);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_full_name'] = $user['full_name'] ?? $user['username'];
        $_SESSION['admin_last_activity'] = time();

        $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

        echo json_encode(['success' => true, 'message' => 'Login realizado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuário ou senha incorretos']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno']);
}
