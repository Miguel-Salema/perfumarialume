<?php
require_once __DIR__ . '/includes/functions.php';
if (current_user()) redirect('index.php');
$pageTitle = 'Entrar | Lume';
$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $stmt = db()->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']];
        flash('Que bom ter você por aqui!');
        redirect($user['role'] === 'admin' ? 'admin/index.php' : 'index.php');
    }
    $error = 'E-mail ou senha incorretos.';
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-layout">
    <div class="auth-aside"><span class="eyebrow">VOLTE A BRILHAR</span><h1>Olá, que bom ter você <em>de volta.</em></h1><p>Entre na sua conta e acompanhe sua experiência Lume.</p></div>
    <div class="auth-card"><h2>Entrar</h2><p class="muted">Acesse sua conta Lume.</p>
        <?php if ($error): ?><div class="error-box"><p><?= e($error) ?></p></div><?php endif; ?>
        <form method="post" class="form-stack">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label for="email">E-mail</label><input id="email" type="email" name="email" autocomplete="username" required value="<?= e($email) ?>">
            <label for="password">Senha</label><div class="password-field"><input id="password" type="password" name="password" autocomplete="current-password" required><button type="button" class="show-password" data-toggle-password="password">Mostrar</button></div>
            <button class="button button-primary button-full" type="submit">Entrar</button>
        </form><p class="auth-switch">Ainda não tem conta? <a href="cadastro.php">Criar conta</a></p>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
