<?php
require_once __DIR__ . '/includes/functions.php';
if (current_user()) redirect('index.php');
$pageTitle = 'Criar conta | Lume';
$errors = [];
$name = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if (mb_strlen($name) < 2) $errors[] = 'Informe seu nome completo.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Informe um e-mail válido.';
    if (strlen($password) < 8) $errors[] = 'A senha deve ter pelo menos 8 caracteres.';
    if ($password !== $confirm) $errors[] = 'As senhas não coincidem.';

    if (!$errors) {
        $check = db()->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) $errors[] = 'Já existe uma conta com esse e-mail.';
        else {
            $stmt = db()->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => (int)db()->lastInsertId(), 'name' => $name, 'email' => $email, 'role' => 'customer'];
            flash('Sua conta foi criada. Boas-vindas à Lume!');
            redirect('index.php');
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-layout">
    <div class="auth-aside"><span class="eyebrow">BEM-VINDA(O) À LUME</span><h1>Seu próximo capítulo começa com <em>luz.</em></h1><p>Crie sua conta para acompanhar pedidos e descobrir seus produtos favoritos.</p></div>
    <div class="auth-card"><h2>Criar conta</h2><p class="muted">Preencha seus dados para continuar.</p>
        <?php if ($errors): ?><div class="error-box"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
        <form method="post" class="form-stack">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label for="name">Nome completo</label><input id="name" name="name" autocomplete="name" required value="<?= e($name) ?>">
            <label for="email">E-mail</label><input id="email" type="email" name="email" autocomplete="email" required value="<?= e($email) ?>">
            <label for="password">Senha (mínimo 8 caracteres)</label><div class="password-field"><input id="password" type="password" name="password" minlength="8" required autocomplete="new-password"><button type="button" class="show-password" data-toggle-password="password">Mostrar</button></div>
            <label for="confirm_password">Confirmar senha</label><div class="password-field"><input id="confirm_password" type="password" name="confirm_password" minlength="8" required autocomplete="new-password"><button type="button" class="show-password" data-toggle-password="confirm_password">Mostrar</button></div>
            <button class="button button-primary button-full" type="submit">Criar minha conta</button>
        </form><p class="auth-switch">Já tem uma conta? <a href="login.php">Entrar</a></p>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
