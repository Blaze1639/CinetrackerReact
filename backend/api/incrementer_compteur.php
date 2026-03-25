<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';
require_auth();
verify_csrf();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non supportée', 405);
$body = get_body();
$id = (int)($body['media_id'] ?? 0);
if (!$id) json_error('ID manquant');
$pdo->prepare("UPDATE media SET view_count=view_count+1 WHERE id=? AND user_id=?")->execute([$id, $_SESSION['user_id']]);
$stmt = $pdo->prepare("SELECT view_count FROM media WHERE id=?");
$stmt->execute([$id]);
$row = $stmt->fetch();
json_success(['view_count' => $row['view_count']]);
