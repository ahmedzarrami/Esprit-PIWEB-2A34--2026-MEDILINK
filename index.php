<?php
session_start();

// ── Dépendances module Gestion Utilisateur ──
require_once __DIR__ . '/controller/AuthController.php';
require_once __DIR__ . '/controller/PatientController.php';
require_once __DIR__ . '/controller/ProfessionnelController.php';
require_once __DIR__ . '/controller/PasswordResetController.php';
require_once __DIR__ . '/controller/FaceAuthController.php';

// ── Dépendances module Forum ──
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/Model/Utilisateur.php';
require_once __DIR__ . '/Model/Forum.php';
require_once __DIR__ . '/Model/Post.php';
require_once __DIR__ . '/Model/Commentaire.php';
require_once __DIR__ . '/Model/BadWordsFilter.php';
require_once __DIR__ . '/Controller/ForumController.php';
require_once __DIR__ . '/Controller/PostController.php';
require_once __DIR__ . '/Controller/CommentaireController.php';

// ── Dépendances module Gestion RDV ──
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Controller/rendezvousC.php';
require_once __DIR__ . '/Controller/fichePatientC.php';
require_once __DIR__ . '/Controller/evaluationC.php';

$authCtrl    = new AuthController();
$patientCtrl = new PatientController();
$proCtrl     = new ProfessionnelController();
$resetCtrl   = new PasswordResetController();
$faceCtrl    = new FaceAuthController();

$controller = $_GET['controller'] ?? null;
$page       = $_GET['page']       ?? null;
$action     = $_GET['action']     ?? ($_POST['action'] ?? null);
$id         = $_GET['id']         ?? null;

// ── Routing contrôleur (Forum / Médicaments / RDV) ──
if ($controller !== null) {
    $routeAction = $action ?? 'list';
    try {
        switch ($controller) {
            case 'forum':
                $ctrl = new ForumController();
                break;
            case 'post':
                $ctrl = new PostController();
                break;
            case 'commentaire':
                $ctrl = new CommentaireController();
                break;
            default:
                throw new Exception("Contrôleur introuvable : " . htmlspecialchars($controller));
        }

        if (!method_exists($ctrl, $routeAction)) {
            throw new Exception("Action introuvable : " . htmlspecialchars($routeAction));
        }

        if ($id !== null) {
            $ctrl->$routeAction($id);
        } else {
            $ctrl->$routeAction();
        }
    } catch (Exception $e) {
        http_response_code(404);
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>MediLink — Erreur</title>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Inter', sans-serif;
                    background: #0f172a;
                    color: #e2e8f0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                }
                .error-container {
                    text-align: center;
                    padding: 3rem;
                    background: rgba(30, 41, 59, 0.8);
                    border: 1px solid rgba(59, 130, 246, 0.2);
                    border-radius: 1.5rem;
                    backdrop-filter: blur(20px);
                    max-width: 500px;
                }
                .error-code {
                    font-size: 5rem;
                    font-weight: 700;
                    background: linear-gradient(135deg, #3b82f6, #14b8a6);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    line-height: 1;
                }
                .error-message { margin-top: 1rem; font-size: 1.1rem; color: #94a3b8; }
                .error-detail  { margin-top: 0.5rem; font-size: 0.85rem; color: #64748b; }
                .error-link {
                    display: inline-block;
                    margin-top: 2rem;
                    padding: 0.75rem 2rem;
                    background: linear-gradient(135deg, #3b82f6, #14b8a6);
                    color: #fff;
                    text-decoration: none;
                    border-radius: 0.75rem;
                    font-weight: 600;
                    transition: transform 0.2s, box-shadow 0.2s;
                }
                .error-link:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-code">404</div>
                <p class="error-message">Page introuvable</p>
                <p class="error-detail"><?= htmlspecialchars($e->getMessage()) ?></p>
                <a href="index.php" class="error-link">Retour à l'accueil</a>
            </div>
        </body>
        </html>
        <?php
    }
    exit;
}

// ── Routing page RDV (sans controller dans l'URL, via ?action=) ──
if ($page === null && $action !== null && in_array($action, ['patient', 'medecin', 'home', 'admin'])) {
    switch ($action) {
        case 'patient':
            require __DIR__ . '/Views/front/homePatient.php';
            break;
        case 'medecin':
        case 'home':
            require __DIR__ . '/Views/front/home.php';
            break;
        case 'admin':
            require __DIR__ . '/Views/admin/admin.php';
            break;
    }
    exit;
}

// ── Routing Gestion Utilisateur (?page=...) ──
$page   = $page ?? 'home';
$errors = [];
$flash  = null;
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

        case 'forgot_password':
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

        case 'face_login':
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

if ($page === 'reset_code' && empty($_SESSION['reset_email'])) {
    $page = 'forgot_password';
}
if ($page === 'reset_password' && (empty($_SESSION['reset_email']) || empty($_SESSION['reset_verified']))) {
    $page = 'forgot_password';
}

$viewFile = __DIR__ . '/view/frontoffice/' . $page . '.php';

include __DIR__ . '/view/frontoffice/layout.php';
