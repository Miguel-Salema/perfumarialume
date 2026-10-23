<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Produtos | Lume';
$search = trim((string)($_GET['q'] ?? ''));
$sql = 'SELECT * FROM products WHERE active = 1';
$params = [];
if ($search !== '') {
    $sql .= ' AND (name LIKE ? OR category LIKE ? OR description LIKE ?)';
    $like = '%' . $search . '%';
    $params = [$like, $like, $like];
}
$sql .= ' ORDER BY name';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <span class="eyebrow">A COLEÇÃO LUME</span>
    <h1>Encontre o seu <em>brilho.</em></h1>
    <p>Explore nossa seleção de perfumaria e cosméticos.</p>
</section>
<form class="search-form" method="get" action="catalogo.php">
    <label class="sr-only" for="q">Buscar produto</label>
    <input id="q" name="q" type="search" placeholder="Busque por produto ou categoria..." value="<?= e($search) ?>">
    <button class="button button-primary" type="submit">Buscar</button>
</form>
<?php if (!$products): ?>
    <div class="empty-state"><h2>Nenhum produto encontrado</h2><p>Tente outro nome ou confira todos os produtos.</p><a class="button button-primary" href="catalogo.php">Ver catálogo completo</a></div>
<?php else: ?>
<div class="product-grid catalog-grid">
    <?php foreach ($products as $product): ?>
    <article class="product-card">
        <a class="product-art art-<?= (int)$product['id'] ?>" href="produto.php?id=<?= (int)$product['id'] ?>" style="--product-accent:<?= e($product['accent_color']) ?>">
            <span class="product-label">LUME</span><span class="package-name"><?= e($product['name']) ?></span>
            <span class="package-type"><?= e($product['category']) ?></span><span class="package-base"><?= e($product['id'] == 1 ? '30 g' : ($product['id'] == 3 ? '150 ml' : 'BLUSH')) ?></span>
        </a>
        <div class="product-info"><div><h3><?= e($product['name']) ?></h3><p><?= e($product['category']) ?></p></div><strong><?= money($product['price']) ?></strong></div>
        <a class="card-link" href="produto.php?id=<?= (int)$product['id'] ?>">Ver detalhes <span>→</span></a>
    </article>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
