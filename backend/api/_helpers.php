<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => true,         // HTTPS en production Railway
        'httponly' => true,
        'samesite' => 'None',       // 'None' obligatoire pour cross-origin (Railway)
    ]);
    session_start();
}

// ── CORS ─────────────────────────────────────────────────────
// Accepte localhost en dev ET le domaine Railway en prod
$allowed_origins = [
    'http://localhost:5173',                      // dev local
    'http://localhost:3000',
];

// Ajouter le domaine frontend Railway si défini
if (!empty(getenv('FRONTEND_URL'))) {
    $allowed_origins[] = rtrim(getenv('FRONTEND_URL'), '/');
}

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: " . ($allowed_origins[0]));
}

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, X-CSRF-Token');
header('Access-Control-Allow-Credentials: true');

// Sécurité HTTP
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// ── CSRF ─────────────────────────────────────────────────────
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    $exempt = ['connexion.php', 'inscription.php'];
    $script = basename($_SERVER['SCRIPT_FILENAME']);
    if (in_array($script, $exempt)) return;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            json_error('Token CSRF invalide', 403);
        }
    }
}

// ── Sanitisation ─────────────────────────────────────────────
function sanitize(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

function sanitize_array(array $data): array {
    $clean = [];
    foreach ($data as $key => $value) {
        if (is_array($value))        $clean[$key] = sanitize_array($value);
        elseif (is_string($value))   $clean[$key] = sanitize($value);
        else                         $clean[$key] = $value;
    }
    return $clean;
}

// ── Réponses JSON ─────────────────────────────────────────────
function json_success($data = [], $message = null): void {
    $out = ['success' => true];
    if ($message) $out['message'] = $message;
    if (is_array($data)) $out = array_merge($out, $data);
    echo json_encode($out);
    exit;
}

function json_error($message, $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

// ── Auth ──────────────────────────────────────────────────────
function require_auth(): void {
    if (!isset($_SESSION['user_id'])) json_error('Non authentifié', 401);
}

// ── Body JSON ─────────────────────────────────────────────────
function get_body(): array {
    $raw = json_decode(file_get_contents('php://input'), true) ?? [];
    return sanitize_array($raw);
}
