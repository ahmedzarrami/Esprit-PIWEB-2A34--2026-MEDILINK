<?php
$pageTitle = 'Gestion des Forums';
require __DIR__ . '/../../layout/back_header.php';
?>

<!-- Page Header -->
<div class="admin-page-header">
    <div>
        <h2><i class="fas fa-layer-group" style="color: var(--admin-accent);"></i> Gestion des Forums</h2>
        <p>Créez, modifiez et supprimez les forums de discussion.</p>
    </div>
    <a href="index.php?controller=forum&action=create" class="admin-btn admin-btn-primary">
        <i class="fas fa-plus"></i> Nouveau Forum
    </a>
</div>

<!-- Stats -->
<div class="stats-grid">
<<<<<<< HEAD
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-layer-group"></i></div>
        <div class="stat-info">
            <h3><?= count($forums) ?></h3>
            <p>Forums actifs</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-file-alt"></i></div>
        <div class="stat-info">
            <h3><?= array_sum(array_column($forums, 'nb_posts')) ?></h3>
            <p>Posts au total</p>
        </div>
    </div>
=======
    <div class="stat-card c-blue">
        <div class="stat-info">
            <p>TOTAL FORUMS</p>
            <h3><?= $totalForums ?></h3>
        </div>
    </div>
    <div class="stat-card c-yellow">
        <div class="stat-info">
            <p>POSTS AU TOTAL</p>
            <h3><?= $totalPosts ?></h3>
        </div>
    </div>
    <div class="stat-card c-cyan">
        <div class="stat-info">
            <p>LE PLUS ACTIF</p>
            <?php if ($topForum && $topForum['nb'] > 0): ?>
                <h3 style="font-size: 1.5rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($topForum['titre']) ?>">
                    <?= htmlspecialchars($topForum['titre']) ?>
                </h3>
            <?php else: ?>
                <h3 style="font-size: 1.5rem;">N/A</h3>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Filtres et Recherche (Partie Métier : Recherche et Trie) -->
<div class="admin-filters" style="display: flex; gap: 1rem; margin-bottom: 1.5rem; background: var(--admin-card); padding: 1rem; border-radius: 0.5rem; box-shadow: var(--admin-shadow);">
    <form action="index.php" method="GET" style="display: flex; gap: 1rem; flex: 1; align-items: center; flex-wrap: wrap;">
        <input type="hidden" name="controller" value="forum">
        <input type="hidden" name="action" value="adminList">
        
        <div style="flex: 1; min-width: 250px;">
            <input type="text" name="search" placeholder="Rechercher par titre ou description..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control" style="width: 100%;">
        </div>
        
        <div>
            <select name="sort" class="form-control">
                <option value="date_desc" <?= ($_GET['sort'] ?? '') === 'date_desc' ? 'selected' : '' ?>>Plus récents en premier</option>
                <option value="date_asc" <?= ($_GET['sort'] ?? '') === 'date_asc' ? 'selected' : '' ?>>Plus anciens en premier</option>
                <option value="posts_desc" <?= ($_GET['sort'] ?? '') === 'posts_desc' ? 'selected' : '' ?>>Plus de posts</option>
            </select>
        </div>
        
        <button type="submit" class="admin-btn admin-btn-primary">
            <i class="fas fa-search"></i> Filtrer
        </button>
        <?php if (!empty($_GET['search']) || !empty($_GET['sort'])): ?>
            <a href="index.php?controller=forum&action=adminList" class="admin-btn admin-btn-secondary">Réinitialiser</a>
        <?php endif; ?>
    </form>
>>>>>>> master
</div>

<!-- Forums Table -->
<div class="table-card">
    <div class="table-header">
        <h3>Liste des forums</h3>
        <span style="font-size: 0.8rem; color: var(--admin-text-muted);"><?= count($forums) ?> résultat(s)</span>
    </div>

    <?php if (!empty($forums)): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Posts</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($forums as $f): ?>
                    <tr>
                        <td><span class="badge badge-purple">#<?= $f['id_forum'] ?></span></td>
                        <td><strong><?= htmlspecialchars($f['titre']) ?></strong></td>
                        <td class="table-text-truncate"><?= htmlspecialchars($f['description'] ?? '—') ?></td>
                        <td><span class="badge badge-blue"><?= (int)($f['nb_posts'] ?? 0) ?></span></td>
                        <td><?= date('d/m/Y', strtotime($f['created_at'])) ?></td>
                        <td>
                            <div class="action-buttons">
<<<<<<< HEAD
                                <a href="index.php?controller=forum&action=show&id=<?= $f['id_forum'] ?>" class="btn-action" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="index.php?controller=forum&action=edit&id=<?= $f['id_forum'] ?>" class="btn-action" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn-action delete" title="Supprimer"
                                    onclick="confirmDelete('index.php?controller=forum&action=delete&id=<?= $f['id_forum'] ?>', 'le forum « <?= htmlspecialchars(addslashes($f['titre'])) ?> »')">
                                    <i class="fas fa-trash-alt"></i>
=======
                                <a href="index.php?controller=forum&action=show&id=<?= $f['id_forum'] ?>" class="btn-action btn-voir" title="Voir">
                                    Voir
                                </a>
                                <a href="index.php?controller=forum&action=edit&id=<?= $f['id_forum'] ?>" class="btn-action btn-modifier" title="Modifier">
                                    Modifier
                                </a>
                                <button class="btn-action btn-supprimer" title="Supprimer"
                                    onclick="confirmDelete('index.php?controller=forum&action=delete&id=<?= $f['id_forum'] ?>', 'le forum « <?= htmlspecialchars(addslashes($f['titre'])) ?> »')">
                                    Supprimer
>>>>>>> master
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
            <p>Aucun forum trouvé. <a href="index.php?controller=forum&action=create">Créer le premier forum</a></p>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../layout/back_footer.php'; ?>
