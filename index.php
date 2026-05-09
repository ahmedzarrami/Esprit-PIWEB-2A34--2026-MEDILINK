<?php
session_start();

// ── Dépendances module principal (Forum / Médicaments) ──
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

// Session utilisateur par défaut (démo)
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'id'     => 1,
        'nom'    => 'Dupont',
        'prenom' => 'Jean',
        'email'  => 'jean.dupont@medilink.com',
        'role'   => 'administrateur'
    ];
}

$controller = $_GET['controller'] ?? null;
$action     = $_GET['action']     ?? null;
$id         = $_GET['id']         ?? null;

// ── Routing page (Gestion RDV) — pas de controller dans l'URL ──
if ($controller === null) {
    $page = $action ?? 'home';
    switch ($page) {
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
        default:
            require __DIR__ . '/Views/front/home.php';
    }
    exit;
}

// ── Routing contrôleur (Forum / Médicaments / Ordonnances) ──
$action = $action ?? 'list';

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

    if ($id !== null) {
        $ctrl->$action($id);
    } else {
        $ctrl->$action();
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
