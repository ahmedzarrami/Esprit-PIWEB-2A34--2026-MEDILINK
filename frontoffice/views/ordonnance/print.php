<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordonnance <?= htmlspecialchars($ordonnance['numero']) ?> - MediLink</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Georgia', serif; }
        body { background: #fff; color: #1a1a2e; padding: 40px; max-width: 800px; margin: 0 auto; }

        .print-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #2c73ae; padding-bottom: 20px; margin-bottom: 28px; }
        .brand { font-size: 22px; font-weight: 700; color: #2c73ae; }
        .brand span { color: #4ec2cf; }
        .ord-numero { font-size: 13px; color: #666; text-align: right; }
        .ord-numero strong { display: block; font-size: 16px; color: #2c73ae; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; }
        .info-box { border: 1px solid #d7e4f0; border-radius: 8px; padding: 16px; }
        .info-box h3 { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 8px; }
        .info-box p { font-size: 15px; color: #1a1a2e; line-height: 1.6; }
        .info-box .sub { font-size: 13px; color: #666; }

        .meds-title { font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: #2c73ae; margin-bottom: 14px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 24px; }
        thead th { background: #2c73ae; color: #fff; padding: 10px 12px; text-align: left; font-size: 12px; text-transform: uppercase; }
        tbody tr:nth-child(even) { background: #f4f8fc; }
        tbody td { padding: 10px 12px; border-bottom: 1px solid #e2ecf5; }

        .notes-box { border: 1px solid #d7e4f0; border-radius: 8px; padding: 16px; margin-bottom: 36px; }
        .notes-box h3 { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 8px; }

        .print-footer { border-top: 2px solid #2c73ae; padding-top: 20px; display: flex; justify-content: space-between; align-items: flex-end; }
        .signature-box { text-align: center; }
        .signature-line { border-top: 1px solid #333; width: 200px; margin: 50px auto 8px; }
        .signature-label { font-size: 12px; color: #666; }
        .footer-brand { font-size: 12px; color: #999; }

        .no-print { margin-bottom: 20px; }
        .btn-print { background: #2c73ae; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 14px; margin-right: 10px; }
        .btn-close { background: #eee; color: #333; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 14px; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 20px; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button class="btn-print" onclick="window.print()">Imprimer</button>
    <button class="btn-close" onclick="window.close()">Fermer</button>
</div>

<div class="print-header">
    <div>
        <div class="brand">Medi<span>Link</span></div>
        <div style="font-size:12px;color:#666;margin-top:4px;">Système de gestion médicale</div>
    </div>
    <div class="ord-numero">
        <strong><?= htmlspecialchars($ordonnance['numero']) ?></strong>
        Date : <?= htmlspecialchars(date('d/m/Y', strtotime($ordonnance['date_ordonnance']))) ?>
    </div>
</div>

<div class="info-grid" style="grid-template-columns:1fr;">
    <div class="info-box">
        <h3>Patient</h3>
        <p><?= htmlspecialchars($ordonnance['patient_nom']) ?></p>
        <p class="sub">
            <?php if ($ordonnance['patient_age']): ?>Âge : <?= (int) $ordonnance['patient_age'] ?> ans<?php endif; ?>
            <?php if ($ordonnance['patient_sexe']): ?> · <?= $ordonnance['patient_sexe'] === 'M' ? 'Masculin' : 'Féminin' ?><?php endif; ?>
        </p>
    </div>
</div>

<div class="meds-title">Médicaments prescrits</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Médicament</th>
            <th>Forme / Dosage</th>
            <th>Posologie</th>
            <th>Durée</th>
            <th>Qté</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lignes as $i => $ligne): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($ligne['medicament_nom']) ?></td>
                <td><?= htmlspecialchars(($ligne['forme'] ?? '') . ($ligne['dosage'] ? ' · ' . $ligne['dosage'] : '')) ?></td>
                <td><?= htmlspecialchars($ligne['posologie']) ?></td>
                <td><?= htmlspecialchars($ligne['duree'] ?? '—') ?></td>
                <td><?= (int) $ligne['quantite'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($ordonnance['notes']): ?>
    <div class="notes-box">
        <h3>Notes / Observations</h3>
        <p style="line-height:1.7;font-size:14px;"><?= nl2br(htmlspecialchars($ordonnance['notes'])) ?></p>
    </div>
<?php endif; ?>

<div class="print-footer">
    <div>
        <p style="font-size:13px;color:#666;">Émis le <?= htmlspecialchars(date('d/m/Y', strtotime($ordonnance['created_at']))) ?></p>
    </div>
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="signature-label">Signature et cachet du médecin</div>
    </div>
</div>

</body>
</html>
