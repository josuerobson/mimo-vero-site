<?php
/**
 * Admin Header
 * SuaNet Fibra
 */
require_once __DIR__ . '/auth.php';
require_auth();

$admin_user = get_admin_user();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SuaNet Fibra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="index.php" class="sidebar-logo">
                    <span class="logo-text">SuaNet</span><span class="logo-suffix">Admin</span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <a href="index.php" class="sidebar-link <?= $currentPage === 'index' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="settings.php" class="sidebar-link <?= $currentPage === 'settings' ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i>
                    <span>Configurações</span>
                </a>
                <a href="plans.php" class="sidebar-link <?= $currentPage === 'plans' ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i>
                    <span>Planos</span>
                </a>
                <a href="differentials.php" class="sidebar-link <?= $currentPage === 'differentials' ? 'active' : '' ?>">
                    <i class="fas fa-star"></i>
                    <span>Diferenciais</span>
                </a>
                <a href="submissions.php" class="sidebar-link <?= $currentPage === 'submissions' ? 'active' : '' ?>">
                    <i class="fas fa-inbox"></i>
                    <span>Mensagens</span>
                    <?php
                    try {
                        $db = getDB();
                        $cnt = $db->query("SELECT COUNT(*) FROM contact_submissions WHERE read_status = 0")->fetchColumn();
                        if ($cnt > 0) echo '<span class="badge">' . $cnt . '</span>';
                    } catch (Exception $e) {}
                    ?>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="../" target="_blank" class="sidebar-link">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Ver site</span>
                </a>
                <a href="api/logout.php" class="sidebar-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sair</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="admin-header-right">
                    <span class="admin-user">
                        <i class="fas fa-user-circle"></i>
                        <?= htmlspecialchars($admin_user['full_name'] ?? 'Admin') ?>
                    </span>
                </div>
            </header>
            <div class="admin-content">
