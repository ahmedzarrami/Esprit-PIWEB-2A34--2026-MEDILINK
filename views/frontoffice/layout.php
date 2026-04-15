<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediLink — Espace Patient</title>
<meta name="description" content="MediLink — Plateforme de santé digitale. Prenez rendez-vous, consultez vos ordonnances et accédez aux meilleurs professionnels de santé.">
<link rel="stylesheet" href="assets/css/front.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="nav-inner">
    <a class="nav-logo" href="index.php?page=home">
      <img src="assets/img/logo.png" alt="MediLink" class="nav-logo-img">
    </a>
    <div class="nav-links" id="navLinks">
      <a class="nav-link <?= ($page ?? '') === 'home' ? 'active' : '' ?>" href="index.php?page=home">Accueil</a>
      <a class="nav-link" href="#">Médecins</a>
      <a class="nav-link" href="#">Parapharmacie</a>
      <a class="nav-link" href="#">Forum</a>
    </div>
    <?php if (empty($_SESSION['user_id'])): ?>
    <div class="nav-right" id="navRight">
      <a class="btn btn-outline btn-sm" href="index.php?page=login">Connexion</a>
      <a class="btn btn-primary btn-sm" href="index.php?page=register">S'inscrire</a>
    </div>
    <?php else: ?>
    <div class="nav-right" id="navLogged">
      <a class="btn btn-outline btn-sm" href="index.php?page=profile">Mon profil</a>
      <a class="btn btn-sm" style="background:#f1f5f9;color:var(--text2)" href="index.php?action=logout">Déconnexion</a>
    </div>
    <?php endif; ?>
  </div>
</nav>

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

<script src="assets/js/validation.js"></script>
<script src="assets/js/front.js"></script>
</body>
</html>
