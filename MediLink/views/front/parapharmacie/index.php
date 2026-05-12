<?php require __DIR__ . '/../layouts/header.php'; ?>

<!-- PANIER FLOTTANT -->
<div id="cartPanel" style="display:none;position:fixed;top:0;right:0;width:380px;height:100vh;background:#fff;box-shadow:-4px 0 30px rgba(0,0,0,.12);z-index:9999;display:flex;flex-direction:column;transform:translateX(100%);transition:transform .3s ease">
  <div style="padding:20px 24px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center">
    <div style="font-size:18px;font-weight:800;color:#0f172a">🛒 Panier</div>
    <button onclick="closeCart()" style="background:none;border:none;font-size:22px;cursor:pointer;color:#64748b">×</button>
  </div>
  <div id="cartItems" style="flex:1;overflow-y:auto;padding:16px 24px"></div>
  <div style="padding:20px 24px;border-top:1px solid #e2e8f0">
    <div style="display:flex;justify-content:space-between;margin-bottom:16px">
      <span style="font-weight:700;color:#334155">Total</span>
      <span style="font-size:20px;font-weight:800;color:#059669" id="cartTotal">0.000 DT</span>
    </div>
    <button onclick="openOrderModal()" style="width:100%;padding:14px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer">Commander →</button>
    <button onclick="clearCart()" style="width:100%;padding:10px;background:#f1f5f9;color:#64748b;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;margin-top:8px">Vider le panier</button>
  </div>
</div>
<div id="cartOverlay" onclick="closeCart()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.3);z-index:9998"></div>

<!-- MODAL COMMANDE -->
<div id="orderOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:10000;display:flex;align-items:center;justify-content:center;display:none">
  <div style="background:#fff;border-radius:20px;padding:32px;width:420px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,.2)">
    <h3 style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:20px">📋 Valider la commande</h3>
    <div id="orderError" style="display:none;background:#fee2e2;color:#dc2626;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px"></div>
    <label style="display:block;margin-bottom:12px">
      <span style="font-size:12px;font-weight:700;color:#334155;display:block;margin-bottom:4px">Votre nom / identifiant *</span>
      <input id="oClient" type="text" placeholder="Ex: Jean Dupont" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit">
    </label>
    <label style="display:block;margin-bottom:20px">
      <span style="font-size:12px;font-weight:700;color:#334155;display:block;margin-bottom:4px">Mode de paiement *</span>
      <select id="oPayment" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit">
        <option value="">— Choisir —</option>
        <option value="especes">💵 Espèces à la livraison</option>
        <option value="carte_bancaire">💳 Carte bancaire</option>
        <option value="virement">🏦 Virement bancaire</option>
        <option value="paypal">🅿️ PayPal</option>
      </select>
    </label>
    <div style="display:flex;gap:10px">
      <button onclick="closeOrderModal()" style="flex:1;padding:12px;background:#f1f5f9;color:#334155;border:none;border-radius:10px;font-weight:700;cursor:pointer">Annuler</button>
      <button onclick="submitOrder()" id="btnSubmitOrder" style="flex:2;padding:12px;background:linear-gradient(135deg,#059669,#047857);color:#fff;border:none;border-radius:10px;font-weight:700;cursor:pointer">✅ Confirmer</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div id="cartToast" style="display:none;position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#0f172a;color:#fff;padding:12px 24px;border-radius:100px;font-size:14px;font-weight:600;z-index:11000;box-shadow:0 8px 24px rgba(0,0,0,.2)"></div>

<!-- BOUTON PANIER FLOTTANT -->
<button onclick="openCart()" style="position:fixed;bottom:32px;right:32px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border:none;border-radius:50%;width:60px;height:60px;font-size:24px;cursor:pointer;box-shadow:0 8px 24px rgba(37,99,235,.4);z-index:9000;display:flex;align-items:center;justify-content:center">
  🛒<span id="cartBadge" style="position:absolute;top:-4px;right:-4px;background:#dc2626;color:#fff;border-radius:50%;width:20px;height:20px;font-size:11px;font-weight:800;display:none;align-items:center;justify-content:center">0</span>
</button>

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
?>

<section class="listing-hero">
    <h1>Parapharmacie</h1>
    <p>Retrouvez nos produits de soins, hygiène et compléments alimentaires sélectionnés par nos experts.</p>
</section>

