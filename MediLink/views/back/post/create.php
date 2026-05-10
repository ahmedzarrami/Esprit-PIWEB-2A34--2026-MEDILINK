<?php
$pageTitle = 'Créer un Post';
require __DIR__ . '/../../layout/back_header.php';
?>

<!-- Page Header -->
<div class="admin-page-header">
    <div>
        <h2><i class="fas fa-plus-circle" style="color: var(--admin-blue);"></i> Créer un Nouveau Post</h2>
        <p>Ajouter une publication dans un forum existant.</p>
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

<!-- Form -->
<div class="admin-form-card">
    <form action="index.php?controller=post&action=adminCreate" method="POST" data-validate="true">

        <div class="form-group">
            <label for="post-forum">
                Forum cible <span class="required">*</span>
            </label>
            <select id="post-forum" name="id_forum" class="form-control" data-required="true" data-label="Le forum">
                <option value="">-- Sélectionner un forum --</option>
                <?php foreach ($forums as $f): ?>
                    <option value="<?= $f['id_forum'] ?>" <?= (($_POST['id_forum'] ?? '') == $f['id_forum']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($f['titre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="post-auteur">
                Auteur du post <span class="required">*</span>
            </label>
            <select id="post-auteur" name="id_auteur" class="form-control" data-required="true" data-label="L'auteur">
                <option value="">-- Sélectionner un auteur --</option>
                <?php foreach ($utilisateurs as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= (($_POST['id_auteur'] ?? '') == $u['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?> (<?= htmlspecialchars($u['role']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

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
            ><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="submit" class="admin-btn admin-btn-primary">
                <i class="fas fa-paper-plane"></i> Publier le post
            </button>
            <a href="index.php?controller=post&action=adminList" class="admin-btn admin-btn-secondary">
                Annuler
            </a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../../layout/back_footer.php'; ?>
