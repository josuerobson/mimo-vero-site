-- ============================================================
-- SuaNet Fibra - ISP Landing Page Database Schema
-- MySQL/MariaDB 10.4+ compatible
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------
-- Site Settings (key-value for all configurable text)
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) UNIQUE NOT NULL,
    `setting_value` TEXT,
    `setting_group` VARCHAR(50) DEFAULT 'general',
    `label` VARCHAR(200),
    `field_type` ENUM('text','textarea','email','phone','url','image','color','number','rich') DEFAULT 'text',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Plans
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `plans`;
CREATE TABLE `plans` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `plan_name` VARCHAR(100) NOT NULL,
    `speed` VARCHAR(50),
    `badge` VARCHAR(200),
    `mobile_data` VARCHAR(50),
    `mobile_desc` VARCHAR(200),
    `streaming_name` VARCHAR(200),
    `price_decimal` VARCHAR(10),
    `price_cents` VARCHAR(10),
    `price_period` VARCHAR(100),
    `payment_note` VARCHAR(200),
    `cta_text` VARCHAR(100) DEFAULT 'Assine já',
    `cta_link` VARCHAR(500),
    `info_details` TEXT,
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Differentials
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `differentials`;
CREATE TABLE `differentials` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `icon` VARCHAR(100),
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Contact Submissions
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `contact_submissions`;
CREATE TABLE `contact_submissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200),
    `phone` VARCHAR(30),
    `subject` VARCHAR(200),
    `message` TEXT,
    `source` VARCHAR(50) DEFAULT 'form',
    `read_status` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Admin Users
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(200),
    `last_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA
-- ============================================================

