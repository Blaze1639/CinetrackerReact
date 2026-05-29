<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';
require_auth();
verify_csrf();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Méthode non supportée', 405);
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if ($user['role'] !== 'admin') {
    json_error('Non autorisé', 403);
}
$body = get_body();
$titre = trim($body['titre'] ?? '');
$contenu = trim($body['contenu'] ?? '');
if (!$titre || !$contenu) {
    json_error('Champs manquants');
}
$pdo->prepare("INSERT INTO actualite (titre,contenu,user_id,created_at) VALUES (?,?,?,NOW())")->execute([$titre,$contenu,$user_id]);
json_success([], 'Actualité publiée');
