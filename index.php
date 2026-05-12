<?php
/**
 * MediLink - Page d'accueil de la version integration.
 * Sert de hub vers les 5 modules.
 */

$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$modules = [
    [
        'icon'  => 'U',
        'title' => 'Gestion des utilisateurs',
        'desc'  => 'Inscription, connexion, profils, reconnaissance faciale, reset mot de passe.',
        'href'  => $base . '/modules/utilisateur/index.php',
        'tag'   => 'Auth',
    ],
    [
        'icon'  => 'R',
        'title' => 'Rendez-vous & fiches patient',
        'desc'  => 'Prise de rendez-vous, planning medecins, fiches patient, evaluations.',
        'href'  => $base . '/modules/rdv/MediLink/index.php',
        'tag'   => 'RDV',
    ],
    [
        'icon'  => 'M',
        'title' => 'Medicaments & ordonnances',
        'desc'  => 'Catalogue medicaments, ordonnances numeriques, assistant IA.',
        'href'  => $base . '/modules/medicaments/MediLink/public/front/index.php',
        'tag'   => 'Pharmacie',
    ],
    [
        'icon'  => 'P',
        'title' => 'Parapharmacie (shop)',
        'desc'  => 'Catalogue produits, commandes en ligne, livraison, evaluations clients.',
        'href'  => $base . '/modules/parapharmacie/index.php',
        'tag'   => 'Shop',
    ],
    [
        'icon'  => 'F',
        'title' => 'Forum communautaire',
        'desc'  => 'Forums thematiques, posts, commentaires, reactions, filtrage de contenu.',
        'href'  => $base . '/modules/forum/index.php',
        'tag'   => 'Forum',
    ],
];
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink &middot; Plateforme integree</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars($base) ?>/assets/css/theme.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($base) ?>/assets/css/unified.css">
</head>
<body>

<nav class="ml-navbar">
    <a class="ml-navbar__brand" href="<?= htmlspecialchars($base) ?>/index.php">MediLink</a>
    <div class="ml-navbar__links">
        <a href="<?= htmlspecialchars($base) ?>/index.php" class="active">Accueil</a>
        <?php foreach ($modules as $m): ?>
            <a href="<?= htmlspecialchars($m['href']) ?>"><?= htmlspecialchars($m['title']) ?></a>
        <?php endforeach; ?>
        <a href="<?= htmlspecialchars($base) ?>/admin/index.php" style="background:#1d4ed8;color:#fff;padding:8px 18px;border-radius:8px;font-weight:700;">BackOffice</a>
    </div>
</nav>

<div class="ml-container">

    <section class="ml-hero">
        <h1>Plateforme MediLink integree</h1>
        <p>Cinq modules unifies pour la sante : utilisateurs, rendez-vous, medicaments, parapharmacie et forum communautaire. Une seule base de donnees, une seule experience.</p>
    </section>

    <h2 style="font-size:24px;margin:0 0 18px;">Acceder aux modules</h2>
    <section class="ml-modules">
        <?php foreach ($modules as $m): ?>
            <article class="ml-module-card">
                <div class="ml-module-card__icon"><?= htmlspecialchars($m['icon']) ?></div>
                <span class="ml-badge"><?= htmlspecialchars($m['tag']) ?></span>
                <h3><?= htmlspecialchars($m['title']) ?></h3>
                <p><?= htmlspecialchars($m['desc']) ?></p>
                <a class="ml-btn" href="<?= htmlspecialchars($m['href']) ?>">Ouvrir le module &rarr;</a>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="ml-card" style="margin-top:32px;">
        <h3 style="margin-top:0">Configuration</h3>
        <p style="color:var(--ml-muted)">
            La base de donnees commune est <code>medilinkintegration</code>. Pour l'installer :
        </p>
        <ol style="color:var(--ml-muted);line-height:1.8">
            <li>Demarrer Apache + MySQL dans XAMPP</li>
            <li>Ouvrir phpMyAdmin et importer <code>medilinkintegration.sql</code> (a la racine du projet)</li>
            <li>Verifier que <code>config/database.php</code> pointe sur la bonne instance MySQL</li>
            <li>Ouvrir un module via les liens ci-dessus</li>
        </ol>
        <p style="color:var(--ml-muted)">
            Compte admin de demonstration : <code>admin@medilink.tn</code> / <code>Pass@1234</code>
        </p>
    </section>

</div>

<footer class="ml-footer">
    MediLink &copy; 2026 &middot; Branche <strong>integration</strong> &middot; Projet PIWEB Esprit
</footer>

</body>
</html>
