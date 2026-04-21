<?php require __DIR__ . '/../layouts/header.php'; ?>

<section class="section-block narrow">
    <a class="back-link" href="index.php?action=medicaments">← Retour au catalogue</a>

    <div class="detail-card">
        <div class="detail-header">
            <div>
                <span class="pill large"><?= htmlspecialchars($medicament['forme']) ?></span>
                <h1><?= htmlspecialchars($medicament['nom']) ?></h1>
                <p><?= htmlspecialchars($medicament['dosage']) ?> - <?= htmlspecialchars($medicament['fabricant']) ?></p>
            </div>
            <div class="detail-price"><?= number_format((float) $medicament['prix'], 2, ',', ' ') ?> TND</div>
        </div>

        <div class="detail-grid">
            <div>
                <h3>Description</h3>
                <p><?= nl2br(htmlspecialchars($medicament['description'])) ?></p>
            </div>
            <div>
                <h3>Informations</h3>
                <ul class="detail-list">
                    <li><strong>Forme :</strong> <?= htmlspecialchars($medicament['forme']) ?></li>
                    <li><strong>Fabricant :</strong> <?= htmlspecialchars($medicament['fabricant']) ?></li>
                    <li><strong>Stock :</strong> <?= (int) $medicament['stock'] ?></li>
                    <li><strong>Date d'expiration :</strong> <?= htmlspecialchars($medicament['date_expiration']) ?></li>
                    <li><strong>Date d'ajout :</strong> <?= htmlspecialchars($medicament['created_at']) ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
