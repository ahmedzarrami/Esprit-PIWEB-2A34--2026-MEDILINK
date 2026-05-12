<?php
/**
 * Login medecin legacy : sert maintenant de point d'entree intelligent vers
 * l'espace medecin. L'authentification reelle se fait via MediLink.
 */
require_once __DIR__ . '/../../../../../config/session.php';

$u = current_user();
if ($u === null) {
    // Pas connecte : on envoie au login MediLink, qui reviendra ici apres
    $_SESSION['login_redirect'] = '/files40/modules/rdv/MediLink/index.php?action=medecin';
    header('Location: /files40/modules/utilisateur/index.php?page=login');
    exit;
}

if ($u['role'] !== 'Professionnel') {
    // Mauvais role : retour a l'accueil
    header('Location: /files40/index.php');
    exit;
}

// Connecte et bon role -> espace medecin (page de gestion des fiches patient)
header('Location: /files40/modules/rdv/MediLink/View/front/gestionFichePatient.php');
exit;
