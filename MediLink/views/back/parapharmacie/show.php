<?php
$catIcons = [
    'Soins visage' => '🧴', 'Soins corps' => '🫧', 'Hygiène' => '🪥',
    'Compléments alimentaires' => '💊', 'Bébé & Maman' => '🍼',
    'Capillaire' => '💆', 'Solaire' => '☀️', 'Minceur' => '⚖️',
    'Orthopédie' => '🦴', 'Autre' => '📦'
];
$stockVal   = (int) $produit['stock'];
$stockClass = $stockVal === 0 ? 'stock-out' : ($stockVal <= 5 ? 'stock-low' : 'stock-ok');
$stockLabel = $stockVal === 0 ? '⛔ Rupture de stock' : ($stockVal <= 5 ? '⚠ Stock faible (' . $stockVal . ')' : '✓ En stock (' . $stockVal . ')');
$stockColor = $stockVal === 0 ? '#dc2626' : ($stockVal <= 5 ? '#d97706' : '#059669');
?>

<section class="panel" style="max-width:700px">
    <div class="panel-header between">
        <h2>Détail du produit</h2>
        <div style="display:flex;gap:8px">
            <a class="button secondary" href="parapharmacie.php?action=index">← Retour</a>
            <a class="button primary" href="parapharmacie.php?action=edit&id=<?= e((string) $produit['id']) ?>">✏ Modifier</a>
        </div>
    </div>

    <div style="padding:28px;display:grid;grid-template-columns:140px 1fr;gap:16px 24px;font-size:14px">
        <div style="font-weight:700;color:#64748b">ID</div>
        <div style="font-weight:600">#<?= e((string) $produit['id']) ?></div>

        <div style="font-weight:700;color:#64748b">Référence</div>
        <div><code style="background:rgba(37,99,235,.08);padding:3px 10px;border-radius:6px;font-size:13px;color:#2563eb;font-weight:600"><?= e($produit['reference']) ?></code></div>

        <div style="font-weight:700;color:#64748b">Nom</div>
        <div style="font-weight:700;color:#0f172a;font-size:16px"><?= e($produit['nom']) ?></div>

        <div style="font-weight:700;color:#64748b">Catégorie</div>
        <div>
            <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(139,92,246,.08);padding:4px 12px;border-radius:8px;font-size:13px;font-weight:600;color:#7c3aed">
                <?= $catIcons[$produit['categorie']] ?? '📦' ?> <?= e($produit['categorie']) ?>
            </span>
        </div>

        <div style="font-weight:700;color:#64748b">Description</div>
        <div style="color:#475569;line-height:1.6"><?= e($produit['description'] ?? 'Aucune description.') ?></div>

        <div style="font-weight:700;color:#64748b">Prix</div>
        <div style="font-weight:800;color:#0f172a;font-size:18px"><?= e(number_format((float) $produit['prix'], 3, ',', ' ')) ?> <span style="font-size:13px;font-weight:500;color:#64748b">DT</span></div>

        <div style="font-weight:700;color:#64748b">Stock</div>
        <div style="font-weight:700;color:<?= $stockColor ?>"><?= $stockLabel ?></div>

        <div style="font-weight:700;color:#64748b">Ajouté le</div>
        <div style="color:#475569"><?= e($produit['created_at'] ?? '—') ?></div>
    </div>
</section>
