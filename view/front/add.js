// ════════════════════════════════════════════
//  add.js  –  Ajout de produit (modal)
// ════════════════════════════════════════════

function openAddModal() {
  document.getElementById('modalTitleText').textContent = 'Ajouter un produit';
  document.getElementById('btnSubmit').textContent      = 'Enregistrer le produit';
  clearForm();
  document.getElementById('editId').value = '';
  showModal();
}

function showModal() {
  document.getElementById('modalOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('modalOverlay').classList.remove('open');
  document.body.style.overflow = '';
  clearForm();
}

// Fermer en cliquant hors du modal
document.getElementById('modalOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

function clearForm() {
  ['fieldRef','fieldNom','fieldDesc','fieldPrix','fieldStock'].forEach(id => {
    document.getElementById(id).value = '';
    document.getElementById(id).classList.remove('error');
  });
  document.getElementById('fieldCat').value = '';
  document.getElementById('fieldCat').classList.remove('error');
  ['errRef','errNom','errCat','errPrix','errStock'].forEach(id => {
    document.getElementById(id).classList.remove('show');
  });
  hideAlert();
}

function showAlert(msg, type = 'success') {
  const alert = document.getElementById('formAlert');
  const span  = document.getElementById('formAlertMsg');
  alert.className = 'form-alert show ' + type;
  span.textContent = msg;
}
function hideAlert() {
  document.getElementById('formAlert').className = 'form-alert';
}

// ── Validation ───────────────────────────────
function validateForm() {
  let valid = true;

  const fields = [
    { id: 'fieldRef', err: 'errRef', msg: 'La référence est requise.' },
    { id: 'fieldNom', err: 'errNom', msg: 'Le nom est requis.' },
    { id: 'fieldCat', err: 'errCat', msg: 'Veuillez choisir une catégorie.' },
  ];

  fields.forEach(({ id, err, msg }) => {
    const el = document.getElementById(id);
    const errEl = document.getElementById(err);
    if (!el.value.trim()) {
      el.classList.add('error');
      errEl.textContent = msg;
      errEl.classList.add('show');
      valid = false;
    } else {
      el.classList.remove('error');
      errEl.classList.remove('show');
    }
  });

  // Prix
  const prix = parseFloat(document.getElementById('fieldPrix').value);
  const errPrix = document.getElementById('errPrix');
  const elPrix  = document.getElementById('fieldPrix');
  if (isNaN(prix) || prix < 0) {
    elPrix.classList.add('error');
    errPrix.textContent = 'Veuillez entrer un prix valide (≥ 0).';
    errPrix.classList.add('show');
    valid = false;
  } else {
    elPrix.classList.remove('error');
    errPrix.classList.remove('show');
  }

  // Stock
  const stock = parseInt(document.getElementById('fieldStock').value);
  const errStock = document.getElementById('errStock');
  const elStock  = document.getElementById('fieldStock');
  if (isNaN(stock) || stock < 0) {
    elStock.classList.add('error');
    errStock.textContent = 'Veuillez entrer un stock valide (≥ 0).';
    errStock.classList.add('show');
    valid = false;
  } else {
    elStock.classList.remove('error');
    errStock.classList.remove('show');
  }

  return valid;
}

// ── Soumission ───────────────────────────────
function submitForm() {
  hideAlert();
  if (!validateForm()) return;

  const editId = document.getElementById('editId').value;
  const products = JSON.parse(localStorage.getItem('pharma_products') || '[]');

  const produit = {
    reference:   document.getElementById('fieldRef').value.trim(),
    nom:         document.getElementById('fieldNom').value.trim(),
    description: document.getElementById('fieldDesc').value.trim(),
    prix:        parseFloat(document.getElementById('fieldPrix').value),
    stock:       parseInt(document.getElementById('fieldStock').value),
    categorie:   document.getElementById('fieldCat').value,
  };

  if (editId) {
    // Modification
    const idx = products.findIndex(p => p.id == editId);
    if (idx !== -1) {
      produit.id = parseInt(editId);
      products[idx] = produit;
      localStorage.setItem('pharma_products', JSON.stringify(products));
      showAlert('✓ Produit modifié avec succès !', 'success');
      setTimeout(() => { closeModal(); loadProducts(); }, 900);
    }
  } else {
    // Ajout – vérifie unicité référence
    const refExists = products.some(p => p.reference.toLowerCase() === produit.reference.toLowerCase());
    if (refExists) {
      document.getElementById('fieldRef').classList.add('error');
      document.getElementById('errRef').textContent = 'Cette référence existe déjà.';
      document.getElementById('errRef').classList.add('show');
      return;
    }
    produit.id = Date.now();
    products.push(produit);
    localStorage.setItem('pharma_products', JSON.stringify(products));
    showAlert('✓ Produit ajouté avec succès !', 'success');
    setTimeout(() => { closeModal(); loadProducts(); }, 900);
  }
}
