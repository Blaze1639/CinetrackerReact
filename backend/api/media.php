<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';
require_auth();
$user_id = $_SESSION['user_id'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$filtre = $_GET['type'] ?? '';
$recherche = trim($_GET['search'] ?? '');
$year = $_GET['year'] ?? '';
$rating = $_GET['rating'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

if ($id) {
	// Si un id est fourni, on ne retourne que ce média
	$stmt = $pdo->prepare("SELECT * FROM media WHERE id = :id AND user_id = :user_id LIMIT 1");
	$stmt->execute([':id' => $id, ':user_id' => $user_id]);
	$media = $stmt->fetchAll();
	json_success(['media' => $media, 'total' => count($media), 'pages' => 1, 'page' => 1]);
}

$where = "WHERE user_id = :user_id";
$params = [':user_id' => $user_id];
if ($filtre === 'film' || $filtre === 'série') { $where .= " AND type_media = :type"; $params[':type'] = $filtre; }
elseif ($filtre === 'favorite') { $where .= " AND favorite = 1"; }
if ($year) { $where .= " AND YEAR(created_at) = :year"; $params[':year'] = (int)$year; }
if ($rating) { $where .= " AND rating = :rating"; $params[':rating'] = (int)$rating; }
if ($recherche) { $where .= " AND title LIKE :search"; $params[':search'] = "%$recherche%"; }

$countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM media $where");
$countStmt->execute($params);
$total = $countStmt->fetch()['total'];

$params[':limit'] = $perPage;
$params[':offset'] = $offset;
$stmt = $pdo->prepare("SELECT * FROM media $where ORDER BY title ASC LIMIT :limit OFFSET :offset");
foreach ($params as $k => &$v) $stmt->bindValue($k, $v, in_array($k, [':limit',':offset',':user_id',':year',':rating']) ? PDO::PARAM_INT : PDO::PARAM_STR);
$stmt->execute();
$media = $stmt->fetchAll();

json_success(['media' => $media, 'total' => (int)$total, 'pages' => (int)ceil($total / $perPage), 'page' => $page]);
