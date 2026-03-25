<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';
require_auth();
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if ($user['role'] !== 'admin') json_error('Non autorisé', 403);
$id = (int)($_GET['id'] ?? 0);
if (!$id) json_error('ID manquant');
$pdo->prepare("UPDATE notifications SET status='lu' WHERE id=?")->execute([$id]);
json_success([], 'Marqué comme lu');
