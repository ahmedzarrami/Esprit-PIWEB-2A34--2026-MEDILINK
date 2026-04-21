<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MediLink — Plateforme médicale intelligente. Forum de discussion santé pour patients et professionnels.">
    <title>MediLink — <?= htmlspecialchars($pageTitle ?? 'Forum Santé') ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<!-- ===== NAVIGATION BAR ===== -->
<nav class="navbar">
    <div class="navbar-container">
        <!-- Brand -->
        <a href="index.php" class="navbar-brand">
            <div class="brand-icon">
                <i class="fas fa-heartbeat"></i>
            </div>
            <span>MediLink</span>
        </a>

        <!-- Navigation Links -->
        <ul class="navbar-nav">
            <li><a href="index.php" class="<?= ($controller ?? '') === 'forum' && ($action ?? '') === 'list' ? 'active' : '' ?>"><i class="fas fa-home"></i> Accueil</a></li>
            <li><a href="index.php?controller=forum&action=list" class="<?= ($controller ?? '') === 'forum' ? 'active' : '' ?>"><i class="fas fa-comments"></i> Forums</a></li>
        </ul>

        <!-- User Info -->
        <div class="navbar-user">
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'administrateur'): ?>
                <a href="index.php?controller=forum&action=adminList" class="btn-admin-link">
                    <i class="fas fa-cog"></i> Administration
                </a>
            <?php endif; ?>
            <div class="user-avatar">
                <?= strtoupper(substr($_SESSION['user']['prenom'] ?? 'U', 0, 1) . substr($_SESSION['user']['nom'] ?? '', 0, 1)) ?>
            </div>
            <div>
                <div class="user-name"><?= htmlspecialchars(($_SESSION['user']['prenom'] ?? '') . ' ' . ($_SESSION['user']['nom'] ?? '')) ?></div>
                <div class="user-role"><?= htmlspecialchars(ucfirst($_SESSION['user']['role'] ?? 'Utilisateur')) ?></div>
            </div>
        </div>
    </div>
</nav>

<!-- ===== MAIN CONTENT ===== -->
<main class="main-content">
