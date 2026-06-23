<?php
/**
 * Save Plan (AJAX)
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
    'plan_name' => trim($_POST['plan_name'] ?? ''),
    'speed' => trim($_POST['speed'] ?? ''),
    'badge' => trim($_POST['badge'] ?? ''),
    'mobile_data' => trim($_POST['mobile_data'] ?? ''),
    'mobile_desc' => trim($_POST['mobile_desc'] ?? ''),
    'streaming_name' => trim($_POST['streaming_name'] ?? ''),
    'price_decimal' => trim($_POST['price_decimal'] ?? ''),
    'price_cents' => trim($_POST['price_cents'] ?? ''),
    'price_period' => trim($_POST['price_period'] ?? '/mês'),
    'payment_note' => trim($_POST['payment_note'] ?? ''),
    'cta_text' => trim($_POST['cta_text'] ?? 'Assine já'),
    'cta_link' => trim($_POST['cta_link'] ?? ''),
    'info_details' => trim($_POST['info_details'] ?? ''),
    'sort_order' => intval($_POST['sort_order'] ?? 0),
    'active' => isset($_POST['active']) ? 1 : 0,
];

if (empty($data['plan_name'])) {
    echo json_encode(['success' => false, 'message' => 'Nome do plano é obrigatório']);
    exit;
}

try {
    $db = getDB();
    if ($id > 0) {
        $data['id'] = $id;
        $sql = "UPDATE plans SET plan_name=:plan_name, speed=:speed, badge=:badge, mobile_data=:mobile_data, mobile_desc=:mobile_desc, streaming_name=:streaming_name, price_decimal=:price_decimal, price_cents=:price_cents, price_period=:price_period, payment_note=:payment_note, cta_text=:cta_text, cta_link=:cta_link, info_details=:info_details, sort_order=:sort_order, active=:active WHERE id=:id";
    } else {
        $sql = "INSERT INTO plans (plan_name, speed, badge, mobile_data, mobile_desc, streaming_name, price_decimal, price_cents, price_period, payment_note, cta_text, cta_link, info_details, sort_order, active) VALUES (:plan_name, :speed, :badge, :mobile_data, :mobile_desc, :streaming_name, :price_decimal, :price_cents, :price_period, :payment_note, :cta_text, :cta_link, :info_details, :sort_order, :active)";
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($data);

    echo json_encode(['success' => true, 'message' => $id > 0 ? 'Plano atualizado' : 'Plano criado']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar: ' . $e->getMessage()]);
}
