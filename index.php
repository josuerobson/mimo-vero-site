<?php
/**
 * Main Frontend Page
 * SuaNet Fibra - ISP Landing Page
 */

require_once __DIR__ . '/includes/functions.php';

// Load all data
$plans = get_plans();
$differentials = get_differentials();
$settings = get_all_settings();

// Common settings
$site_name = get_setting('site_name', 'SuaNet Fibra');
$logo_text = get_setting('site_logo_text', 'SuaNet');
$logo_suffix = get_setting('site_logo_suffix', 'Fibra');
$phone_0800 = get_setting('phone_0800', '0800 123 4567');
$phone_local = get_setting('phone_local', '(11) 1234-5678');
$color_primary = get_setting('color_primary', '#0066cc');
$color_accent = get_setting('color_accent', '#00b4d8');
$color_dark = get_setting('color_dark', '#1a1a2e');
$whatsapp_link_assinar = get_setting('whatsapp_link_assinar', '#');
$whatsapp_link_cliente = get_setting('whatsapp_link_cliente', '#');
$whatsapp_assinar_text = get_setting('whatsapp_assinar_text', 'Quero assinar');
$whatsapp_cliente_text = get_setting('whatsapp_cliente_text', 'Já sou cliente');
$privacy_policy_link = get_setting('privacy_policy_link', '#');
$terms_link = get_setting('terms_link', '#');
$company_name = get_setting('company_name', 'SuaNet Telecomunicações Ltda.');
$company_cnpj = get_setting('company_cnpj', '12.345.678/0001-99');
$social_instagram = get_setting('social_instagram', '#');
$social_facebook = get_setting('social_facebook', '#');
$social_youtube = get_setting('social_youtube', '#');
$social_linkedin = get_setting('social_linkedin', '');
$footer_disclaimer = get_setting('footer_disclaimer', '');
$contact_email = get_setting('contact_email', 'contato@suanet.com.br');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($site_name) ?> - Internet fibra óptica de alta velocidade. Planos a partir de R$99,99/mês com Wi-Fi 6 incluso.">
    <meta name="theme-color" content="<?= e($color_primary) ?>">
    <title><?= e($site_name) ?> - Internet Fibra Óptica de Alta Velocidade</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        :root {
            --primary: <?= e($color_primary) ?>;
            --accent: <?= e($color_accent) ?>;
            --dark: <?= e($color_dark) ?>;
        }
    </style>
</head>
<body>

<!-- ==================== HEADER ==================== -->
<header class="header" id="header">
    <div class="container header-inner">
        <a href="#" class="logo">
            <span class="logo-text"><?= e($logo_text) ?></span><span class="logo-suffix"><?= e($logo_suffix) ?></span>
        </a>

        <nav class="nav" id="mainNav">
            <a href="#planos" class="nav-link">Planos</a>
            <a href="#diferenciais" class="nav-link">Diferenciais</a>
            <a href="#porque" class="nav-link">Por que a <?= e($logo_text) ?>?</a>
            <a href="#contato" class="nav-link">Contato</a>
        </nav>

        <div class="header-actions">
            <a href="tel:<?= preg_replace('/\D/', '', $phone_0800) ?>" class="header-phone">
                <i class="fas fa-phone"></i>
                <span><?= e($phone_0800) ?></span>
            </a>
            <a href="#planos" class="btn btn-header">Ver planos</a>
        </div>

        <button class="hamburger" id="hamburger" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <nav class="mobile-nav">
            <a href="#planos" class="mobile-nav-link">Planos</a>
            <a href="#diferenciais" class="mobile-nav-link">Diferenciais</a>
            <a href="#porque" class="mobile-nav-link">Por que a <?= e($logo_text) ?>?</a>
            <a href="#contato" class="mobile-nav-link">Contato</a>
        </nav>
        <div class="mobile-phone">
            <a href="tel:<?= preg_replace('/\D/', '', $phone_0800) ?>">
                <i class="fas fa-phone"></i> <?= e($phone_0800) ?>
            </a>
        </div>
    </div>
