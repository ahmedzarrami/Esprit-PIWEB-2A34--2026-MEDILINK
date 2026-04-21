<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="listing-hero section-block">
    <h1>Ordonnances</h1>
    <p>Gérez et consultez toutes les ordonnances émises par les médecins.</p>
</div>

<div class="section-block">

    <?php if ($deleted): ?>
        <div class="alert-success">Ordonnance supprimée avec succès.</div>
    <?php endif; ?>

    <div class="toolbar">
        <form method="GET" action="index.php" class="toolbar-group grow">
            <input type="hidden" name="action" value="ordonnances">
            <label for="search">Rechercher</label>
            <input
                type="text"
                id="search"
                name="search"
                placeholder="Numéro ou patient…"
                value="<?= htmlspecialchars($search) ?>"
            >
        </form>
        <div class="toolbar-actions">
            <a href="index.php?action=ordonnances" class="">Réinitialiser</a>
            <a href="index.php?action=create_ordonnance" class="header-btn">+ Nouvelle ordonnance</a>
        </div>
    </div>

    <?php if (empty($ordonnances)): ?>
        <div class="empty-state">
            <h3>Aucune ordonnance trouvée</h3>
            <p>Aucune ordonnance ne correspond à votre recherche.</p>
            <a href="index.php?action=create_ordonnance">Créer une ordonnance</a>
        </div>
    <?php else: ?>
        <div class="ord-table-wrap detail-card">
            <table class="ord-table">
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Patient</th>
                        <th>Date</th>
                        <th>Créée le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ordonnances as $ord): ?>
                        <tr>
                            <td><span class="pill"><?= htmlspecialchars($ord['numero']) ?></span></td>
                            <td>
                                <?= htmlspecialchars($ord['patient_nom']) ?>
                                <?php if ($ord['patient_age']): ?>
                                    <small class="meta"> · <?= (int) $ord['patient_age'] ?> ans</small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($ord['date_ordonnance']))) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($ord['created_at']))) ?></td>
                            <td class="ord-actions">
                                <a href="index.php?action=show_ordonnance&id=<?= $ord['id'] ?>" class="btn-view">Voir</a>
                                <a href="index.php?action=print_ordonnance&id=<?= $ord['id'] ?>" target="_blank" class="btn-print">Imprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a
                        href="index.php?action=ordonnances&page=<?= $p ?>&search=<?= urlencode($search) ?>"
                        class="<?= $p === $page ? 'current' : '' ?>"
                    ><?= $p ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

        <p class="results-head" style="margin-top:14px;">
            <?= $total ?> ordonnance<?= $total > 1 ? 's' : '' ?> au total
        </p>
    <?php endif; ?>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
