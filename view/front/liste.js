// ════════════════════════════════════════════
//  liste.js  –  Affichage & filtrage produits
// ════════════════════════════════════════════

const CATEGORIES = [
  'Soins visage','Soins corps','Hygiène',
  'Compléments alimentaires','Bébé & Maman',
  'Capillaire','Solaire','Minceur','Orthopédie','Autre'
];

const CAT_ICONS = {
  'Soins visage':'🧴','Soins corps':'🫧','Hygiène':'🪥',
  'Compléments alimentaires':'💊','Bébé & Maman':'🍼',
  'Capillaire':'💆','Solaire':'☀️','Minceur':'⚖️',
  'Orthopédie':'🦴','Autre':'📦'
};

let activeTag = '';

// ── Chargement & affichage ──────────────────
function loadProducts() {
  const products = getProducts();
  updateStats(products);
  buildCatTags(products);
  buildCatFilter(products);
  renderGrid(products);
}

function getProducts() {
  return JSON.parse(localStorage.getItem('pharma_products') || '[]');
}

function saveProducts(list) {
  localStorage.setItem('pharma_products', JSON.stringify(list));
}

// ── Stats bar ───────────────────────────────
function updateStats(products) {
  const cats = [...new Set(products.map(p => p.categorie).filter(Boolean))];
  const dispo = products.filter(p => parseInt(p.stock) > 0).length;
  document.getElementById('statTotal').textContent  = products.length + ' produit' + (products.length !== 1 ? 's' : '');
  document.getElementById('statCats').textContent   = cats.length + ' catégorie' + (cats.length !== 1 ? 's' : '');
  document.getElementById('statDispo').textContent  = dispo;
}

// ── Tags filtres rapides ────────────────────
function buildCatTags(products) {
  const container = document.getElementById('catTags');
  const cats = [...new Set(products.map(p => p.categorie).filter(Boolean))];
  container.innerHTML = '';
  cats.forEach(cat => {
    const btn = document.createElement('button');
    btn.className = 'cat-tag' + (activeTag === cat ? ' active' : '');
    btn.textContent = (CAT_ICONS[cat] || '📦') + ' ' + cat;
    btn.onclick = () => { activeTag = (activeTag === cat ? '' : cat); applyFilters(); };
    container.appendChild(btn);
  });
}

// ── Select catégorie ─────────────────────────
function buildCatFilter(products) {
  const sel = document.getElementById('filterCat');
  const current = sel.value;
  const cats = [...new Set(products.map(p => p.categorie).filter(Boolean))];
  sel.innerHTML = '<option value="">Toutes les catégories</option>';
  cats.forEach(c => {
    const opt = document.createElement('option');
    opt.value = c; opt.textContent = c;
    if (c === current) opt.selected = true;
    sel.appendChild(opt);
  });
}

// ── Filtrage ─────────────────────────────────
function applyFilters() {
  const q   = document.getElementById('searchInput').value.toLowerCase().trim();
  const cat = document.getElementById('filterCat').value || activeTag;
  const products = getProducts();

  // sync tag active
  activeTag = cat || activeTag;
  document.querySelectorAll('.cat-tag').forEach(btn => {
    btn.classList.toggle('active', btn.textContent.includes(activeTag));
  });

  const filtered = products.filter(p => {
    const matchQ = !q ||
      p.nom.toLowerCase().includes(q) ||
      (p.reference || '').toLowerCase().includes(q) ||
      (p.description || '').toLowerCase().includes(q);
    const matchC = !cat || p.categorie === cat;
    return matchQ && matchC;
  });

  const info = document.getElementById('resultsInfo');
  info.innerHTML = filtered.length === products.length
    ? `<strong>${filtered.length}</strong> produit${filtered.length !== 1 ? 's' : ''} au total`
    : `<strong>${filtered.length}</strong> résultat${filtered.length !== 1 ? 's' : ''} sur ${products.length} produit${products.length !== 1 ? 's' : ''}`;

  renderGrid(filtered);
}

function resetFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('filterCat').value   = '';
  activeTag = '';
  applyFilters();
}

// ── Rendu grille ─────────────────────────────
function renderGrid(products) {
  const grid = document.getElementById('productsGrid');
  grid.innerHTML = '';

  if (!products.length) {
    grid.innerHTML = `
      <div class="empty-state">
        <div class="empty-state-icon">📦</div>
        <strong>Aucun produit trouvé</strong>
        Modifiez votre recherche ou ajoutez un nouveau produit.
      </div>`;
    return;
  }

  products.forEach(p => {
    const stock = parseInt(p.stock) || 0;
    const stockClass = stock === 0 ? 'stock-out' : stock <= 5 ? 'stock-low' : 'stock-ok';
    const stockLabel = stock === 0 ? 'Rupture' : stock <= 5 ? `⚠ ${stock} restants` : `✓ En stock (${stock})`;
    const icon = CAT_ICONS[p.categorie] || '📦';
    const prix = parseFloat(p.prix || 0).toFixed(3);

    const card = document.createElement('div');
    card.className = 'product-card';
    card.innerHTML = `
      <div class="product-thumb">${icon}</div>
      <div class="product-body">
        <div class="product-ref">${escHtml(p.reference || '—')}</div>
        <div class="product-name">${escHtml(p.nom)}</div>
        <div class="product-desc">${escHtml(p.description || 'Aucune description.')}</div>
        <span class="product-cat">${escHtml(p.categorie || 'Autre')}</span>
        <div class="product-footer">
          <div class="product-price">${prix} <small>DT</small></div>
          <span class="stock-badge ${stockClass}">${stockLabel}</span>
        </div>
        <div class="product-actions">
          <button class="btn-edit" onclick="openEditModal(${p.id})">✏ Modifier</button>
          <button class="btn-del"  onclick="confirmDelete(${p.id}, '${escHtml(p.nom)}')">🗑 Supprimer</button>
        </div>
      </div>`;
    grid.appendChild(card);
  });
}

function escHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Listeners ────────────────────────────────
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('filterCat').addEventListener('change', applyFilters);

// Initialisation
loadProducts();
