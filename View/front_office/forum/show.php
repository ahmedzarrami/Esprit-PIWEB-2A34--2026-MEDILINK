<?php
$pageTitle = htmlspecialchars($forum->getTitre());
require __DIR__ . '/../../layout/front_header.php';
?>

<!-- Breadcrumb -->
<nav>
    <ul class="breadcrumb">
        <li><a href="index.php?controller=forum&action=list"><i class="fas fa-home"></i> Forums</a></li>
        <li class="separator"><i class="fas fa-chevron-right"></i></li>
        <li class="current"><?= htmlspecialchars($forum->getTitre()) ?></li>
    </ul>
</nav>

<!-- Forum Header -->
<section class="page-hero" style="text-align: left; padding-bottom: 1rem;">
    <h1><?= htmlspecialchars($forum->getTitre()) ?></h1>
    <p><?= htmlspecialchars($forum->getDescription() ?? '') ?></p>
</section>

<!-- Section Header with Action -->
<div class="section-header">
    <h2><i class="fas fa-file-alt" style="color: var(--accent-teal);"></i> Publications (<?= count($posts) ?>)</h2>
    <?php if (isset($_SESSION['user']) && in_array($_SESSION['user']['role'], ['professionnel', 'administrateur'])): ?>
        <a href="index.php?controller=post&action=create&id_forum=<?= $forum->getIdForum() ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Post
        </a>
    <?php endif; ?>
</div>

<!-- Posts List -->
<?php if (!empty($posts)): ?>
    <div class="posts-list">
        <?php foreach ($posts as $index => $p): ?>
            <article class="post-card" style="animation-delay: <?= $index * 0.05 ?>s">
                <div class="post-header">
                    <div class="post-avatar role-<?= htmlspecialchars($p['auteur_role']) ?>">
                        <?= strtoupper(substr($p['auteur_prenom'], 0, 1) . substr($p['auteur_nom'], 0, 1)) ?>
                    </div>
                    <div class="post-author-info">
                        <span class="post-author-name">
                            <?= htmlspecialchars($p['auteur_prenom'] . ' ' . $p['auteur_nom']) ?>
                        </span>
                        <span class="post-author-role role-<?= htmlspecialchars($p['auteur_role']) ?>">
                            <?= htmlspecialchars($p['auteur_role']) ?>
                        </span>
                        <div class="post-date">
                            <i class="fas fa-clock"></i> <?= date('d/m/Y à H:i', strtotime($p['date_publication'])) ?>
                        </div>
                    </div>
                </div>

                <div class="post-content">
                    <?= nl2br(htmlspecialchars(mb_substr($p['contenu'], 0, 300))) ?><?= mb_strlen($p['contenu']) > 300 ? '...' : '' ?>
                </div>

                <div class="post-footer">
                    <div class="post-stats">
                        <span><i class="fas fa-comment"></i> <?= (int)$p['nb_commentaires'] ?> commentaire<?= $p['nb_commentaires'] > 1 ? 's' : '' ?></span>
                    </div>
                    <a href="index.php?controller=post&action=show&id=<?= $p['id_post'] ?>" class="btn btn-secondary btn-sm">
                        Lire la suite <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-file-alt"></i>
        <h3>Aucun post pour le moment</h3>
        <p>Soyez le premier à publier dans ce forum !</p>
        <?php if (isset($_SESSION['user']) && in_array($_SESSION['user']['role'], ['professionnel', 'administrateur'])): ?>
            <a href="index.php?controller=post&action=create&id_forum=<?= $forum->getIdForum() ?>" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-plus"></i> Créer un post
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../../layout/front_footer.php'; ?>
