<?php
$pageTitle = 'Modifier le Forum';
require __DIR__ . '/../../layout/back_header.php';
?>

<!-- Page Header -->
<div class="admin-page-header">
    <div>
        <h2><i class="fas fa-edit" style="color: var(--admin-accent);"></i> Modifier le Forum</h2>
        <p>Modifiez les informations du forum « <?= htmlspecialchars($forum->getTitre()) ?> »</p>
    </div>
    <a href="index.php?controller=forum&action=adminList" class="admin-btn admin-btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<!-- Alerts -->
<?php if (!empty($errors)): ?>
    <div class="admin-alert admin-alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="admin-alert admin-alert-success">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<!-- Form -->
<div class="admin-form-card">
    <form action="index.php?controller=forum&action=edit&id=<?= $forum->getIdForum() ?>" method="POST" data-validate="true">

        <div class="form-group">
            <label for="forum-titre">
                Titre du forum <span class="required">*</span>
            </label>
            <input
                type="text"
                id="forum-titre"
                name="titre"
                class="form-control"
                value="<?= htmlspecialchars($forum->getTitre()) ?>"
                data-required="true"
                data-min="3"
                data-max="200"
                data-label="Le titre"
            >
        </div>

        <div class="form-group">
            <label for="forum-description">Description</label>
            <textarea
                id="forum-description"
                name="description"
                class="form-control"
                rows="4"
                data-max="1000"
                data-label="La description"
            ><?= htmlspecialchars($forum->getDescription() ?? '') ?></textarea>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="submit" class="admin-btn admin-btn-primary">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
            <a href="index.php?controller=forum&action=adminList" class="admin-btn admin-btn-secondary">
                Annuler
            </a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../../layout/back_footer.php'; ?>
