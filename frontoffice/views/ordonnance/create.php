<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="listing-hero section-block">
    <h1>Nouvelle ordonnance</h1>
    <p>Remplissez les informations du médecin et du patient, puis ajoutez les médicaments prescrits.</p>
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

    <form method="POST" action="index.php?action=store_ordonnance" id="ordonnance-form">

        <!-- Patient -->
        <div class="detail-card form-section">
            <h2 class="form-section-title">Informations du patient</h2>
            <div class="form-grid form-grid-3">
                <div class="form-group">
                    <label for="patient_nom">Nom du patient <span class="required">*</span></label>
                    <input
                        type="text"
                        id="patient_nom"
                        name="patient_nom"
                        placeholder="Fatma Trabelsi"
                        value="<?= htmlspecialchars($old['patient_nom'] ?? '') ?>"
                        required
                    >
                </div>
                <div class="form-group">
                    <label for="patient_age">Âge</label>
                    <input
                        type="number"
                        id="patient_age"
                        name="patient_age"
                        placeholder="35"
                        min="0"
                        max="130"
                        value="<?= htmlspecialchars($old['patient_age'] ?? '') ?>"
                    >
                </div>
                <div class="form-group">
                    <label for="patient_sexe">Sexe</label>
                    <select id="patient_sexe" name="patient_sexe">
                        <option value="">-- Non précisé --</option>
                        <option value="M" <?= ($old['patient_sexe'] ?? '') === 'M' ? 'selected' : '' ?>>Masculin</option>
                        <option value="F" <?= ($old['patient_sexe'] ?? '') === 'F' ? 'selected' : '' ?>>Féminin</option>
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
                    <input
                        type="date"
                        id="date_ordonnance"
                        name="date_ordonnance"
                        value="<?= htmlspecialchars($old['date_ordonnance'] ?? date('Y-m-d')) ?>"
                        required
                    >
                </div>
                <div class="form-group">
                    <label for="notes">Notes / Observations</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Instructions particulières…"><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Médicaments -->
        <div class="detail-card form-section">
            <h2 class="form-section-title">Médicaments prescrits <span class="required">*</span></h2>

            <!-- Barre de recherche principale -->
            <div class="med-add-bar">
                <div class="med-autocomplete" id="med-add-wrap">
                    <input type="text" id="med-add-input" class="med-search-input"
                        placeholder="🔍  Rechercher un médicament par nom, ID ou description…"
                        autocomplete="off">
                    <div class="med-dropdown" id="med-add-dropdown"></div>
                </div>
                <p class="med-add-hint">Sélectionnez un médicament dans la liste pour l'ajouter à l'ordonnance.</p>
            </div>

            <!-- Liste des médicaments ajoutés -->
            <div id="lignes-container">
                <?php
                $oldLignes = $old['lignes'] ?? [];
                foreach ($oldLignes as $idx => $ligne):
                    $selectedId  = (int)($ligne['medicament_id'] ?? 0);
                    $selectedNom = ''; $selectedDosage = '';
                    foreach ($medicaments as $med) {
                        if ($med['id'] === $selectedId) {
                            $selectedNom    = $med['nom'];
                            $selectedDosage = $med['dosage'] ?? '';
                            break;
                        }
                    }
                ?>
                <div class="ligne-card" data-index="<?= $idx ?>">
                    <div class="ligne-card-header">
                        <div class="ligne-med-badge">
                            <span class="ligne-med-icon">💊</span>
                            <span class="ligne-med-nom"><?= htmlspecialchars($selectedNom) ?><?= $selectedDosage ? ' <small>('.$selectedDosage.')</small>' : '' ?></span>
                        </div>
                        <button type="button" class="btn-remove-ligne" title="Retirer">✕</button>
                    </div>
                    <input type="hidden" name="lignes[<?= $idx ?>][medicament_id]" class="med-hidden-id" value="<?= $selectedId ?>">
                    <div class="ligne-card-fields">
                        <div class="form-group">
                            <label>Posologie <span class="required">*</span></label>
                            <input type="text" name="lignes[<?= $idx ?>][posologie]"
                                placeholder="Ex : 1 comprimé matin et soir"
                                value="<?= htmlspecialchars($ligne['posologie'] ?? '') ?>">
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
                                min="1" value="<?= (int)($ligne['quantite'] ?? 1) ?: 1 ?>">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div id="lignes-empty" style="<?= empty($oldLignes) ? '' : 'display:none' ?>;text-align:center;padding:32px 20px;color:#94a3b8;font-size:13px;">
                Aucun médicament ajouté — utilisez la recherche ci-dessus.
            </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <a href="index.php?action=ordonnances" class="btn-cancel">Annuler</a>
            <button type="submit" class="header-btn">Enregistrer l'ordonnance</button>
        </div>

    </form>
</div>

<script>
var MEDICAMENTS_DATA = <?= json_encode(array_values($medicaments), JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="js/ordonnance-form.js"></script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
