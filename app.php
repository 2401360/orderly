<?php
// Common helpers (drop-in). Include this after session_start().
function db(): PDO {
    // Uses constants from config.php or db-connect.php for BC.
    $dsn = (defined('SERVER') && defined('DBNAME')) 
        ? 'mysql:host=' . SERVER . ';dbname=' . DBNAME . ';charset=utf8mb4'
        : (defined('DB_DSN') ? DB_DSN : '');
    $user = defined('USER') ? USER : (defined('DB_USER') ? DB_USER : null);
    $pass = defined('PASS') ? PASS : (defined('DB_PASS') ? DB_PASS : null);
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// --- CSRF ---
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}
function csrf_verify(): void {
    $token = $_POST['csrf'] ?? $_GET['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(400);
        echo '<div class="container pt-3"><div class="alert alert-danger">Invalid CSRF token.</div></div>';
        require 'footer.php';
        exit;
    }
}

// --- Flash message ---
function flash(string $type, string $msg): void {
    $_SESSION['flash'][] = ['t'=>$type, 'm'=>$msg];
}
function flashes(): array {
    $msgs = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $msgs;
}

// --- Cart count ---
function cart_count(): int {
    if (empty($_SESSION['product'])) return 0;
    $n = 0;
    foreach ($_SESSION['product'] as $it) { $n += (int)($it['count'] ?? 0); }
    return $n;
}
