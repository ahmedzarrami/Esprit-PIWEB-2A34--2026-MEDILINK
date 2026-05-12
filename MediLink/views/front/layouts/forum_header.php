<?php
$pageTitle = $pageTitle ?? 'Forum — MediLink';
$currentController = $_GET['controller'] ?? 'forum';
$currentAction = $_GET['action'] ?? 'list';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — MediLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        /* ── Cross-module nav bar ── */
        .medilink-topbar {
            background: #0f172a;
            padding: 0 32px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
        }
        .medilink-topbar .logo {
            color: #fff;
            font-weight: 700;
            text-decoration: none;
            font-size: 15px;
        }
        .medilink-topbar .logo span { color: #6694f8; }
        .medilink-topbar .modules {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .medilink-topbar .modules a {
            color: #94a3b8;
            text-decoration: none;
            padding: 4px 12px;
            border-radius: 6px;
            transition: all .2s;
        }
        .medilink-topbar .modules a:hover,
        .medilink-topbar .modules a.active {
            background: rgba(255,255,255,.1);
            color: #fff;
        }
    </style>
</head>
<body>

<div class="medilink-topbar">
    <a href="index.php" class="logo">Medi<span>Link</span></a>
    <div class="modules">
        <a href="/medilink_medicament/MediLink/index.php">⬅ Accueil</a>
        <a href="/medilink_medicament/MediLink/public/front/index.php">💊 Médicaments</a>
        <a href="/medilink_medicament/MediLink/index.php?module=forum&controller=forum&action=list" class="active">💬 Forum</a>
        <a href="/medilink_medicament/MediLink/index.php?module=rdv">📅 Gestion RDV</a>
    </div>
</div>
