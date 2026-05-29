<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/session_handler.php';

$handler = new DbSessionHandler($pdo);
session_set_save_handler($handler, true);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'None',
    ]);
    session_start();
}

// Sécurité HTTP
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// ── CSRF ─────────────────────────────────────────────────────
function generate_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $exempt = ['connexion.php', 'inscription.php'];
    $script = basename($_SERVER['SCRIPT_FILENAME']);
    if (in_array($script, $exempt)) {
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            json_error('Token CSRF invalide', 403);
        }
    }
}

// ── Sanitisation ─────────────────────────────────────────────
function sanitize(string $value): string
{
    // Ne pas utiliser htmlspecialchars - json_encode gère l'échappement
    return trim($value);
}

function sanitize_array(array $data): array
{
    $clean = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $clean[$key] = sanitize_array($value);
        } elseif (is_string($value)) {
            $clean[$key] = sanitize($value);
        } else {
            $clean[$key] = $value;
        }
    }
    return $clean;
}

// ── Réponses JSON ─────────────────────────────────────────────
function json_success($data = [], $message = null): void
{
    $out = ['success' => true];
    if ($message) {
        $out['message'] = $message;
    }
    if (is_array($data)) {
        $out = array_merge($out, $data);
    }
    echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function json_error($message, $code = 400): void
{
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// ── Auth ──────────────────────────────────────────────────────
function require_auth(): void
{
    if (!isset($_SESSION['user_id'])) {
        json_error('Non authentifié', 401);
    }
}

// ── Body JSON ─────────────────────────────────────────────────
function get_body(): array
{
    $raw = json_decode(file_get_contents('php://input'), true) ?? [];
    return sanitize_array($raw);
}
