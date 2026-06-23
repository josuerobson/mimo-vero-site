<?php
/**
 * Generic Delete Item
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

// Whitelist allowed tables
$allowed_tables = ['plans', 'differentials', 'contact_submissions'];

if (!in_array($table, $allowed_tables) || $id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM `$table` WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Item excluído com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item não encontrado']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir']);
}
