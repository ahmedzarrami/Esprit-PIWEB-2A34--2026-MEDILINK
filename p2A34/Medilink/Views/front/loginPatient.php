<?php
session_start();

// Si le patient est déjà connecté, le rediriger vers home
if (isset($_SESSION['patient_id']) && !empty($_SESSION['patient_id'])) {
    header('Location: homePatient.php');
    exit;
}

// Gestion de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once dirname(dirname(__DIR__)) . '/config.php';
    require_once dirname(dirname(__DIR__)) . '/Controller/rendezvousC.php';

    $patient_id = trim($_POST['patient_id'] ?? '');

    if (empty($patient_id)) {
        $error = 'Veuillez entrer votre numéro de patient.';
    } elseif (!is_numeric($patient_id)) {
        $error = 'Le numéro de patient doit être numérique.';
    } else {
        try {
            $rendezvousC = new RendezvousC();
            if ($rendezvousC->patientExists($patient_id)) {
                $patient = $rendezvousC->getPatientById($patient_id);
                $_SESSION['patient_id'] = $patient_id;
                $_SESSION['patient_nom'] = $patient['nom'];
                $_SESSION['patient_prenom'] = $patient['prenom'];
                $_SESSION['patient_email'] = $patient['email'];
                header('Location: homePatient.php');
                exit;
            } else {
                $error = 'Numéro de patient invalide. Veuillez vérifier et réessayer.';
            }
        } catch (Exception $e) {
            $error = 'Erreur: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['logout'])) {
    $success = true;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink — Connexion Patient</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --blue: #1a56db;
            --blue-dark: #1a46c4;
            --navy: #0f1b2d;
            --gray-50: #f8fafc;
            --gray-600: #475569;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #1a46c4 0%, #2563eb 55%, #3b7ff7 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-box {
            background: white;
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .logo {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            background: linear-gradient(90deg, #1a56db 0%, #0da271 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 12px;
            color: var(--navy);
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: var(--gray-600);
            font-size: 14px;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 500;
            color: var(--navy);
            margin-bottom: 8px;
            font-size: 13px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            transition: all 0.15s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.1);
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1a56db 0%, #2563eb 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #1a46c4 0%, #1e5bdb 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(26, 86, 219, 0.3);
        }
        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            border: 1px solid #fecaca;
        }
        .success-message {
            background: #ecfdf5;
            color: #065f46;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            border: 1px solid #a7f3d0;
        }
        .info-box {
            background: #eff4ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 14px;
            margin-top: 20px;
            font-size: 12px;
            color: #1e40af;
            line-height: 1.6;
        }
        .info-box strong { display: block; margin-bottom: 6px; }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: var(--blue);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: color 0.15s;
        }
        .back-link a:hover { color: var(--blue-dark); }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">💙 MediLink</div>
        <h1>Connexion Patient</h1>
        <p class="subtitle">Accédez à votre espace personnel pour consulter vos rendez-vous médicaux</p>

        <?php if ($success): ?>
            <div class="success-message">✅ Vous avez été déconnecté avec succès.</div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="error-message">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="patient_id">🆔 Numéro de Patient</label>
                <input type="text" id="patient_id" name="patient_id" placeholder="Exemple: 000001" required maxlength="6">
            </div>
            <button type="submit" class="btn-login">🔐 Se Connecter</button>
        </form>

        <div class="info-box">
            <strong>💡 Vous êtes nouveau?</strong>
            Votre numéro de patient vous a été communiqué lors de votre inscription. Il est composé de 6 chiffres.
        </div>

        <div class="back-link">
            <a href="../../index.php">← Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
