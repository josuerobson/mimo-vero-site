<?php
/**
 * Save Differential (AJAX)
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

$id = intval($_POST['id'] ?? 0);
$data = [
    'title' => trim($_POST['title'] ?? ''),
    'description' => trim($_POST['description'] ?? ''),
    'icon' => trim($_POST['icon'] ?? ''),
    'sort_order' => intval($_POST['sort_order'] ?? 0),
    'active' => isset($_POST['active']) ? 1 : 0,
];

if (empty($data['title'])) {
    echo json_encode(['success' => false, 'message' => 'Título é obrigatório']);
    exit;
}

try {
    $db = getDB();
    if ($id > 0) {
        $data['id'] = $id;
        $sql = "UPDATE differentials SET title=:title, description=:description, icon=:icon, sort_order=:sort_order, active=:active WHERE id=:id";
    } else {
        $sql = "INSERT INTO differentials (title, description, icon, sort_order, active) VALUES (:title, :description, :icon, :sort_order, :active)";
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($data);

    echo json_encode(['success' => true, 'message' => $id > 0 ? 'Diferencial atualizado' : 'Diferencial criado']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar']);
}
