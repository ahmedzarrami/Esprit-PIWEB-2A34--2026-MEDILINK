<?php
$pageTitle = 'Forums de Discussion';
require __DIR__ . '/../../layout/front_header.php';
?>

<!-- Full Width Blue Hero Section -->
<div class="hero-wrapper">
    <div class="hero-container">
        <div class="hero-badge">
            <span class="dot"></span> Service disponible 24/7
        </div>
        
        <h1 class="hero-title">Échangez et consultez les<br><span>discussions & conseils santé</span></h1>
        <p class="hero-subtitle">Recherchez un forum, consultez les sujets, et participez aux échanges avec d'autres patients et professionnels de santé en quelques secondes.</p>
        
        <form action="index.php" method="GET" class="hero-search-wrapper">
            <input type="hidden" name="controller" value="forum">
            <input type="hidden" name="action" value="list">
            <input type="text" name="search" placeholder="Rechercher un forum, un sujet..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn-search">Rechercher</button>
        </form>
    </div>
</div>

<!-- Info / Stats Bar -->
<div class="info-bar">
    <div class="info-bar-container">
        <div class="info-item">
            <h4>Forums</h4>
            <p>Catalogue complet de sujets</p>
        </div>
        <div class="info-item">
            <h4>Discussions</h4>
            <p>Création rapide de posts</p>
        </div>
        <div class="info-item">
            <h4><?= $totalForums ?> actifs</h4>
            <p><?= $totalPosts ?> posts au total</p>
        </div>
    </div>
</div>

<!-- Main Container -->
<div class="container">
    <div style="margin-bottom: 2rem; border-left: 3px solid #2563eb; padding-left: 1rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b;">Forums mis en avant</h2>
    </div>

    <!-- Filters Sort (Optional inline) -->
    <?php if (!empty($_GET['search'])): ?>
        <div style="margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;">
            <p style="color: #64748b;">Résultats pour "<strong><?= htmlspecialchars($_GET['search']) ?></strong>"</p>
            <a href="index.php?controller=forum&action=list" class="btn-secondary btn-sm" style="border-radius: 100px;">Réinitialiser</a>
        </div>
    <?php endif; ?>

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
