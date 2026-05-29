<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Méthode non supportée', 405);
}

$body = get_body();
$email     = $body['email'] ?? '';
$motdepasse = $_POST['motdepasse'] ?? '';

// Récupérer le mot de passe brut AVANT sanitisation (hash bcrypt)
$raw = json_decode(file_get_contents('php://input'), true) ?? [];
$motdepasse = $raw['motdepasse'] ?? '';
$email = sanitize($raw['email'] ?? '');

if (!$email || !$motdepasse) {
    json_error('Champs manquants');
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($motdepasse, $user['password'])) {
    // Regénérer l'ID de session pour éviter la fixation de session
    session_regenerate_id(true);

    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['pseudo']   = $user['username'];
    $_SESSION['role']     = $user['role'];

    $csrf_token = generate_csrf_token();

    // Force le cookie de session avec les bons paramètres
    $session_params = session_get_cookie_params();
    setcookie(
        session_name(),
        session_id(),
        [
            'expires'  => 0,
            'path'     => '/',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'None',
        ]
    );

    json_success([
        'user_id'    => $user['id'],
        'username'   => $user['username'],
        'role'       => $user['role'],
        'csrf_token' => $csrf_token,
    ]);
} else {
    json_error('Email ou mot de passe incorrect', 401);
}
