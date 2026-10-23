<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
$pageTitle = 'Meus pedidos | Lume';
$stmt = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([current_user()['id']]);
$orders = $stmt->fetchAll();
$itemStmt = db()->prepare('SELECT product_name, unit_price, quantity FROM order_items WHERE order_id = ?');
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading compact-heading"><span class="eyebrow">SUA EXPERIÊNCIA LUME</span><h1>Meus <em>pedidos.</em></h1><p>Acompanhe os pedidos registrados em sua conta.</p></section>
<?php if (!$orders): ?><div class="empty-state"><h2>Você ainda não tem pedidos</h2><p>Seus próximos favoritos estão esperando.</p><a class="button button-primary" href="catalogo.php">Explorar produtos</a></div>
<?php else: ?>
<div class="orders-list">
<?php foreach ($orders as $order): ?>
<article class="order-card"><div class="order-head"><div><span class="eyebrow">PEDIDO #<?= (int)$order['id'] ?></span><h2><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></h2></div><span class="status-pill status-<?= strtolower(str_replace(' ', '-', $order['status'])) ?>"><?= e($order['status']) ?></span></div>
<?php $itemStmt->execute([$order['id']]); foreach ($itemStmt->fetchAll() as $item): ?><div class="order-item"><span><?= e($item['product_name']) ?> × <?= (int)$item['quantity'] ?></span><strong><?= money((float)$item['unit_price'] * (int)$item['quantity']) ?></strong></div><?php endforeach; ?>
<div class="order-total"><span>Total do pedido</span><strong><?= money($order['total']) ?></strong></div><p class="muted small">Entrega: <?= e($order['address']) ?></p></article>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
