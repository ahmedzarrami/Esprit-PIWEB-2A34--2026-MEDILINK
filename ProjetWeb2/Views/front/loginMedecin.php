<?php
// Démarrer la session
session_start();

// Gérer la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: loginMedecin.php');
    exit;
}

$basePath = dirname(__DIR__) . '/..';
require_once $basePath . '/config.php';
require_once $basePath . '/Controller/rendezvousC.php';

$rendezvousController = new RendezvousC();
$error_message = '';
$success_message = '';

// Vérifier si l'utilisateur est déjà authentifié
if (isset($_SESSION['medecin_id']) && !empty($_SESSION['medecin_id'])) {
    // Vérifier que le médecin existe toujours
    if ($rendezvousController->medecinExists($_SESSION['medecin_id'])) {
        header('Location: gestionFichePatient.php');
        exit;
    } else {
        // Si le médecin n'existe plus, détruire la session
        session_destroy();
        $error_message = '❌ Votre compte n\'existe plus. Veuillez vous reconnecter.';
    }
}

// Traiter la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medecin_id = trim($_POST['medecin_id'] ?? '');
    
    if (empty($medecin_id)) {
        $error_message = '❌ Veuillez entrer votre ID médecin';
    } else {
        // Vérifier que l'ID est numérique
        if (!is_numeric($medecin_id)) {
            $error_message = '❌ L\'ID médecin doit être un nombre';
        } else {
            // Vérifier si le médecin existe
            if ($rendezvousController->medecinExists($medecin_id)) {
                $medecin = $rendezvousController->getMedecinById($medecin_id);
                // Créer la session
                $_SESSION['medecin_id'] = $medecin_id;
                $_SESSION['medecin_nom'] = $medecin['nom'];
                $_SESSION['medecin_specialite'] = $medecin['specialite'];
                $success_message = '✅ Authentification réussie! Redirection...';
                // Redirection après un court délai
                header('Refresh: 1; url=gestionFichePatient.php');
            } else {
                $error_message = '❌ ID médecin non trouvé. Accès refusé.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink — Connexion Médecin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --blue:        #1a56db;
            --blue-dark:   #1a46c4;
            --blue-light:  #eff4ff;
            --blue-mid:    #6694f8;
            --green:       #0da271;
            --green-light: #ecfdf5;
            --navy:        #0f1b2d;
            --navy2:       #1e2f45;
            --red:         #dc2626;
            --red-light:   #fee2e2;
            --gray-50:     #f8fafc;
            --gray-100:    #f1f5f9;
            --gray-200:    #e2e8f0;
            --gray-400:    #94a3b8;
            --gray-600:    #475569;
            --gray-900:    #0f172a;
            --radius:      12px;
            --radius-lg:   18px;
            --radius-xl:   24px;
        }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #1a46c4 0%, #2563eb 55%, #3b7ff7 100%);
            color: var(--gray-900);
            font-size: 14px;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Décoration */
        body::before { content:''; position:fixed; top:-80px; right:-80px; width:350px; height:350px; background:rgba(255,255,255,.06); border-radius:50%; z-index:0; }
        body::after { content:''; position:fixed; bottom:-100px; left:42%; width:220px; height:220px; background:rgba(255,255,255,.04); border-radius:50%; z-index:0; }

        .login-container {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: #fff;
            border-radius: var(--radius-xl);
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-logo {
            font-size: 32px;
            margin-bottom: 16px;
        }

        .login-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 13px;
            color: var(--gray-600);
        }

        .alert {
            padding: 12px 14px;
            margin-bottom: 20px;
            border-radius: var(--radius-lg);
            font-size: 13px;
            animation: slideDown 0.3s ease;
        }

        .alert-error {
            background: var(--red-light);
            color: var(--red);
            border: 1px solid rgba(220, 38, 38, 0.2);
        }

        .alert-success {
            background: var(--green-light);
            color: var(--green);
            border: 1px solid rgba(13, 146, 113, 0.2);
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            display: block;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--gray-900);
            background: #fff;
            outline: none;
            transition: 0.15s;
        }

        .form-input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.1);
        }

        .form-input::placeholder {
            color: var(--gray-400);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--blue) 0%, var(--blue-dark) 100%);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-bottom: 16px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(26, 86, 219, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
        }

        .back-link a {
            font-size: 13px;
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.15s;
        }

        .back-link a:hover {
            opacity: 0.8;
        }

        .info-box {
            background: var(--blue-light);
            border: 1px solid rgba(26, 86, 219, 0.2);
            border-radius: var(--radius-lg);
            padding: 12px 14px;
            margin-top: 20px;
            font-size: 12px;
            color: var(--blue-dark);
            line-height: 1.5;
        }

        .info-box strong {
            display: block;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">🏥</div>
                <h1 class="login-title">MediLink</h1>
                <p class="login-subtitle">Espace Médecin</p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label class="form-label" for="medecin_id">ID Médecin</label>
                    <input 
                        type="text" 
                        id="medecin_id" 
                        name="medecin_id" 
                        class="form-input" 
                        placeholder="Entrez votre identifiant..." 
                        required 
                        autofocus
                        autocomplete="off"
                    >
                </div>

                <button type="submit" class="btn-login">
                    ✅ Accéder à mon espace
                </button>
            </form>

            <div class="info-box">
                <strong>📌 Information</strong>
                Veuillez entrer votre ID médecin pour accéder à votre espace de gestion des fiches patients.
            </div>

            <div class="back-link">
                <a href="home.php">← Retour à l'accueil</a>
            </div>
        </div>
    </div>
</body>
</html>
