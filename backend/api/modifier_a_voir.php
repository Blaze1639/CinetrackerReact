<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';
require_auth();
verify_csrf();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Méthode non supportée', 405);
}
$body = get_body();
$user_id = $_SESSION['user_id'];
$id = (int)($body['media_id'] ?? 0);
if (!$id) {
    json_error('ID manquant');
}
$stmt = $pdo->prepare("UPDATE media_to_watch SET title=?,type_media=?,image_url=? WHERE id=? AND user_id=?");
$stmt->execute([trim($body['title']),$body['type_media'],$body['image_url'],$id,$user_id]);
json_success([], 'Modifié');
