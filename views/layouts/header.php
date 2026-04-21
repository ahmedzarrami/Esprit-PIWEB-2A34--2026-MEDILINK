<?php $currentAction = $_GET['action'] ?? 'home'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink – Front Office</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar-medilink">
    <a href="index.php" class="nav-logo">
        <div class="logo-mark">+</div>
        <div class="logo-text">Medi<span>Link</span></div>
    </a>

    <div class="nav-links">
        <a href="index.php?action=home"         class="<?= $currentAction === 'home' ? 'active' : '' ?>">Accueil</a>
        <a href="index.php?action=medicaments"  class="<?= $currentAction === 'medicaments' ? 'active' : '' ?>">Médicaments</a>
        <a href="index.php?action=ordonnances"  class="<?= in_array($currentAction, ['ordonnances','create_ordonnance','show_ordonnance'], true) ? 'active' : '' ?>">Ordonnances</a>
        <a href="#contact">Contact</a>
    </div>

    <a class="btn-admin" href="index.php?action=create_ordonnance">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>Nouvelle ordonnance</span>
    </a>
</nav>
