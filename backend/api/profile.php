<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';
require_auth();
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id,username,email,role,created_at FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) {
    json_error('Utilisateur introuvable', 404);
}
$stmt = $pdo->prepare("SELECT SUM(type_media='film') AS films, SUM(type_media='série') AS series, COUNT(*) AS total, SUM(favorite=1) AS favoris FROM media WHERE user_id=?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM media_to_watch WHERE user_id=?");
$stmt->execute([$user_id]);
$a_voir = $stmt->fetch();
json_success([
    'username' => $user['username'], 'email' => $user['email'],
    'role' => $user['role'], 'created_at' => $user['created_at'],
    'total_films' => (int)($stats['films'] ?? 0),
    'total_series' => (int)($stats['series'] ?? 0),
    'total_media' => (int)($stats['total'] ?? 0),
    'total_favoris' => (int)($stats['favoris'] ?? 0),
    'total_a_voir' => (int)($a_voir['count'] ?? 0),
]);
