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
                                <a href="index.php?controller=forum&action=show&id=<?= $f['id_forum'] ?>" class="btn-action" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="index.php?controller=forum&action=edit&id=<?= $f['id_forum'] ?>" class="btn-action" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn-action delete" title="Supprimer"
                                    onclick="confirmDelete('index.php?controller=forum&action=delete&id=<?= $f['id_forum'] ?>', 'le forum « <?= htmlspecialchars(addslashes($f['titre'])) ?> »')">
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
            <p>Aucun forum trouvé. <a href="index.php?controller=forum&action=create">Créer le premier forum</a></p>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../layout/back_footer.php'; ?>
