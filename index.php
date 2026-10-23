<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Lume — Sinta o brilho. Seja a luz.';
$products = db()->query('SELECT * FROM products WHERE active = 1 ORDER BY id ASC LIMIT 3')->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow">PERFUMARIA &amp; COSMÉTICOS</span>
        <h1>Seu brilho.<br><em>Sua luz.</em></h1>
        <p>Descubra fragrâncias e beleza que acompanham a sua essência. Elegância, inovação e personalidade em cada detalhe.</p>
        <a class="button button-light" href="catalogo.php">Descobrir produtos <span aria-hidden="true">→</span></a>
    </div>
    <div class="hero-art" aria-label="Composição ilustrativa de produtos Lume">
        <div class="orbit orbit-one"></div><div class="orbit orbit-two"></div>
        <div class="hero-bottle"><span>Lume</span><small>EAU DE PARFUM</small></div>
        <div class="spark spark-one">✦</div><div class="spark spark-two">✧</div>
        <div class="hero-caption">A beleza de ser você.</div>
    </div>
</section>

<section class="section intro-section">
    <div class="section-heading">
        <span class="eyebrow">FEITOS PARA VOCÊ</span>
        <h2>Pequenos detalhes.<br><em>Grandes sensações.</em></h2>
    </div>
    <p class="intro-text">A Lume celebra diferentes estilos e públicos, unindo uma identidade sofisticada a produtos cheios de personalidade.</p>
</section>

<section class="section products-section">
    <div class="section-title-row">
        <div><span class="eyebrow">ESCOLHAS LUME</span><h2>Conheça nossos destaques</h2></div>
        <a class="text-link" href="catalogo.php">Ver todos os produtos ↗</a>
    </div>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <article class="product-card">
                <a class="product-art art-<?= e((string)$product['id']) ?>" href="produto.php?id=<?= (int)$product['id'] ?>" style="--product-accent:<?= e($product['accent_color']) ?>">
                    <span class="product-label">LUME</span>
                    <span class="package-name"><?= e($product['name']) ?></span>
                    <span class="package-type"><?= e($product['category']) ?></span>
                    <span class="package-base"><?= $product['id'] == 1 ? '30 g' : ($product['id'] == 3 ? '150 ml' : 'BLUSH') ?></span>
                </a>
                <div class="product-info">
                    <div><h3><?= e($product['name']) ?></h3><p><?= e($product['category']) ?></p></div>
                    <strong><?= money($product['price']) ?></strong>
                </div>
                <a class="card-link" href="produto.php?id=<?= (int)$product['id'] ?>">Ver detalhes <span>→</span></a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="quote-banner">
    <span class="quote-mark">“</span>
    <p>Sinta o brilho. <em>Seja a luz.</em></p>
    <span class="quote-line"></span>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
