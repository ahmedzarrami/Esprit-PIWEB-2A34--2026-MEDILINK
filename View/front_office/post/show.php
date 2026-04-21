<?php
$pageTitle = 'Post — Discussion';
require __DIR__ . '/../../layout/front_header.php';
?>

<!-- Breadcrumb -->
<nav>
    <ul class="breadcrumb">
        <li><a href="index.php?controller=forum&action=list"><i class="fas fa-home"></i> Forums</a></li>
        <li class="separator"><i class="fas fa-chevron-right"></i></li>
        <li><a href="index.php?controller=forum&action=show&id=<?= $post['id_forum'] ?>"><?= htmlspecialchars($forum->getTitre()) ?></a></li>
        <li class="separator"><i class="fas fa-chevron-right"></i></li>
        <li class="current">Post #<?= $post['id_post'] ?></li>
    </ul>
</nav>

<!-- Post Detail -->
<article class="post-card" style="margin-top: 1.5rem;">
    <div class="post-header">
        <div class="post-avatar role-<?= htmlspecialchars($post['auteur_role']) ?>">
            <?= strtoupper(substr($post['auteur_prenom'], 0, 1) . substr($post['auteur_nom'], 0, 1)) ?>
        </div>
        <div class="post-author-info">
            <span class="post-author-name">
                <?= htmlspecialchars($post['auteur_prenom'] . ' ' . $post['auteur_nom']) ?>
            </span>
            <span class="post-author-role role-<?= htmlspecialchars($post['auteur_role']) ?>">
                <?= htmlspecialchars($post['auteur_role']) ?>
            </span>
            <div class="post-date">
                <i class="fas fa-clock"></i> Publié le <?= date('d/m/Y à H:i', strtotime($post['date_publication'])) ?>
            </div>
        </div>
    </div>

    <div class="post-content full">
        <?= nl2br(htmlspecialchars($post['contenu'])) ?>
    </div>
</article>

<!-- ===== COMMENTS SECTION ===== -->
<section class="comments-section">
    <h3>
        <i class="fas fa-comments" style="color: var(--accent-teal);"></i>
        Commentaires
        <span class="count-badge"><?= count($commentaires) ?></span>
    </h3>

    <!-- Session alerts (redirect feedback) -->
    <?php if (isset($_SESSION['comment_success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['comment_success']) ?>
        </div>
        <?php unset($_SESSION['comment_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['comment_errors'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars(implode(' ', $_SESSION['comment_errors'])) ?>
        </div>
        <?php unset($_SESSION['comment_errors']); ?>
    <?php endif; ?>

    <!-- Comment Form -->
    <?php if (isset($_SESSION['user'])): ?>
        <div class="form-card" style="margin-bottom: 2rem;">
            <form action="index.php?controller=commentaire&action=add" method="POST" data-validate="true">
                <input type="hidden" name="id_post" value="<?= $post['id_post'] ?>">

                <div class="form-group">
                    <label for="commentaire-contenu">
                        Votre commentaire <span class="required">*</span>
                    </label>
                    <textarea
                        id="commentaire-contenu"
                        name="contenu"
                        class="form-control"
                        rows="4"
                        placeholder="Partagez votre avis ou posez une question..."
                        data-required="true"
                        data-min="3"
                        data-max="2000"
                        data-label="Le commentaire"
                    ><?= htmlspecialchars($_SESSION['comment_contenu'] ?? '') ?></textarea>
                    <?php unset($_SESSION['comment_contenu']); ?>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Publier le commentaire
                </button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Comments List -->
    <?php if (!empty($commentaires)): ?>
        <?php foreach ($commentaires as $index => $c): ?>
            <div class="comment-item" style="animation-delay: <?= $index * 0.05 ?>s">
                <div class="comment-avatar role-<?= htmlspecialchars($c['auteur_role']) ?>"
                     style="background: <?= $c['auteur_role'] === 'professionnel' ? 'linear-gradient(135deg, #3b82f6, #14b8a6)' : 'linear-gradient(135deg, #8b5cf6, #3b82f6)' ?>">
                    <?= strtoupper(substr($c['auteur_prenom'], 0, 1) . substr($c['auteur_nom'], 0, 1)) ?>
                </div>
                <div class="comment-body">
                    <div class="comment-author">
                        <?= htmlspecialchars($c['auteur_prenom'] . ' ' . $c['auteur_nom']) ?>
                        <span class="post-author-role role-<?= htmlspecialchars($c['auteur_role']) ?>" style="font-size: 0.65rem;">
                            <?= htmlspecialchars($c['auteur_role']) ?>
                        </span>
                    </div>
                    <div class="comment-date">
                        <i class="fas fa-clock"></i> <?= date('d/m/Y à H:i', strtotime($c['date_commentaire'])) ?>
                    </div>
                    <div class="comment-text">
                        <?= nl2br(htmlspecialchars($c['contenu'])) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state" style="padding: 2rem;">
            <i class="fas fa-comment-slash"></i>
            <h3>Aucun commentaire</h3>
            <p>Soyez le premier à commenter ce post !</p>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../../layout/front_footer.php'; ?>
