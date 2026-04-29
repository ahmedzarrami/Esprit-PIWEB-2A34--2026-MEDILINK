<?php
/**
 * MediLink — Front Office Router (index.php)
 * Point d'entrée pour les patients et les professionnels de santé
 */
session_start();

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/PatientController.php';
require_once __DIR__ . '/controllers/ProfessionnelController.php';

$authCtrl    = new AuthController();
$patientCtrl = new PatientController();
$proCtrl     = new ProfessionnelController();

$page   = $_GET['page']   ?? 'home';
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

$errors = [];
$flash  = null;

// ─── Traitement des actions POST ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($action)) {

    switch ($action) {
        case 'login':
            $result = $authCtrl->login(
                $_POST['email'] ?? '',
                $_POST['mot_de_passe'] ?? ''
            );
            if ($result['success']) {
                $flash = ['message' => 'Bienvenue ' . ($_SESSION['user_nom'] ?? '') . ' !', 'type' => 'success'];
                $role = $_SESSION['user_role'] ?? 'Patient';
                if ($role === 'Administrateur') {
                    header('Location: admin.php');
                    exit;
                } elseif ($role === 'Professionnel') {
                    $page = 'professionnel';
                } else {
                    $page = 'profile';
                }
            } else {
                $errors = $result['errors'];
                $page = 'login';
            }
            break;

        case 'register':
            $result = $authCtrl->register($_POST);
            if ($result['success']) {
                $flash = ['message' => 'Compte créé avec succès !', 'type' => 'success'];
                $page = 'profile';
            } else {
                $errors = $result['errors'];
                $page = 'register';
            }
            break;

        case 'logout':
            $authCtrl->logout();
            // logout fait un redirect
            break;

        case 'update_profile':
            if (empty($_SESSION['user_id'])) {
                header('Location: index.php?page=login');
                exit;
            }
            $result = $patientCtrl->updateProfile($_SESSION['user_id'], $_POST);
            if ($result['success']) {
                $flash = ['message' => 'Profil mis à jour avec succès', 'type' => 'success'];
            } else {
                $errors = $result['errors'];
                $flash = ['message' => 'Erreur lors de la mise à jour', 'type' => 'error'];
            }
            $page = 'profile';
            break;

        case 'change_password':
            if (empty($_SESSION['user_id'])) {
                header('Location: index.php?page=login');
                exit;
            }
            $result = $patientCtrl->changePassword(
                $_SESSION['user_id'],
                $_POST['old_password'] ?? '',
                $_POST['new_password'] ?? '',
                $_POST['confirm_password'] ?? ''
            );
            if ($result['success']) {
                $flash = ['message' => 'Mot de passe mis à jour', 'type' => 'success'];
            } else {
                $errors = $result['errors'];
                $flash = ['message' => $errors['old_password'] ?? $errors['new_password'] ?? 'Erreur', 'type' => 'error'];
            }
            $page = 'profile';
            break;

        case 'update_pro_profile':
            if (empty($_SESSION['user_id'])) {
                header('Location: index.php?page=login');
                exit;
            }
            $result = $proCtrl->updateProfile($_SESSION['user_id'], $_POST);
            if ($result['success']) {
                $flash = ['message' => 'Profil mis à jour avec succès', 'type' => 'success'];
            } else {
                $errors = $result['errors'];
                $flash = ['message' => 'Erreur lors de la mise à jour', 'type' => 'error'];
            }
            $page = 'professionnel';
            break;

        case 'change_pro_password':
            if (empty($_SESSION['user_id'])) {
                header('Location: index.php?page=login');
                exit;
            }
            $result = $proCtrl->changePassword(
                $_SESSION['user_id'],
                $_POST['old_password'] ?? '',
                $_POST['new_password'] ?? '',
                $_POST['confirm_password'] ?? ''
            );
            if ($result['success']) {
                $flash = ['message' => 'Mot de passe mis à jour', 'type' => 'success'];
            } else {
                $errors = $result['errors'];
                $flash = ['message' => $errors['old_password'] ?? $errors['new_password'] ?? 'Erreur', 'type' => 'error'];
            }
            $page = 'professionnel';
            break;
    }
}

// ─── Préparer les données pour la vue ───
$profileData = null;
if ($page === 'profile') {
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }
    // Rediriger un professionnel qui tenterait d'accéder à /profile
    if (($_SESSION['user_role'] ?? '') === 'Professionnel') {
        header('Location: index.php?page=professionnel');
        exit;
    }
    $profileData = $patientCtrl->getProfil($_SESSION['user_id']);
}

if ($page === 'professionnel') {
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }
    // Rediriger un patient qui tenterait d'accéder à /professionnel
    if (($_SESSION['user_role'] ?? '') !== 'Professionnel') {
        header('Location: index.php?page=profile');
        exit;
    }
    $profileData = $proCtrl->getProfil($_SESSION['user_id']);
}

// ─── Routing des vues ───
$validPages = ['home', 'login', 'register', 'profile', 'professionnel'];
if (!in_array($page, $validPages)) {
    $page = 'home';
}

$viewFile = __DIR__ . '/views/frontoffice/' . $page . '.php';

// ─── Rendu du layout ───
include __DIR__ . '/views/frontoffice/layout.php';
