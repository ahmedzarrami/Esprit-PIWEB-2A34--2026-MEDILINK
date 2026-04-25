<?php
$pageTitle = 'Nouveau Post';
require __DIR__ . '/../../layout/front_header.php';
?>

<!-- Breadcrumb -->
<nav>
    <ul class="breadcrumb">
        <li><a href="index.php?controller=forum&action=list"><i class="fas fa-home"></i> Forums</a></li>
        <li class="separator"><i class="fas fa-chevron-right"></i></li>
        <li><a href="index.php?controller=forum&action=show&id=<?= $forum->getIdForum() ?>"><?= htmlspecialchars($forum->getTitre()) ?></a></li>
        <li class="separator"><i class="fas fa-chevron-right"></i></li>
        <li class="current">Nouveau post</li>
    </ul>
</nav>

<!-- Page Header -->
<section class="page-hero" style="padding-bottom: 1rem;">
    <h1><i class="fas fa-pen-fancy"></i> Nouveau Post</h1>
    <p>Publiez dans le forum « <?= htmlspecialchars($forum->getTitre()) ?> »</p>
</section>

<!-- Form -->
<div class="form-container">
    <!-- Server-side errors -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <form action="index.php?controller=post&action=create&id_forum=<?= $forum->getIdForum() ?>" method="POST" data-validate="true">

            <div class="form-group">
                <label for="post-contenu">
                    Contenu du post <span class="required">*</span>
                </label>
                <textarea
                    id="post-contenu"
                    name="contenu"
                    class="form-control"
                    rows="8"
                    placeholder="Partagez vos connaissances, posez une question ou lancez une discussion..."
                    data-required="true"
                    data-min="10"
                    data-max="5000"
                    data-label="Le contenu"
                ><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center;">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane"></i> Publier
                </button>
                <a href="index.php?controller=forum&action=show&id=<?= $forum->getIdForum() ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../../layout/front_footer.php'; ?>
