<?php require __DIR__ . '/../layouts/header.php'; ?>

<section class="section-block narrow">
    <div class="empty-state">
        <h3>Produit introuvable</h3>
        <p><?= htmlspecialchars($errorMessage ?? 'Le produit demandé est introuvable.') ?></p>
        <a href="index.php?action=parapharmacie">← Retour au catalogue</a>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
