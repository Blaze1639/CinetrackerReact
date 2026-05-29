<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';
require_auth();
verify_csrf();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Méthode non supportée', 405);
}
$body = get_body();
$id = (int)($body['media_id'] ?? 0);
if (!$id) {
    json_error('ID manquant');
}
$stmt = $pdo->prepare("SELECT favorite FROM media WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);
$media = $stmt->fetch();
if (!$media) {
    json_error('Média introuvable', 404);
}
$new = $media['favorite'] ? 0 : 1;
$pdo->prepare("UPDATE media SET favorite=? WHERE id=?")->execute([$new, $id]);
json_success(['favorite' => $new]);
