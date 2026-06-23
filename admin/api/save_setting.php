<?php
/**
 * Save Single Setting
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

$key = trim($_POST['key'] ?? '');
$value = $_POST['value'] ?? '';

if (empty($key)) {
    echo json_encode(['success' => false, 'message' => 'Chave não informada']);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
    $stmt->execute([$value, $key]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Salvo']);
    } else {
        // Try insert if not exists
        $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
        echo json_encode(['success' => true, 'message' => 'Salvo']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar']);
}
