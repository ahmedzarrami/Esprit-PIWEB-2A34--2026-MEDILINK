<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="listing-hero section-block">
    <h1>Modifier l'ordonnance <span class="pill"><?= htmlspecialchars($ordonnance['numero']) ?></span></h1>
    <p>Modifiez les informations du patient et les médicaments prescrits.</p>
</div>

<div class="section-block">

    <?php if (!empty($errors)): ?>
        <div class="alert-errors">
            <strong>Veuillez corriger les erreurs suivantes :</strong>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=update_ordonnance" id="ordonnance-form">
        <input type="hidden" name="id" value="<?= (int) $ordonnance['id'] ?>">

        <!-- Patient -->
        <div class="detail-card form-section">
            <h2 class="form-section-title">Informations du patient</h2>
            <div class="form-grid form-grid-3">
                <div class="form-group">
                    <label for="patient_nom">Nom du patient <span class="required">*</span></label>
                    <input type="text" id="patient_nom" name="patient_nom"
                        value="<?= htmlspecialchars($ordonnance['patient_nom']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="patient_age">Âge</label>
                    <input type="number" id="patient_age" name="patient_age"
                        min="0" max="130"
                        value="<?= htmlspecialchars((string)($ordonnance['patient_age'] ?? '')) ?>">
                </div>
                <div class="form-group">
                    <label for="patient_sexe">Sexe</label>
                    <select id="patient_sexe" name="patient_sexe">
                        <option value="">-- Non précisé --</option>
                        <option value="M" <?= $ordonnance['patient_sexe'] === 'M' ? 'selected' : '' ?>>Masculin</option>
                        <option value="F" <?= $ordonnance['patient_sexe'] === 'F' ? 'selected' : '' ?>>Féminin</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Ordonnance -->
        <div class="detail-card form-section">
            <h2 class="form-section-title">Détails de l'ordonnance</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="date_ordonnance">Date <span class="required">*</span></label>
                    <input type="date" id="date_ordonnance" name="date_ordonnance"
                        value="<?= htmlspecialchars($ordonnance['date_ordonnance']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="notes">Notes / Observations</label>
                    <textarea id="notes" name="notes" rows="3"><?= htmlspecialchars((string)($ordonnance['notes'] ?? '')) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Médicaments -->
        <div class="detail-card form-section">
            <h2 class="form-section-title">Médicaments prescrits <span class="required">*</span></h2>

            <div class="med-add-bar">
                <div class="med-autocomplete" id="med-add-wrap">
                    <input type="text" id="med-add-input" class="med-search-input"
                        placeholder="🔍  Rechercher un médicament par nom, ID ou description…"
                        autocomplete="off">
                    <div class="med-dropdown" id="med-add-dropdown"></div>
                </div>
                <p class="med-add-hint">Sélectionnez un médicament dans la liste pour l'ajouter à l'ordonnance.</p>
            </div>

            <div id="lignes-container">
                <?php foreach ($lignes as $idx => $ligne): ?>
                <div class="ligne-card" data-index="<?= $idx ?>">
                    <div class="ligne-card-header">
                        <div class="ligne-med-badge">
                            <span class="ligne-med-icon">💊</span>
                            <span class="ligne-med-nom"><?= htmlspecialchars($ligne['medicament_nom']) ?><?= $ligne['dosage'] ? ' <small>('.htmlspecialchars($ligne['dosage']).')</small>' : '' ?></span>
                        </div>
                        <button type="button" class="btn-remove-ligne" title="Retirer">✕</button>
                    </div>
                    <input type="hidden" name="lignes[<?= $idx ?>][medicament_id]" class="med-hidden-id" value="<?= (int)$ligne['medicament_id'] ?>">
                    <div class="ligne-card-fields">
                        <div class="form-group">
                            <label>Posologie <span class="required">*</span></label>
                            <input type="text" name="lignes[<?= $idx ?>][posologie]"
                                placeholder="Ex : 1 comprimé matin et soir"
                                value="<?= htmlspecialchars($ligne['posologie']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Durée</label>
                            <input type="text" name="lignes[<?= $idx ?>][duree]"
                                placeholder="Ex : 7 jours"
                                value="<?= htmlspecialchars($ligne['duree'] ?? '') ?>">
                        </div>
                        <div class="form-group ligne-qte">
                            <label>Qté</label>
                            <input type="number" name="lignes[<?= $idx ?>][quantite]"
                                min="1" value="<?= (int)($ligne['quantite'] ?? 1) ?>">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div id="lignes-empty" style="display:none;text-align:center;padding:32px 20px;color:#94a3b8;font-size:13px;">
                Aucun médicament ajouté — utilisez la recherche ci-dessus.
            </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <a href="index.php?action=show_ordonnance&id=<?= (int) $ordonnance['id'] ?>" class="btn-cancel">Annuler</a>
            <button type="submit" class="header-btn">Enregistrer les modifications</button>
        </div>

    </form>
</div>

<script>
var MEDICAMENTS_DATA = <?= json_encode(array_values($medicaments), JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="js/ordonnance-form.js"></script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
