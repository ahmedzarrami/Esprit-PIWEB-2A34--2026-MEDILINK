// ════════════════════════════════════════════
//  sup.js  –  Suppression d'un produit
// ════════════════════════════════════════════

let _pendingDeleteId = null;

function confirmDelete(id, nom) {
  _pendingDeleteId = id;
  document.getElementById('confirmMsg').textContent =
    `Vous allez supprimer « ${nom} ». Cette action est irréversible.`;
  document.getElementById('confirmOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeConfirm() {
  _pendingDeleteId = null;
  document.getElementById('confirmOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

document.getElementById('btnConfirmDel').addEventListener('click', function () {
  if (_pendingDeleteId === null) return;

  const products = JSON.parse(localStorage.getItem('pharma_products') || '[]');
  const updated  = products.filter(p => p.id != _pendingDeleteId);
  localStorage.setItem('pharma_products', JSON.stringify(updated));

  closeConfirm();
  loadProducts();
});

// Fermer en cliquant hors du modal
document.getElementById('confirmOverlay').addEventListener('click', function (e) {
  if (e.target === this) closeConfirm();
});
