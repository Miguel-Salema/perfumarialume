<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$pageTitle = 'Produtos | Administração Lume';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'toggle' && $id > 0) {
        $stmt = db()->prepare('UPDATE products SET active = IF(active = 1, 0, 1) WHERE id = ?');
        $stmt->execute([$id]);
        flash('Visibilidade do produto atualizada.');
    } elseif ($action === 'save') {
        $name = trim((string)($_POST['name'] ?? ''));
        $slug = trim((string)($_POST['slug'] ?? ''));
        $category = trim((string)($_POST['category'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $details = trim((string)($_POST['details'] ?? ''));
        $price = (float)($_POST['price'] ?? 0);
        $stock = max(0, (int)($_POST['stock'] ?? 0));
        $color = trim((string)($_POST['accent_color'] ?? '#2b5e8b'));
        if ($name === '' || $slug === '' || $category === '' || $description === '' || $price < 0 || !preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            flash('Preencha os campos obrigatórios com valores válidos.');
        } else {
            try {
                if ($id > 0) {
                    $stmt = db()->prepare('UPDATE products SET name=?, slug=?, category=?, description=?, details=?, price=?, stock=?, accent_color=? WHERE id=?');
                    $stmt->execute([$name, $slug, $category, $description, $details, $price, $stock, $color, $id]);
                } else {
                    $stmt = db()->prepare('INSERT INTO products (name, slug, category, description, details, price, stock, accent_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$name, $slug, $category, $description, $details, $price, $stock, $color]);
                }
                flash('Produto salvo com sucesso.');
            } catch (PDOException $exception) { flash('Não foi possível salvar. Verifique se o identificador (slug) já está em uso.'); }
        }
    }
    redirect('produtos.php');
}
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}
$products = db()->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading compact-heading"><span class="eyebrow">PAINEL INTERNO</span><h1>Gerenciar <em>produtos.</em></h1><p>Cadastre, edite ou oculte produtos do catálogo.</p></section>
<div class="admin-form-card"><h2><?= $edit ? 'Editar produto' : 'Novo produto' ?></h2>
<form method="post" class="admin-product-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<div class="form-grid"><div><label for="name">Nome *</label><input id="name" name="name" required value="<?= e($edit['name'] ?? '') ?>"></div><div><label for="slug">Identificador (slug) *</label><input id="slug" name="slug" required value="<?= e($edit['slug'] ?? '') ?>" placeholder="meu-produto"></div><div><label for="category">Categoria *</label><input id="category" name="category" required value="<?= e($edit['category'] ?? '') ?>"></div><div><label for="price">Preço (R$) *</label><input id="price" type="number" name="price" min="0" step="0.01" required value="<?= e((string)($edit['price'] ?? '0.00')) ?>"></div><div><label for="stock">Estoque *</label><input id="stock" type="number" name="stock" min="0" required value="<?= (int)($edit['stock'] ?? 0) ?>"></div><div><label for="accent_color">Cor da embalagem</label><input id="accent_color" type="color" name="accent_color" value="<?= e($edit['accent_color'] ?? '#2b5e8b') ?>"></div></div>
<label for="description">Descrição *</label><textarea id="description" name="description" rows="3" required><?= e($edit['description'] ?? '') ?></textarea><label for="details">Detalhes complementares</label><input id="details" name="details" value="<?= e($edit['details'] ?? '') ?>"><button class="button button-primary" type="submit">Salvar produto</button><?php if ($edit): ?> <a class="button button-outline" href="produtos.php">Cancelar edição</a><?php endif; ?></form></div>
<div class="table-wrap"><table><thead><tr><th>Produto</th><th>Categoria</th><th>Preço</th><th>Estoque</th><th>Visibilidade</th><th>Ações</th></tr></thead><tbody><?php foreach ($products as $product): ?><tr><td><strong><?= e($product['name']) ?></strong><br><small><?= e($product['slug']) ?></small></td><td><?= e($product['category']) ?></td><td><?= money($product['price']) ?></td><td><?= (int)$product['stock'] ?></td><td><?= $product['active'] ? 'Ativo' : 'Oculto' ?></td><td class="table-actions"><a href="produtos.php?edit=<?= (int)$product['id'] ?>">Editar</a><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int)$product['id'] ?>"><button type="submit"><?= $product['active'] ? 'Ocultar' : 'Ativar' ?></button></form></td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
