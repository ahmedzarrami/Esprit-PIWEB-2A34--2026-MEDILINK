<?php require __DIR__ . '/../layouts/header.php'; ?>

<section class="listing-hero">
    <h1>Catalogue des médicaments</h1>
    <p>Parcourez la liste, recherchez un produit et consultez ses détails.</p>
</section>

<section class="section-block narrow">
    <form method="GET" action="index.php" class="toolbar">
        <input type="hidden" name="action" value="medicaments">

        <div class="toolbar-group grow">
            <label for="search">Recherche</label>
            <input
                id="search"
                type="text"
                name="search"
                value="<?= htmlspecialchars($search) ?>"
                placeholder="Nom, description, forme, fabricant">
        </div>

        <div class="toolbar-group">
            <label for="sort">Tri</label>
            <select name="sort" id="sort">
                <option value="nom_asc" <?= $sort === 'nom_asc' ? 'selected' : '' ?>>Nom A-Z</option>
                <option value="nom_desc" <?= $sort === 'nom_desc' ? 'selected' : '' ?>>Nom Z-A</option>
                <option value="prix_asc" <?= $sort === 'prix_asc' ? 'selected' : '' ?>>Prix croissant</option>
                <option value="prix_desc" <?= $sort === 'prix_desc' ? 'selected' : '' ?>>Prix décroissant</option>
                <option value="date_exp_asc" <?= $sort === 'date_exp_asc' ? 'selected' : '' ?>>Expiration proche</option>
                <option value="date_exp_desc" <?= $sort === 'date_exp_desc' ? 'selected' : '' ?>>Expiration lointaine</option>
            </select>
        </div>

        <div class="toolbar-actions">
            <button type="submit">Appliquer</button>
            <a href="index.php?action=medicaments">Réinitialiser</a>
        </div>
    </form>

    <div class="results-head">
        <p><strong><?= (int) $total ?></strong> médicament(s) trouvé(s)</p>
        <p>Page <?= (int) $page ?> / <?= (int) $totalPages ?></p>
    </div>

    <?php if (empty($medicaments)): ?>
        <div class="empty-state">
            <h3>Aucun médicament trouvé</h3>
            <p>Essaie avec un autre mot-clé ou change le tri.</p>
        </div>
    <?php else: ?>
        <div class="medicine-list-grid">
            <?php foreach ($medicaments as $medicament): ?>
                <article class="medicine-list-card">
                    <div class="top-row">
                        <span class="pill"><?= htmlspecialchars($medicament['forme']) ?></span>
                        <span class="price"><?= number_format((float) $medicament['prix'], 2, ',', ' ') ?> TND</span>
                    </div>
                    <h3><?= htmlspecialchars($medicament['nom']) ?></h3>
                    <p class="meta"><?= htmlspecialchars($medicament['dosage']) ?> - <?= htmlspecialchars($medicament['fabricant']) ?></p>
                    <p class="description"><?= htmlspecialchars(mb_strimwidth($medicament['description'], 0, 140, '...')) ?></p>
                    <div class="bottom-row">
                        <span>Stock : <?= (int) $medicament['stock'] ?></span>
                        <a href="index.php?action=show_medicament&id=<?= (int) $medicament['id'] ?>">Voir plus</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a
                        href="index.php?action=medicaments&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>&page=<?= $i ?>"
                        class="<?= $i === $page ? 'current' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