</header>

<!-- ==================== HERO / PLANS SECTION ==================== -->
<section class="hero" id="planos">
    <div class="hero-bg">
        <div class="hero-particles"></div>
    </div>
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title"><?= e(get_setting('hero_tagline', 'A internet que você merece')) ?></h1>
            <p class="hero-subtitle"><?= e(get_setting('hero_subtitle', '')) ?></p>
        </div>

        <div class="section-header">
            <h2 class="section-title"><?= e(get_setting('plans_title', 'Os melhores planos para você')) ?></h2>
            <p class="section-subtitle"><?= e(get_setting('plans_subtitle', '')) ?></p>
        </div>

        <div class="plans-grid">
            <?php foreach ($plans as $index => $plan): ?>
            <div class="plan-card <?= $index === 0 ? 'plan-popular' : '' ?>" data-plan-id="<?= $plan['id'] ?>">
                <?php if (!empty($plan['badge'])): ?>
                <div class="plan-badge"><?= e($plan['badge']) ?></div>
                <?php endif; ?>

                <div class="plan-header">
                    <h3 class="plan-name"><?= e($plan['plan_name']) ?></h3>
                    <div class="plan-speed">
                        <span class="speed-value"><?= e($plan['speed']) ?></span>
                        <span class="speed-unit">Mega</span>
                    </div>
                </div>

                <?php if (!empty($plan['mobile_data'])): ?>
                <div class="plan-mobile">
                    <div class="mobile-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="mobile-info">
                        <span class="mobile-data"><?= e($plan['mobile_data']) ?></span>
                        <span class="mobile-desc"><?= e($plan['mobile_desc']) ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($plan['streaming_name'])): ?>
                <div class="plan-streaming">
                    <i class="fas fa-play-circle"></i>
                    <span><?= e($plan['streaming_name']) ?></span>
                </div>
                <?php endif; ?>

                <div class="plan-price">
                    <span class="price-currency">R$</span>
                    <span class="price-decimal"><?= e($plan['price_decimal']) ?></span>
                    <span class="price-cents">,<?= e($plan['price_cents']) ?></span>
                    <span class="price-period"><?= e($plan['price_period']) ?></span>
                </div>
                <?php if (!empty($plan['payment_note'])): ?>
                <p class="plan-payment-note"><?= e($plan['payment_note']) ?></p>
                <?php endif; ?>

                <a href="<?= e($plan['cta_link'] ?: '#contato') ?>" class="btn btn-plan"><?= e($plan['cta_text'] ?: 'Assine já') ?></a>

                <button class="plan-info-btn" data-plan-id="<?= $plan['id'] ?>">
                    <i class="fas fa-info-circle"></i> Mais detalhes
                </button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==================== MESH WIFI SECTION ==================== -->
<section class="mesh-section" id="mesh">
    <div class="container">
        <div class="mesh-card">
            <div class="mesh-content">
                <?php if (get_setting('mesh_badge')): ?>
                <span class="mesh-badge"><?= e(get_setting('mesh_badge')) ?></span>
                <?php endif; ?>
                <h2 class="mesh-title"><?= e(get_setting('mesh_title', 'Wi-Fi 6 com Mesh')) ?></h2>
                <p class="mesh-description"><?= e(get_setting('mesh_description', '')) ?></p>
                <a href="#planos" class="btn btn-mesh">
                    <i class="fas fa-wifi"></i> Conheça os planos
                </a>
            </div>
            <div class="mesh-visual">
                <div class="mesh-icon-grid">
                    <div class="mesh-icon-item"><i class="fas fa-wifi"></i></div>
                    <div class="mesh-icon-item"><i class="fas fa-laptop"></i></div>
                    <div class="mesh-icon-item"><i class="fas fa-mobile-alt"></i></div>
                    <div class="mesh-icon-item"><i class="fas fa-tv"></i></div>
                    <div class="mesh-icon-item"><i class="fas fa-gamepad"></i></div>
                    <div class="mesh-icon-item"><i class="fas fa-home"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== DIFFERENTIALS SECTION ==================== -->
