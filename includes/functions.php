<?php
declare(strict_types=1);

/**
 * Bazaar shared helper functions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

/**
 * Escape output safely.
 */
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Get base URL for links.
 * Works if project is in root or inside a folder like localhost/bazaar/
 */
function base_url(string $path = ''): string
{
    $script = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    $root = rtrim(dirname($script), '/');

    return $root . '/' . ltrim($path, '/');
}

/**
 * Get asset URL with cache busting.
 */
function asset(string $path): string
{
    $relative = 'assets/' . ltrim($path, '/');
    $absolute = dirname(__DIR__) . '/' . $relative;

    $url = base_url($relative);

    if (is_file($absolute)) {
        $url .= '?v=' . filemtime($absolute);
    }

    return $url;
}

/**
 * Current page file name.
 */
function current_page(): string
{
    return basename($_SERVER['SCRIPT_NAME'] ?? '');
}

/**
 * Active navigation class.
 */
function is_active(string $fileName): string
{
    return current_page() === $fileName ? 'active-page' : '';
}

/**
 * CSRF token.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Hidden CSRF input.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Validate CSRF token.
 */
function verify_csrf(): bool
{
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], (string)$_POST['csrf_token']);
}

/**
 * Flash messages.
 */
function flash_set(string $type, string $message): void
{
    $_SESSION['flash'][] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Render and clear flash messages.
 */
function flash_render(): string
{
    if (empty($_SESSION['flash'])) {
        return '';
    }

    $html = '';

    foreach ($_SESSION['flash'] as $flash) {
        $html .= '<div class="flash ' . e($flash['type']) . '">'
            . e($flash['message'])
            . '</div>';
    }

    unset($_SESSION['flash']);

    return $html;
}

/**
 * Redirect helper.
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Check POST request.
 */
function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}
