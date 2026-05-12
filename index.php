<?php
/**
 * MediLink - Page d'accueil de la version integration.
 * Affiche une landing publique si non connecte, sinon un menu adapte
 * au role de l'utilisateur.
 */
require_once __DIR__ . '/config/session.php';

$base = '/files40';
$user = current_user();
$role = $user['role'] ?? null;

// Sections accessibles par role
$sections_patient = [
    ['icon'=>'R','title'=>'Mes rendez-vous','desc'=>'Prendre, modifier ou annuler un RDV avec un medecin.','href'=>$base.'/modules/rdv/MediLink/index.php?action=patient','tag'=>'RDV'],
    ['icon'=>'M','title'=>'Medicaments','desc'=>'Catalogue de medicaments et assistant IA.','href'=>$base.'/modules/medicaments/MediLink/public/front/index.php','tag'=>'Pharmacie'],
    ['icon'=>'P','title'=>'Parapharmacie','desc'=>'Acheter des produits de soin, suivre vos commandes.','href'=>$base.'/modules/parapharmacie/index.php','tag'=>'Shop'],
    ['icon'=>'F','title'=>'Forum','desc'=>'Lire et participer aux discussions sante.','href'=>$base.'/modules/forum/index.php','tag'=>'Communaute'],
    ['icon'=>'U','title'=>'Mon profil','desc'=>'Mettre a jour mes informations, ma photo, mes coordonnees.','href'=>$base.'/modules/utilisateur/index.php?page=profile','tag'=>'Compte'],
];

$sections_pro = [
    ['icon'=>'R','title'=>'Mon planning','desc'=>'Voir et gerer mes rendez-vous patients.','href'=>$base.'/modules/rdv/MediLink/index.php?action=medecin','tag'=>'Planning'],
    ['icon'=>'F','title'=>'Fiches patient','desc'=>'Remplir et consulter les fiches medicales.','href'=>$base.'/modules/rdv/MediLink/View/front/gestionFichePatient.php','tag'=>'Dossiers'],
    ['icon'=>'O','title'=>'Ordonnances','desc'=>'Rediger et imprimer des ordonnances.','href'=>$base.'/modules/medicaments/MediLink/public/front/index.php?action=ordonnances','tag'=>'Prescription'],
    ['icon'=>'M','title'=>'Catalogue medicaments','desc'=>'Consulter le catalogue de medicaments disponibles.','href'=>$base.'/modules/medicaments/MediLink/public/front/index.php','tag'=>'Pharmacie'],
    ['icon'=>'Fo','title'=>'Forum','desc'=>'Repondre aux questions des patients dans les forums.','href'=>$base.'/modules/forum/index.php','tag'=>'Communaute'],
    ['icon'=>'U','title'=>'Mon profil','desc'=>'Specialite, biographie, coordonnees.','href'=>$base.'/modules/utilisateur/index.php?page=professionnel','tag'=>'Compte'],
];

$sections_admin = [
    ['icon'=>'B','title'=>'BackOffice unifie','desc'=>'Tableau de bord global avec acces a toutes les administrations.','href'=>$base.'/admin/index.php','tag'=>'Admin'],
];

