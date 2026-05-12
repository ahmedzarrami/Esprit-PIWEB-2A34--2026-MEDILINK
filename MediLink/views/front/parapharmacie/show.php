<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$catIcons = [
    'Soins visage'             => '🧴',
    'Soins corps'              => '🫧',
    'Hygiène'                  => '🪥',
    'Compléments alimentaires' => '💊',
    'Bébé & Maman'             => '🍼',
    'Capillaire'               => '💆',
    'Solaire'                  => '☀️',
    'Minceur'                  => '⚖️',
    'Orthopédie'               => '🦴',
    'Autre'                    => '📦',
];
$stock      = (int) $produit['stock'];
$stockLabel = $stock === 0 ? '⛔ Rupture de stock' : ($stock <= 5 ? '⚠ Stock faible (' . $stock . ')' : '✓ En stock (' . $stock . ')');
$stockColor = $stock === 0 ? '#dc2626' : ($stock <= 5 ? '#d97706' : '#059669');
?>

<section class="section-block narrow">
    <a class="back-link" href="index.php?action=parapharmacie">← Retour au catalogue</a>

    <div class="detail-card">
        <?php if (!empty($produit['image_path'])): ?>
        <div style="width:calc(100% + 48px);margin:-24px -24px 24px;overflow:hidden;border-radius:16px 16px 0 0;max-height:320px">
            <img src="/medilink_medicament/MediLink/public/img/parapharmacie/<?= htmlspecialchars($produit['image_path']) ?>"
                 alt="<?= htmlspecialchars($produit['nom']) ?>"
                 style="width:100%;height:320px;object-fit:cover;display:block">
        </div>
        <?php endif; ?>
        <div class="detail-header">
            <div>
                <span class="pill large">
                    <?= $catIcons[$produit['categorie']] ?? '📦' ?>
                    <?= htmlspecialchars($produit['categorie']) ?>
                </span>
                <h1><?= htmlspecialchars($produit['nom']) ?></h1>
                <p style="color:#64748b;font-size:14px;margin-top:6px">Réf. <strong><?= htmlspecialchars($produit['reference']) ?></strong></p>
            </div>
            <div class="detail-price"><?= number_format((float) $produit['prix'], 3, ',', ' ') ?> <small style="font-size:14px;font-weight:600;color:#64748b">DT</small></div>
        </div>

        <div class="detail-grid">
            <div>
                <h3>Description</h3>
                <?php if (!empty($produit['description'])): ?>
                    <p><?= nl2br(htmlspecialchars($produit['description'])) ?></p>
                <?php else: ?>
                    <p style="color:#94a3b8;font-style:italic">Aucune description disponible.</p>
                <?php endif; ?>
            </div>
            <div>
                <h3>Informations</h3>
                <ul class="detail-list">
                    <li><strong>Catégorie :</strong> <?= ($catIcons[$produit['categorie']] ?? '📦') . ' ' . htmlspecialchars($produit['categorie']) ?></li>
                    <li><strong>Référence :</strong> <?= htmlspecialchars($produit['reference']) ?></li>
                    <li><strong>Disponibilité :</strong> <span style="color:<?= $stockColor ?>;font-weight:600"><?= $stockLabel ?></span></li>
                    <li><strong>Date d'ajout :</strong> <?= htmlspecialchars($produit['created_at'] ?? '—') ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- RATINGS -->