<section class="differentials-section" id="diferenciais">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?= e(get_setting('diff_title', 'Nossos diferenciais')) ?></h2>
            <p class="section-subtitle"><?= e(get_setting('diff_subtitle', '')) ?></p>
        </div>

        <div class="diff-grid">
            <?php foreach ($differentials as $diff): ?>
            <div class="diff-card">
                <div class="diff-icon">
                    <i class="fas <?= e($diff['icon']) ?>"></i>
                </div>
                <h3 class="diff-title"><?= e($diff['title']) ?></h3>
                <p class="diff-desc"><?= e($diff['description']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==================== WHY CHOOSE US SECTION ==================== -->
<section class="why-section" id="porque">
    <div class="container">
        <div class="why-content">
            <h2 class="why-title"><?= e(get_setting('why_title', 'Por que contratar a SuaNet?')) ?></h2>
            <div class="why-text">
                <?= nl2br(e(get_setting('why_text', ''))) ?>
            </div>
            <a href="<?= e(get_setting('why_cta_link', '#contato')) ?>" class="btn btn-why">
                <?= e(get_setting('why_cta_text', 'Fale conosco')) ?>
            </a>
        </div>
    </div>
</section>

<!-- ==================== FOOTER ==================== -->
<footer class="footer" id="contato">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="#" class="footer-logo">
                    <span class="logo-text"><?= e($logo_text) ?></span><span class="logo-suffix"><?= e($logo_suffix) ?></span>
                </a>
                <p class="footer-company-name"><?= e($company_name) ?></p>
                <p class="footer-cnpj">CNPJ: <?= e($company_cnpj) ?></p>
                <p class="footer-address"><?= e(get_setting('company_address', '')) ?></p>
                <p class="footer-email">
                    <a href="mailto:<?= e($contact_email) ?>">
                        <i class="fas fa-envelope"></i> <?= e($contact_email) ?>
                    </a>
                </p>
            </div>

            <div class="footer-links">
                <h3 class="footer-title">Links Úteis</h3>
                <ul>
                    <li><a href="#planos">Nossos Planos</a></li>
                    <li><a href="#diferenciais">Diferenciais</a></li>
                    <li><a href="#porque">Por que a <?= e($logo_text) ?>?</a></li>
                    <li><a href="<?= e($privacy_policy_link) ?>">Política de Privacidade</a></li>
                    <li><a href="<?= e($terms_link) ?>">Termos de Uso</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h3 class="footer-title">Fale Conosco</h3>
                <div class="footer-phone-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <span class="phone-label">Ligue grátis</span>
                        <a href="tel:<?= preg_replace('/\D/', '', $phone_0800) ?>"><?= e($phone_0800) ?></a>
                    </div>
                </div>
                <div class="footer-phone-item">
                    <i class="fas fa-phone-alt"></i>
                    <div>
                        <span class="phone-label">Ou ligue</span>
                        <a href="tel:<?= preg_replace('/\D/', '', $phone_local) ?>"><?= e($phone_local) ?></a>
                    </div>
                </div>

                <div class="footer-whatsapp">
                    <a href="<?= e($whatsapp_link_assinar) ?>" class="btn btn-whatsapp-footer" target="_blank" rel="noopener">
                        <i class="fab fa-whatsapp"></i> <?= e($whatsapp_assinar_text) ?>
                    </a>
                    <a href="<?= e($whatsapp_link_cliente) ?>" class="btn btn-whatsapp-footer btn-whatsapp-outline" target="_blank" rel="noopener">
                        <i class="fab fa-whatsapp"></i> <?= e($whatsapp_cliente_text) ?>
                    </a>
                </div>
            </div>

            <div class="footer-social">
                <h3 class="footer-title">Redes Sociais</h3>
                <div class="social-links">
                    <?php if ($social_instagram): ?>
                    <a href="<?= e($social_instagram) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if ($social_facebook): ?>
                    <a href="<?= e($social_facebook) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if ($social_youtube): ?>
                    <a href="<?= e($social_youtube) ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                    <?php if ($social_linkedin): ?>
                    <a href="<?= e($social_linkedin) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                </div>

                <!-- "Nós ligamos" form -->
                <div class="footer-callback">
                    <h4>Nós ligamos para você</h4>
                    <form id="callbackForm" class="callback-form" data-source="callback">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="subject" value="Solicitação de ligação">
                        <input type="hidden" name="source" value="callback">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Seu nome" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" name="phone" placeholder="Seu telefone" required>
                        </div>
                        <button type="submit" class="btn btn-callback">
                            <i class="fas fa-phone"></i> Me ligue
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p><?= e($footer_disclaimer) ?></p>
        </div>
    </div>
</footer>

<!-- ==================== CONTACT MODAL ==================== -->
<div class="modal" id="contactModal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <button class="modal-close" aria-label="Fechar"><i class="fas fa-times"></i></button>
        <div class="modal-header">
            <h3>Fale conosco</h3>
            <p>Envie sua mensagem e entraremos em contato</p>
        </div>
        <form id="contactForm" class="contact-form" data-source="modal">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="source" value="contact_modal">
            <div class="form-group">
                <label for="contactName">Nome completo</label>
                <input type="text" id="contactName" name="name" placeholder="Seu nome completo" required>
            </div>
            <div class="form-group">
                <label for="contactPhone">Telefone</label>
                <input type="tel" id="contactPhone" name="phone" placeholder="(00) 00000-0000" required>
            </div>
            <div class="form-group">
                <label for="contactSubject">Assunto</label>
                <select id="contactSubject" name="subject">
                    <option value="">Selecione...</option>
                    <option value="Assinar plano">Quero assinar um plano</option>
                    <option value="Dúvidas">Tenho dúvidas</option>
                    <option value="Suporte">Preciso de suporte</option>
                    <option value="Outro">Outro assunto</option>
                </select>
            </div>
            <div class="form-group">
                <label for="contactMessage">Mensagem</label>
                <textarea id="contactMessage" name="message" rows="4" placeholder="Como podemos ajudar?"></textarea>
            </div>
            <button type="submit" class="btn btn-submit">
                <i class="fas fa-paper-plane"></i> Enviar mensagem
            </button>
            <div class="form-feedback" id="contactFeedback"></div>
        </form>
    </div>
</div>

<!-- ==================== PLAN DETAILS MODAL ==================== -->
<div class="modal" id="planModal">
    <div class="modal-overlay"></div>
    <div class="modal-content modal-plan">
        <button class="modal-close" aria-label="Fechar"><i class="fas fa-times"></i></button>
        <div id="planModalContent">
            <!-- Filled by JS -->
        </div>
    </div>
</div>

<!-- ==================== WHATSAPP FLOATING BUTTON ==================== -->
<?php if (setting_enabled('whatsapp_enabled')): ?>
<div class="whatsapp-float" id="whatsappFloat">
    <div class="whatsapp-menu" id="whatsappMenu">
        <a href="<?= e($whatsapp_link_assinar) ?>" class="whatsapp-menu-item" target="_blank" rel="noopener">
            <i class="fas fa-shopping-cart"></i>
            <span><?= e($whatsapp_assinar_text) ?></span>
        </a>
        <a href="<?= e($whatsapp_link_cliente) ?>" class="whatsapp-menu-item" target="_blank" rel="noopener">
            <i class="fas fa-user"></i>
            <span><?= e($whatsapp_cliente_text) ?></span>
        </a>
    </div>
    <button class="whatsapp-btn" id="whatsappBtn" aria-label="WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </button>
</div>
<?php endif; ?>

<!-- Plan data for JS -->
<script>
    window.plansData = <?= json_encode($plans, JSON_UNESCAPED_UNICODE) ?>;
</script>

<!-- Main JS -->
<script src="assets/js/main.js"></script>

</body>
</html>