if ($role === 'Administrateur') {
    $sections = $sections_admin;
    $welcome = 'Bonjour ' . htmlspecialchars($user['prenom']) . ', vous etes connecte en tant qu\'Administrateur.';
} elseif ($role === 'Professionnel') {
    $sections = $sections_pro;
    $welcome = 'Bienvenue Dr. ' . htmlspecialchars($user['prenom'] . ' ' . $user['nom']) . '.';
} elseif ($role === 'Patient') {
    $sections = $sections_patient;
    $welcome = 'Bienvenue ' . htmlspecialchars($user['prenom']) . ' sur votre espace MediLink.';
} else {
    $sections = [];
    $welcome  = null;
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink &middot; Plateforme integree</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/theme.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/unified.css">
</head>
<body>

<nav class="ml-navbar">
    <a class="ml-navbar__brand" href="<?= $base ?>/index.php">MediLink</a>
    <div class="ml-navbar__links">
        <a href="<?= $base ?>/index.php" class="active">Accueil</a>
        <?php if ($user): ?>
            <?php foreach ($sections as $s): ?>
                <a href="<?= htmlspecialchars($s['href']) ?>"><?= htmlspecialchars($s['title']) ?></a>
            <?php endforeach; ?>
            <span style="margin-left:auto;color:#64748b;font-size:14px;">
                <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                <span style="background:#dbeafe;color:#1d4ed8;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;margin-left:6px;">
                    <?= htmlspecialchars($role) ?>
                </span>
            </span>
            <a href="<?= $base ?>/modules/utilisateur/index.php?action=logout"
               style="background:#dc2626;color:#fff;padding:8px 16px;border-radius:8px;font-weight:700;">
                Deconnexion
            </a>
        <?php else: ?>
            <span style="margin-left:auto"></span>
            <a href="<?= $base ?>/modules/utilisateur/index.php?page=login"
               style="background:#1d4ed8;color:#fff;padding:8px 18px;border-radius:8px;font-weight:700;">
                Connexion
            </a>
            <a href="<?= $base ?>/modules/utilisateur/index.php?page=register"
               style="border:2px solid #1d4ed8;color:#1d4ed8;padding:6px 16px;border-radius:8px;font-weight:700;">
                Inscription
            </a>
        <?php endif; ?>
    </div>
</nav>

<div class="ml-container">

    <section class="ml-hero">
        <?php if ($welcome): ?>
            <span class="ml-badge" style="background:rgba(255,255,255,.18);color:#fff;margin-bottom:18px;">
                <?= $welcome ?>
            </span>
        <?php endif; ?>
        <h1>Plateforme MediLink</h1>
        <p>
            <?php if ($user): ?>
                Accedez a tous les services adaptes a votre profil
                <strong><?= htmlspecialchars($role) ?></strong>.
            <?php else: ?>
                Cinq modules unifies pour la sante : prise de rendez-vous,
                medicaments, parapharmacie et forum communautaire.
                Connectez-vous pour commencer.
            <?php endif; ?>
        </p>
        <?php if (!$user): ?>
            <div style="margin-top:24px;display:flex;gap:12px;justify-content:center;">
                <a class="ml-btn" href="<?= $base ?>/modules/utilisateur/index.php?page=login"
                   style="background:#fff;color:#1d4ed8;">
                    Se connecter
                </a>
                <a class="ml-btn ml-btn--ghost" href="<?= $base ?>/modules/utilisateur/index.php?page=register"
                   style="background:transparent;color:#fff;border-color:#fff;">
                    Creer un compte
                </a>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($user): ?>
        <h2 style="font-size:24px;margin:0 0 18px;">Vos espaces</h2>
        <section class="ml-modules">
            <?php foreach ($sections as $s): ?>
                <article class="ml-module-card">
                    <div class="ml-module-card__icon"><?= htmlspecialchars($s['icon']) ?></div>
                    <span class="ml-badge"><?= htmlspecialchars($s['tag']) ?></span>
                    <h3><?= htmlspecialchars($s['title']) ?></h3>
                    <p><?= htmlspecialchars($s['desc']) ?></p>
                    <a class="ml-btn" href="<?= htmlspecialchars($s['href']) ?>">Ouvrir &rarr;</a>
                </article>
            <?php endforeach; ?>
        </section>
    <?php else: ?>
        <section class="ml-card">
            <h3 style="margin-top:0">Plateforme integree</h3>
            <p style="color:var(--ml-muted)">
                Apres connexion, vous accedez automatiquement aux modules adaptes a votre role :
            </p>
            <ul style="color:var(--ml-muted);line-height:1.9">
                <li><strong>Patient</strong> : RDV, ordonnances, parapharmacie, forum.</li>
                <li><strong>Professionnel de sante</strong> : planning, fiches patient, redaction d'ordonnances.</li>
                <li><strong>Administrateur</strong> : back-office complet (gestion utilisateurs, contenu, commandes).</li>
            </ul>
        </section>
    <?php endif; ?>

</div>

<footer class="ml-footer">
    MediLink &copy; 2026 &middot; Branche <strong>integration</strong> &middot; Projet PIWEB Esprit
</footer>

</body>
</html>
