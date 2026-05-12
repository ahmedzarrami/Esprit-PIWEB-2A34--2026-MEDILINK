<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediLink — Gestion Utilisateurs</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/medilink_medicament/MediLink/public/css/back.css">
<style>
body{font-family:'Plus Jakarta Sans',sans-serif;background:#f8fafc;display:flex;min-height:100vh;margin:0}
.main-wrap{margin-left:280px;flex:1;display:flex;flex-direction:column;min-height:100vh}
</style>
</head>
<body>

<?php include __DIR__ . '/../back/layouts/sidebar.php'; ?>

<div class="main-wrap">
  <!-- Topbar -->
  <div style="background:#fff;border-bottom:1px solid #e2e8f0;padding:0 40px;height:64px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50">
    <div>
      <div style="font-size:18px;font-weight:800;color:#0f172a">👥 Gestion des Utilisateurs</div>
      <div style="font-size:13px;color:#64748b">Gérez les comptes patients, médecins et administrateurs</div>
    </div>
  </div>

  <div style="padding:32px 40px;flex:1">
    <?php include $viewFile; ?>
  </div>
</div>

<!-- TOAST CONTAINER -->
<div class="toast-container" id="toastContainer"></div>

<script src="/medilink_medicament/MediLink/public/js/back.js"></script>

<?php if (!empty($usersJson)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initUsers(<?= $usersJson ?>);
});
</script>
<?php endif; ?>
</body>
</html>
