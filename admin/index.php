<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$pageTitle = 'Administração | Lume';
$counts = [
    'products' => (int)db()->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'orders' => (int)db()->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'customers' => (int)db()->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn(),
];
$orders = db()->query('SELECT id, customer_name, total, status, created_at FROM orders ORDER BY created_at DESC LIMIT 8')->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading compact-heading"><span class="eyebrow">PAINEL INTERNO</span><h1>Administração <em>Lume.</em></h1><p>Gerencie o catálogo e acompanhe os pedidos.</p></section>
<div class="admin-stats"><div class="stat-card"><span>Produtos cadastrados</span><strong><?= $counts['products'] ?></strong></div><div class="stat-card"><span>Pedidos recebidos</span><strong><?= $counts['orders'] ?></strong></div><div class="stat-card"><span>Clientes</span><strong><?= $counts['customers'] ?></strong></div></div>
<div class="admin-actions"><a class="button button-primary" href="produtos.php">Gerenciar produtos</a><a class="button button-outline" href="pedidos.php">Ver todos os pedidos</a></div>
<section class="admin-section"><div class="section-title-row"><h2>Pedidos recentes</h2><a class="text-link" href="pedidos.php">Ver todos ↗</a></div>
<?php if (!$orders): ?><p class="muted">Ainda não há pedidos.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Pedido</th><th>Cliente</th><th>Data</th><th>Total</th><th>Status</th></tr></thead><tbody><?php foreach ($orders as $order): ?><tr><td>#<?= (int)$order['id'] ?></td><td><?= e($order['customer_name']) ?></td><td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td><td><?= money($order['total']) ?></td><td><?= e($order['status']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
