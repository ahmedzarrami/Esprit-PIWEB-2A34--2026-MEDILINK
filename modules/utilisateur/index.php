<?php
/**
 * MediLink - Front Office Router (index.php)
 * Point d'entree pour les patients et les professionnels de sante
 */
require_once __DIR__ . '/../../config/session.php';

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/PatientController.php';
require_once __DIR__ . '/controllers/ProfessionnelController.php';
require_once __DIR__ . '/controllers/PasswordResetController.php';
require_once __DIR__ . '/controllers/FaceAuthController.php';

$authCtrl    = new AuthController();
$patientCtrl = new PatientController();
$proCtrl     = new ProfessionnelController();
$resetCtrl   = new PasswordResetController();
$faceCtrl    = new FaceAuthController();

$page   = $_GET['page']   ?? 'home';
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

$errors  = [];
$flash   = null;
$devCode = null;

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
                // Redirection vers la cible originale si stockee, sinon par role
                $back = $_SESSION['login_redirect'] ?? null;
                unset($_SESSION['login_redirect']);
                if ($back) {
                    header('Location: ' . $back);
                    exit;
                }
                if ($role === 'Administrateur') {
                    header('Location: /files40/admin/index.php');
                    exit;
                } elseif ($role === 'Professionnel') {
                    header('Location: /files40/index.php');
                    exit;
                } else {
                    header('Location: /files40/index.php');
                    exit;
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

        // ─── RÉINITIALISATION MOT DE PASSE ───

        case 'forgot_password':
            // Accepte aussi GET avec ?action=forgot_password&email=... (lien "renvoyer")
            $emailInput = $_POST['email'] ?? $_GET['email'] ?? '';
            $result = $resetCtrl->demanderReinitialisation($emailInput);
            if ($result['success']) {
                $devCode = $result['dev_code'] ?? null;
                $flash   = ['message' => $result['message'], 'type' => 'info'];
                $page    = 'reset_code';
            } else {
                $errors = $result['errors'];
                $page   = 'forgot_password';
            }
            break;

        case 'verify_reset_code':
            $result = $resetCtrl->verifierCodeSession($_POST['code'] ?? '');
            if ($result['success']) {
                // Stocker le code pour la prochaine étape (double vérification serveur)
                $_SESSION['reset_code_used'] = trim($_POST['code'] ?? '');
                $page = 'reset_password';
            } else {
                $errors  = $result['errors'];
                $devCode = null;
                $page    = 'reset_code';
            }
            break;

        case 'reset_password':
            $result = $resetCtrl->reinitialiserMotDePasse(
                $_SESSION['reset_code_used'] ?? '',
                $_POST['nouveau_mdp']  ?? '',
                $_POST['confirm_mdp']  ?? ''
            );
            if ($result['success']) {
                unset($_SESSION['reset_code_used']);
                $flash = ['message' => 'Mot de passe réinitialisé avec succès. Vous pouvez vous connecter.', 'type' => 'success'];
                $page  = 'login';
            } else {
                $errors = $result['errors'];
                $page   = 'reset_password';
            }
            break;

        // ─── RECONNAISSANCE FACIALE (AJAX JSON) ───

        case 'face_login':
            // Réponse JSON pour les appels AJAX (face-api.js)
            header('Content-Type: application/json; charset=utf-8');
            $body = file_get_contents('php://input');
            $data = json_decode($body, true);
            $descriptor = $data['descriptor'] ?? null;
            if (!$descriptor || !is_array($descriptor)) {
                echo json_encode(['success' => false, 'error' => 'Descripteur manquant.']);
                exit;
            }
            $result = $authCtrl->faceLogin(json_encode($descriptor));
            echo json_encode($result);
            exit;

        case 'save_face':
            // Réponse JSON pour l'enregistrement du visage depuis le profil
            header('Content-Type: application/json; charset=utf-8');
            if (empty($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Non authentifié.']);
                exit;
            }
            $body = file_get_contents('php://input');
            $data = json_decode($body, true);
            $descriptor = $data['descriptor'] ?? null;
            if (!$descriptor || !is_array($descriptor)) {
                echo json_encode(['success' => false, 'error' => 'Descripteur manquant.']);
                exit;
            }
            $result = $faceCtrl->enregistrerVisage((int)$_SESSION['user_id'], json_encode($descriptor));
            echo json_encode($result);
            exit;

        case 'delete_face':
            // Suppression du descripteur facial
            header('Content-Type: application/json; charset=utf-8');
            if (empty($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Non authentifié.']);
                exit;
            }
            $ok = FaceAuthController::supprimerDescripteur((int)$_SESSION['user_id']);
            echo json_encode(['success' => $ok]);
            exit;
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
$validPages = ['home', 'login', 'register', 'profile', 'professionnel',
               'forgot_password', 'reset_code', 'reset_password'];
if (!in_array($page, $validPages)) {
    $page = 'home';
}

// Si l'utilisateur est deja connecte et qu'il atterrit sur login/register/home,
// on l'envoie soit a la cible memorisee, soit a l'accueil approprie.
if (is_logged_in() && in_array($page, ['login', 'register', 'home'], true)) {
    $back = $_SESSION['login_redirect'] ?? null;
    unset($_SESSION['login_redirect']);
    if ($back) {
        header('Location: ' . $back);
        exit;
    }
    $role = current_role();
    if ($role === 'Administrateur') {
        header('Location: /files40/admin/index.php');
    } else {
        header('Location: /files40/index.php');
    }
    exit;
}

// Protéger les pages reset_code et reset_password : nécessitent une session de reset active
if ($page === 'reset_code' && empty($_SESSION['reset_email'])) {
    $page = 'forgot_password';
}
if ($page === 'reset_password' && (empty($_SESSION['reset_email']) || empty($_SESSION['reset_verified']))) {
    $page = 'forgot_password';
}

$viewFile = __DIR__ . '/views/frontoffice/' . $page . '.php';

// ─── Rendu du layout ───
include __DIR__ . '/views/frontoffice/layout.php';
