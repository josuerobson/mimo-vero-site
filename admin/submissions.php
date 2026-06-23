<?php
/**
 * Admin Submissions Page
 * SuaNet Fibra
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/site.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$admin_user = get_admin_user();
$db = getDB();

// Mark as read
if (isset($_GET['read'])) {
    $id = intval($_GET['read']);
    $db->prepare("UPDATE contact_submissions SET read_status = 1 WHERE id = ?")->execute([$id]);
    header('Location: submissions.php');
    exit;
}

// Mark all as read
if (isset($_GET['mark_all_read'])) {
    $db->exec("UPDATE contact_submissions SET read_status = 1");
    header('Location: submissions.php');
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $db->prepare("DELETE FROM contact_submissions WHERE id = ?")->execute([$id]);
    header('Location: submissions.php');
    exit;
}

// Get submissions
$filter = $_GET['filter'] ?? 'all';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

$where = '';
if ($filter === 'unread') {
    $where = 'WHERE read_status = 0';
} elseif ($filter === 'read') {
    $where = 'WHERE read_status = 1';
}

$total = $db->query("SELECT COUNT(*) FROM contact_submissions $where")->fetchColumn();
$total_pages = max(1, ceil($total / $per_page));

$submissions = $db->query("SELECT * FROM contact_submissions $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset")->fetchAll();

$unread_count = $db->query("SELECT COUNT(*) FROM contact_submissions WHERE read_status = 0")->fetchColumn();

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-inbox"></i> Mensagens</h1>
    <p>Formulários de contato recebidos</p>
</div>

<div class="page-actions">
    <div class="filter-tabs">
        <a href="submissions.php?filter=all" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">
            Todas (<?= $total ?>)
        </a>
        <a href="submissions.php?filter=unread" class="filter-tab <?= $filter === 'unread' ? 'active' : '' ?>">
            Não lidas (<?= $unread_count ?>)
        </a>
        <a href="submissions.php?filter=read" class="filter-tab <?= $filter === 'read' ? 'active' : '' ?>">
            Lidas
        </a>
    </div>
    <?php if ($unread_count > 0): ?>
    <a href="submissions.php?mark_all_read=1" class="btn btn-sm btn-outline">
        <i class="fas fa-check-double"></i> Marcar todas como lidas
    </a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($submissions)): ?>
        <p class="empty-state">
            <i class="fas fa-inbox" style="font-size:2rem;margin-bottom:10px;display:block;"></i>
            Nenhuma mensagem encontrada.
        </p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Assunto</th>
                        <th>Mensagem</th>
                        <th>Origem</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $sub): ?>
                    <tr class="<?= $sub['read_status'] ? '' : 'unread' ?>">
                        <td>
                            <?php if ($sub['read_status']): ?>
                            <span class="status-badge status-read"><i class="fas fa-envelope-open"></i></span>
                            <?php else: ?>
                            <span class="status-badge status-unread"><i class="fas fa-envelope"></i></span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($sub['name']) ?></strong></td>
                        <td>
                            <a href="tel:<?= htmlspecialchars($sub['phone']) ?>"><?= htmlspecialchars($sub['phone']) ?></a>
                        </td>
                        <td><?= htmlspecialchars($sub['subject'] ?? '-') ?></td>
                        <td class="msg-cell">
                            <?php if ($sub['message']): ?>
                            <span class="msg-preview"><?= htmlspecialchars(mb_substr($sub['message'], 0, 60)) ?><?= mb_strlen($sub['message']) > 60 ? '...' : '' ?></span>
                            <span class="msg-full" style="display:none;"><?= htmlspecialchars($sub['message']) ?></span>
                            <button class="btn-link expand-msg">ver mais</button>
                            <?php else: ?>
                            -
                            <?php endif; ?>
                        </td>
                        <td><span class="source-badge"><?= htmlspecialchars($sub['source']) ?></span></td>
                        <td><?= date('d/m/Y H:i', strtotime($sub['created_at'])) ?></td>
                        <td>
                            <div class="action-btns">
                                <?php if (!$sub['read_status']): ?>
                                <a href="submissions.php?read=<?= $sub['id'] ?>&filter=<?= $filter ?>" class="btn btn-xs btn-outline" title="Marcar como lida">
                                    <i class="fas fa-check"></i>
                                </a>
                                <?php endif; ?>
                                <a href="submissions.php?delete=<?= $sub['id'] ?>&filter=<?= $filter ?>" class="btn btn-xs btn-danger" title="Excluir" onclick="return confirm('Excluir esta mensagem?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="submissions.php?filter=<?= $filter ?>&page=<?= $page - 1 ?>" class="btn btn-sm btn-outline">&laquo; Anterior</a>
            <?php endif; ?>
            <span class="page-info">Página <?= $page ?> de <?= $total_pages ?></span>
            <?php if ($page < $total_pages): ?>
            <a href="submissions.php?filter=<?= $filter ?>&page=<?= $page + 1 ?>" class="btn btn-sm btn-outline">Próxima &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
