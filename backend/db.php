<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// ── CORS ─────────────────────────────────────────────────────
$allowed_origins = [
    'http://localhost:5173',
    'http://localhost:3000',
    'https://cinetrack-frontend.onrender.com',
];
if (!empty(getenv('FRONTEND_URL'))) {
    $allowed_origins[] = rtrim(getenv('FRONTEND_URL'), '/');
}
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: https://cinetrack-frontend.onrender.com");
}
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, X-CSRF-Token');
header('Access-Control-Allow-Credentials: true');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// ── Session ───────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: 'media_tracker';
$db_port = getenv('DB_PORT') ?: '3306';
$db_user = getenv('DB_USER') ?: 'cinetrack';
$db_pass = getenv('DB_PASS') ?: 'cinetrack';

try {
    $pdo = new PDO(
        "mysql:host={$db_host};dbname={$db_name};port={$db_port};charset=utf8mb4",
        $db_user,
        $db_pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Erreur de connexion BDD', 'detail' => $e->getMessage()]);
    exit;
}