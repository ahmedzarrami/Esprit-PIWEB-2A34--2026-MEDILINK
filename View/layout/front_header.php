<?php
$currentController = $_GET['controller'] ?? 'forum';
$currentAction     = $_GET['action'] ?? 'list';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink – <?= htmlspecialchars($pageTitle ?? 'Forum Santé') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css?v=<?= time() ?>">
</head>
<body>

<nav class="navbar-medilink">
    <a href="index.php" class="nav-logo">
        <div class="logo-mark">+</div>
        <div class="logo-text">Medi<span>Link</span></div>
    </a>

    <div class="nav-links">
        <a href="public/front/index.php?action=home">Accueil</a>
        <a href="public/front/index.php?action=medicaments">Médicaments</a>
        <a href="public/front/index.php?action=ordonnances">Ordonnances</a>
        <a href="public/front/index.php?action=assistant">🤖 Assistant</a>
        <a href="index.php?controller=forum&action=list" class="<?= $currentController === 'forum' ? 'active' : '' ?>">Forum</a>
    </div>

    <div class="nav-actions">
        <a class="btn-backoffice" href="index.php?controller=forum&action=adminList">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            <span>Administration</span>
        </a>
        <a class="btn-admin" href="index.php?controller=post&action=create">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Nouveau post</span>
        </a>
    </div>
</nav>

<main class="main-content">
