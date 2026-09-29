<?php
session_start();

require_once __DIR__ . '/../config/db.php';

// Autoloader: loads a class file the first time the class is used.
// App\Models\Student  ->  app/Models/Student.php
spl_autoload_register(function (string $class) {
    if (str_starts_with($class, 'App\\')) {
        $file = __DIR__ . '/../app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});

// Escape output before printing it into HTML (prevents XSS).
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// A per-session token that every form sends back, so other sites
// can't submit forms on the user's behalf (CSRF protection).
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!hash_equals(csrf_token(), $sent)) {
        http_response_code(400);
        exit('Invalid form token. Go back, reload the page and try again.');
    }
}

// One-time messages that survive a redirect.
function flash(string $message): void
{
    $_SESSION['flash'] = $message;
}

function take_flash(): ?string
{
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $message;
}

function redirect(string $to): void
{
    header('Location: ' . $to);
    exit;
}
