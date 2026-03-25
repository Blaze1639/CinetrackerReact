<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';
require_auth();
$user_id = $_SESSION['user_id'];
$filtre = $_GET['type'] ?? '';
$recherche = trim($_GET['search'] ?? '');
$where = "WHERE user_id = :user_id";
$params = [':user_id' => $user_id];
if ($filtre === 'film' || $filtre === 'série') { $where .= " AND type_media = :type"; $params[':type'] = $filtre; }
if ($recherche) { $where .= " AND title LIKE :search"; $params[':search'] = "%$recherche%"; }
$stmt = $pdo->prepare("SELECT * FROM media_to_watch $where ORDER BY added_date DESC");
$stmt->execute($params);
json_success(['items' => $stmt->fetchAll()]);
