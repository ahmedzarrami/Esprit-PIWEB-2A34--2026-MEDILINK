<?php
/**
 * Login patient legacy : sert de point d'entree intelligent vers
 * l'espace patient. L'authentification reelle se fait via MediLink.
 */
require_once __DIR__ . '/../../../../../config/session.php';

$u = current_user();
if ($u === null) {
    $_SESSION['login_redirect'] = '/files40/modules/rdv/MediLink/index.php?action=patient';
    header('Location: /files40/modules/utilisateur/index.php?page=login');
    exit;
}

if ($u['role'] !== 'Patient') {
    header('Location: /files40/index.php');
    exit;
}

// Connecte et patient -> espace patient (homePatient)
header('Location: /files40/modules/rdv/MediLink/View/front/homePatient.php');
exit;
