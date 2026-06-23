<?php
/**
 * Admin Dashboard / Login Page
 * SuaNet Fibra
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/site.php';
require_once __DIR__ . '/includes/auth.php';

// If already logged in, show dashboard
if (is_logged_in()) {
    $admin_user = get_admin_user();

    // Get stats
    $db = getDB();
    $total_plans = $db->query("SELECT COUNT(*) FROM plans")->fetchColumn();
    $active_plans = $db->query("SELECT COUNT(*) FROM plans WHERE active = 1")->fetchColumn();
    $total_diffs = $db->query("SELECT COUNT(*) FROM differentials WHERE active = 1")->fetchColumn();
    $total_submissions = $db->query("SELECT COUNT(*) FROM contact_submissions")->fetchColumn();
    $unread_submissions = $db->query("SELECT COUNT(*) FROM contact_submissions WHERE read_status = 0")->fetchColumn();
    $recent_submissions = $db->query("SELECT * FROM contact_submissions ORDER BY created_at DESC LIMIT 5")->fetchAll();

    include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
    <p>Visão geral do seu site</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #e6f0ff; color: #0066cc;">
            <i class="fas fa-layer-group"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $active_plans ?> / <?= $total_plans ?></span>
            <span class="stat-label">Planos ativos</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #ecfdf5; color: #059669;">
            <i class="fas fa-star"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $total_diffs ?></span>
            <span class="stat-label">Diferenciais</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #fef3c7; color: #d97706;">
            <i class="fas fa-inbox"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $total_submissions ?></span>
            <span class="stat-label">Mensagens totais</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #fee2e2; color: #dc2626;">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $unread_submissions ?></span>
            <span class="stat-label">Não lidas</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-clock"></i> Mensagens recentes</h3>
        <a href="submissions.php" class="btn btn-sm">Ver todas</a>
    </div>
    <div class="card-body">
        <?php if (empty($recent_submissions)): ?>
        <p class="empty-state">Nenhuma mensagem recebida ainda.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Assunto</th>
                        <th>Data</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_submissions as $sub): ?>
                    <tr class="<?= $sub['read_status'] ? '' : 'unread' ?>">
                        <td><?= htmlspecialchars($sub['name']) ?></td>
                        <td><?= htmlspecialchars($sub['phone']) ?></td>
                        <td><?= htmlspecialchars($sub['subject'] ?? '-') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($sub['created_at'])) ?></td>
                        <td>
                            <?php if ($sub['read_status']): ?>
                            <span class="status-badge status-read">Lida</span>
                            <?php else: ?>
                            <span class="status-badge status-unread">Nova</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="quick-links">
    <h3>Ações rápidas</h3>
    <div class="quick-links-grid">
        <a href="settings.php" class="quick-link-card">
            <i class="fas fa-cog"></i>
            <span>Configurações</span>
        </a>
        <a href="plans.php" class="quick-link-card">
            <i class="fas fa-layer-group"></i>
            <span>Gerenciar Planos</span>
        </a>
        <a href="differentials.php" class="quick-link-card">
            <i class="fas fa-star"></i>
            <span>Diferenciais</span>
        </a>
        <a href="../" target="_blank" class="quick-link-card">
            <i class="fas fa-external-link-alt"></i>
            <span>Ver Site</span>
        </a>
    </div>
</div>

<?php
    include __DIR__ . '/includes/footer.php';
    exit;
}

// Not logged in - show login form
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $login_error = 'Preencha todos os campos.';
    } else {
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

                // Update last_login
                $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

                header('Location: index.php');
                exit;
            } else {
                $login_error = 'Usuário ou senha incorretos.';
            }
        } catch (Exception $e) {
            $login_error = 'Erro ao tentar fazer login. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin SuaNet Fibra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <span class="logo-text">SuaNet</span><span class="logo-suffix">Admin</span>
                </div>
                <p>Painel de Administração</p>
            </div>

            <?php if ($login_error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($login_error) ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['expired'])): ?>
            <div class="alert alert-warning">
                <i class="fas fa-clock"></i> Sua sessão expirou. Faça login novamente.
            </div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Usuário
                    </label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" placeholder="Seu usuário" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Senha
                    </label>
                    <input type="password" id="password" name="password" placeholder="Sua senha" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
            </form>

            <div class="login-footer">
                <a href="../">&larr; Voltar ao site</a>
            </div>
        </div>
    </div>
</body>
</html>
