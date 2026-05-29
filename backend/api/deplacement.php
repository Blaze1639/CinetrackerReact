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
$media_id = (int)($body['media_id'] ?? 0);
$title = trim($body['title'] ?? '');
$type_media = $body['type_media'] ?? 'film';
$image_url = $body['image_url'] ?? null;
$rating = (float)($body['rating'] ?? 0);
$commentaire = trim($body['commentaire'] ?? '');
if ($rating < 1 || $rating > 5) {
    json_error('Note invalide (1-5)');
}
$stmt = $pdo->prepare("INSERT INTO media (title,type_media,image_url,rating,commentaire,user_id,favorite,view_count,created_at) VALUES (?,?,?,?,?,?,0,0,NOW())");
$stmt->execute([$title,$type_media,$image_url,$rating,$commentaire,$user_id]);
$pdo->prepare("DELETE FROM media_to_watch WHERE id=? AND user_id=?")->execute([$media_id,$user_id]);
json_success([], 'Déplacé vers votre liste');
