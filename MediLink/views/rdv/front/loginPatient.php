<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Déconnexion ──
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /medilink_medicament/MediLink/index.php?module=rdv&action=patient&logout=1');
    exit;
}

// ── Déjà connecté → rediriger ──
if (isset($_SESSION['patient_id']) && !empty($_SESSION['patient_id'])) {
    header('Location: /medilink_medicament/MediLink/index.php?module=rdv&action=patient');
    exit;
}

$error   = '';
$success = isset($_GET['deconnecte']);

// ── Traitement connexion ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once dirname(dirname(dirname(__DIR__))) . '/config.php';
    require_once dirname(dirname(dirname(__DIR__))) . '/controllers/rendezvousC.php';

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
                $_SESSION['patient_id']     = $patient_id;
                $_SESSION['patient_nom']    = $patient['nom'];
                $_SESSION['patient_prenom'] = $patient['prenom'];
                $_SESSION['patient_email']  = $patient['email'];
                header('Location: /medilink_medicament/MediLink/index.php?module=rdv&action=patient');
                exit;
            } else {
                $error = 'Numéro de patient invalide. Veuillez vérifier et réessayer.';
            }
        } catch (Exception $e) {
            $error = 'Erreur : ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink — Connexion Patient</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --blue:      #1a56db;
            --blue-dark: #1a46c4;
            --green:     #0da271;
            --navy:      #0f1b2d;
            --gray-200:  #e2e8f0;
            --gray-400:  #94a3b8;
            --gray-600:  #475569;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #0f1b2d 0%, #1a46c4 55%, #2563eb 100%);
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: fixed; top: -120px; right: -120px;
            width: 420px; height: 420px;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
        }
        body::after {
            content: '';
            position: fixed; bottom: -100px; left: 30%;
            width: 280px; height: 280px;
            background: rgba(255,255,255,.04);
            border-radius: 50%;
        }

        /* ── CARD ── */
        .card {
            background: #fff;
            border-radius: 24px;
            padding: 44px 40px 36px;
            width: 100%;
            max-width: 430px;
            box-shadow: 0 24px 64px rgba(0,0,0,.22);
            position: relative;
            z-index: 1;
            animation: slideUp .45s cubic-bezier(.34,1.56,.64,1);
        }
        @keyframes slideUp {
            from { opacity:0; transform:translateY(32px) scale(.96); }
            to   { opacity:1; transform:none; }
        }

        /* ── LOGO ── */
        .logo {
            text-align: center;
            margin-bottom: 28px;
        }
        .logo-icon {
            width: 64px; height: 64px;
            border-radius: 18px;
            background: linear-gradient(135deg, #1a56db, #0da271);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 28px;
            box-shadow: 0 8px 24px rgba(26,86,219,.35);
            margin-bottom: 14px;
        }
        .logo-name {
            font-size: 26px; font-weight: 700; letter-spacing: -.5px;
            background: linear-gradient(90deg, #1a56db, #0da271);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .logo-sub { font-size: 13px; color: var(--gray-400); margin-top: 4px; }

        /* ── TITRE ── */
        .page-title {
            font-size: 20px; font-weight: 700;
            color: var(--navy); text-align: center;
            margin-bottom: 6px;
        }
        .page-sub {
            font-size: 13px; color: var(--gray-600);
            text-align: center; margin-bottom: 28px; line-height: 1.5;
        }

        /* ── ALERTES ── */
        .alert {
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex; align-items: flex-start; gap: 8px;
            animation: fadeIn .3s ease;
        }
        @keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:none} }
        .alert.error   { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
        .alert.success { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }

        /* ── FORM ── */
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 11px; font-weight: 700;
            color: var(--gray-600);
            text-transform: uppercase; letter-spacing: .06em;
            margin-bottom: 8px;
        }
        .form-input {
            width: 100%;
            padding: 13px 16px;
            border: 1.5px solid var(--gray-200);
            border-radius: 12px;
            font-family: inherit; font-size: 15px;
            color: var(--navy); background: #f8fafc;
            transition: .15s; outline: none;
            letter-spacing: 3px;
        }
        .form-input:focus {
            border-color: var(--blue);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(26,86,219,.1);
        }
        .form-input::placeholder { color: var(--gray-400); letter-spacing: 0; }

        /* ── BOUTON ── */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--blue), #2563eb);
            color: #fff; border: none; border-radius: 12px;
            font-family: inherit; font-size: 14px; font-weight: 700;
            cursor: pointer; transition: .2s; margin-top: 4px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(26,86,219,.38);
        }
        .btn-login:active { transform: none; }

        /* ── INFO BOX ── */
        .info-box {
            background: #eff4ff;
            border: 1px solid rgba(26,86,219,.2);
            border-radius: 12px;
            padding: 14px 16px;
            margin-top: 20px;
            font-size: 12px; color: #1e40af;
            line-height: 1.6;
        }
        .info-box strong { display: block; margin-bottom: 5px; }

        /* ── LIEN RETOUR ── */
        .back-link {
            text-align: center;
            margin-top: 22px;
            position: relative; z-index: 1;
        }
        .back-link a {
            font-size: 13px; font-weight: 500;
            color: rgba(255,255,255,.75);
            text-decoration: none; transition: .15s;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .back-link a:hover { color: #fff; }

        @media (max-width: 480px) {
            .card { padding: 32px 24px 28px; }
        }
    </style>
</head>
<body>

<div class="card">

    <div class="logo">
        <div class="logo-icon">🏥</div>
        <div class="logo-name">MediLink</div>
        <div class="logo-sub">Plateforme médicale · Espace Patient</div>
    </div>

    <div class="page-title">Connexion Patient</div>
    <p class="page-sub">Accédez à votre espace pour gérer vos rendez-vous médicaux</p>

    <?php if ($success): ?>
        <div class="alert success">✅ Vous avez été déconnecté avec succès.</div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert error">❌ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <div class="form-group">
            <label class="form-label" for="patient_id">🆔 Numéro de Patient</label>
            <input
                type="text"
                id="patient_id"
                name="patient_id"
                class="form-input"
                placeholder="Ex : 000001"
                required
                maxlength="6"
                autofocus
                inputmode="numeric"
                pattern="[0-9]*"
            >
        </div>
        <button type="submit" class="btn-login">🔐 Se Connecter</button>
    </form>

    <div class="info-box">
        <strong>💡 Première connexion ?</strong>
        Votre numéro de patient vous a été communiqué lors de votre inscription. Il est composé de 6 chiffres.
    </div>

</div>

<div class="back-link">
    <a href="/medilink_medicament/MediLink/index.php">← Retour à l'accueil</a>
</div>

</body>
</html>