<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$pageTitle = 'Pedidos | Administração Lume';
$statuses = ['Recebido', 'Em preparação', 'Enviado', 'Concluído', 'Cancelado'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['order_id'] ?? 0);
    $status = (string)($_POST['status'] ?? '');
    if (in_array($status, $statuses, true)) {
        $stmt = db()->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        flash('Status do pedido atualizado.');
    }
    redirect('pedidos.php');
}
$orders = db()->query('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll();
$itemStmt = db()->prepare('SELECT product_name, unit_price, quantity FROM order_items WHERE order_id = ?');
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading compact-heading"><span class="eyebrow">PAINEL INTERNO</span><h1>Pedidos <em>recebidos.</em></h1><p>Consulte pedidos e atualize o andamento.</p></section>
<?php if (!$orders): ?><div class="empty-state"><h2>Nenhum pedido ainda</h2><p>Quando os clientes finalizarem compras demonstrativas, elas aparecerão aqui.</p></div><?php else: ?>
<div class="orders-list"><?php foreach ($orders as $order): ?><article class="order-card"><div class="order-head"><div><span class="eyebrow">PEDIDO #<?= (int)$order['id'] ?></span><h2><?= e($order['customer_name']) ?></h2><p class="muted"><?= e($order['customer_email']) ?> • <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p></div><strong class="order-admin-total"><?= money($order['total']) ?></strong></div>
<?php $itemStmt->execute([$order['id']]); foreach ($itemStmt->fetchAll() as $item): ?><div class="order-item"><span><?= e($item['product_name']) ?> × <?= (int)$item['quantity'] ?></span><strong><?= money((float)$item['unit_price'] * (int)$item['quantity']) ?></strong></div><?php endforeach; ?>
<div class="order-admin-bottom"><span class="muted small">Entrega: <?= e($order['address']) ?></span><form method="post" class="status-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>"><label class="sr-only" for="status-<?= (int)$order['id'] ?>">Status</label><select id="status-<?= (int)$order['id'] ?>" name="status"><?php foreach ($statuses as $status): ?><option value="<?= e($status) ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option><?php endforeach; ?></select><button class="button button-small button-primary" type="submit">Atualizar</button></form></div></article><?php endforeach; ?></div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