-- -----------------------------------------------------------
-- Default Admin User (password: admin123)
-- -----------------------------------------------------------
INSERT INTO `admin_users` (`username`, `password_hash`, `full_name`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador');

-- -----------------------------------------------------------
-- Site Settings
-- -----------------------------------------------------------
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_group`, `label`, `field_type`) VALUES
-- Geral
('site_name', 'SuaNet Fibra', 'general', 'Nome do Site', 'text'),
('site_logo_text', 'SuaNet', 'general', 'Texto do Logo', 'text'),
('site_logo_suffix', 'Fibra', 'general', 'Sufixo do Logo', 'text'),
('hero_tagline', 'A internet que você merece, com a velocidade que você precisa.', 'general', 'Tagline do Hero', 'textarea'),
('hero_subtitle', 'Navegue sem limites com nossa fibra óptica de alta performance.', 'general', 'Subtítulo do Hero', 'textarea'),

-- Planos
('plans_title', 'Os melhores planos para você', 'plans', 'Título da Seção de Planos', 'text'),
('plans_subtitle', 'Escolha o plano ideal para sua casa ou empresa', 'plans', 'Subtítulo da Seção de Planos', 'text'),

-- Mesh Wi-Fi
('mesh_title', 'Wi-Fi 6 com Mesh em toda a sua casa', 'mesh', 'Título da Seção Mesh', 'text'),
('mesh_description', 'Tenha a melhor experiência de Wi-Fi com tecnologia Mesh. Sinal forte e estável em todos os cômodos, sem pontos cegos. Conecte todos os seus dispositivos com a máxima velocidade.', 'mesh', 'Descrição da Seção Mesh', 'textarea'),
('mesh_badge', 'Novo', 'mesh', 'Badge da Seção Mesh', 'text'),

-- Diferenciais
('diff_title', 'Nossos diferenciais', 'differentials', 'Título da Seção de Diferenciais', 'text'),
('diff_subtitle', 'Veja por que somos a melhor escolha para sua internet', 'differentials', 'Subtítulo da Seção de Diferenciais', 'text'),

-- Por que contratar
('why_title', 'Por que contratar a SuaNet?', 'why', 'Título da Seção "Por que"', 'text'),
('why_text', 'A SuaNet Fibra é a provedora de internet que mais cresce na região. Com tecnologia de ponta e atendimento humanizado, oferecemos a melhor experiência em conectividade. Nossa rede 100% fibra óptica garante velocidade real e estabilidade para você e sua família.', 'why', 'Texto "Por que contratar"', 'textarea'),
('why_cta_text', 'Fale conosco', 'why', 'Texto do Botão CTA', 'text'),
('why_cta_link', '#contato', 'why', 'Link do Botão CTA', 'url'),

-- Contato
('phone_0800', '0800 123 4567', 'contact', 'Telefone 0800', 'phone'),
('phone_local', '(11) 1234-5678', 'contact', 'Telefone Local', 'phone'),
('phone_whatsapp', '5511912345678', 'contact', 'WhatsApp (com código do país)', 'phone'),
('whatsapp_link_assinar', 'https://wa.me/5511912345678?text=Olá! Quero assinar um plano da SuaNet!', 'contact', 'Link WhatsApp - Assinar', 'url'),
('whatsapp_link_cliente', 'https://wa.me/5511912345678?text=Olá! Já sou cliente e preciso de ajuda.', 'contact', 'Link WhatsApp - Cliente', 'url'),
('contact_email', 'contato@suanet.com.br', 'contact', 'E-mail de Contato', 'email'),

-- Empresa
('company_name', 'SuaNet Telecomunicações Ltda.', 'company', 'Nome da Empresa', 'text'),
('company_cnpj', '12.345.678/0001-99', 'company', 'CNPJ', 'text'),
('company_address', 'Rua Exemplo, 123 - Centro - São Paulo/SP', 'company', 'Endereço', 'text'),

-- Redes Sociais
('social_instagram', 'https://instagram.com/suanetfibra', 'social', 'Instagram', 'url'),
('social_facebook', 'https://facebook.com/suanetfibra', 'social', 'Facebook', 'url'),
('social_youtube', 'https://youtube.com/@suanetfibra', 'social', 'YouTube', 'url'),
('social_linkedin', '', 'social', 'LinkedIn', 'url'),

-- WhatsApp
('whatsapp_enabled', '1', 'whatsapp', 'Botão WhatsApp Ativo', 'text'),
('whatsapp_assinar_text', 'Quero assinar', 'whatsapp', 'Texto Botão Assinar', 'text'),
('whatsapp_cliente_text', 'Já sou cliente', 'whatsapp', 'Texto Botão Cliente', 'text'),

-- Rodapé
('footer_disclaimer', '© 2024 SuaNet Telecomunicações Ltda. Todos os direitos reservados. CNPJ: 12.345.678/0001-99. Velocidades contratadas podem variar conforme condições de uso e infraestrutura local.', 'footer', 'Texto de Disclaimer do Rodapé', 'textarea'),
('footer_links_title', 'Links Úteis', 'footer', 'Título dos Links do Rodapé', 'text'),
('privacy_policy_link', '#', 'footer', 'Link Política de Privacidade', 'url'),
('terms_link', '#', 'footer', 'Link Termos de Uso', 'url'),

-- Cores
('color_primary', '#0066cc', 'colors', 'Cor Primária', 'color'),
('color_accent', '#00b4d8', 'colors', 'Cor de Destaque', 'color'),
('color_dark', '#1a1a2e', 'colors', 'Cor Escura', 'color');

-- -----------------------------------------------------------
-- Plans (seed data)
-- -----------------------------------------------------------
INSERT INTO `plans` (`plan_name`, `speed`, `badge`, `mobile_data`, `mobile_desc`, `streaming_name`, `price_decimal`, `price_cents`, `price_period`, `payment_note`, `cta_text`, `cta_link`, `info_details`, `sort_order`, `active`) VALUES
('550 Mega', '550', 'Mais popular', '15 GB', '4G/5G nacional', '', '99', '99', '/mês', 'Nos 12 primeiros meses', 'Assine já', '#contato', 'Ideal para uso diário. Inclui Wi-Fi 6, suporte 24h e instalação gratuita.', 1, 1),
('700 Mega', '700', 'Custo-benefício', '25 GB', '4G/5G nacional', '', '129', '99', '/mês', 'Nos 12 primeiros meses', 'Assine já', '#contato', 'Para quem precisa de mais velocidade. Inclui Wi-Fi 6, suporte 24h e instalação gratuita.', 2, 1),
('800 Mega + Móvel', '800', 'Combo completo', '40 GB', '4G/5G nacional + ligações ilimitadas', '', '159', '99', '/mês', 'Nos 12 primeiros meses', 'Assine já', '#contato', 'Internet fibra + plano celular. Ligações ilimitadas para todo o Brasil.', 3, 1),
('800 Mega + Streaming', '800', 'Maior velocidade', '40 GB', '4G/5G nacional + ligações ilimitadas', 'Paramount+ incluso', '179', '99', '/mês', 'Nos 12 primeiros meses', 'Assine já', '#contato', 'Nosso plano premium com streaming incluso. Internet ultra-rápida + entretenimento.', 4, 1);

-- -----------------------------------------------------------
-- Differentials (seed data)
-- -----------------------------------------------------------
INSERT INTO `differentials` (`title`, `description`, `icon`, `sort_order`, `active`) VALUES
('Wi-Fi 6 de verdade', 'Roteador com tecnologia Mesh Wi-Fi 6 incluso em todos os planos para máxima cobertura.', 'fa-wifi', 1, 1),
('Ligações ilimitadas', 'Ligações ilimitadas para fixo e móvel em todo o Brasil, sem custo adicional.', 'fa-phone', 2, 1),
('Instalação ágil', 'Instalação profissional gratuita e agendada no melhor horário para você.', 'fa-bolt', 3, 1),
('Simplicidade', 'Sem letras miúdas, sem surpresas. Planos claros e transparentes.', 'fa-check-circle', 4, 1),
('Suporte 24h', 'Equipe de suporte técnica disponível 24 horas, 7 dias por semana.', 'fa-headset', 5, 1),
('Fibra óptica', 'Rede 100% fibra óptica até a sua casa para a melhor experiência de internet.', 'fa-network-wired', 6, 1),
('Serviços acessíveis', 'Planos a partir de R$99,99/mês com a melhor velocidade da região.', 'fa-tag', 7, 1),
('Autoatendimento', 'Gerencie sua conta, 2ª via e suporte pelo app ou portal do cliente.', 'fa-mobile-alt', 8, 1);

SET FOREIGN_KEY_CHECKS = 1;
