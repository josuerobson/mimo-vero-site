<?php
/**
 * Admin Settings Page
 * SuaNet Fibra
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/site.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$admin_user = get_admin_user();
$db = getDB();

// Get all settings grouped
$settings = get_all_settings();

// Group labels
$group_labels = [
    'general' => ['label' => 'Geral', 'icon' => 'fa-cog'],
    'plans' => ['label' => 'Planos', 'icon' => 'fa-layer-group'],
    'mesh' => ['label' => 'Mesh Wi-Fi', 'icon' => 'fa-wifi'],
    'differentials' => ['label' => 'Diferenciais', 'icon' => 'fa-star'],
    'why' => ['label' => 'Por que contratar', 'icon' => 'fa-question-circle'],
    'contact' => ['label' => 'Contato', 'icon' => 'fa-phone'],
    'company' => ['label' => 'Empresa', 'icon' => 'fa-building'],
    'social' => ['label' => 'Redes Sociais', 'icon' => 'fa-share-alt'],
    'whatsapp' => ['label' => 'WhatsApp', 'icon' => 'fab fa-whatsapp'],
    'footer' => ['label' => 'Rodapé', 'icon' => 'fa-shoe-prints'],
    'colors' => ['label' => 'Cores', 'icon' => 'fa-palette'],
];

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-cog"></i> Configurações</h1>
    <p>Gerencie todas as configurações do site</p>
</div>

<div class="settings-container">
    <?php foreach ($settings as $group => $items): ?>
    <?php $group_info = $group_labels[$group] ?? ['label' => ucfirst($group), 'icon' => 'fa-cog']; ?>
    <div class="card settings-group" id="group-<?= htmlspecialchars($group) ?>">
        <div class="card-header">
            <h3><i class="fas <?= htmlspecialchars($group_info['icon']) ?>"></i> <?= htmlspecialchars($group_info['label']) ?></h3>
        </div>
        <div class="card-body">
            <?php foreach ($items as $item): ?>
            <div class="setting-row" data-key="<?= htmlspecialchars($item['setting_key']) ?>">
                <div class="setting-label">
                    <label for="setting-<?= htmlspecialchars($item['setting_key']) ?>">
                        <?= htmlspecialchars($item['label'] ?? $item['setting_key']) ?>
                    </label>
                    <small class="setting-key"><?= htmlspecialchars($item['setting_key']) ?></small>
                </div>
                <div class="setting-control">
                    <?php
                    $key = htmlspecialchars($item['setting_key']);
                    $val = htmlspecialchars($item['setting_value'] ?? '');
                    $type = $item['field_type'] ?? 'text';
                    ?>
                    <?php if ($type === 'textarea' || $type === 'rich'): ?>
                    <textarea id="setting-<?= $key ?>" data-key="<?= $key ?>" class="setting-input" rows="3"><?= $val ?></textarea>
                    <?php elseif ($type === 'color'): ?>
                    <div class="color-input-wrap">
                        <input type="color" id="setting-<?= $key ?>" data-key="<?= $key ?>" class="setting-input setting-color" value="<?= $val ?>">
                        <input type="text" class="color-text" value="<?= $val ?>" data-for="setting-<?= $key ?>">
                    </div>
                    <?php elseif ($type === 'url'): ?>
                    <input type="url" id="setting-<?= $key ?>" data-key="<?= $key ?>" class="setting-input" value="<?= $val ?>">
                    <?php elseif ($type === 'email'): ?>
                    <input type="email" id="setting-<?= $key ?>" data-key="<?= $key ?>" class="setting-input" value="<?= $val ?>">
                    <?php else: ?>
                    <input type="text" id="setting-<?= $key ?>" data-key="<?= $key ?>" class="setting-input" value="<?= $val ?>">
                    <?php endif; ?>
                    <button class="btn btn-sm btn-save-setting" data-key="<?= $key ?>">
                        <i class="fas fa-save"></i>
                    </button>
                    <span class="save-status" id="status-<?= $key ?>"></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
