<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';
require_auth();
verify_csrf();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non supportée', 405);
$body = get_body();
$user_id = $_SESSION['user_id'];
$title = trim($body['title'] ?? '');
$type_media = ($body['type_media'] === 'série') ? 'série' : 'film';
$image_url = $body['image_url'] ?? null;
if (!$title) json_error('Titre requis');
$stmt = $pdo->prepare("INSERT INTO media_to_watch (title,type_media,image_url,user_id,added_date) VALUES (?,?,?,?,NOW())");
$stmt->execute([$title,$type_media,$image_url,$user_id]);
json_success(['id' => $pdo->lastInsertId()], 'Ajouté à la liste');
