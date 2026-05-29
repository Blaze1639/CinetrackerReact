<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';
require_auth();
$user_id = $_SESSION['user_id'];
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$stmt = $pdo->prepare("SELECT SUM(type_media='film') AS films, SUM(type_media='série') AS series, COUNT(*) AS total FROM media WHERE YEAR(created_at)=? AND user_id=?");
$stmt->execute([$year,$user_id]);
$year_stats = $stmt->fetch();
$stmt2 = $pdo->prepare("SELECT months.mois, COALESCE(SUM(type_media='film'),0) AS films, COALESCE(SUM(type_media='série'),0) AS series, COALESCE(COUNT(media.id),0) AS total FROM (SELECT 1 AS mois UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) months LEFT JOIN media ON MONTH(created_at)=months.mois AND YEAR(created_at)=? AND user_id=? GROUP BY months.mois ORDER BY mois");
$stmt2->execute([$year,$user_id]);
$months = $stmt2->fetchAll();
$stmt3 = $pdo->prepare("SELECT m.title,m.rating,m.image_url,m.created_at,m.commentaire,u.username,m.user_id FROM media m JOIN users u ON m.user_id=u.id WHERE m.user_id!=? AND m.type_media='film' ORDER BY RAND() LIMIT 20");
$stmt3->execute([$user_id]);
$all_films = $stmt3->fetchAll();
$leaderboard_films = [];
$seen = [];
foreach ($all_films as $f) {
    if (!in_array($f['user_id'], $seen)) {
        $leaderboard_films[] = $f;
        $seen[] = $f['user_id'];
        if (count($leaderboard_films) >= 5) {
            break;
        }
    }
}
$stmt4 = $pdo->prepare("SELECT m.title,m.rating,m.image_url,m.created_at,m.commentaire,u.username,m.user_id FROM media m JOIN users u ON m.user_id=u.id WHERE m.user_id!=? AND m.type_media='série' ORDER BY RAND() LIMIT 20");
$stmt4->execute([$user_id]);
$all_series = $stmt4->fetchAll();
$leaderboard_series = [];
$seen2 = [];
foreach ($all_series as $s) {
    if (!in_array($s['user_id'], $seen2)) {
        $leaderboard_series[] = $s;
        $seen2[] = $s['user_id'];
        if (count($leaderboard_series) >= 5) {
            break;
        }
    }
}
$stmt5 = $pdo->prepare("SELECT a.*,u.username as admin_username FROM actualite a JOIN users u ON a.user_id=u.id ORDER BY a.created_at DESC LIMIT 5");
$stmt5->execute();
$actualites = $stmt5->fetchAll();
json_success(['year_stats' => $year_stats,'months' => $months,'leaderboard_films' => $leaderboard_films,'leaderboard_series' => $leaderboard_series,'actualites' => $actualites]);
