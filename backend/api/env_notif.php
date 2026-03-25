<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';
require_auth();
verify_csrf();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non supportée', 405);
$body = get_body();
$type_message = trim($body['type_message'] ?? '');
$message = trim($body['message'] ?? '');
if (!$type_message || !$message) json_error('Champs manquants');
if (strlen($message) < 10) json_error('Message trop court (min 10 caractères)');
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username FROM users WHERE id=?");
$stmt->execute([$user_id]);
$username = $stmt->fetch()['username'];
$stmt = $pdo->prepare("INSERT INTO notifications (user_id,type_message,message,username,created_at,status) VALUES (?,?,?,?,NOW(),'non_lu')");
$stmt->execute([$user_id,$type_message,$message,$username]);
json_success([], 'Message envoyé');
