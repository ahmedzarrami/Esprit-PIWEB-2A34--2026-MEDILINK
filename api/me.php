<?php
/**
 * Endpoint utilise par la barre flottante de navigation pour connaitre
 * l'utilisateur connecte. Retourne {user: null} si non connecte.
 */
require_once __DIR__ . '/../config/session.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$u = current_user();
echo json_encode([
    'user' => $u ? [
        'id'     => $u['id'],
        'nom'    => $u['nom'],
        'prenom' => $u['prenom'],
        'email'  => $u['email'],
        'role'   => $u['role'],
    ] : null,
]);
