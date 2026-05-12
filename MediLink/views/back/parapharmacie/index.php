<?php
$buildQuery = static function (array $overrides = []) use ($search, $sortBy, $sortDirection, $filterCat, $page): string {
    $params = [
        'action'    => 'index',
        'search'    => $search ?? '',
        'sort_by'   => $sortBy ?? 'id',
        'sort_dir'  => $sortDirection ?? 'desc',
        'categorie' => $filterCat ?? '',
        'page'      => $page ?? 1,
    ];
    foreach ($overrides as $key => $value) {
        $params[$key] = $value;
    }
    return 'parapharmacie.php?' . http_build_query($params);
};

$startItem    = $totalItems > 0 ? (($page - 1) * $perPage) + 1 : 0;
$endItem      = $totalItems > 0 ? min($page * $perPage, $totalItems) : 0;
$visibleCount = count($produits);

$catIcons = [
    'Soins visage' => '🧴', 'Soins corps' => '🫧', 'Hygiène' => '🪥',
    'Compléments alimentaires' => '💊', 'Bébé & Maman' => '🍼',
    'Capillaire' => '💆', 'Solaire' => '☀️', 'Minceur' => '⚖️',
    'Orthopédie' => '🦴', 'Autre' => '📦'
];
?>
<section class="stats-grid">
    <article class="stat-card">
        <span>Total produits</span>
        <strong><?= e((string) $stats['total']) ?></strong>
    </article>
    <article class="stat-card warning">
        <span>Catégories</span>
        <strong><?= e((string) $stats['categories']) ?></strong>
    </article>
    <article class="stat-card" style="border-left-color:#059669">
        <span>En stock</span>
        <strong><?= e((string) $stats['disponible']) ?></strong>
    </article>
    <article class="stat-card danger">
        <span>Rupture de stock</span>
        <strong><?= e((string) $stats['rupture']) ?></strong>
    </article>
</section>

<section class="panel">
    <div class="panel-header between">
        <div>
            <h2>Produits Parapharmacie</h2>
        </div>
        <a class="button primary" href="parapharmacie.php?action=create">+ Ajouter un produit</a>
    </div>

    <form class="search-bar" method="GET" action="parapharmacie.php" novalidate>
        <input type="hidden" name="action" value="index">
        <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="Rechercher par nom, référence, description...">

        <select name="categorie" class="select-field">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat) ?>" <?= ($filterCat ?? '') === $cat ? 'selected' : '' ?>>
                    <?= $catIcons[$cat] ?? '📦' ?> <?= e($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="sort_by" class="select-field">
            <?php foreach ($sortOptions as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= (($sortBy ?? 'id') === $value) ? 'selected' : '' ?>>
                    Trier par : <?= e($label) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="sort_dir" class="select-field">
            <option value="asc"  <?= (($sortDirection ?? 'desc') === 'asc')  ? 'selected' : '' ?>>Ordre croissant</option>
            <option value="desc" <?= (($sortDirection ?? 'desc') === 'desc') ? 'selected' : '' ?>>Ordre décroissant</option>
        </select>

        <button type="submit" class="button primary">Appliquer</button>
        <a href="parapharmacie.php?action=index" class="button secondary">Réinitialiser</a>
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
                    <th>Référence</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Description</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($produits === []): ?>
                    <tr><td colspan="8" class="empty-state">Aucun produit trouvé.</td></tr>
                <?php else: ?>
                    <?php foreach ($produits as $p): ?>
                        <?php
                            $stockVal = (int) $p['stock'];
                            $stockClass = $stockVal === 0 ? 'color:#dc2626;font-weight:700' : ($stockVal <= 5 ? 'color:#d97706;font-weight:600' : 'color:#059669');
                            $stockLabel = $stockVal === 0 ? '⛔ Rupture' : ($stockVal <= 5 ? '⚠ ' . $stockVal : '✓ ' . $stockVal);
                        ?>
                        <tr>
                            <td><?= e((string) $p['id']) ?></td>
                            <td><code style="background:rgba(37,99,235,.08);padding:2px 8px;border-radius:4px;font-size:12px;color:#2563eb"><?= e($p['reference']) ?></code></td>
                            <td style="font-weight:600"><?= e($p['nom']) ?></td>
                            <td>
                                <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(139,92,246,.08);padding:3px 10px;border-radius:6px;font-size:12px;font-weight:600;color:#7c3aed">
                                    <?= $catIcons[$p['categorie']] ?? '📦' ?> <?= e($p['categorie']) ?>
                                </span>
                            </td>
                            <td class="description-cell"><?= e($p['description'] ?? '') ?></td>
                            <td style="font-weight:700;color:#0f172a"><?= e(number_format((float) $p['prix'], 3, ',', ' ')) ?> DT</td>
                            <td style="<?= $stockClass ?>"><?= $stockLabel ?></td>
                            <td>
                                <div class="action-group">
                                    <a class="mini-button info"    href="parapharmacie.php?action=show&id=<?= e((string) $p['id']) ?>">Voir</a>
                                    <a class="mini-button warning" href="parapharmacie.php?action=edit&id=<?= e((string) $p['id']) ?>">Modifier</a>
                                    <form method="POST" action="parapharmacie.php?action=delete" onsubmit="return confirm('Supprimer ce produit ?');">
                                        <input type="hidden" name="id" value="<?= e((string) $p['id']) ?>">
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
        <nav class="pagination" aria-label="Pagination">
            <a class="page-link <?= $page <= 1 ? 'disabled' : '' ?>"
               href="<?= $page <= 1 ? '#' : e($buildQuery(['page' => $page - 1])) ?>">Précédent</a>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a class="page-link <?= $i === $page ? 'active' : '' ?>"
                   href="<?= e($buildQuery(['page' => $i])) ?>"><?= e((string) $i) ?></a>
            <?php endfor; ?>
            <a class="page-link <?= $page >= $totalPages ? 'disabled' : '' ?>"
               href="<?= $page >= $totalPages ? '#' : e($buildQuery(['page' => $page + 1])) ?>">Suivant</a>
        </nav>
    <?php endif; ?>
</section>
