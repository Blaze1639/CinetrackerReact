<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';

// Génère/renouvelle le token CSRF et l'envoie au frontend
$csrf_token = generate_csrf_token();

if (isset($_SESSION['user_id'])) {
    json_success([
        'user_id'    => $_SESSION['user_id'],
        'username'   => $_SESSION['username'] ?? $_SESSION['pseudo'] ?? null,
        'role'       => $_SESSION['role'] ?? 'utilisateur',
        'csrf_token' => $csrf_token,
    ]);
} else {
    json_success([
        'user_id'    => null,
        'csrf_token' => $csrf_token,
    ]);
}
