<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if (!function_exists('e')) {
    function e(string $value): string {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

$pageTitle       = $pageTitle ?? 'Administration Forum';
$currentCtrl     = $_GET['controller'] ?? 'forum';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — MediLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="public/css/admin.css">
    <style>
        .topbar-medilink {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 24px;
            height: 52px;
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 12px;
            color: #94a3b8;
        }
        .topbar-medilink a { color: #94a3b8; text-decoration: none; }
        .topbar-medilink a:hover { color: #2563eb; }
        .topbar-medilink .sep { opacity: .4; }
    </style>
</head>
<body class="admin-body">

<?php include dirname(dirname(dirname(__DIR__))) . '/views/back/layouts/sidebar.php'; ?>

<!-- Main area -->
<main class="admin-main" style="margin-left: 280px; min-height: 100vh;">
    <!-- Topbar (Sub-tabs for forum could go here if needed, or just a title) -->
    <div class="topbar" style="background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0,0,0,0.05); padding: 16px 40px; display: flex; flex-direction:column; align-items:flex-start; gap: 16px; position: sticky; top: 0; z-index: 50; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
            <div>
                <div style="font-size: 18px; font-weight: 800; color: #0f172a; letter-spacing: -0.3px;">Administration Forum</div>
                <div style="font-size: 13px; color: #64748b; margin-top: 2px; font-weight: 500;">Gérez les discussions et la communauté</div>
            </div>
        </div>
        
        <!-- SUB TABS -->
        <div style="display:flex; gap: 8px; border-bottom: 1px solid #e2e8f0; width: 100%;">
            <a href="index.php?controller=forum&action=adminList" class="nav-item <?= $currentCtrl === 'forum' ? 'active' : '' ?>" style="padding: 10px 16px; margin: 0; border-radius: 8px 8px 0 0; border-bottom: 3px solid <?= $currentCtrl === 'forum' ? '#2563eb' : 'transparent' ?>; text-decoration: none; color: #475569; font-weight: 600; font-size: 14px;">
                Forums
            </a>
            <a href="index.php?controller=post&action=adminList" class="nav-item <?= $currentCtrl === 'post' ? 'active' : '' ?>" style="padding: 10px 16px; margin: 0; border-radius: 8px 8px 0 0; border-bottom: 3px solid <?= $currentCtrl === 'post' ? '#2563eb' : 'transparent' ?>; text-decoration: none; color: #475569; font-weight: 600; font-size: 14px;">
                Posts
            </a>
            <a href="index.php?controller=commentaire&action=adminList" class="nav-item <?= $currentCtrl === 'commentaire' ? 'active' : '' ?>" style="padding: 10px 16px; margin: 0; border-radius: 8px 8px 0 0; border-bottom: 3px solid <?= $currentCtrl === 'commentaire' ? '#2563eb' : 'transparent' ?>; text-decoration: none; color: #475569; font-weight: 600; font-size: 14px;">
                Commentaires
            </a>
        </div>
    </div>

    <div class="admin-content">
        <?php if (is_array($flash)): ?>
            <div class="admin-alert admin-alert-<?= e((string)($flash['type'] ?? 'success')) ?>" style="margin-bottom:16px;">
                <?= e((string)($flash['message'] ?? '')) ?>
            </div>
        <?php endif; ?>
