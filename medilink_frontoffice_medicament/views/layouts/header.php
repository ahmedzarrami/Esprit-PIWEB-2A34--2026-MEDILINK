<?php $currentAction = $_GET['action'] ?? 'home'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink - Front Office</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <div class="logo-wrap">
        <div class="logo-mark">+</div>
        <div class="logo-text">Medi<span>Link</span></div>
    </div>

    <nav class="main-nav">
        <a href="index.php?action=home" class="<?= $currentAction === 'home' ? 'active' : '' ?>">Accueil</a>
        <a href="index.php?action=medicaments" class="<?= $currentAction === 'medicaments' ? 'active' : '' ?>">Médicaments</a>
        <a href="#services">Nos services</a>
        <a href="#contact">Contact</a>
    </nav>

    <a class="header-btn" href="index.php?action=medicaments">Consulter les médicaments</a>
</header>
