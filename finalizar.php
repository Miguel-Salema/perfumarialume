<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
$cart = $_SESSION['cart'] ?? [];
if (!$cart) { flash('Sua sacola está vazia.'); redirect('carrinho.php'); }
$user = current_user();
$error = '';
$address = '';
$stmt = db()->prepare('SELECT id, name, price, stock FROM products WHERE active = 1 AND id = ?');
$items = [];
$total = 0.0;
foreach ($cart as $id => $qty) {
    $stmt->execute([(int)$id]);
    $product = $stmt->fetch();
    if (!$product || (int)$product['stock'] < (int)$qty) {
        $error = 'Um dos produtos ficou indisponível ou teve o estoque alterado. Revise sua sacola.';
        break;
    }
    $product['quantity'] = (int)$qty;
    $total += (float)$product['price'] * (int)$qty;
    $items[] = $product;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    verify_csrf();
    $address = trim((string)($_POST['address'] ?? ''));
    if (mb_strlen($address) < 8) $error = 'Informe um endereço de entrega válido.';
    else {
        $pdo = db();
        try {
            $pdo->beginTransaction();
            $orderStmt = $pdo->prepare('INSERT INTO orders (user_id, customer_name, customer_email, address, total) VALUES (?, ?, ?, ?, ?)');
            $orderStmt->execute([$user['id'], $user['name'], $user['email'], $address, $total]);
            $orderId = (int)$pdo->lastInsertId();
            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity) VALUES (?, ?, ?, ?, ?)');
            $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');
            foreach ($items as $item) {
                $itemStmt->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['quantity']]);
                $stockStmt->execute([$item['quantity'], $item['id'], $item['quantity']]);
                if ($stockStmt->rowCount() !== 1) throw new RuntimeException('Estoque insuficiente.');
            }
            $pdo->commit();
            unset($_SESSION['cart']);
            flash('Pedido #' . $orderId . ' registrado com sucesso! Esta é uma compra demonstrativa.');
            redirect('meus_pedidos.php');
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = 'Não foi possível registrar o pedido. Confira o estoque e tente novamente.';
        }
    }
}
$pageTitle = 'Finalizar pedido | Lume';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading compact-heading"><span class="eyebrow">QUASE LÁ</span><h1>Finalizar <em>pedido.</em></h1><p>Confirme os dados para registrar seu pedido demonstrativo.</p></section>
<?php if ($error): ?><div class="error-box"><p><?= e($error) ?></p></div><?php endif; ?>
<div class="checkout-layout">
    <form method="post" class="auth-card checkout-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <h2>Entrega</h2><p class="muted">Pedido em nome de <?= e($user['name']) ?> (<?= e($user['email']) ?>).</p>
        <label for="address">Endereço completo</label><textarea id="address" name="address" rows="4" required minlength="8" placeholder="Rua, número, bairro, cidade e CEP"><?= e($address) ?></textarea>
        <div class="notice-box">Este projeto não realiza cobrança nem envia produtos. O pedido será salvo no banco de dados para demonstração.</div>
        <button class="button button-primary button-full" type="submit">Confirmar pedido • <?= money($total) ?></button>
    </form>
    <aside class="summary-card"><h2>Seu pedido</h2><?php foreach ($items as $item): ?><div class="summary-row"><span><?= e($item['name']) ?> × <?= (int)$item['quantity'] ?></span><strong><?= money((float)$item['price'] * $item['quantity']) ?></strong></div><?php endforeach; ?><div class="summary-total"><span>Total</span><strong><?= money($total) ?></strong></div><a class="text-link" href="carrinho.php">← Voltar à sacola</a></aside>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
