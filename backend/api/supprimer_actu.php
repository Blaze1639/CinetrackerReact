<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';

require_auth();
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non supportée', 405);

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user || $user['role'] !== 'admin') json_error('Non autorisé', 403);

$body = get_body();
$id = (int)($body['id'] ?? 0);
if (!$id) json_error('ID manquant');

$pdo->prepare("DELETE FROM actualite WHERE id = ?")->execute([$id]);

json_success([], 'Actualité supprimée');
