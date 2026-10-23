<?php
$pageTitle = $pageTitle ?? 'Lume — Sinta o brilho. Seja a luz.';
$user = current_user();
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$baseHref = (strpos($scriptPath, '/admin/') !== false) ? '../' : ''; 
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2b5e8b">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= e($baseHref) ?>assets/css/style.css">
    <script src="<?= e($baseHref) ?>assets/js/app.js" defer></script>
</head>
<body>
<header class="topbar">
    <a class="brand" href="<?= e($baseHref) ?>index.php" aria-label="Lume, página inicial">
        <span class="brand-name">Lume</span>
        <span class="brand-tagline">Sinta o brilho. Seja a luz.</span>
    </a>
    <button class="menu-toggle" type="button" aria-label="Abrir menu" aria-expanded="false">☰</button>
    <nav class="nav-links" aria-label="Navegação principal">
        <a href="<?= e($baseHref) ?>index.php">Início</a>
        <a href="<?= e($baseHref) ?>catalogo.php">Produtos</a>
        <?php if ($user): ?>
            <a href="<?= e($baseHref) ?>meus_pedidos.php">Meus pedidos</a>
            <?php if ($user['role'] === 'admin'): ?><a href="<?= e($baseHref) ?>admin/index.php">Administração</a><?php endif; ?>
            <a href="<?= e($baseHref) ?>logout.php">Sair</a>
        <?php else: ?>
            <a href="<?= e($baseHref) ?>login.php">Entrar</a>
            <a class="nav-cta" href="<?= e($baseHref) ?>cadastro.php">Criar conta</a>
        <?php endif; ?>
        <a class="cart-link" href="<?= e($baseHref) ?>carrinho.php" aria-label="Carrinho">Sacola <span><?= cart_count() ?></span></a>
    </nav>
</header>
<main class="page-shell">
<?php if ($message = flash()): ?>
    <div class="flash" role="status"><?= e($message) ?></div>
<?php endif; ?>
