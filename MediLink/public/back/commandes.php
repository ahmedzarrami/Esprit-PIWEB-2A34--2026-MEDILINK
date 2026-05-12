<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/auth_guard.php';
require_once __DIR__ . '/../../config/database.php';

$db   = Database::getConnection();
$page = $_GET['page'] ?? 'commandes';
$pageTitle = $page === 'ratings' ? 'Avis Clients' : 'Gestion des Commandes';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if (!function_exists('e')) {
    function e(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
}

include __DIR__ . '/../../views/back/layouts/header.php';
?>
<div class="content-area" style="margin-left:280px;min-height:100vh;padding:32px 40px;">

<?php if ($page === 'ratings'): ?>
<!-- ══════════ AVIS CLIENTS ══════════ -->
<div style="margin-bottom:28px">
  <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin-bottom:4px">⭐ Avis Clients</h2>
  <p style="color:#64748b;font-size:14px">Consultez et gérez les évaluations laissées par vos clients.</p>
</div>

<div id="ratingsStats" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px"></div>

<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:0;overflow:hidden">
  <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center">
    <strong>Liste des avis</strong>
    <input id="rSearch" type="text" placeholder="Rechercher..." style="padding:8px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;width:250px">
  </div>
  <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse">
      <thead style="background:#f8fafc">
        <tr>
          <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Produit</th>
          <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Client</th>
          <th style="padding:12px 16px;text-align:center;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Note</th>
          <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Commentaire</th>
          <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Date</th>
          <th style="padding:12px 16px;text-align:center;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Action</th>
        </tr>
      </thead>
      <tbody id="ratingsBody">
        <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8">Chargement...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<?php else: ?>
<!-- ══════════ COMMANDES ══════════ -->
<div style="margin-bottom:28px">
  <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin-bottom:4px">🛒 Gestion des Commandes</h2>
  <p style="color:#64748b;font-size:14px">Consultez et gérez toutes les commandes passées par vos clients.</p>
</div>

<!-- Stats commandes -->
<div id="cmdStats" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px"></div>

<!-- Météo livraison -->
<div id="weatherBox" style="background:linear-gradient(135deg,#eff6ff,#e0f2fe);border:1px solid #93c5fd;border-radius:16px;padding:20px 24px;margin-bottom:28px;display:flex;align-items:center;gap:20px">
  <div style="font-size:40px" id="wIcon">🌤️</div>
  <div>
    <div style="font-size:22px;font-weight:800;color:#1e40af" id="wTemp">--°C</div>
    <div style="font-size:13px;color:#3b82f6;font-weight:600" id="wDesc">Chargement météo Tunis...</div>
    <div style="font-size:12px;color:#64748b;margin-top:4px" id="wAdvice"></div>
  </div>
  <button onclick="loadWeather()" style="margin-left:auto;padding:8px 16px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">Actualiser</button>
</div>

<!-- Filtres + tableau -->
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden">
  <div style="padding:16px 24px;border-bottom:1px solid #f1f5f9;display:flex;gap:12px;align-items:center;flex-wrap:wrap">
    <input id="cmdSearch" type="text" placeholder="Rechercher (produit, client)..." style="padding:8px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;flex:1;min-width:200px">
    <select id="cmdStatus" style="padding:8px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px">
      <option value="">Tous les statuts</option>
      <option value="En attente">⏳ En attente</option>
      <option value="Confirmée">✅ Confirmée</option>
      <option value="Livrée">🚚 Livrée</option>
      <option value="Annulée">❌ Annulée</option>
    </select>
    <select id="cmdPayment" style="padding:8px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px">
      <option value="">Tous paiements</option>
      <option value="virement">Virement</option>
      <option value="carte_bancaire">Carte bancaire</option>
      <option value="paypal">PayPal</option>
      <option value="especes">Espèces</option>
    </select>
  </div>
  <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse">
      <thead style="background:#f8fafc">
        <tr>
          <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">ID</th>
          <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Client</th>
          <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Produit</th>
          <th style="padding:12px 16px;text-align:center;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Qté</th>
          <th style="padding:12px 16px;text-align:right;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Total</th>
          <th style="padding:12px 16px;text-align:center;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Statut</th>
          <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Paiement</th>
          <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Date</th>
          <th style="padding:12px 16px;text-align:center;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0">Actions</th>
        </tr>
      </thead>
      <tbody id="cmdBody">
        <tr><td colspan="9" style="text-align:center;padding:40px;color:#94a3b8">Chargement...</td></tr>
      </tbody>
    </table>
  </div>
  <div style="padding:12px 24px;border-top:1px solid #f1f5f9;font-size:12px;color:#94a3b8" id="cmdInfo">—</div>
</div>

<?php endif; ?>
</div>

<script>
const API = '/medilink_medicament/MediLink/api/parapharmacie.php';

<?php if ($page === 'ratings'): ?>
// ── RATINGS ──
let allRatings = [];
async function loadRatings() {
  const r = await fetch(API + '?resource=all_ratings');
  const d = await r.json();
  allRatings = d.success ? d.data : [];
  renderRatingsStats();
  renderRatings();
}
function renderRatingsStats() {
  const total = allRatings.length;
  const avg   = total ? (allRatings.reduce((s,r)=>s+parseInt(r.rating),0)/total).toFixed(1) : '0.0';
  const withComment = allRatings.filter(r=>r.comment && r.comment.trim()).length;
  const now = Date.now(), week = 7*24*60*60*1000;
  const recent = allRatings.filter(r => now - new Date(r.created_at).getTime() < week).length;
  document.getElementById('ratingsStats').innerHTML = `
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:20px;text-align:center"><div style="font-size:24px">⭐</div><div style="font-size:28px;font-weight:800;color:#1e40af">${total}</div><div style="font-size:12px;color:#3b82f6;font-weight:600">Total avis</div></div>
    <div style="background:#ecfdf5;border:1px solid #bbf7d0;border-radius:12px;padding:20px;text-align:center"><div style="font-size:24px">📊</div><div style="font-size:28px;font-weight:800;color:#059669">${avg}</div><div style="font-size:12px;color:#059669;font-weight:600">Note moyenne</div></div>
    <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:20px;text-align:center"><div style="font-size:24px">📝</div><div style="font-size:28px;font-weight:800;color:#ea580c">${withComment}</div><div style="font-size:12px;color:#ea580c;font-weight:600">Avec commentaire</div></div>
    <div style="background:#f5f3ff;border:1px solid #ddd6fe;border-radius:12px;padding:20px;text-align:center"><div style="font-size:24px">🆕</div><div style="font-size:28px;font-weight:800;color:#7c3aed">${recent}</div><div style="font-size:12px;color:#7c3aed;font-weight:600">7 derniers jours</div></div>
  `;
}
function renderRatings(list = allRatings) {
  const q = (document.getElementById('rSearch').value||'').toLowerCase();
  const filtered = list.filter(r =>
    !q || r.produit_nom?.toLowerCase().includes(q) || r.client_id?.toLowerCase().includes(q)
  );
  const stars = n => '⭐'.repeat(n) + '☆'.repeat(5-n);
  document.getElementById('ratingsBody').innerHTML = filtered.length === 0
    ? `<tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8">Aucun avis.</td></tr>`
    : filtered.map(r => `
      <tr style="border-bottom:1px solid #f1f5f9">
        <td style="padding:12px 16px;font-weight:600">${r.produit_nom||'—'}</td>
        <td style="padding:12px 16px;color:#64748b">${r.client_id}</td>
        <td style="padding:12px 16px;text-align:center;font-size:13px">${stars(parseInt(r.rating))}</td>
        <td style="padding:12px 16px;color:#475569;font-size:13px;max-width:300px">${r.comment||'<em style="color:#94a3b8">—</em>'}</td>
        <td style="padding:12px 16px;color:#94a3b8;font-size:12px">${r.created_at?.substring(0,16)||''}</td>
        <td style="padding:12px 16px;text-align:center">
          <button onclick="deleteRating(${r.id})" style="padding:5px 10px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer">Supprimer</button>
        </td>
      </tr>`).join('');
}
async function deleteRating(id) {
  if (!confirm('Supprimer cet avis ?')) return;
  await fetch(API + '?resource=all_ratings', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({_method:'DELETE',id}) });
  loadRatings();
}
document.getElementById('rSearch').addEventListener('input', () => renderRatings());
loadRatings();

