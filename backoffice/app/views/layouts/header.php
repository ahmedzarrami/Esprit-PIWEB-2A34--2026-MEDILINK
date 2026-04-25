<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if (!function_exists('e')) {
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'MediLink') ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="backoffice-shell">
        <aside class="sidebar">
            <div class="brand-block">
                <div class="brand-logo">Medi<span>Link</span></div>
                <p class="brand-text">Back Office Secrétaire</p>
            </div>

            <nav class="sidebar-nav">
                <a href="index.php?action=index" class="nav-item <?= (($_GET['action'] ?? 'index') === 'index') ? 'active' : '' ?>">Tableau de bord</a>
                <a href="#" class="nav-item">Agenda</a>
                <a href="#" class="nav-item">Patients</a>
                <a href="#" class="nav-item">Messagerie</a>
                <a href="#" class="nav-item">Profils médecins</a>
                <a href="index.php?action=index" class="nav-item active-soft">Médicaments</a>
            </nav>
        </aside>

        <div class="content-area">
            <header class="topbar">
                <div>
                    <h1><?= e($pageTitle ?? 'MediLink') ?></h1>
                    
                </div>
                <a href="../../frontoffice/public/index.php" class="btn-frontoffice" title="Aller au Front Office">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Front Office
                </a>
                <div class="topbar-badge">Secrétaire</div>
            </header>

            <section class="module-strip">
                <div class="module-card">Gestion des RDV</div>
                <div class="module-card">Suivi des Patients</div>
                <div class="module-card">Messagerie Sécurisée</div>
                <div class="module-card">Gestion des Profils</div>
                <div class="module-card selected">Gestion des Médicaments</div>
            </section>

            <?php if (is_array($flash)): ?>
                <div class="flash flash-<?= e((string) ($flash['type'] ?? 'success')) ?>">
                    <?= e((string) ($flash['message'] ?? '')) ?>
                </div>
            <?php endif; ?>
