<?php
/**
 * MediLink — Routeur principal
 * Modules : Médicament | Forum | Gestion RDV
 */
session_start();

// ── Config BDD Forum ──
require_once __DIR__ . '/config/database.php';

// ── Config BDD RDV ──
require_once __DIR__ . '/config.php';

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

// Session utilisateur démo
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'id'     => 1,
        'nom'    => 'Dupont',
        'prenom' => 'Jean',
        'email'  => 'jean.dupont@medilink.com',
        'role'   => 'administrateur'
    ];
}

$module     = $_GET['module']     ?? 'rdv';
$controller = $_GET['controller'] ?? '';
$action     = $_GET['action']     ?? 'home';
$id         = $_GET['id']         ?? null;

switch ($module) {

    // ── Module Forum ──
    case 'forum':
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
            if (!method_exists($ctrl, $action)) {
                throw new Exception("Action introuvable : " . htmlspecialchars($action));
            }
            $id !== null ? $ctrl->$action($id) : $ctrl->$action();
        } catch (Exception $e) {
            http_response_code(404);
            echo '<p style="color:red">Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        break;

    // ── Module Médicament — redirige vers public/ ──
    case 'medicament':
        $side = $_GET['side'] ?? 'front';
        if ($side === 'back') {
            require __DIR__ . '/public/back/index.php';
        } else {
            require __DIR__ . '/public/front/index.php';
        }
        break;

    // ── Module RDV (défaut) ──
    case 'rdv':
    default:
        switch ($action) {
            case 'patient': require __DIR__ . '/View/front/homePatient.php'; break;
            case 'medecin': require __DIR__ . '/View/front/home.php';        break;
            case 'admin':   require __DIR__ . '/View/admin/admin.php';       break;
            default:        require __DIR__ . '/View/front/home.php';
        }
        break;
}
?>