<section class="section-block narrow">

    <!-- Filtres -->
    <form method="GET" action="index.php" class="toolbar">
        <input type="hidden" name="action" value="parapharmacie">

        <div class="toolbar-group grow">
            <label for="search">Recherche</label>
            <input id="search" type="text" name="search"
                   value="<?= htmlspecialchars($search) ?>"
                   placeholder="Nom, catégorie, description...">
        </div>

        <div class="toolbar-group">
            <label for="categorie">Catégorie</label>
            <select name="categorie" id="categorie">
                <option value="">Toutes</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"
                        <?= $categorie === $cat ? 'selected' : '' ?>>
                        <?= ($catIcons[$cat] ?? '📦') . ' ' . htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="toolbar-group">
            <label for="sort">Tri</label>
            <select name="sort" id="sort">
                <option value="nom_asc"   <?= $sort === 'nom_asc'   ? 'selected' : '' ?>>Nom A-Z</option>
                <option value="nom_desc"  <?= $sort === 'nom_desc'  ? 'selected' : '' ?>>Nom Z-A</option>
                <option value="prix_asc"  <?= $sort === 'prix_asc'  ? 'selected' : '' ?>>Prix croissant</option>
                <option value="prix_desc" <?= $sort === 'prix_desc' ? 'selected' : '' ?>>Prix décroissant</option>
            </select>
        </div>

        <div class="toolbar-actions">
            <button type="submit">Appliquer</button>
            <a href="index.php?action=parapharmacie">Réinitialiser</a>
        </div>
    </form>

    <div class="results-head">
        <p><strong><?= (int) $total ?></strong> produit(s) trouvé(s)</p>
        <p>Page <?= (int) $page ?> / <?= (int) $totalPages ?></p>
    </div>

    <!-- Grille produits -->
    <?php if (empty($produits)): ?>
        <div class="empty-state">
            <h3>Aucun produit trouvé</h3>
            <p>Essayez avec un autre mot-clé ou changez le filtre de catégorie.</p>
        </div>
    <?php else: ?>
        <div class="medicine-list-grid">
            <?php foreach ($produits as $p): ?>
                <article class="medicine-list-card para-card">
                    <!-- Image produit -->
                    <?php if (!empty($p['image_path'])): ?>
                        <div class="para-img-wrap">
                            <img src="/medilink_medicament/MediLink/public/img/parapharmacie/<?= htmlspecialchars($p['image_path']) ?>"
                                 alt="<?= htmlspecialchars($p['nom']) ?>"
                                 class="para-img">
                        </div>
                    <?php else: ?>
                        <div class="para-img-placeholder">
                            <span><?= $catIcons[$p['categorie']] ?? '📦' ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="top-row">
                        <span class="pill para-pill">
                            <?= $catIcons[$p['categorie']] ?? '📦' ?>
                            <?= htmlspecialchars($p['categorie']) ?>
                        </span>
                        <span class="price"><?= number_format((float) $p['prix'], 3, ',', ' ') ?> DT</span>
                    </div>
                    <h3><?= htmlspecialchars($p['nom']) ?></h3>
                    <p class="meta">Réf : <?= htmlspecialchars($p['reference']) ?></p>
                    <?php if (!empty($p['description'])): ?>
                        <p class="description"><?= htmlspecialchars(mb_strimwidth($p['description'], 0, 130, '…')) ?></p>
                    <?php endif; ?>
                    <div class="bottom-row">
                        <?php
                            $stock = (int) $p['stock'];
                            if ($stock === 0):
                        ?>
                            <span style="color:#dc2626;font-weight:600;font-size:12px">⛔ Rupture</span>
                        <?php elseif ($stock <= 5): ?>
                            <span style="color:#d97706;font-weight:600;font-size:12px">⚠ Stock faible (<?= $stock ?>)</span>
                        <?php else: ?>
                            <span style="color:#059669;font-weight:600;font-size:12px">✓ En stock (<?= $stock ?>)</span>
                        <?php endif; ?>
                        <a href="index.php?action=show_produit&id=<?= (int) $p['id'] ?>">Voir plus</a>
                        <?php if ($stock > 0): ?>
                        <button onclick='addToCart(<?= json_encode(['id'=>(int)$p['id'],'nom'=>$p['nom'],'prix'=>(float)$p['prix'],'ref'=>$p['reference']]) ?>)' style="padding:6px 12px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer">+ Panier</button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="index.php?action=parapharmacie&search=<?= urlencode($search) ?>&categorie=<?= urlencode($categorie) ?>&sort=<?= urlencode($sort) ?>&page=<?= $i ?>"
                       class="<?= $i === $page ? 'current' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

</section>

