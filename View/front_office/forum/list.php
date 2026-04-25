<?php
$pageTitle = 'Forums de Discussion';
require __DIR__ . '/../../layout/front_header.php';
?>

<!-- Hero Section -->
<section class="page-hero">
    <h1><i class="fas fa-comments"></i> Forums de Discussion</h1>
    <p>Échangez avec des professionnels de santé et d'autres patients sur des sujets médicaux importants.</p>
</section>

<!-- Forums Grid -->
<?php if (!empty($forums)): ?>
    <div class="forums-grid">
        <?php foreach ($forums as $index => $f): ?>
            <article class="forum-card" style="animation-delay: <?= $index * 0.05 ?>s">
                <div class="forum-card-icon">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <h3><?= htmlspecialchars($f['titre']) ?></h3>
                <p><?= htmlspecialchars($f['description'] ?? 'Aucune description disponible.') ?></p>
                <div class="forum-card-meta">
                    <span><i class="fas fa-file-alt"></i> <?= (int)($f['nb_posts'] ?? 0) ?> post<?= ($f['nb_posts'] ?? 0) > 1 ? 's' : '' ?></span>
                    <span><i class="fas fa-clock"></i> <?= date('d/m/Y', strtotime($f['created_at'])) ?></span>
                </div>
                <a href="index.php?controller=forum&action=show&id=<?= $f['id_forum'] ?>" class="forum-card-link">
                    Accéder au forum <i class="fas fa-arrow-right"></i>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-comments"></i>
        <h3>Aucun forum disponible</h3>
        <p>Les forums de discussion seront bientôt disponibles. Revenez plus tard !</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../../layout/front_footer.php'; ?>
