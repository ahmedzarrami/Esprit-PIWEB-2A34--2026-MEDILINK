<form method="POST" class="med-form" id="medicament-form" novalidate>
    <?php if (!empty($formMessage ?? null)): ?>
        <div class="form-message form-message-error">
            <?= e((string) $formMessage) ?>
        </div>
    <?php endif; ?>

    <div class="form-grid">
        <div class="field-group">
            <label for="nom">Nom du médicament</label>
            <input id="nom" name="nom" type="text" value="<?= e((string) ($data['nom'] ?? '')) ?>" data-rule="nom">
            <small class="error"><?= e($errors['nom'] ?? '') ?></small>
        </div>

        <div class="field-group">
            <label for="dosage">Dosage</label>
            <input id="dosage" name="dosage" type="text" value="<?= e((string) ($data['dosage'] ?? '')) ?>" placeholder="Ex: 500 mg" data-rule="dosage">
            <small class="error"><?= e($errors['dosage'] ?? '') ?></small>
        </div>

        <div class="field-group">
            <label for="forme">Forme</label>
            <input id="forme" name="forme" type="text" value="<?= e((string) ($data['forme'] ?? '')) ?>" placeholder="Ex: Comprimé, Sirop" data-rule="forme">
            <small class="error"><?= e($errors['forme'] ?? '') ?></small>
        </div>

        <div class="field-group">
            <label for="fabricant">Fabricant</label>
            <input id="fabricant" name="fabricant" type="text" value="<?= e((string) ($data['fabricant'] ?? '')) ?>" data-rule="fabricant">
            <small class="error"><?= e($errors['fabricant'] ?? '') ?></small>
        </div>

        <div class="field-group">
            <label for="prix">Prix</label>
            <input id="prix" name="prix" type="text" value="<?= e((string) ($data['prix'] ?? '')) ?>" placeholder="Ex: 12.50" data-rule="prix">
            <small class="error"><?= e($errors['prix'] ?? '') ?></small>
        </div>

        <div class="field-group">
            <label for="stock">Stock</label>
            <input id="stock" name="stock" type="text" value="<?= e((string) ($data['stock'] ?? '')) ?>" data-rule="stock">
            <small class="error"><?= e($errors['stock'] ?? '') ?></small>
        </div>

        <div class="field-group full-width">
            <label for="description">Description du médicament</label>
            <textarea id="description" name="description" rows="5" placeholder="Décrire brièvement l’utilisation ou les caractéristiques du médicament." data-rule="description"><?= e((string) ($data['description'] ?? '')) ?></textarea>
            <small class="error"><?= e($errors['description'] ?? '') ?></small>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="button primary">
            <?= (($_GET['action'] ?? '') === 'edit') ? 'Mettre à jour' : 'Enregistrer' ?>
        </button>
        <a href="index.php?action=index" class="button secondary">Annuler</a>
    </div>
</form>

<script src="js/validation.js"></script>
