<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('A sessão do formulário expirou. Volte à página e tente novamente.');
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!current_user()) {
        $_SESSION['flash'] = 'Entre na sua conta para continuar.';
        redirect('login.php');
    }
}

function require_admin(): void
{
    if (!current_user() || current_user()['role'] !== 'admin') {
        http_response_code(403);
        exit('Acesso restrito à administração.');
    }
}

function flash(string $message = ''): ?string
{
    if ($message !== '') {
        $_SESSION['flash'] = $message;
        return null;
    }

    $value = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $value;
}

function money(float|string $value): string
{
    return 'R$ ' . number_format((float)$value, 2, ',', '.');
}

function cart_count(): int
{
    return array_sum($_SESSION['cart'] ?? []);
}
