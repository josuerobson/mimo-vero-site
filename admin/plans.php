<?php
/**
 * Admin Plans Page
 * SuaNet Fibra
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/site.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$admin_user = get_admin_user();
$db = getDB();

// Handle form submission for new/edit plan
$edit_plan = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verify_csrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $error = 'Token inválido.';
    } else {
        $action = $_POST['action'];

        if ($action === 'save') {
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
                $error = 'Nome do plano é obrigatório.';
            } else {
                if ($id > 0) {
                    // Update
                    $sql = "UPDATE plans SET plan_name=:plan_name, speed=:speed, badge=:badge, mobile_data=:mobile_data, mobile_desc=:mobile_desc, streaming_name=:streaming_name, price_decimal=:price_decimal, price_cents=:price_cents, price_period=:price_period, payment_note=:payment_note, cta_text=:cta_text, cta_link=:cta_link, info_details=:info_details, sort_order=:sort_order, active=:active WHERE id=:id";
                    $data['id'] = $id;
                } else {
                    // Insert
                    $sql = "INSERT INTO plans (plan_name, speed, badge, mobile_data, mobile_desc, streaming_name, price_decimal, price_cents, price_period, payment_note, cta_text, cta_link, info_details, sort_order, active) VALUES (:plan_name, :speed, :badge, :mobile_data, :mobile_desc, :streaming_name, :price_decimal, :price_cents, :price_period, :payment_note, :cta_text, :cta_link, :info_details, :sort_order, :active)";
                }
                $stmt = $db->prepare($sql);
                $stmt->execute($data);
                $success = $id > 0 ? 'Plano atualizado com sucesso!' : 'Plano criado com sucesso!';
            }
        }
    }
}

// Get all plans
$plans = $db->query("SELECT * FROM plans ORDER BY sort_order ASC, id ASC")->fetchAll();

// Get plan for editing
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $db->prepare("SELECT * FROM plans WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_plan = $stmt->fetch();
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-layer-group"></i> Planos</h1>
    <p>Gerencie os planos de internet</p>
</div>

<?php if (!empty($success)): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Plan Form -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-<?= $edit_plan ? 'edit' : 'plus' ?>"></i> <?= $edit_plan ? 'Editar Plano' : 'Novo Plano' ?></h3>
        <?php if ($edit_plan): ?>
        <a href="plans.php" class="btn btn-sm btn-outline">Cancelar</a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <form method="POST" class="plan-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save">
            <?php if ($edit_plan): ?>
            <input type="hidden" name="id" value="<?= $edit_plan['id'] ?>">
            <?php endif; ?>

            <div class="form-grid">
                <div class="form-group">
                    <label>Nome do Plano *</label>
                    <input type="text" name="plan_name" value="<?= htmlspecialchars($edit_plan['plan_name'] ?? '') ?>" required placeholder="Ex: 550 Mega">
                </div>
                <div class="form-group">
                    <label>Velocidade (Mbps)</label>
                    <input type="text" name="speed" value="<?= htmlspecialchars($edit_plan['speed'] ?? '') ?>" placeholder="Ex: 550">
                </div>
                <div class="form-group">
                    <label>Badge</label>
                    <input type="text" name="badge" value="<?= htmlspecialchars($edit_plan['badge'] ?? '') ?>" placeholder="Ex: Mais popular">
                </div>
                <div class="form-group">
                    <label>Dados móveis</label>
                    <input type="text" name="mobile_data" value="<?= htmlspecialchars($edit_plan['mobile_data'] ?? '') ?>" placeholder="Ex: 15 GB">
                </div>
                <div class="form-group">
                    <label>Descrição dados móveis</label>
                    <input type="text" name="mobile_desc" value="<?= htmlspecialchars($edit_plan['mobile_desc'] ?? '') ?>" placeholder="Ex: 4G/5G nacional">
                </div>
                <div class="form-group">
                    <label>Streaming</label>
                    <input type="text" name="streaming_name" value="<?= htmlspecialchars($edit_plan['streaming_name'] ?? '') ?>" placeholder="Ex: Paramount+ incluso">
                </div>
                <div class="form-group">
                    <label>Preço (inteiro)</label>
                    <input type="text" name="price_decimal" value="<?= htmlspecialchars($edit_plan['price_decimal'] ?? '') ?>" placeholder="Ex: 99">
                </div>
                <div class="form-group">
                    <label>Preço (centavos)</label>
                    <input type="text" name="price_cents" value="<?= htmlspecialchars($edit_plan['price_cents'] ?? '99') ?>" placeholder="Ex: 99">
                </div>
                <div class="form-group">
                    <label>Período do preço</label>
                    <input type="text" name="price_period" value="<?= htmlspecialchars($edit_plan['price_period'] ?? '/mês') ?>" placeholder="Ex: /mês">
                </div>
                <div class="form-group">
                    <label>Nota de pagamento</label>
                    <input type="text" name="payment_note" value="<?= htmlspecialchars($edit_plan['payment_note'] ?? '') ?>" placeholder="Ex: Nos 12 primeiros meses">
                </div>
                <div class="form-group">
                    <label>Texto do botão</label>
                    <input type="text" name="cta_text" value="<?= htmlspecialchars($edit_plan['cta_text'] ?? 'Assine já') ?>">
                </div>
                <div class="form-group">
                    <label>Link do botão</label>
                    <input type="text" name="cta_link" value="<?= htmlspecialchars($edit_plan['cta_link'] ?? '') ?>" placeholder="#contato">
                </div>
                <div class="form-group">
                    <label>Ordem</label>
                    <input type="number" name="sort_order" value="<?= intval($edit_plan['sort_order'] ?? 0) ?>">
                </div>
                <div class="form-group form-group-checkbox">
                    <label>
                        <input type="checkbox" name="active" value="1" <?= ($edit_plan['active'] ?? 1) ? 'checked' : '' ?>>
                        Ativo
                    </label>
                </div>
            </div>
            <div class="form-group form-group-full">
                <label>Detalhes / Informações</label>
                <textarea name="info_details" rows="3" placeholder="Informações adicionais sobre o plano"><?= htmlspecialchars($edit_plan['info_details'] ?? '') ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?= $edit_plan ? 'Atualizar' : 'Criar' ?> Plano
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Plans List -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Planos cadastrados</h3>
    </div>
    <div class="card-body">
        <?php if (empty($plans)): ?>
        <p class="empty-state">Nenhum plano cadastrado.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ordem</th>
                        <th>Nome</th>
                        <th>Velocidade</th>
                        <th>Preço</th>
                        <th>Badge</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plans as $plan): ?>
                    <tr class="<?= $plan['active'] ? '' : 'inactive-row' ?>">
                        <td><?= $plan['sort_order'] ?></td>
                        <td><strong><?= htmlspecialchars($plan['plan_name']) ?></strong></td>
                        <td><?= htmlspecialchars($plan['speed']) ?> Mega</td>
                        <td>R$ <?= htmlspecialchars($plan['price_decimal']) ?>,<?= htmlspecialchars($plan['price_cents']) ?></td>
                        <td><?= htmlspecialchars($plan['badge'] ?? '-') ?></td>
                        <td>
                            <button class="btn btn-xs toggle-active" data-table="plans" data-id="<?= $plan['id'] ?>" data-active="<?= $plan['active'] ?>">
                                <?php if ($plan['active']): ?>
                                <span class="status-badge status-active">Ativo</span>
                                <?php else: ?>
                                <span class="status-badge status-inactive">Inativo</span>
                                <?php endif; ?>
                            </button>
                        </td>
                        <td>
                            <a href="plans.php?edit=<?= $plan['id'] ?>" class="btn btn-xs btn-outline" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-xs btn-danger delete-item" data-table="plans" data-id="<?= $plan['id'] ?>" title="Excluir">
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
