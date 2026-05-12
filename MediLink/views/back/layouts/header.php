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
    <link rel="stylesheet" href="/medilink_medicament/MediLink/public/back/css/style.css">
</head>
<body>
    <div class="backoffice-shell">
        <?php include dirname(__DIR__) . '/layouts/sidebar.php'; ?>

        <div class="content-area" style="margin-left: 280px; min-height: 100vh;">
            <div class="topbar" style="background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0,0,0,0.05); padding: 16px 40px; display: flex; flex-direction:column; align-items:flex-start; gap: 16px; position: sticky; top: 0; z-index: 50; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
                    <div>
                        <div style="font-size: 18px; font-weight: 800; color: #0f172a; letter-spacing: -0.3px;"><?= e($pageTitle ?? 'Gestion des Médicaments') ?></div>
                        <div style="font-size: 13px; color: #64748b; margin-top: 2px; font-weight: 500;">Gérez l'inventaire et les produits</div>
                    </div>
                </div>
            </div>

            <?php if (is_array($flash)): ?>
                <div class="flash flash-<?= e((string) ($flash['type'] ?? 'success')) ?>">
                    <?= e((string) ($flash['message'] ?? '')) ?>
                </div>
            <?php endif; ?>
