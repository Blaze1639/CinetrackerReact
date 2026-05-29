<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';
require_auth();
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if ($user['role'] !== 'admin') {
    json_error('Non autorisé', 403);
}
$stmt = $pdo->prepare("SELECT n.*,u.username as sender_username,u.email as sender_email FROM notifications n JOIN users u ON n.user_id=u.id ORDER BY n.created_at DESC");
$stmt->execute();
$notifications = $stmt->fetchAll();
$stmt2 = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE status='non_lu'");
$stmt2->execute();
$unread = $stmt2->fetch()['count'];
json_success(['notifications' => $notifications,'unread_count' => (int)$unread]);
