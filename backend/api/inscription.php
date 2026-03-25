<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Méthode non supportée', 405);

// Récupérer le mot de passe brut avant sanitisation
$raw = json_decode(file_get_contents('php://input'), true) ?? [];
$motdepasse = $raw['motdepasse'] ?? '';
$confirmer  = $raw['confirmer_motdepasse'] ?? '';
$pseudo     = sanitize($raw['pseudo'] ?? '');
$email      = sanitize($raw['email'] ?? '');

if (!$pseudo || !$email || !$motdepasse) json_error('Tous les champs sont obligatoires');
if ($motdepasse !== $confirmer) json_error('Les mots de passe ne correspondent pas');
if (strlen($motdepasse) < 6) json_error('Le mot de passe doit contenir au moins 6 caractères');

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->execute([$email, $pseudo]);
if ($stmt->fetch()) json_error('Ce pseudo ou cet email existe déjà');

$hash = password_hash($motdepasse, PASSWORD_BCRYPT, ['cost' => 12]);
$stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'utilisateur')");
$stmt->execute([$pseudo, $email, $hash]);
$id = $pdo->lastInsertId();

session_regenerate_id(true);
$_SESSION['user_id']  = $id;
$_SESSION['username'] = $pseudo;
$_SESSION['pseudo']   = $pseudo;
$_SESSION['role']     = 'utilisateur';

$csrf_token = generate_csrf_token();

json_success([
    'user_id'    => $id,
    'username'   => $pseudo,
    'role'       => 'utilisateur',
    'csrf_token' => $csrf_token,
]);
