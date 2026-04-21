<section class="panel detail-panel">
    <div class="panel-header between">
        <div>
            <h2>Détail du médicament</h2>
            <p>Consultation des informations enregistrées.</p>
        </div>
        <div class="inline-actions">
            <a class="button warning" href="index.php?action=edit&id=<?= e((string) $medicament['id']) ?>">Modifier</a>
            <a class="button secondary" href="index.php?action=index">Retour à la liste</a>
        </div>
    </div>

    <?php
        $stock = (int) $medicament['stock'];
        $stockClass = $stock === 0 ? 'status-danger' : ($stock <= 10 ? 'status-warning' : 'status-success');
        $stockLabel = $stock === 0 ? 'Rupture' : ($stock <= 10 ? 'Stock faible' : 'Disponible');
    ?>

    <div class="detail-grid">
        <div class="detail-card"><span>ID</span><strong><?= e((string) $medicament['id']) ?></strong></div>
        <div class="detail-card"><span>Nom</span><strong><?= e($medicament['nom']) ?></strong></div>
        <div class="detail-card"><span>Dosage</span><strong><?= e($medicament['dosage']) ?></strong></div>
        <div class="detail-card"><span>Forme</span><strong><?= e($medicament['forme']) ?></strong></div>
        <div class="detail-card"><span>Fabricant</span><strong><?= e($medicament['fabricant']) ?></strong></div>
        <div class="detail-card"><span>Prix</span><strong><?= e(number_format((float) $medicament['prix'], 2, ',', ' ')) ?> TND</strong></div>
        <div class="detail-card"><span>Stock</span><strong><?= e((string) $stock) ?></strong></div>
        <div class="detail-card"><span>Statut</span><strong><span class="status-pill <?= e($stockClass) ?>"><?= e($stockLabel) ?></span></strong></div>
    </div>

    <div class="description-block">
        <h3>Description du médicament</h3>
        <p><?= nl2br(e($medicament['description'])) ?></p>
    </div>
</section>
