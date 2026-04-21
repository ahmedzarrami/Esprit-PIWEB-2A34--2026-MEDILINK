<?php
$buildQuery = static function (array $overrides = []) use ($search, $sortBy, $sortDirection, $page): string {
    $params = [
        'action' => 'index',
        'search' => $search ?? '',
        'sort_by' => $sortBy ?? 'id',
        'sort_dir' => $sortDirection ?? 'desc',
        'page' => $page ?? 1,
    ];

    foreach ($overrides as $key => $value) {
        $params[$key] = $value;
    }

    return 'index.php?' . http_build_query($params);
};

$startItem = $totalItems > 0 ? (($page - 1) * $perPage) + 1 : 0;
$endItem = $totalItems > 0 ? min($page * $perPage, $totalItems) : 0;
$visibleCount = count($medicaments);
?>
<section class="stats-grid">
    <article class="stat-card">
        <span>Total médicaments</span>
        <strong><?= e((string) $stats['total']) ?></strong>
    </article>
    <article class="stat-card warning">
        <span>Stock faible</span>
        <strong><?= e((string) $stats['lowStock']) ?></strong>
    </article>
    <article class="stat-card danger">
        <span>Rupture</span>
        <strong><?= e((string) $stats['outOfStock']) ?></strong>
    </article>
    <article class="stat-card info">
        <span>Affichés sur cette page</span>
        <strong><?= e((string) $visibleCount) ?></strong>
    </article>
</section>

<section class="panel">
    <div class="panel-header between">
        <div>
            <h2>Liste des médicaments</h2>
            <p>Recherche, tri, pagination et actions CRUD dans un seul espace.</p>
        </div>
        <a class="button primary" href="index.php?action=create">+ Ajouter un médicament</a>
    </div>

    <form class="search-bar" method="GET" action="index.php" novalidate>
        <input type="hidden" name="action" value="index">
        <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="Rechercher par nom, description, dosage, forme ou fabricant">

        <select name="sort_by" class="select-field">
            <?php foreach ($sortOptions as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= (($sortBy ?? 'id') === $value) ? 'selected' : '' ?>>
                    Trier par : <?= e($label) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="sort_dir" class="select-field">
            <option value="asc" <?= (($sortDirection ?? 'desc') === 'asc') ? 'selected' : '' ?>>Ordre croissant</option>
            <option value="desc" <?= (($sortDirection ?? 'desc') === 'desc') ? 'selected' : '' ?>>Ordre décroissant</option>
        </select>

        <button type="submit" class="button primary">Appliquer</button>
        <a href="index.php?action=index" class="button secondary">Réinitialiser</a>
    </form>

    <div class="list-meta">
        <span><?= e((string) $totalItems) ?> résultat(s)</span>
        <span>Affichage <?= e((string) $startItem) ?> à <?= e((string) $endItem) ?></span>
        <span>Page <?= e((string) $page) ?> / <?= e((string) $totalPages) ?></span>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Dosage</th>
                    <th>Forme</th>
                    <th>Fabricant</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($medicaments === []): ?>
                    <tr>
                        <td colspan="10" class="empty-state">Aucun médicament trouvé.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($medicaments as $medicament): ?>
                        <?php
                            $stock = (int) $medicament['stock'];
                            $stockClass = $stock === 0 ? 'status-danger' : ($stock <= 10 ? 'status-warning' : 'status-success');
                            $stockLabel = $stock === 0 ? 'Rupture' : ($stock <= 10 ? 'Stock faible' : 'Disponible');
                        ?>
                        <tr>
                            <td><?= e((string) $medicament['id']) ?></td>
                            <td><?= e($medicament['nom']) ?></td>
                            <td class="description-cell"><?= e($medicament['description']) ?></td>
                            <td><?= e($medicament['dosage']) ?></td>
                            <td><?= e($medicament['forme']) ?></td>
                            <td><?= e($medicament['fabricant']) ?></td>
                            <td><?= e(number_format((float) $medicament['prix'], 2, ',', ' ')) ?> TND</td>
                            <td><?= e((string) $stock) ?></td>
                            <td><span class="status-pill <?= e($stockClass) ?>"><?= e($stockLabel) ?></span></td>
                            <td>
                                <div class="action-group">
                                    <a class="mini-button info" href="index.php?action=show&id=<?= e((string) $medicament['id']) ?>">Voir</a>
                                    <a class="mini-button warning" href="index.php?action=edit&id=<?= e((string) $medicament['id']) ?>">Modifier</a>
                                    <form method="POST" action="index.php?action=delete" onsubmit="return confirm('Supprimer ce médicament ?');">
                                        <input type="hidden" name="id" value="<?= e((string) $medicament['id']) ?>">
                                        <button type="submit" class="mini-button danger">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Pagination des médicaments">
            <a class="page-link <?= $page <= 1 ? 'disabled' : '' ?>" href="<?= $page <= 1 ? '#' : e($buildQuery(['page' => $page - 1])) ?>">Précédent</a>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a class="page-link <?= $i === $page ? 'active' : '' ?>" href="<?= e($buildQuery(['page' => $i])) ?>"><?= e((string) $i) ?></a>
            <?php endfor; ?>
            <a class="page-link <?= $page >= $totalPages ? 'disabled' : '' ?>" href="<?= $page >= $totalPages ? '#' : e($buildQuery(['page' => $page + 1])) ?>">Suivant</a>
        </nav>
    <?php endif; ?>
</section>
