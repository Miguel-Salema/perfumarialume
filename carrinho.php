<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');
    $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($action === 'add' && $id) {
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $stmt = db()->prepare('SELECT id, stock, active FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if ($product && $product['active'] && $product['stock'] > 0) {
            $existing = (int)($_SESSION['cart'][$id] ?? 0);
            $_SESSION['cart'][$id] = min($existing + $quantity, (int)$product['stock']);
            flash('Produto adicionado à sacola.');
        } else flash('Este produto não está disponível.');
    } elseif ($action === 'update') {
        foreach (($_POST['quantities'] ?? []) as $productId => $quantity) {
            $productId = (int)$productId;
            $quantity = (int)$quantity;
            if ($quantity <= 0) unset($_SESSION['cart'][$productId]);
            else {
                $stmt = db()->prepare('SELECT stock FROM products WHERE id = ? AND active = 1');
                $stmt->execute([$productId]);
                $stock = $stmt->fetchColumn();
                if ($stock !== false) $_SESSION['cart'][$productId] = min($quantity, (int)$stock);
                else unset($_SESSION['cart'][$productId]);
            }
        }
        flash('Sacola atualizada.');
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][(int)($_POST['product_id'] ?? 0)]);
        flash('Produto removido da sacola.');
    }
    redirect('carrinho.php');
}

$pageTitle = 'Sua sacola | Lume';
$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0.0;
if ($cart) {
    $ids = array_map('intval', array_keys($cart));
    $marks = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT * FROM products WHERE active = 1 AND id IN ($marks)");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $product) {
        $quantity = min((int)$cart[$product['id']], (int)$product['stock']);
        if ($quantity < 1) { unset($_SESSION['cart'][$product['id']]); continue; }
        $_SESSION['cart'][$product['id']] = $quantity;
        $product['quantity'] = $quantity;
        $product['subtotal'] = $quantity * (float)$product['price'];
        $total += $product['subtotal'];
        $items[] = $product;
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading compact-heading"><span class="eyebrow">SUA SELEÇÃO</span><h1>Sua <em>sacola.</em></h1><p>Revise seus produtos antes de finalizar.</p></section>
<?php if (!$items): ?>
<div class="empty-state"><div class="empty-icon">✧</div><h2>Sua sacola está esperando por você</h2><p>Descubra algo que combine com a sua luz.</p><a class="button button-primary" href="catalogo.php">Explorar produtos</a></div>
<?php else: ?>
<div class="cart-layout">
    <form method="post" class="cart-items">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="update">
        <?php foreach ($items as $item): ?>
        <article class="cart-item">
            <div class="cart-thumb" style="--product-accent:<?= e($item['accent_color']) ?>"><span><?= e($item['name']) ?></span></div>
            <div class="cart-item-copy"><h3><?= e($item['name']) ?></h3><p><?= e($item['category']) ?></p><strong><?= money($item['price']) ?></strong></div>
            <label class="quantity-label">Qtd.<input type="number" name="quantities[<?= (int)$item['id'] ?>]" min="0" max="<?= (int)$item['stock'] ?>" value="<?= (int)$item['quantity'] ?>"></label>
            <strong class="item-subtotal"><?= money($item['subtotal']) ?></strong>
        </article>
        <?php endforeach; ?>
        <button class="button button-outline" type="submit">Atualizar sacola</button>
    </form>
    <aside class="summary-card"><h2>Resumo do pedido</h2><div class="summary-row"><span>Subtotal</span><strong><?= money($total) ?></strong></div><div class="summary-row"><span>Entrega</span><span>Calculada à parte</span></div><div class="summary-total"><span>Total</span><strong><?= money($total) ?></strong></div><p class="muted small">Checkout demonstrativo: nenhum pagamento será cobrado.</p><a class="button button-primary button-full" href="finalizar.php">Finalizar pedido</a><a class="text-link centered-link" href="catalogo.php">Continuar comprando</a></aside>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