<?php else: ?>
// ── COMMANDES ──
let allCmds = [];
async function loadCommandes() {
  const r = await fetch(API + '?resource=commandes');
  const d = await r.json();
  allCmds = d.success ? d.data : [];
  renderStats();
  renderCommandes();
}
function renderStats() {
  const total   = allCmds.length;
  const revenu  = allCmds.reduce((s,c) => s + parseFloat(c.total||0), 0).toFixed(3);
  const pending = allCmds.filter(c => c.status === 'En attente').length;
  const confirmed = allCmds.filter(c => c.status === 'Confirmée').length;
  document.getElementById('cmdStats').innerHTML = `
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:20px;text-align:center"><div style="font-size:24px">📋</div><div style="font-size:28px;font-weight:800;color:#1e40af">${total}</div><div style="font-size:12px;color:#3b82f6;font-weight:600">Total commandes</div></div>
    <div style="background:#ecfdf5;border:1px solid #bbf7d0;border-radius:12px;padding:20px;text-align:center"><div style="font-size:24px">💰</div><div style="font-size:22px;font-weight:800;color:#059669">${revenu} DT</div><div style="font-size:12px;color:#059669;font-weight:600">Revenu total</div></div>
    <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:20px;text-align:center"><div style="font-size:24px">⏳</div><div style="font-size:28px;font-weight:800;color:#ea580c">${pending}</div><div style="font-size:12px;color:#ea580c;font-weight:600">En attente</div></div>
    <div style="background:#f5f3ff;border:1px solid #ddd6fe;border-radius:12px;padding:20px;text-align:center"><div style="font-size:24px">✅</div><div style="font-size:28px;font-weight:800;color:#7c3aed">${confirmed}</div><div style="font-size:12px;color:#7c3aed;font-weight:600">Confirmées</div></div>
  `;
}
const STATUS_COLORS = {
  'En attente': '#f59e0b', 'Confirmée': '#10b981', 'Livrée': '#2563eb', 'Annulée': '#ef4444'
};
const PAYMENT_LABELS = { virement:'🏦 Virement', carte_bancaire:'💳 Carte', paypal:'🅿️ PayPal', especes:'💵 Espèces' };
function renderCommandes() {
  const q  = (document.getElementById('cmdSearch').value||'').toLowerCase();
  const st = document.getElementById('cmdStatus').value;
  const pm = document.getElementById('cmdPayment').value;
  const filtered = allCmds.filter(c =>
    (!q  || c.nom_produit?.toLowerCase().includes(q) || c.client_id?.toLowerCase().includes(q)) &&
    (!st || c.status === st) &&
    (!pm || c.mode_paiement === pm)
  );
  document.getElementById('cmdInfo').textContent = `${filtered.length} commande(s) affichée(s)`;
  document.getElementById('cmdBody').innerHTML = filtered.length === 0
    ? `<tr><td colspan="9" style="text-align:center;padding:40px;color:#94a3b8">Aucune commande trouvée.</td></tr>`
    : filtered.map(c => `
      <tr style="border-bottom:1px solid #f1f5f9">
        <td style="padding:12px 16px;font-size:12px;color:#94a3b8;font-weight:600">#${c.id}</td>
        <td style="padding:12px 16px;font-size:13px">${c.client_id}</td>
        <td style="padding:12px 16px;font-weight:600">${c.nom_produit}</td>
        <td style="padding:12px 16px;text-align:center">${c.quantite}</td>
        <td style="padding:12px 16px;text-align:right;font-weight:700">${parseFloat(c.total).toFixed(3)} DT</td>
        <td style="padding:12px 16px;text-align:center">
          <select onchange="updateStatus(${c.id},this.value)" style="padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;border:1.5px solid ${STATUS_COLORS[c.status]||'#e2e8f0'};color:${STATUS_COLORS[c.status]||'#334155'};background:#fff;cursor:pointer">
            ${['En attente','Confirmée','Livrée','Annulée'].map(s=>`<option value="${s}" ${s===c.status?'selected':''}>${s}</option>`).join('')}
          </select>
        </td>
        <td style="padding:12px 16px;font-size:12px;color:#64748b">${PAYMENT_LABELS[c.mode_paiement]||c.mode_paiement}</td>
        <td style="padding:12px 16px;font-size:12px;color:#94a3b8">${c.created_at?.substring(0,16)||''}</td>
        <td style="padding:12px 16px;text-align:center">
          <button onclick="deleteCmd(${c.id})" style="padding:5px 10px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer">Supprimer</button>
        </td>
      </tr>`).join('');
}
async function updateStatus(id, status) {
  await fetch(API + '?resource=commandes', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({_method:'PATCH',id,status}) });
  loadCommandes();
}
async function deleteCmd(id) {
  if (!confirm('Supprimer cette commande ?')) return;
  await fetch(API + '?resource=commandes', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({_method:'DELETE',id}) });
  loadCommandes();
}
['cmdSearch','cmdStatus','cmdPayment'].forEach(id => document.getElementById(id).addEventListener('input', renderCommandes));
document.getElementById('cmdStatus').addEventListener('change', renderCommandes);
document.getElementById('cmdPayment').addEventListener('change', renderCommandes);
loadCommandes();

async function loadWeather() {
  try {
    const r = await fetch(API + '?resource=weather');
    const d = await r.json();
    if (d.success) {
      const w = d.data;
      document.getElementById('wTemp').textContent = w.temperature + '°C';
      document.getElementById('wDesc').textContent = w.description + ' · ' + w.humidity + '% humidité · ' + w.wind_speed + ' km/h';
      const t = w.temperature;
      document.getElementById('wAdvice').textContent = t > 35 ? '⚠️ Forte chaleur — prévoir livraisons tôt le matin.' : t > 25 ? '☀️ Bonne journée de livraison.' : t < 10 ? '🧥 Froid — équiper les livreurs.' : '✅ Conditions de livraison favorables.';
      document.getElementById('wIcon').textContent = t > 35 ? '🌡️' : t > 25 ? '☀️' : t > 15 ? '🌤️' : t > 5 ? '🌥️' : '🥶';
    }
  } catch(e) { document.getElementById('wDesc').textContent = 'Météo indisponible.'; }
}
loadWeather();
<?php endif; ?>
</script>

<?php include __DIR__ . '/../../views/back/layouts/footer.php'; ?>
