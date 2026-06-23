<?php
/**
 * Contact Form API Handler
 * SuaNet Fibra - ISP Landing Page
 * Handles AJAX form submissions
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/site.php';

header('Content-Type: application/json; charset=utf-8');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

// Verify CSRF
$token = $_POST[CSRF_TOKEN_NAME] ?? '';
if (!verify_csrf($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token de segurança inválido. Recarregue a página e tente novamente.']);
    exit;
}

// Get and sanitize input
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');
$source = trim($_POST['source'] ?? 'form');

// Validate required fields
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, informe seu nome.']);
    exit;
}

if (empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, informe seu telefone.']);
    exit;
}

// Validate phone format (basic)
$phone_digits = preg_replace('/\D/', '', $phone);
if (strlen($phone_digits) < 10 || strlen($phone_digits) > 15) {
    echo json_encode(['success' => false, 'message' => 'Por favor, informe um telefone válido.']);
    exit;
}

// Validate name length
if (strlen($name) < 2 || strlen($name) > 200) {
    echo json_encode(['success' => false, 'message' => 'Nome deve ter entre 2 e 200 caracteres.']);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("
        INSERT INTO contact_submissions (name, phone, subject, message, source)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $name,
        $phone,
        $subject ?: null,
        $message ?: null,
        $source ?: 'form'
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Mensagem enviada com sucesso! Entraremos em contato em breve.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao enviar mensagem. Por favor, tente novamente mais tarde.'
    ]);
}
