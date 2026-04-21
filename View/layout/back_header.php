<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MediLink — Administration. Gérez les forums, posts et commentaires.">
    <title>MediLink Admin — <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Admin Styles -->
    <link rel="stylesheet" href="public/css/admin.css">
</head>
<body class="admin-body">

<!-- ===== SIDEBAR ===== -->
<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="fas fa-heartbeat"></i>
        </div>
        <div>
            <h2>MediLink</h2>
            <small>Panneau d'administration</small>
        </div>
    </div>

    <nav class="sidebar-menu">
        <div class="sidebar-section">Navigation</div>

        <a href="index.php?controller=forum&action=list" class="sidebar-link">
            <i class="fas fa-globe"></i> Voir le site
        </a>

        <div class="sidebar-section">Module Forum</div>

        <a href="index.php?controller=forum&action=adminList" class="sidebar-link <?= ($controller ?? '') === 'forum' && in_array(($action ?? ''), ['adminList', 'create', 'edit']) ? 'active' : '' ?>">
            <i class="fas fa-layer-group"></i> Forums
        </a>

        <a href="index.php?controller=post&action=adminList" class="sidebar-link <?= ($controller ?? '') === 'post' && in_array(($action ?? ''), ['adminList', 'edit']) ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i> Posts
        </a>

        <a href="index.php?controller=commentaire&action=adminList" class="sidebar-link <?= ($controller ?? '') === 'commentaire' ? 'active' : '' ?>">
            <i class="fas fa-comment-dots"></i> Commentaires
        </a>

        <div class="sidebar-section">Autres Modules</div>

        <a href="#" class="sidebar-link">
            <i class="fas fa-users"></i> Utilisateurs
        </a>

        <a href="#" class="sidebar-link">
            <i class="fas fa-calendar-check"></i> Rendez-vous
        </a>

        <a href="#" class="sidebar-link">
            <i class="fas fa-pills"></i> Médicaments
        </a>

        <a href="#" class="sidebar-link">
            <i class="fas fa-shopping-bag"></i> Parapharmacie
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="#">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </div>
</aside>

<!-- ===== MAIN WRAPPER ===== -->
<div class="admin-main">

    <!-- Top Bar -->
    <header class="admin-topbar">
        <div class="topbar-left">
            <h1><?= htmlspecialchars($pageTitle ?? 'Administration') ?></h1>
        </div>
        <div class="topbar-right">
            <a href="index.php?controller=forum&action=list" class="topbar-link">
                <i class="fas fa-external-link-alt"></i> Voir le site
            </a>
            <div class="topbar-user">
                <div class="topbar-avatar">
                    <?= strtoupper(substr($_SESSION['user']['prenom'] ?? 'A', 0, 1) . substr($_SESSION['user']['nom'] ?? '', 0, 1)) ?>
                </div>
                <div class="topbar-user-info">
                    <div class="name"><?= htmlspecialchars(($_SESSION['user']['prenom'] ?? '') . ' ' . ($_SESSION['user']['nom'] ?? '')) ?></div>
                    <div class="role">Administrateur</div>
                </div>
            </div>
        </div>
    </header>

    <!-- Admin Content -->
    <div class="admin-content">
