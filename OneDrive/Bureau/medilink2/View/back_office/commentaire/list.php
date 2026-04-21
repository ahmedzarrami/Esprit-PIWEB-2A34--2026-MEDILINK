<?php
$pageTitle = 'Modération des Commentaires';
require __DIR__ . '/../../layout/back_header.php';
?>

<!-- Page Header -->
<div class="admin-page-header">
    <div>
        <h2><i class="fas fa-comment-dots" style="color: var(--admin-teal);"></i> Modération des Commentaires</h2>
        <p>Gérez et modérez les commentaires publiés sur les forums.</p>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon teal"><i class="fas fa-comments"></i></div>
        <div class="stat-info">
            <h3><?= count($commentaires) ?></h3>
            <p>Commentaires au total</p>
        </div>
    </div>
</div>

<!-- Comments Table -->
<div class="table-card">
    <div class="table-header">
        <h3>Liste des commentaires</h3>
        <span style="font-size: 0.8rem; color: var(--admin-text-muted);"><?= count($commentaires) ?> résultat(s)</span>
    </div>

    <?php if (!empty($commentaires)): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Commentaire</th>
                    <th>Auteur</th>
                    <th>Post / Forum</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commentaires as $c): ?>
                    <tr>
                        <td><span class="badge badge-green">#<?= $c['id_commentaire'] ?></span></td>
                        <td class="table-text-truncate"><?= htmlspecialchars(mb_substr($c['contenu'], 0, 100)) ?><?= mb_strlen($c['contenu']) > 100 ? '...' : '' ?></td>
                        <td>
                            <strong><?= htmlspecialchars($c['auteur_prenom'] . ' ' . $c['auteur_nom']) ?></strong>
                            <br>
                            <span class="badge badge-<?= $c['auteur_role'] === 'professionnel' ? 'blue' : ($c['auteur_role'] === 'administrateur' ? 'orange' : 'purple') ?>">
                                <?= htmlspecialchars($c['auteur_role']) ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-size: 0.82rem;">
                                <div style="color: var(--admin-text-secondary);"><i class="fas fa-file-alt"></i> <?= htmlspecialchars(mb_substr($c['post_contenu'], 0, 40)) ?>...</div>
                                <div style="color: var(--admin-text-muted); font-size: 0.75rem; margin-top: 0.2rem;"><i class="fas fa-folder"></i> <?= htmlspecialchars($c['forum_titre']) ?></div>
                            </div>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($c['date_commentaire'])) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="index.php?controller=post&action=show&id=<?= $c['id_post'] ?>" class="btn-action" title="Voir le post">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn-action delete" title="Supprimer"
                                    onclick="confirmDelete('index.php?controller=commentaire&action=delete&id=<?= $c['id_commentaire'] ?>', 'ce commentaire')">
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
            <p>Aucun commentaire à modérer.</p>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../layout/back_footer.php'; ?>
