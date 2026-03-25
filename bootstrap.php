<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/db.php';

const SESSION_TIMEOUT = 900;

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit();
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function require_login(): void
{
    $lastAction = $_SESSION['last_action'] ?? null;

    if ($lastAction !== null && (time() - (int) $lastAction) > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        session_start();
        set_flash('error', 'Votre session a expire. Veuillez vous reconnecter.');
        redirect('login.php');
    }

    if (empty($_SESSION['user'])) {
        redirect('login.php');
    }

    $_SESSION['last_action'] = time();
}

function current_user(): string
{
    return $_SESSION['user'] ?? 'Utilisateur';
}

function format_price(float $value): string
{
    return number_format($value, 0, ',', ' ') . ' FCFA';
}

function format_date_fr(string $date): string
{
    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return $date;
    }

    return date('d/m/Y', $timestamp);
}
