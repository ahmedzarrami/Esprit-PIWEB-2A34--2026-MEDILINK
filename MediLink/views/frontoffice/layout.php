<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediLink — Espace Patient</title>
<meta name="description" content="MediLink — Plateforme de santé digitale. Prenez rendez-vous, consultez vos ordonnances et accédez aux meilleurs professionnels de santé.">
<link rel="stylesheet" href="/medilink_medicament/MediLink/public/css/front.css">
<link rel="stylesheet" href="/medilink_medicament/MediLink/public/css/face-auth.css">
</head>
<body>

<?php
$_noNavPages = ['home', 'login', 'register', 'forgot_password', 'reset_code', 'reset_password'];
if (!in_array($page ?? 'home', $_noNavPages)):
?>
<!-- NAVBAR -->
<nav class="navbar">
  <div class="nav-inner">
    <a class="nav-logo" href="index.php?page=home" style="text-decoration:none">
      <span style="font-size:20px;font-weight:800;background:linear-gradient(90deg,#1a56db,#0da271);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:-0.5px">MediLink</span>
    </a>
    <?php $_navRole = strtolower($_SESSION['user_role'] ?? ''); ?>
    <div class="nav-links" id="navLinks">
      <?php if ($_navRole === 'administrateur'): ?>
        <a class="nav-link" href="/medilink_medicament/MediLink/public/back/index.php">🔧 Back-office</a>
        <a class="nav-link" href="/medilink_medicament/MediLink/index.php?module=rdv&action=admin">📅 Gestion RDV</a>
        <a class="nav-link" href="/medilink_medicament/MediLink/index.php?module=forum&controller=forum&action=adminList">💬 Forum Admin</a>
      <?php elseif ($_navRole === 'professionnel'): ?>
        <a class="nav-link" href="/medilink_medicament/MediLink/public/front/index.php?action=medicaments">💊 Médicaments</a>
        <a class="nav-link" href="/medilink_medicament/MediLink/public/front/index.php?action=ordonnances">📋 Ordonnances</a>
        <a class="nav-link" href="/medilink_medicament/MediLink/index.php?module=rdv&action=medecin">📅 Mes RDV</a>
        <a class="nav-link" href="/medilink_medicament/MediLink/index.php?module=forum&controller=forum&action=list">💬 Forum</a>
      <?php else: ?>
        <a class="nav-link" href="/medilink_medicament/MediLink/public/front/index.php?action=medicaments">💊 Médicaments</a>
        <a class="nav-link" href="/medilink_medicament/MediLink/public/front/index.php?action=parapharmacie">🧴 Parapharmacie</a>
        <a class="nav-link" href="/medilink_medicament/MediLink/index.php?module=rdv">📅 Rendez-vous</a>
        <a class="nav-link" href="/medilink_medicament/MediLink/index.php?module=forum&controller=forum&action=list">💬 Forum</a>
      <?php endif; ?>
    </div>
    <?php if (empty($_SESSION['user_id'])): ?>
    <div class="nav-right" id="navRight">
      <a class="btn btn-outline btn-sm" href="index.php?page=login">Connexion</a>
      <a class="btn btn-primary btn-sm" href="index.php?page=register">S'inscrire</a>
    </div>
    <?php else: ?>
    <div class="nav-right" id="navLogged">
      <span style="font-size:13px;color:var(--teal);font-weight:600"><?= htmlspecialchars($_SESSION['user_nom'] ?? '') ?></span>
      <?php if ($_navRole === 'professionnel'): ?>
        <a class="btn btn-outline btn-sm" style="border-color:var(--teal);color:var(--teal)" href="index.php?page=professionnel">Mon espace</a>
      <?php elseif ($_navRole === 'administrateur'): ?>
        <a class="btn btn-outline btn-sm" style="border-color:#8b5cf6;color:#8b5cf6" href="/medilink_medicament/MediLink/public/back/index.php">Administration</a>
      <?php else: ?>
        <a class="btn btn-outline btn-sm" href="index.php?page=profile">Mon profil</a>
      <?php endif; ?>
      <a class="btn btn-sm" style="background:#f1f5f9;color:var(--text2)" href="index.php?action=logout">Déconnexion</a>
    </div>
    <?php endif; ?>
  </div>
</nav>
<?php endif; ?>

<!-- PAGE CONTENT -->
<?php include $viewFile; ?>

<div class="toast-wrap" id="toastWrap"></div>

<?php if (!empty($flash)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    toast('<?= addslashes($flash['message']) ?>', '<?= $flash['type'] ?>');
});
</script>
<?php endif; ?>

<script src="/medilink_medicament/MediLink/public/js/validation.js"></script>
<script src="/medilink_medicament/MediLink/public/js/front.js"></script>
<?php if (in_array($page ?? '', ['login', 'profile'])): ?>
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="/medilink_medicament/MediLink/public/js/face-auth.js"></script>
<?php endif; ?>
</body>
</html>
