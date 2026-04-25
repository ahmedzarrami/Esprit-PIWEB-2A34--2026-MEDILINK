<?php
$pageTitle = 'Modifier le Post';
require __DIR__ . '/../../layout/back_header.php';
?>

<!-- Page Header -->
<div class="admin-page-header">
    <div>
        <h2><i class="fas fa-edit" style="color: var(--admin-blue);"></i> Modifier le Post</h2>
        <p>Post #<?= $post['id_post'] ?> par <?= htmlspecialchars($post['auteur_prenom'] . ' ' . $post['auteur_nom']) ?></p>
    </div>
    <a href="index.php?controller=post&action=adminList" class="admin-btn admin-btn-secondary">
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

<!-- Post Info -->
<div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <div class="stat-card" style="flex: 1; min-width: 200px;">
        <div class="stat-icon blue"><i class="fas fa-user"></i></div>
        <div class="stat-info">
            <h3 style="font-size: 1rem;"><?= htmlspecialchars($post['auteur_prenom'] . ' ' . $post['auteur_nom']) ?></h3>
            <p>Auteur (<?= htmlspecialchars($post['auteur_role']) ?>)</p>
        </div>
    </div>
    <div class="stat-card" style="flex: 1; min-width: 200px;">
        <div class="stat-icon green"><i class="fas fa-calendar"></i></div>
        <div class="stat-info">
            <h3 style="font-size: 1rem;"><?= date('d/m/Y H:i', strtotime($post['date_publication'])) ?></h3>
            <p>Date de publication</p>
        </div>
    </div>
</div>

<!-- Form -->
<div class="admin-form-card">
    <form action="index.php?controller=post&action=edit&id=<?= $post['id_post'] ?>" method="POST" data-validate="true">

        <div class="form-group">
            <label for="post-contenu">
                Contenu du post <span class="required">*</span>
            </label>
            <textarea
                id="post-contenu"
                name="contenu"
                class="form-control"
                rows="8"
                data-required="true"
                data-min="10"
                data-max="5000"
                data-label="Le contenu"
            ><?= htmlspecialchars($post['contenu']) ?></textarea>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="submit" class="admin-btn admin-btn-primary">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <button type="button" class="admin-btn admin-btn-danger"
                onclick="confirmDelete('index.php?controller=post&action=delete&id=<?= $post['id_post'] ?>', 'ce post')">
                <i class="fas fa-trash-alt"></i> Supprimer
            </button>
            <a href="index.php?controller=post&action=adminList" class="admin-btn admin-btn-secondary">
                Annuler
            </a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../../layout/back_footer.php'; ?>