<section class="section-block narrow" style="margin-top:0">
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:28px">
    <h3 style="font-size:18px;font-weight:800;color:#0f172a;margin-bottom:20px">⭐ Avis clients</h3>

    <div id="ratingSummary" style="display:flex;align-items:center;gap:20px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid #f1f5f9">
      <div style="text-align:center">
        <div style="font-size:48px;font-weight:900;color:#0f172a" id="rAvg">—</div>
        <div id="rStars" style="font-size:20px;color:#f59e0b">☆☆☆☆☆</div>
        <div style="font-size:12px;color:#94a3b8" id="rCount">0 avis</div>
      </div>
    </div>

    <div id="ratingsList" style="margin-bottom:28px">
      <p style="color:#94a3b8;font-size:14px">Chargement des avis...</p>
    </div>

    <!-- Formulaire avis -->
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px">
      <h4 style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:16px">Laisser un avis</h4>
      <div id="rError" style="display:none;background:#fee2e2;color:#dc2626;padding:8px 12px;border-radius:8px;font-size:13px;margin-bottom:12px"></div>
      <div id="rSuccess" style="display:none;background:#ecfdf5;color:#059669;padding:8px 12px;border-radius:8px;font-size:13px;margin-bottom:12px"></div>
      <label style="display:block;margin-bottom:12px">
        <span style="font-size:12px;font-weight:700;color:#334155;display:block;margin-bottom:4px">Votre nom *</span>
        <input id="rClient" type="text" placeholder="Ex: Jean Dupont" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit">
      </label>
      <div style="margin-bottom:12px">
        <span style="font-size:12px;font-weight:700;color:#334155;display:block;margin-bottom:8px">Note *</span>
        <div id="starPicker" style="display:flex;gap:6px">
          <?php for($s=1;$s<=5;$s++): ?>
          <button type="button" onclick="selectStar(<?=$s?>)" data-star="<?=$s?>" style="font-size:28px;background:none;border:none;cursor:pointer;color:#e2e8f0;transition:.15s">★</button>
          <?php endfor; ?>
        </div>
        <input type="hidden" id="rRating" value="0">
      </div>
      <label style="display:block;margin-bottom:16px">
        <span style="font-size:12px;font-weight:700;color:#334155;display:block;margin-bottom:4px">Commentaire (optionnel)</span>
        <textarea id="rComment" rows="3" placeholder="Votre avis sur ce produit..." style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical"></textarea>
      </label>
      <button onclick="submitRating()" style="padding:12px 24px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border:none;border-radius:10px;font-weight:700;font-size:14px;cursor:pointer">Publier mon avis</button>
    </div>
  </div>
</section>

<script>
const API_URL = '/medilink_medicament/MediLink/api/parapharmacie.php';
const PRODUIT_ID = <?= (int) $produit['id'] ?>;
let selectedStar = 0;

function selectStar(n) {
  selectedStar = n;
  document.getElementById('rRating').value = n;
  document.querySelectorAll('#starPicker button').forEach((b,i) => {
    b.style.color = i < n ? '#f59e0b' : '#e2e8f0';
  });
}

async function loadRatings() {
  const r = await fetch(API_URL + '?resource=ratings&produit_id=' + PRODUIT_ID);
  const d = await r.json();
  if (!d.success) return;
  const {ratings, average, total} = d.data;
  document.getElementById('rAvg').textContent   = total ? average : '—';
  document.getElementById('rCount').textContent  = total + ' avis';
  document.getElementById('rStars').textContent  = total ? '★'.repeat(Math.round(average)) + '☆'.repeat(5 - Math.round(average)) : '☆☆☆☆☆';
  document.getElementById('ratingsList').innerHTML = ratings.length === 0
    ? '<p style="color:#94a3b8;font-size:14px">Aucun avis pour le moment. Soyez le premier !</p>'
    : ratings.map(r => `
      <div style="padding:14px 0;border-bottom:1px solid #f1f5f9">
        <div style="display:flex;justify-content:space-between;margin-bottom:4px">
          <span style="font-weight:700;font-size:14px;color:#0f172a">${r.client_id}</span>
          <span style="font-size:12px;color:#94a3b8">${r.created_at?.substring(0,10)||''}</span>
        </div>
        <div style="color:#f59e0b;font-size:16px;margin-bottom:4px">${'★'.repeat(parseInt(r.rating))}${'☆'.repeat(5-parseInt(r.rating))}</div>
        ${r.comment ? `<p style="color:#475569;font-size:13px;line-height:1.6">${r.comment}</p>` : ''}
      </div>`).join('');
}

async function submitRating() {
  const client  = document.getElementById('rClient').value.trim();
  const rating  = parseInt(document.getElementById('rRating').value);
  const comment = document.getElementById('rComment').value.trim();
  const errBox  = document.getElementById('rError');
  const okBox   = document.getElementById('rSuccess');
  errBox.style.display = 'none'; okBox.style.display = 'none';
  if (!client) { errBox.textContent='Veuillez entrer votre nom.'; errBox.style.display='block'; return; }
  if (rating < 1) { errBox.textContent='Veuillez choisir une note.'; errBox.style.display='block'; return; }
  const r = await fetch(API_URL + '?resource=ratings', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body:JSON.stringify({produit_id:PRODUIT_ID, client_id:client, rating, comment})
  });
  const d = await r.json();
  if (d.success) { okBox.textContent='✓ Avis publié !'; okBox.style.display='block'; document.getElementById('rComment').value=''; selectStar(0); loadRatings(); }
  else { errBox.textContent = d.message || 'Erreur.'; errBox.style.display='block'; }
}

loadRatings();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
