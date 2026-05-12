<?php
/**
 * Back-office unifie de MediLink.
 * Hub central qui agrege les statistiques de tous les modules et
 * propose l'acces aux back-offices de chaque module.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_role('Administrateur');

require_once __DIR__ . '/../config/database.php';

$pdo = null;
$dbError = null;
$stats = [
    'utilisateurs' => 0,
    'patients'     => 0,
    'medecins'     => 0,
    'rendezvous'   => 0,
    'medicaments'  => 0,
    'ordonnances'  => 0,
    'produits'     => 0,
    'commandes'    => 0,
    'forums'       => 0,
    'posts'        => 0,
    'commentaires' => 0,
];

try {
    $pdo = medilink_pdo();
    foreach ([
        'utilisateurs' => 'utilisateur',
        'patients'     => 'patients',
        'medecins'     => 'medecins',
        'rendezvous'   => 'rendezvous',
        'medicaments'  => 'medicaments',
        'ordonnances'  => 'ordonnances',
        'produits'     => 'produits',
        'commandes'    => 'commandes',
        'forums'       => 'forum',
        'posts'        => 'post',
        'commentaires' => 'commentaire',
    ] as $key => $table) {
        try {
            $row = $pdo->query("SELECT COUNT(*) AS c FROM `$table`")->fetch();
            $stats[$key] = (int)($row['c'] ?? 0);
        } catch (Throwable $e) {
            $stats[$key] = 0;
        }
    }
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

$base = '/files40';
$activeSection = $_GET['section'] ?? 'dashboard';

$modules = [
    'utilisateur' => [
        'label' => 'Utilisateurs',
        'icon'  => 'U',
        'url'   => $base . '/modules/utilisateur/admin.php',
        'desc'  => 'Comptes, roles, statuts, reconnaissance faciale.',
        'stat'  => $stats['utilisateurs'],
        'statLabel' => 'utilisateurs',
    ],
    'rdv' => [
        'label' => 'Rendez-vous',
        'icon'  => 'R',
        'url'   => $base . '/modules/rdv/MediLink/index.php?action=admin',
        'desc'  => 'Planning medecins, fiches patient, evaluations.',
        'stat'  => $stats['rendezvous'],
        'statLabel' => 'rendez-vous',
    ],
    'medicaments' => [
        'label' => 'Medicaments',
        'icon'  => 'M',
        'url'   => $base . '/modules/medicaments/MediLink/public/back/index.php',
        'desc'  => 'Catalogue medicaments et ordonnances numeriques.',
        'stat'  => $stats['medicaments'],
        'statLabel' => 'medicaments',
    ],
    'parapharmacie' => [
        'label' => 'Parapharmacie',
        'icon'  => 'P',
        'url'   => $base . '/modules/parapharmacie/view/back/admin.php',
        'desc'  => 'Produits, commandes, ratings, livraison.',
        'stat'  => $stats['produits'],
        'statLabel' => 'produits',
    ],
    'forum' => [
        'label' => 'Forum',
        'icon'  => 'F',
        'url'   => $base . '/modules/forum/index.php?controller=forum&action=adminList',
        'desc'  => 'Forums, posts, commentaires, moderation.',
        'stat'  => $stats['posts'],
        'statLabel' => 'posts',
    ],
];

function formatN(int $n): string { return number_format($n, 0, ',', ' '); }
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink - Back Office unifie</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars($base) ?>/assets/css/theme.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($base) ?>/assets/css/unified.css">
    <style>
        body { background: #f8fafc; }

        .admin-shell { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }

        .admin-side {
            background: linear-gradient(180deg, #1d4ed8 0%, #1e40af 100%);
            color: #fff;
            padding: 24px 18px;
            position: sticky; top: 0; height: 100vh;
            display: flex; flex-direction: column; gap: 6px;
        }
        .admin-side__brand {
            display: flex; align-items: center; gap: 10px;
            font-weight: 800; font-size: 20px;
            padding: 6px 8px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.18);
            margin-bottom: 8px;
        }
        .admin-side__brand .mark {
            width: 32px; height: 32px; border-radius: 8px;
            background: #fff; color: #1d4ed8;
            display: flex; align-items: center; justify-content: center;
            font-weight: 900;
        }
        .admin-side a, .admin-side button {
            display: flex; align-items: center; gap: 10px;
            color: #dbeafe;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            background: transparent;
            cursor: pointer;
            text-align: left;
            width: 100%;
            font-family: inherit;
            transition: background .15s, color .15s;
        }
        .admin-side a:hover, .admin-side button:hover {
            background: rgba(255,255,255,0.14);
            color: #fff;
        }
        .admin-side a.active {
            background: rgba(255,255,255,0.22);
            color: #fff;
        }
        .admin-side__icon {
            width: 28px; height: 28px; border-radius: 7px;
            background: rgba(255,255,255,0.18);
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 13px;
        }
        .admin-side__footer { margin-top: auto; font-size: 12px; opacity: .8; padding: 10px 8px; }

        .admin-main { padding: 32px 40px; }
        .admin-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .admin-topbar h1 { margin: 0; font-size: 28px; font-weight: 800; letter-spacing: -0.5px; }
        .admin-topbar p  { margin: 4px 0 0; color: #64748b; font-size: 14px; }
        .admin-pill {
            background: #dbeafe;
            color: #1d4ed8;
            padding: 6px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 13px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px 20px;
            box-shadow: 0 1px 3px rgba(15,23,42,.04);
        }
        .stat-card__label { color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
        .stat-card__value { font-size: 32px; font-weight: 800; color: #1d4ed8; margin-top: 4px; }
        .stat-card__hint  { color: #94a3b8; font-size: 12px; margin-top: 2px; }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .module-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 22px;
            transition: transform .15s, border-color .15s, box-shadow .15s;
            display: flex; flex-direction: column; gap: 10px;
        }
        .module-card:hover { transform: translateY(-3px); border-color: #1d4ed8; box-shadow: 0 12px 28px rgba(29,78,216,.10); }
        .module-card__head {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 4px;
        }
        .module-card__icon {
            width: 44px; height: 44px;
            border-radius: 11px;
            background: #dbeafe; color: #1d4ed8;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 20px;
        }
        .module-card__title { font-size: 17px; font-weight: 700; margin: 0; color: #0f172a; }
        .module-card__stat { font-size: 13px; color: #64748b; }
        .module-card__stat strong { color: #1d4ed8; font-weight: 800; font-size: 16px; }
        .module-card__desc { color: #475569; font-size: 14px; margin: 0; flex: 1; }
        .module-card__btn {
            margin-top: 8px;
            background: #1d4ed8;
            color: #fff;
            padding: 9px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px; font-weight: 600;
            text-align: center;
            transition: background .12s, transform .1s;
        }
        .module-card__btn:hover { background: #1e40af; transform: translateY(-1px); color: #fff; text-decoration: none; }

        .admin-banner {
            background: #fff;
            border-left: 4px solid #dc2626;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            color: #991b1b;
            font-size: 14px;
        }
        @media (max-width: 920px) {
            .admin-shell { grid-template-columns: 1fr; }
            .admin-side { position: static; height: auto; flex-direction: row; flex-wrap: wrap; }
            .admin-main { padding: 20px 16px; }
        }
    </style>
</head>
<body>

<div class="admin-shell">

    <aside class="admin-side">
        <div class="admin-side__brand">
            <span class="mark">+</span>
            <span>MediLink</span>
        </div>
        <a href="<?= htmlspecialchars($base) ?>/admin/index.php" class="active">
            <span class="admin-side__icon">D</span> Dashboard
        </a>
        <?php foreach ($modules as $key => $m): ?>
            <a href="<?= htmlspecialchars($m['url']) ?>">
                <span class="admin-side__icon"><?= htmlspecialchars($m['icon']) ?></span>
                <?= htmlspecialchars($m['label']) ?>
            </a>
        <?php endforeach; ?>
        <a href="<?= htmlspecialchars($base) ?>/index.php">
            <span class="admin-side__icon">&larr;</span> Retour au site
        </a>
        <div class="admin-side__footer">
            MediLink &middot; integration<br>
            <?= date('d/m/Y H:i') ?>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <h1>Tableau de bord</h1>
                <p>Vue d'ensemble des cinq modules de la plateforme</p>
            </div>
            <span class="admin-pill">BackOffice unifie</span>
        </header>

        <?php if ($dbError !== null): ?>
            <div class="admin-banner">
                <strong>Connexion DB impossible :</strong> <?= htmlspecialchars($dbError) ?>
                <br>Verifiez que MySQL est demarre et que <code>medilinkintegration.sql</code> a ete importe.
            </div>
        <?php endif; ?>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-card__label">Utilisateurs</div>
                <div class="stat-card__value"><?= formatN($stats['utilisateurs']) ?></div>
                <div class="stat-card__hint">comptes auth</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Patients RDV</div>
                <div class="stat-card__value"><?= formatN($stats['patients']) ?></div>
                <div class="stat-card__hint">fiches RDV</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Medecins</div>
                <div class="stat-card__value"><?= formatN($stats['medecins']) ?></div>
                <div class="stat-card__hint">professionnels RDV</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Rendez-vous</div>
                <div class="stat-card__value"><?= formatN($stats['rendezvous']) ?></div>
                <div class="stat-card__hint">total enregistres</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Medicaments</div>
                <div class="stat-card__value"><?= formatN($stats['medicaments']) ?></div>
                <div class="stat-card__hint">references</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Ordonnances</div>
                <div class="stat-card__value"><?= formatN($stats['ordonnances']) ?></div>
                <div class="stat-card__hint">delivrees</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Produits shop</div>
                <div class="stat-card__value"><?= formatN($stats['produits']) ?></div>
                <div class="stat-card__hint">parapharmacie</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Commandes</div>
                <div class="stat-card__value"><?= formatN($stats['commandes']) ?></div>
                <div class="stat-card__hint">parapharmacie</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Forums</div>
                <div class="stat-card__value"><?= formatN($stats['forums']) ?></div>
                <div class="stat-card__hint">categories</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Posts</div>
                <div class="stat-card__value"><?= formatN($stats['posts']) ?></div>
                <div class="stat-card__hint">publications</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Commentaires</div>
                <div class="stat-card__value"><?= formatN($stats['commentaires']) ?></div>
                <div class="stat-card__hint">forum</div>
            </div>
        </section>

        <h2 style="font-size:20px;font-weight:700;margin:0 0 16px;color:#0f172a;">Modules</h2>
        <section class="modules-grid">
            <?php foreach ($modules as $key => $m): ?>
                <article class="module-card">
                    <div class="module-card__head">
                        <div class="module-card__icon"><?= htmlspecialchars($m['icon']) ?></div>
                        <div>
                            <h3 class="module-card__title"><?= htmlspecialchars($m['label']) ?></h3>
                            <div class="module-card__stat"><strong><?= formatN($m['stat']) ?></strong> <?= htmlspecialchars($m['statLabel']) ?></div>
                        </div>
                    </div>
                    <p class="module-card__desc"><?= htmlspecialchars($m['desc']) ?></p>
                    <a class="module-card__btn" href="<?= htmlspecialchars($m['url']) ?>">Gerer &rarr;</a>
                </article>
            <?php endforeach; ?>
        </section>

    </main>
</div>

</body>
</html>
