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
$title = trim($body['title'] ?? '');
$rating = (float)($body['rating'] ?? 0);
$image_url = $body['image_url'] ?? null;
$type_media = ($body['type_media'] === 'série') ? 'série' : 'film';
$commentaire = trim($body['commentaire'] ?? ''); // Pas de sanitize ici, on le fera à l'affichage
if (!$title) {
    json_error('Le titre est requis');
}
if ($rating < 1 || $rating > 5) {
    json_error('La note doit être entre 1 et 5');
}
$stmt = $pdo->prepare("SELECT COUNT(*) FROM media WHERE LOWER(title)=LOWER(?) AND type_media=? AND user_id=?");
$stmt->execute([$title, $type_media, $user_id]);
if ($stmt->fetchColumn() > 0) {
    json_error('Ce média existe déjà dans votre liste');
}
$stmt = $pdo->prepare("INSERT INTO media (title,rating,image_url,type_media,commentaire,user_id,favorite,view_count,created_at) VALUES (?,?,?,?,?,?,0,0,NOW())");
$stmt->execute([$title,$rating,$image_url,$type_media,$commentaire,$user_id]);
json_success(['id' => $pdo->lastInsertId()], ucfirst($type_media).' ajouté avec succès');
