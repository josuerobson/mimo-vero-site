<?php
/**
 * Admin Differentials Page
 * SuaNet Fibra
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/site.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$admin_user = get_admin_user();
$db = getDB();

$edit_diff = null;
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $error = 'Token inválido.';
    } else {
        $action = $_POST['action'];

        if ($action === 'save') {
            $id = intval($_POST['id'] ?? 0);
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'icon' => trim($_POST['icon'] ?? ''),
                'sort_order' => intval($_POST['sort_order'] ?? 0),
                'active' => isset($_POST['active']) ? 1 : 0,
            ];

            if (empty($data['title'])) {
                $error = 'Título é obrigatório.';
            } else {
                if ($id > 0) {
                    $sql = "UPDATE differentials SET title=:title, description=:description, icon=:icon, sort_order=:sort_order, active=:active WHERE id=:id";
                    $data['id'] = $id;
                } else {
                    $sql = "INSERT INTO differentials (title, description, icon, sort_order, active) VALUES (:title, :description, :icon, :sort_order, :active)";
                }
                $stmt = $db->prepare($sql);
                $stmt->execute($data);
                $success = $id > 0 ? 'Diferencial atualizado!' : 'Diferencial criado!';
            }
        }
    }
}

// Get all differentials
$differentials = $db->query("SELECT * FROM differentials ORDER BY sort_order ASC, id ASC")->fetchAll();

// Get for editing
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM differentials WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $edit_diff = $stmt->fetch();
}

// Common Font Awesome icons for suggestions
$icon_suggestions = [
    'fa-wifi', 'fa-phone', 'fa-bolt', 'fa-check-circle', 'fa-headset',
    'fa-network-wired', 'fa-tag', 'fa-mobile-alt', 'fa-shield-alt',
    'fa-clock', 'fa-globe', 'fa-rocket', 'fa-heart', 'fa-tachometer-alt',
    'fa-lock', 'fa-users', 'fa-tools', 'fa-handshake'
];

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-star"></i> Diferenciais</h1>
    <p>Gerencie os diferenciais exibidos no site</p>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Differential Form -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-<?= $edit_diff ? 'edit' : 'plus' ?>"></i> <?= $edit_diff ? 'Editar Diferencial' : 'Novo Diferencial' ?></h3>
        <?php if ($edit_diff): ?>
        <a href="differentials.php" class="btn btn-sm btn-outline">Cancelar</a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <form method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save">
            <?php if ($edit_diff): ?>
            <input type="hidden" name="id" value="<?= $edit_diff['id'] ?>">
            <?php endif; ?>

            <div class="form-grid">
                <div class="form-group">
                    <label>Título *</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($edit_diff['title'] ?? '') ?>" required placeholder="Ex: Wi-Fi 6 de verdade">
                </div>
                <div class="form-group">
                    <label>Ícone (Font Awesome)</label>
                    <div class="icon-input-wrap">
                        <input type="text" name="icon" id="iconInput" value="<?= htmlspecialchars($edit_diff['icon'] ?? '') ?>" placeholder="Ex: fa-wifi">
                        <span class="icon-preview" id="iconPreview">
                            <?php if (!empty($edit_diff['icon'])): ?>
                            <i class="fas <?= htmlspecialchars($edit_diff['icon']) ?>"></i>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="icon-suggestions">
                        <?php foreach ($icon_suggestions as $icon): ?>
                        <button type="button" class="icon-suggestion <?= ($edit_diff['icon'] ?? '') === $icon ? 'selected' : '' ?>" data-icon="<?= $icon ?>" title="<?= $icon ?>">
                            <i class="fas <?= $icon ?>"></i>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Ordem</label>
                    <input type="number" name="sort_order" value="<?= intval($edit_diff['sort_order'] ?? 0) ?>">
                </div>
                <div class="form-group form-group-checkbox">
                    <label>
                        <input type="checkbox" name="active" value="1" <?= ($edit_diff['active'] ?? 1) ? 'checked' : '' ?>>
                        Ativo
                    </label>
                </div>
            </div>
            <div class="form-group form-group-full">
                <label>Descrição</label>
                <textarea name="description" rows="3" placeholder="Descrição do diferencial"><?= htmlspecialchars($edit_diff['description'] ?? '') ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?= $edit_diff ? 'Atualizar' : 'Criar' ?> Diferencial
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Differentials List -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Diferenciais cadastrados</h3>
    </div>
    <div class="card-body">
        <?php if (empty($differentials)): ?>
        <p class="empty-state">Nenhum diferencial cadastrado.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ordem</th>
                        <th>Ícone</th>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($differentials as $diff): ?>
                    <tr class="<?= $diff['active'] ? '' : 'inactive-row' ?>">
                        <td><?= $diff['sort_order'] ?></td>
                        <td><i class="fas <?= htmlspecialchars($diff['icon'] ?? '') ?>" style="font-size:1.2rem;color:var(--primary);"></i></td>
                        <td><strong><?= htmlspecialchars($diff['title']) ?></strong></td>
                        <td class="desc-cell"><?= htmlspecialchars(mb_substr($diff['description'] ?? '', 0, 80)) ?><?= mb_strlen($diff['description'] ?? '') > 80 ? '...' : '' ?></td>
                        <td>
                            <button class="btn btn-xs toggle-active" data-table="differentials" data-id="<?= $diff['id'] ?>" data-active="<?= $diff['active'] ?>">
                                <?php if ($diff['active']): ?>
                                <span class="status-badge status-active">Ativo</span>
                                <?php else: ?>
                                <span class="status-badge status-inactive">Inativo</span>
                                <?php endif; ?>
                            </button>
                        </td>
                        <td>
                            <a href="differentials.php?edit=<?= $diff['id'] ?>" class="btn btn-xs btn-outline" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-xs btn-danger delete-item" data-table="differentials" data-id="<?= $diff['id'] ?>" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
