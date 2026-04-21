<?php require __DIR__ . '/../layouts/header.php'; ?>
<section class="section-block narrow">
    <div class="empty-state">
        <h1>404</h1>
        <p><?= htmlspecialchars($errorMessage ?? 'Contenu introuvable.') ?></p>
        <a href="index.php?action=medicaments">Retour à la liste</a>
    </div>
</section>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
