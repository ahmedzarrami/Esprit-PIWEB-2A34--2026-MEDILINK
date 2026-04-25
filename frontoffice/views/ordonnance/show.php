<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="section-block" style="padding-top:28px;">
    <a href="index.php?action=ordonnances" class="back-link">← Retour aux ordonnances</a>
</div>

<div class="section-block">

    <?php if ($success === 'created'): ?>
        <div class="alert-success">Ordonnance créée avec succès.</div>
    <?php elseif ($success === 'updated'): ?>
        <div class="alert-success">Ordonnance modifiée avec succès.</div>
    <?php endif; ?>

    <!-- En-tête ordonnance -->
    <div class="detail-card" style="margin-bottom:20px;">
        <div class="detail-header">
            <div>
                <span class="pill large"><?= htmlspecialchars($ordonnance['numero']) ?></span>
                <h1 style="font-size:26px;margin-bottom:6px;">Ordonnance médicale</h1>
                <p>Date : <strong><?= htmlspecialchars(date('d/m/Y', strtotime($ordonnance['date_ordonnance']))) ?></strong></p>
                <p style="font-size:12px;color:#94a3b8;margin-top:6px;">
                    Créée le <?= date('d/m/Y à H:i', strtotime($ordonnance['created_at'])) ?>
                    <?php if (!empty($ordonnance['updated_at'])): ?>
                        &nbsp;·&nbsp; <span style="color:#0da271;">Modifiée le <?= date('d/m/Y à H:i', strtotime($ordonnance['updated_at'])) ?></span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="ord-show-actions">
                <a href="index.php?action=edit_ordonnance&id=<?= $ordonnance['id'] ?>" class="btn-edit-lg">Modifier</a>
                <a href="index.php?action=print_ordonnance&id=<?= $ordonnance['id'] ?>" target="_blank" class="btn-print-lg">Imprimer</a>
                <form method="POST" action="index.php?action=delete_ordonnance" onsubmit="return confirm('Supprimer cette ordonnance définitivement ?')">
                    <input type="hidden" name="id" value="<?= $ordonnance['id'] ?>">
                    <button type="submit" class="btn-danger">Supprimer</button>
                </form>
            </div>
        </div>

        <div>
            <h3>Patient</h3>
            <ul class="detail-list">
                <li><strong><?= htmlspecialchars($ordonnance['patient_nom']) ?></strong></li>
                <?php if ($ordonnance['patient_age']): ?>
                    <li class="meta">Âge : <?= (int) $ordonnance['patient_age'] ?> ans</li>
                <?php endif; ?>
                <?php if ($ordonnance['patient_sexe']): ?>
                    <li class="meta">Sexe : <?= $ordonnance['patient_sexe'] === 'M' ? 'Masculin' : 'Féminin' ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Médicaments -->
    <div class="detail-card" style="margin-bottom:20px;">
        <h3 style="margin-bottom:18px;color:#345f84;">Médicaments prescrits</h3>
        <?php if (empty($lignes)): ?>
            <p class="meta">Aucun médicament enregistré.</p>
        <?php else: ?>
            <div class="ord-table-wrap">
                <table class="ord-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Médicament</th>
                            <th>Forme / Dosage</th>
                            <th>Posologie</th>
                            <th>Durée</th>
                            <th>Quantité</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lignes as $i => $ligne): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= htmlspecialchars($ligne['medicament_nom']) ?></strong></td>
                                <td>
                                    <?= htmlspecialchars($ligne['forme'] ?? '') ?>
                                    <?php if ($ligne['dosage']): ?>
                                        <span class="meta"> · <?= htmlspecialchars($ligne['dosage']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($ligne['posologie']) ?></td>
                                <td><?= htmlspecialchars($ligne['duree'] ?? '—') ?></td>
                                <td><?= (int) $ligne['quantite'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Notes -->
    <?php if ($ordonnance['notes']): ?>
        <div class="detail-card">
            <h3 style="margin-bottom:12px;color:#345f84;">Notes / Observations</h3>
            <p style="line-height:1.7;"><?= nl2br(htmlspecialchars($ordonnance['notes'])) ?></p>
        </div>
    <?php endif; ?>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
