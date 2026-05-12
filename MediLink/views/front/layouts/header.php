<?php $currentAction = $_GET['action'] ?? 'home'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink – Front Office</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/medilink_medicament/MediLink/public/front/css/style.css">
</head>
<body>

<nav class="navbar-medilink">
    <a href="index.php" class="nav-logo">
        <div class="logo-mark">+</div>
        <div class="logo-text">Medi<span>Link</span></div>
    </a>

    <?php $_hRole = strtolower($_SESSION['user_role'] ?? ''); ?>
    <div class="nav-links">
        <a href="/medilink_medicament/MediLink/index.php" style="color:var(--gray-400);font-size:12px;">⬅ MediLink</a>
        <a href="index.php?action=home" class="<?= $currentAction === 'home' ? 'active' : '' ?>">Accueil</a>
        <a href="index.php?action=medicaments" class="<?= $currentAction === 'medicaments' ? 'active' : '' ?>">Médicaments</a>

        <?php /* Ordonnances : réservé aux médecins et admins uniquement */ ?>
        <?php if (in_array($_hRole, ['professionnel', 'administrateur'])): ?>
        <a href="index.php?action=ordonnances" class="<?= in_array($currentAction, ['ordonnances', 'create_ordonnance', 'show_ordonnance'], true) ? 'active' : '' ?>">Ordonnances</a>
        <?php endif; ?>

        <a href="index.php?action=assistant" class="<?= $currentAction === 'assistant' ? 'active' : '' ?>">🤖 Assistant</a>

        <?php /* Parapharmacie : visible pour patients et visiteurs non connectés */ ?>
        <?php if (!in_array($_hRole, ['professionnel', 'administrateur'])): ?>
        <a href="index.php?action=parapharmacie" class="<?= $currentAction === 'parapharmacie' ? 'active' : '' ?>">Parapharmacie</a>
        <?php endif; ?>

        <a href="/medilink_medicament/MediLink/index.php?module=forum&controller=forum&action=list">Forum</a>

        <?php if ($_hRole === 'professionnel'): ?>
        <a href="/medilink_medicament/MediLink/index.php?module=rdv&action=medecin">Mes RDV</a>
        <?php elseif ($_hRole === 'administrateur'): ?>
        <a href="/medilink_medicament/MediLink/index.php?module=rdv&action=admin">Gestion RDV</a>
        <?php else: ?>
        <a href="/medilink_medicament/MediLink/index.php?module=rdv">Rendez-vous</a>
        <?php endif; ?>
    </div>

    <div class="nav-actions">
        <?php if (!empty($_SESSION['user_id'])): ?>
        <span style="font-size:13px;color:var(--gray-600);font-weight:600">
            👤 <?= htmlspecialchars($_SESSION['user_nom'] ?? '') ?>
        </span>
        <?php if ($_hRole === 'administrateur'): ?>
        <a class="btn-backoffice" href="/medilink_medicament/MediLink/public/back/index.php">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            <span>Back Office</span>
        </a>
        <?php endif; ?>
        <?php if ($_hRole === 'professionnel'): ?>
        <a class="btn-admin" href="index.php?action=create_ordonnance">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Nouvelle ordonnance</span>
        </a>
        <?php endif; ?>
        <a class="btn-backoffice" href="/medilink_medicament/MediLink/index.php?action=logout" style="border-color:#dc2626;color:#dc2626">
            Déconnexion
        </a>
        <?php else: ?>
        <a class="btn-backoffice" href="/medilink_medicament/MediLink/index.php?page=login">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            <span>Connexion</span>
        </a>
        <?php endif; ?>
    </div>
</nav>
