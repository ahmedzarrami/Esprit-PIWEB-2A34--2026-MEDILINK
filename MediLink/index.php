<?php
/**
 * MediLink — Routeur principal intégré
 * Modules : Authentification | Gestion Utilisateurs | Médicament | Forum | Gestion RDV
 */
session_start();

// ── Flash session (PRG pattern) ──
$flash = null;
if (!empty($_SESSION['_flash'])) {
    $flash = $_SESSION['_flash'];
    unset($_SESSION['_flash']);
}

// ── Config BDD ──
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config.php';

// ── Contrôleurs Authentification & Gestion Utilisateurs ──
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/PatientController.php';
require_once __DIR__ . '/controllers/ProfessionnelController.php';
require_once __DIR__ . '/controllers/PasswordResetController.php';
require_once __DIR__ . '/controllers/FaceAuthController.php';

// ── Modèles Forum ──
require_once __DIR__ . '/models/Utilisateur.php';
require_once __DIR__ . '/models/Forum.php';
require_once __DIR__ . '/models/Post.php';
require_once __DIR__ . '/models/Commentaire.php';
require_once __DIR__ . '/models/BadWordsFilter.php';

// ── Contrôleurs Forum ──
require_once __DIR__ . '/controllers/ForumController.php';
require_once __DIR__ . '/controllers/PostController.php';
require_once __DIR__ . '/controllers/CommentaireController.php';

$page   = $_GET['page'] ?? 'home';
$module = $_GET['module'] ?? 'home';
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// Auto-détecter le module forum si controller est fourni sans module
$forumControllers = ['forum', 'post', 'commentaire'];
if ($module === 'home' && in_array($_GET['controller'] ?? '', $forumControllers, true)) {
    $module = 'forum';
}

$errors  = [];
$flash   = null;
$devCode = null;

