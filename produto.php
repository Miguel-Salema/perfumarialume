<?php
require_once __DIR__ . '/includes/functions.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = db()->prepare('SELECT * FROM products WHERE id = ? AND active = 1');
$stmt->execute([$id ?: 0]);
$product = $stmt->fetch();
if (!$product) { http_response_code(404); $pageTitle = 'Produto não encontrado | Lume'; }
else { $pageTitle = $product['name'] . ' | Lume'; }
require __DIR__ . '/includes/header.php';
?>
<?php if (!$product): ?>
<section class="empty-state"><h1>Não encontramos esse produto.</h1><a class="button button-primary" href="catalogo.php">Voltar ao catálogo</a></section>
<?php else: ?>
<section class="product-detail">
    <div class="detail-art product-art art-<?= (int)$product['id'] ?>" style="--product-accent:<?= e($product['accent_color']) ?>">
        <span class="product-label">LUME</span><span class="package-name"><?= e($product['name']) ?></span><span class="package-type"><?= e($product['category']) ?></span><span class="package-base"><?= e($product['id'] == 1 ? '30 g' : ($product['id'] == 3 ? '150 ml' : 'BLUSH')) ?></span>
    </div>
    <div class="detail-copy">
        <a class="back-link" href="catalogo.php">← Voltar aos produtos</a>
        <span class="eyebrow"><?= e($product['category']) ?> • LUME</span>
        <h1><?= e($product['name']) ?></h1>
        <p class="detail-description"><?= e($product['description']) ?></p>
        <?php if ($product['details']): ?><p class="detail-extra"><?= e($product['details']) ?></p><?php endif; ?>
        <div class="detail-price"><?= money($product['price']) ?></div>
        <?php if ((int)$product['stock'] > 0): ?>
        <p class="stock-note">Disponível • <?= (int)$product['stock'] ?> em estoque</p>
        <form class="add-form" action="carrinho.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <label for="quantity">Quantidade</label>
            <input id="quantity" type="number" name="quantity" min="1" max="<?= (int)$product['stock'] ?>" value="1">
            <button class="button button-primary" type="submit">Adicionar à sacola</button>
        </form>
        <?php else: ?><p class="stock-note out-of-stock">Produto indisponível no momento.</p><?php endif; ?>
        <div class="brand-note"><strong>Lume</strong><span>Sinta o brilho. Seja a luz.</span></div>
    </div>
</section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
