<?php
/**
 * Formulaire partagé : Ajout / Modification produit parapharmacie
 * Variables attendues : $data, $errors, $formMessage, $categories, $isEdit (bool)
 */
$isEdit = $isEdit ?? false;
$catIcons = [
    'Soins visage' => '🧴', 'Soins corps' => '🫧', 'Hygiène' => '🪥',
    'Compléments alimentaires' => '💊', 'Bébé & Maman' => '🍼',
    'Capillaire' => '💆', 'Solaire' => '☀️', 'Minceur' => '⚖️',
    'Orthopédie' => '🦴', 'Autre' => '📦'
];
?>

<?php if ($formMessage): ?>
    <div class="flash flash-error" style="margin-bottom:20px"><?= e($formMessage) ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel-header between">
        <h2><?= $isEdit ? 'Modifier le produit' : 'Ajouter un nouveau produit' ?></h2>
        <a class="button secondary" href="parapharmacie.php?action=index">← Retour à la liste</a>
    </div>

    <form method="POST" class="form-grid" novalidate enctype="multipart/form-data" style="padding:24px;display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <!-- Référence -->
        <div class="form-group">
            <label for="reference">Référence *</label>
            <input type="text" id="reference" name="reference" value="<?= e($data['reference'] ?? '') ?>"
                   placeholder="Ex: REF-SV-001" class="<?= isset($errors['reference']) ? 'error' : '' ?>">
            <?php if (isset($errors['reference'])): ?>
                <small class="field-error"><?= e($errors['reference']) ?></small>
            <?php endif; ?>
        </div>

        <!-- Nom -->
        <div class="form-group">
            <label for="nom">Nom du produit *</label>
            <input type="text" id="nom" name="nom" value="<?= e($data['nom'] ?? '') ?>"
                   placeholder="Ex: Crème Hydratante Visage" class="<?= isset($errors['nom']) ? 'error' : '' ?>">
            <?php if (isset($errors['nom'])): ?>
                <small class="field-error"><?= e($errors['nom']) ?></small>
            <?php endif; ?>
        </div>

        <!-- Catégorie -->
        <div class="form-group">
            <label for="categorie">Catégorie *</label>
            <select id="categorie" name="categorie" class="select-field <?= isset($errors['categorie']) ? 'error' : '' ?>">
                <option value="">— Choisir —</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat) ?>" <?= ($data['categorie'] ?? '') === $cat ? 'selected' : '' ?>>
                        <?= $catIcons[$cat] ?? '📦' ?> <?= e($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['categorie'])): ?>
                <small class="field-error"><?= e($errors['categorie']) ?></small>
            <?php endif; ?>
        </div>

        <!-- Prix -->
        <div class="form-group">
            <label for="prix">Prix (DT) *</label>
            <input type="text" id="prix" name="prix" value="<?= e($data['prix'] ?? '') ?>"
                   placeholder="Ex: 25.900" class="<?= isset($errors['prix']) ? 'error' : '' ?>">
            <?php if (isset($errors['prix'])): ?>
                <small class="field-error"><?= e($errors['prix']) ?></small>
            <?php endif; ?>
        </div>

        <!-- Stock -->
        <div class="form-group">
            <label for="stock">Stock *</label>
            <input type="number" id="stock" name="stock" value="<?= e($data['stock'] ?? '0') ?>"
                   min="0" class="<?= isset($errors['stock']) ? 'error' : '' ?>">
            <?php if (isset($errors['stock'])): ?>
                <small class="field-error"><?= e($errors['stock']) ?></small>
            <?php endif; ?>
        </div>

        <!-- Image (pleine largeur) -->
        <div class="form-group" style="grid-column:1/-1">
            <label for="image">Image du produit <?= $isEdit ? '(laisser vide pour conserver l\'actuelle)' : '' ?></label>
            <?php if ($isEdit && !empty($data['image_path'])): ?>
                <div style="margin-bottom:10px;display:flex;align-items:center;gap:12px">
                    <img src="/medilink_medicament/MediLink/public/img/parapharmacie/<?= htmlspecialchars($data['image_path']) ?>"
                         alt="Image actuelle" style="width:80px;height:80px;object-fit:cover;border-radius:10px;border:1px solid #e2e8f0">
                    <span style="font-size:12px;color:#64748b">Image actuelle</span>
                </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                   class="<?= isset($errors['image']) ? 'error' : '' ?>"
                   style="padding:8px 12px">
            <small style="color:#64748b;font-size:12px">JPG, PNG ou WebP — max 2 Mo</small>
            <?php if (isset($errors['image'])): ?>
                <small class="field-error"><?= e($errors['image']) ?></small>
            <?php endif; ?>
        </div>

        <!-- Description (pleine largeur) -->
        <div class="form-group" style="grid-column:1/-1">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"
                      placeholder="Description du produit (optionnel, max 500 caractères)"
                      class="<?= isset($errors['description']) ? 'error' : '' ?>"><?= e($data['description'] ?? '') ?></textarea>
            <?php if (isset($errors['description'])): ?>
                <small class="field-error"><?= e($errors['description']) ?></small>
            <?php endif; ?>
        </div>

        <!-- Bouton submit -->
        <div style="grid-column:1/-1;display:flex;gap:12px;justify-content:flex-end;padding-top:12px;border-top:1px solid rgba(0,0,0,.06)">
            <a href="parapharmacie.php?action=index" class="button secondary">Annuler</a>
            <button type="submit" class="button primary"><?= $isEdit ? '💾 Enregistrer les modifications' : '➕ Ajouter le produit' ?></button>
        </div>
    </form>
</section>

<style>
.form-grid .form-group { display:flex; flex-direction:column; gap:6px; }
.form-grid label { font-size:13px; font-weight:700; color:#334155; letter-spacing:.02em; }
.form-grid input, .form-grid textarea, .form-grid select {
    padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px;
    transition:all .2s ease; background:#fff; color:#0f172a; font-family:inherit;
}
.form-grid input:focus, .form-grid textarea:focus, .form-grid select:focus {
    outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1);
}
.form-grid input.error, .form-grid textarea.error, .form-grid select.error {
    border-color:#dc2626; box-shadow:0 0 0 3px rgba(220,38,38,.08);
}
.field-error { color:#dc2626; font-size:12px; font-weight:600; }
</style>