// ─── Routes Authentification ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($action)) {
    $authCtrl    = new AuthController();
    $patientCtrl = new PatientController();
    $proCtrl     = new ProfessionnelController();
    $resetCtrl   = new PasswordResetController();
    $faceCtrl    = new FaceAuthController();

    switch ($action) {
        case 'login':
            $result = $authCtrl->login($_POST['email'] ?? '', $_POST['mot_de_passe'] ?? '');
            if ($result['success']) {
                $role = strtolower($_SESSION['user_role'] ?? 'patient');
                $_SESSION['_flash'] = ['message' => 'Bienvenue ' . ($_SESSION['user_nom'] ?? '') . ' !', 'type' => 'success'];
                if ($role === 'administrateur') {
                    header('Location: /medilink_medicament/MediLink/index.php?module=rdv&action=admin');
                } elseif ($role === 'professionnel') {
                    header('Location: /medilink_medicament/MediLink/index.php?page=professionnel');
                } else {
                    header('Location: /medilink_medicament/MediLink/index.php?page=profile');
                }
                exit;
            } else {
                $errors = $result['errors'];
                $page = 'login';
            }
            break;

        case 'register':
            $result = $authCtrl->register($_POST);
            if ($result['success']) {
                $_SESSION['_flash'] = ['message' => 'Compte créé avec succès !', 'type' => 'success'];
                header('Location: /medilink_medicament/MediLink/index.php?page=profile');
                exit;
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
                header('Location: /medilink_medicament/MediLink/index.php?page=login');
                exit;
            }
            $result = $patientCtrl->updateProfile($_SESSION['user_id'], $_POST);
            $_SESSION['_flash'] = $result['success']
                ? ['message' => 'Profil mis à jour avec succès', 'type' => 'success']
                : ['message' => 'Erreur lors de la mise à jour', 'type' => 'error'];
            header('Location: /medilink_medicament/MediLink/index.php?page=profile');
            exit;

        case 'change_password':
            if (empty($_SESSION['user_id'])) {
                header('Location: /medilink_medicament/MediLink/index.php?page=login');
                exit;
            }
            $result = $patientCtrl->changePassword(
                $_SESSION['user_id'],
                $_POST['old_password'] ?? '',
                $_POST['new_password'] ?? '',
                $_POST['confirm_password'] ?? ''
            );
            if ($result['success']) {
                $_SESSION['_flash'] = ['message' => 'Mot de passe mis à jour', 'type' => 'success'];
            } else {
                $errs = $result['errors'];
                $_SESSION['_flash'] = ['message' => $errs['old_password'] ?? $errs['new_password'] ?? 'Erreur', 'type' => 'error'];
            }
            header('Location: /medilink_medicament/MediLink/index.php?page=profile');
            exit;

        case 'update_pro_profile':
            if (empty($_SESSION['user_id'])) {
                header('Location: /medilink_medicament/MediLink/index.php?page=login');
                exit;
            }
            $result = $proCtrl->updateProfile($_SESSION['user_id'], $_POST);
            $_SESSION['_flash'] = $result['success']
                ? ['message' => 'Profil mis à jour avec succès', 'type' => 'success']
                : ['message' => 'Erreur lors de la mise à jour', 'type' => 'error'];
            header('Location: /medilink_medicament/MediLink/index.php?page=professionnel');
            exit;

        case 'change_pro_password':
            if (empty($_SESSION['user_id'])) {
                header('Location: /medilink_medicament/MediLink/index.php?page=login');
                exit;
            }
            $result = $proCtrl->changePassword(
                $_SESSION['user_id'],
                $_POST['old_password'] ?? '',
                $_POST['new_password'] ?? '',
                $_POST['confirm_password'] ?? ''
            );
            if ($result['success']) {
                $_SESSION['_flash'] = ['message' => 'Mot de passe mis à jour', 'type' => 'success'];
            } else {
                $errs = $result['errors'];
                $_SESSION['_flash'] = ['message' => $errs['old_password'] ?? $errs['new_password'] ?? 'Erreur', 'type' => 'error'];
            }
            header('Location: /medilink_medicament/MediLink/index.php?page=professionnel');
            exit;

        case 'face_login':
            header('Content-Type: application/json');
            $input      = json_decode(file_get_contents('php://input'), true);
            $descriptor = $input['descriptor'] ?? null;
            if (!is_array($descriptor) || count($descriptor) !== 128) {
                echo json_encode(['success' => false, 'error' => 'Descripteur facial invalide.']);
                exit;
            }
            $result = $faceCtrl->seConnecterParVisage(json_encode($descriptor));
            echo json_encode($result);
            exit;

        case 'save_face':
            header('Content-Type: application/json');
            if (empty($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Non connecté.']);
                exit;
            }
            $input      = json_decode(file_get_contents('php://input'), true);
            $descriptor = $input['descriptor'] ?? null;
            if (!is_array($descriptor) || count($descriptor) !== 128) {
                echo json_encode(['success' => false, 'error' => 'Descripteur facial invalide.']);
                exit;
            }
            $result = $faceCtrl->enregistrerVisage((int) $_SESSION['user_id'], json_encode($descriptor));
            echo json_encode($result);
            exit;

        case 'delete_face':
            header('Content-Type: application/json');
            if (empty($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Non connecté.']);
                exit;
            }
            $ok = FaceAuthController::supprimerDescripteur((int) $_SESSION['user_id']);
            echo json_encode(['success' => $ok]);
            exit;
    }
}

// ─── Routes Modules ───
switch ($module) {

    // ── Page d'accueil / Auth ──
    case 'home':
    case 'auth':
        // Redirection automatique si l'utilisateur est connecté et visite la home
        if ($page === 'home' && !empty($_SESSION['user_id'])) {
            $_roleHome = strtolower($_SESSION['user_role'] ?? 'patient');
            if ($_roleHome === 'administrateur') {
                header('Location: /medilink_medicament/MediLink/public/back/index.php');
                exit;
            } elseif ($_roleHome === 'professionnel') {
                header('Location: /medilink_medicament/MediLink/index.php?page=professionnel');
                exit;
            } else {
                header('Location: /medilink_medicament/MediLink/index.php?page=profile');
                exit;
            }
        }
        if ($page === 'profile' && !empty($_SESSION['user_id']) && empty($profileData)) {
            if (!isset($patientCtrl)) $patientCtrl = new PatientController();
            $profileData = $patientCtrl->getProfil((int) $_SESSION['user_id']);
        }

        if ($page === 'professionnel' && !empty($_SESSION['user_id']) && empty($profileData)) {
            if (!isset($patientCtrl)) $patientCtrl = new PatientController();
            $profileData = $patientCtrl->getProfil((int) $_SESSION['user_id']);

            // Bridge auth unifiée → medecin_id RDV
            require_once __DIR__ . '/controllers/rendezvousC.php';
            if (empty($_SESSION['medecin_id'])) {
                $pdo = config::getConnexion();
                $stmt = $pdo->prepare("SELECT id, nom, specialite FROM medecins WHERE email = :email LIMIT 1");
                $stmt->execute([':email' => $_SESSION['user_email'] ?? '']);
                $rdvM = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$rdvM) {
                    $stmtU = $pdo->prepare("
                        SELECT u.prenom, u.nom, u.email, u.telephone, ps.specialite
                        FROM utilisateur u
                        LEFT JOIN professionnel_sante ps ON ps.id = u.id
                        WHERE u.id = :id LIMIT 1
                    ");
                    $stmtU->execute([':id' => (int)$_SESSION['user_id']]);
                    $uInfo = $stmtU->fetch(PDO::FETCH_ASSOC);
                    if ($uInfo) {
                        $nomComplet = 'Dr. ' . trim($uInfo['prenom']) . ' ' . trim($uInfo['nom']);
                        $spec = !empty($uInfo['specialite']) ? $uInfo['specialite'] : 'Médecine générale';
                        $stmtIns = $pdo->prepare("INSERT IGNORE INTO medecins (nom, specialite, email, telephone) VALUES (:nom, :spec, :email, :tel)");
                        $stmtIns->execute([':nom' => $nomComplet, ':spec' => $spec, ':email' => $uInfo['email'], ':tel' => $uInfo['telephone'] ?? '']);
                        $stmt2 = $pdo->prepare("SELECT id, nom, specialite FROM medecins WHERE email = :email LIMIT 1");
                        $stmt2->execute([':email' => $uInfo['email']]);
                        $rdvM = $stmt2->fetch(PDO::FETCH_ASSOC);
                    }
                }
                if ($rdvM) {
                    $_SESSION['medecin_id']         = $rdvM['id'];
                    $_SESSION['medecin_nom']        = $rdvM['nom'];
                    $_SESSION['medecin_specialite'] = $rdvM['specialite'];
                }
            }

            // Charger les RDVs du médecin connecté
            $medecin_rdvs    = [];
            $medecin_patients = [];
            $pro_stats        = ['today' => 0, 'pending' => 0, 'total_patients' => 0, 'month' => 0];
            if (!empty($_SESSION['medecin_id'])) {
                $rdvCtrl      = new RendezvousC();
                $medecin_rdvs = $rdvCtrl->getRendezvousByMedecinId((int)$_SESSION['medecin_id']);
                $today        = date('Y-m-d');
                $thisMonth    = date('Y-m');
                $seen_patients = [];
                foreach ($medecin_rdvs as $rdv) {
                    if ($rdv['date_rdv'] === $today) $pro_stats['today']++;
                    if (($rdv['statut'] ?? '') === 'en attente') $pro_stats['pending']++;
                    if (str_starts_with($rdv['date_rdv'], $thisMonth)) $pro_stats['month']++;
                    $pid = $rdv['patient_id'] ?? null;
                    if ($pid && !isset($seen_patients[$pid])) {
                        $seen_patients[$pid] = [
                            'id'       => $pid,
                            'nom'      => $rdv['patient_nom']    ?? '',
                            'prenom'   => $rdv['patient_prenom'] ?? '',
                            'tel'      => $rdv['patient_tel']    ?? '',
                            'email'    => $rdv['patient_email']  ?? '',
                            'last_rdv' => $rdv['date_rdv'],
                        ];
                    }
                }
                $medecin_patients        = array_values($seen_patients);
                $pro_stats['total_patients'] = count($medecin_patients);
            }
        }
        $viewFile = __DIR__ . '/views/frontoffice/' . $page . '.php';
        if (!file_exists($viewFile)) {
            $viewFile = __DIR__ . '/views/frontoffice/home.php';
        }
        require __DIR__ . '/views/frontoffice/layout.php';
        break;

    // ── Module Forum ──
    case 'forum':
        $controller = $_GET['controller'] ?? '';
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
            $id = $_GET['id'] ?? null;
            if (!method_exists($ctrl, $action)) {
                throw new Exception("Action introuvable : " . htmlspecialchars($action));
            }
            $id !== null ? $ctrl->$action($id) : $ctrl->$action();
        } catch (Exception $e) {
            http_response_code(404);
            echo '<p style="color:red">Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        break;

    // ── Module Médicament ──
    case 'medicament':
        $side = $_GET['side'] ?? 'front';
        if ($side === 'back') {
            require __DIR__ . '/public/back/index.php';
        } else {
            require __DIR__ . '/public/front/index.php';
        }
        break;

    // ── Module RDV ──
    case 'rdv':
        switch ($action) {
            case 'patient': require __DIR__ . '/views/rdv/front/homePatient.php'; break;
            case 'medecin': require __DIR__ . '/views/rdv/front/loginMedecin.php'; break;
            case 'admin':   require __DIR__ . '/views/rdv/admin/admin.php';       break;
            default:        require __DIR__ . '/views/rdv/front/home.php';
        }
        break;

    // ── Module Parapharmacie ──
    case 'parapharmacie':
        require_once __DIR__ . '/controllers/ParapharmacieController.php';
        $paraCtrl = new ParapharmacieController();
        $categorie = $_GET['categorie'] ?? null;
        $produits = $paraCtrl->listProduits($categorie);
        $categories = $paraCtrl->getCategories();
        $viewFile = __DIR__ . '/views/frontoffice/parapharmacie.php';
        require __DIR__ . '/views/frontoffice/layout.php';
        break;

    default:
        // Page par défaut (home)
        $viewFile = __DIR__ . '/views/frontoffice/home.php';
        require __DIR__ . '/views/frontoffice/layout.php';
        break;
}
?>
