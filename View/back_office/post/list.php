<?php
$pageTitle = 'Gestion des Posts';
require __DIR__ . '/../../layout/back_header.php';
?>

<!-- Page Header -->
<div class="admin-page-header">
    <div>
        <h2><i class="fas fa-file-alt" style="color: var(--admin-blue);"></i> Gestion des Posts</h2>
        <p>Consultez, modifiez et modérez les publications du forum.</p>
    </div>
    <a href="index.php?controller=post&action=adminCreate" class="admin-btn admin-btn-primary">
        <i class="fas fa-plus"></i> Nouveau Post
    </a>
</div>

<!-- Stats (Partie Métier : Statistique) -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-file-alt"></i></div>
        <div class="stat-info">
            <h3><?= $totalPosts ?></h3>
            <p>Posts au total</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-comment"></i></div>
        <div class="stat-info">
            <h3><?= $totalComments ?></h3>
            <p>Commentaires au total</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-fire"></i></div>
        <div class="stat-info">
            <?php if ($topPost): ?>
                <h3 style="font-size: 1.2rem;">Post #<?= $topPost['id_post'] ?></h3>
                <p>Le plus commenté (<?= $topPost['nb'] ?> comm.)</p>
            <?php else: ?>
                <h3 style="font-size: 1.2rem;">N/A</h3>
                <p>Aucun post</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Filtres et Recherche (Partie Métier : Recherche et Trie) -->
<div class="admin-filters" style="display: flex; gap: 1rem; margin-bottom: 1.5rem; background: var(--admin-card); padding: 1rem; border-radius: 0.5rem; box-shadow: var(--admin-shadow);">
    <form action="index.php" method="GET" style="display: flex; gap: 1rem; flex: 1; align-items: center; flex-wrap: wrap;">
        <input type="hidden" name="controller" value="post">
        <input type="hidden" name="action" value="adminList">
        
        <div style="flex: 1; min-width: 250px;">
            <input type="text" name="search" placeholder="Rechercher par contenu, auteur ou forum..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control" style="width: 100%;">
        </div>
        
        <div>
            <select name="sort" class="form-control">
                <option value="date_desc" <?= ($_GET['sort'] ?? '') === 'date_desc' ? 'selected' : '' ?>>Plus récents en premier</option>
                <option value="date_asc" <?= ($_GET['sort'] ?? '') === 'date_asc' ? 'selected' : '' ?>>Plus anciens en premier</option>
                <option value="comments_desc" <?= ($_GET['sort'] ?? '') === 'comments_desc' ? 'selected' : '' ?>>Plus commentés</option>
            </select>
        </div>
        
        <button type="submit" class="admin-btn admin-btn-primary">
            <i class="fas fa-search"></i> Filtrer
        </button>
        <?php if (!empty($_GET['search']) || !empty($_GET['sort'])): ?>
            <a href="index.php?controller=post&action=adminList" class="admin-btn admin-btn-secondary">Réinitialiser</a>
        <?php endif; ?>
    </form>
</div>

<!-- Posts Table -->
<div class="table-card">
    <div class="table-header">
        <h3>Liste des posts</h3>
        <span style="font-size: 0.8rem; color: var(--admin-text-muted);"><?= count($posts) ?> résultat(s)</span>
    </div>

    <?php if (!empty($posts)): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Contenu</th>
                    <th>Auteur</th>
                    <th>Forum</th>
                    <th>Commentaires</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $p): ?>
                    <tr>
                        <td><span class="badge badge-blue">#<?= $p['id_post'] ?></span></td>
                        <td class="table-text-truncate"><?= htmlspecialchars(mb_substr($p['contenu'], 0, 80)) ?><?= mb_strlen($p['contenu']) > 80 ? '...' : '' ?></td>
                        <td>
                            <strong><?= htmlspecialchars($p['auteur_prenom'] . ' ' . $p['auteur_nom']) ?></strong>
                            <br>
                            <span class="badge badge-<?= $p['auteur_role'] === 'professionnel' ? 'blue' : ($p['auteur_role'] === 'administrateur' ? 'orange' : 'purple') ?>">
                                <?= htmlspecialchars($p['auteur_role']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($p['forum_titre']) ?></td>
                        <td><span class="badge badge-green"><?= (int)$p['nb_commentaires'] ?></span></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['date_publication'])) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="index.php?controller=post&action=show&id=<?= $p['id_post'] ?>" class="btn-action" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="index.php?controller=post&action=edit&id=<?= $p['id_post'] ?>" class="btn-action" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn-action delete" title="Supprimer"
                                    onclick="confirmDelete('index.php?controller=post&action=delete&id=<?= $p['id_post'] ?>', 'ce post')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="padding: 3rem; text-align: center; color: var(--admin-text-muted);">
            <i class="fas fa-inbox" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
            <p>Aucun post trouvé.</p>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../layout/back_footer.php'; ?>
