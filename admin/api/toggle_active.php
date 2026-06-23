<?php
/**
 * Toggle Active Status
 * SuaNet Fibra
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/site.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$table = $_POST['table'] ?? '';
$id = intval($_POST['id'] ?? 0);

$allowed_tables = ['plans', 'differentials'];

if (!in_array($table, $allowed_tables) || $id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("UPDATE `$table` SET active = NOT active WHERE id = ?");
    $stmt->execute([$id]);

    // Get new status
    $stmt = $db->prepare("SELECT active FROM `$table` WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'active' => (int)$row['active'],
        'message' => $row['active'] ? 'Ativado' : 'Desativado'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao alterar status']);
}
