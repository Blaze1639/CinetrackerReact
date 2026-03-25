<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';
require_auth();
verify_csrf();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non supportée', 405);
$user_id = $_SESSION['user_id'];
try {
    $pdo->beginTransaction();
    $pdo->prepare("DELETE FROM media WHERE user_id=?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM media_to_watch WHERE user_id=?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM notifications WHERE user_id=?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$user_id]);
    $pdo->commit();
    session_unset(); session_destroy();
    json_success([], 'Compte supprimé');
} catch (Exception $e) {
    $pdo->rollBack();
    json_error('Erreur lors de la suppression: '.$e->getMessage(), 500);
}
