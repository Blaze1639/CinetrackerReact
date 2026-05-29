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
$stmt = $pdo->prepare("DELETE FROM media WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);
json_success([], 'Média supprimé');
