CREATE DATABASE IF NOT EXISTS lume_perfumaria
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lume_perfumaria;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(140) NOT NULL UNIQUE,
    category VARCHAR(80) NOT NULL,
    description TEXT NOT NULL,
    details VARCHAR(255) DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    accent_color VARCHAR(7) NOT NULL DEFAULT '#2b5e8b',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    customer_name VARCHAR(120) NOT NULL,
    customer_email VARCHAR(190) NOT NULL,
    address TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('Recebido','Em preparação','Enviado','Concluído','Cancelado') NOT NULL DEFAULT 'Recebido',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NULL,
    product_name VARCHAR(120) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Conta administrativa local. A senha corresponde a Lume@123.
INSERT INTO users (name, email, password_hash, role)
VALUES ('Administração Lume', 'admin@lume.local',
'$2y$12$HWM33Ueovd6CxBVDn5cQV.AxsqyJN8V1oQy/s7u.Qy8oufxVGydju', 'admin')
ON DUPLICATE KEY UPDATE email = VALUES(email);

INSERT INTO products (name, slug, category, description, details, price, stock, active, accent_color)
VALUES
('Galasias', 'galasias-po-facial', 'Maquiagem',
 'Pó facial com proposta lúdica inspirada no universo e nas galáxias.',
 '30 g • Resistente à água • Efeito matte • Tons para diferentes peles',
 39.90, 25, 1, '#c68642'),
('Chérie', 'cherie-blush', 'Maquiagem',
 'Blush elegante, pensado para valorizar diferentes estilos e faixas etárias.',
 'Blush • Paleta rosa e coral',
 34.90, 30, 1, '#ff6472'),
('Vías', 'vias-perfume-lavanda', 'Perfumaria',
 'Perfume de lavanda que combina frescor e energia para o dia a dia.',
 '150 ml • Notas de saída: lavanda fresca, bergamota e limão siciliano',
 89.90, 18, 1, '#8aa6c7')
ON DUPLICATE KEY UPDATE name = VALUES(name);
