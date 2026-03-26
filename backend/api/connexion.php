<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// 🔥 preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Méthode non supportée', 405);
}

// ✅ récupération JSON propre
$body = json_decode(file_get_contents('php://input'), true);

$email = sanitize($body['email'] ?? '');
$motdepasse = $body['motdepasse'] ?? '';

if (!$email || !$motdepasse) {
    json_error('Champs manquants');
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($motdepasse, $user['password'])) {

    json_success([
        'user_id'  => $user['id'],
        'username' => $user['username'],
        'role'     => $user['role']
    ]);

} else {
    json_error('Email ou mot de passe incorrect', 401);
}