<style>
.para-pill {
    background: rgba(139, 92, 246, 0.1);
    color: #7c3aed;
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 100px;
    font-weight: 600;
    white-space: nowrap;
}
.para-card .bottom-row {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 8px;
}
/* Image produit */
.para-img-wrap {
    width: 100%;
    height: 180px;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
    margin: -16px -16px 12px -16px;
    width: calc(100% + 32px);
}
.para-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .3s ease;
}
.para-card:hover .para-img {
    transform: scale(1.04);
}
.para-img-placeholder {
    width: calc(100% + 32px);
    height: 120px;
    margin: -16px -16px 12px -16px;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    border-radius: 12px 12px 0 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 42px;
}
</style>

<script>
const API_URL = '/medilink_medicament/MediLink/api/parapharmacie.php';
let cart = JSON.parse(localStorage.getItem('medilink_cart') || '[]');

function saveCart() { localStorage.setItem('medilink_cart', JSON.stringify(cart)); updateCartUI(); }

function addToCart(p) {
  const existing = cart.find(i => i.id === p.id);
  if (existing) { existing.qty++; } else { cart.push({...p, qty:1}); }
  saveCart();
  toast('✓ ' + p.nom + ' ajouté au panier');
}

function removeFromCart(id) { cart = cart.filter(i => i.id !== id); saveCart(); }
function clearCart() { cart = []; saveCart(); }

function updateCartUI() {
  const total = cart.reduce((s,i) => s + i.prix * i.qty, 0);
  const count = cart.reduce((s,i) => s + i.qty, 0);
  document.getElementById('cartTotal').textContent = total.toFixed(3) + ' DT';
  const badge = document.getElementById('cartBadge');
  if (count > 0) { badge.textContent = count; badge.style.display = 'flex'; } else { badge.style.display = 'none'; }
  document.getElementById('cartItems').innerHTML = cart.length === 0
    ? '<p style="color:#94a3b8;text-align:center;padding:40px 0;font-size:14px">Panier vide</p>'
    : cart.map(i => `
      <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f1f5f9">
        <div>
          <div style="font-weight:700;color:#0f172a;font-size:14px">${i.nom}</div>
          <div style="font-size:12px;color:#64748b">${i.prix.toFixed(3)} DT × ${i.qty}</div>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
          <span style="font-weight:700;color:#059669">${(i.prix*i.qty).toFixed(3)} DT</span>
          <button onclick="removeFromCart(${i.id})" style="background:none;border:none;color:#dc2626;font-size:18px;cursor:pointer;line-height:1">×</button>
        </div>
      </div>`).join('');
}

function openCart()  { document.getElementById('cartPanel').style.transform='translateX(0)'; document.getElementById('cartOverlay').style.display='block'; }
function closeCart() { document.getElementById('cartPanel').style.transform='translateX(100%)'; document.getElementById('cartOverlay').style.display='none'; }
function openOrderModal()  { if (!cart.length) { toast('⚠️ Panier vide'); return; } document.getElementById('orderOverlay').style.display='flex'; closeCart(); }
function closeOrderModal() { document.getElementById('orderOverlay').style.display='none'; }

async function submitOrder() {
  const client  = document.getElementById('oClient').value.trim();
  const payment = document.getElementById('oPayment').value;
  const errBox  = document.getElementById('orderError');
  if (!client || !payment) { errBox.textContent='Veuillez remplir tous les champs.'; errBox.style.display='block'; return; }
  errBox.style.display='none';
  document.getElementById('btnSubmitOrder').textContent='⏳ Envoi...';
  document.getElementById('btnSubmitOrder').disabled = true;

  let allOk = true;
  for (const item of cart) {
    const r = await fetch(API_URL + '?resource=commandes', {
      method:'POST', headers:{'Content-Type':'application/json'},
      body:JSON.stringify({ clientId:client, productId:item.id, productRef:item.ref, productNom:item.nom, productPrix:item.prix, qty:item.qty, total:item.prix*item.qty, payment })
    });
    const d = await r.json();
    if (!d.success) { allOk = false; errBox.textContent = d.message; errBox.style.display='block'; break; }
  }

  document.getElementById('btnSubmitOrder').textContent='✅ Confirmer';
  document.getElementById('btnSubmitOrder').disabled = false;
  if (allOk) { clearCart(); closeOrderModal(); toast('🎉 Commande passée avec succès !'); }
}

function toast(msg) {
  const t = document.getElementById('cartToast');
  t.textContent = msg; t.style.display='block';
  setTimeout(() => { t.style.display='none'; }, 2500);
}

updateCartUI();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
