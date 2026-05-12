<?php
require_once __DIR__ . '/../../../config/session.php';
require_login();

$ml_user = current_user();
$role    = $ml_user['role'];

// Bridge legacy session keys utilises par les vues / controleurs du module RDV
if ($role === 'Patient' && !empty($ml_user['rdv_patient_id'])) {
    $_SESSION['patient_id']     = $ml_user['rdv_patient_id'];
    $_SESSION['patient_nom']    = $ml_user['nom'];
    $_SESSION['patient_prenom'] = $ml_user['prenom'];
    $_SESSION['patient_email']  = $ml_user['email'];
} elseif ($role === 'Professionnel' && !empty($ml_user['rdv_medecin_id'])) {
    $_SESSION['medecin_id']     = $ml_user['rdv_medecin_id'];
    $_SESSION['medecin_nom']    = $ml_user['nom'];
    $_SESSION['medecin_prenom'] = $ml_user['prenom'];
    $_SESSION['medecin_email']  = $ml_user['email'];
}

$action = $_GET['action'] ?? '';
// Defaut : redirection automatique en fonction du role
if ($action === '') {
    if ($role === 'Patient')          $action = 'patient';
    elseif ($role === 'Professionnel') $action = 'medecin';
    elseif ($role === 'Administrateur') $action = 'admin';
    else $action = 'home';
}

// Gardes par role
if ($action === 'admin' && $role !== 'Administrateur') {
    header('Location: /files40/'); exit;
}

switch ($action) {
    case 'home':
        require "View/front/home.php";
        break;
    case 'patient':
        // Espace patient : page de gestion des RDV du patient
        require "View/front/homePatient.php";
        break;
    case 'medecin':
        // Espace medecin : page de gestion des fiches patients
        header('Location: /files40/modules/rdv/MediLink/View/front/gestionFichePatient.php');
        exit;
    case 'admin':
        require "View/admin/admin.php";
        break;
    default:
        require "View/front/home.php";
        break;
}
