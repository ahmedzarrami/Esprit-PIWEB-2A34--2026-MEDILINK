// ════════════════════════════════════════════
//  edit.js  –  Modification d'un produit
// ════════════════════════════════════════════

function openEditModal(id) {
  const products = JSON.parse(localStorage.getItem('pharma_products') || '[]');
  const produit  = products.find(p => p.id == id);
  if (!produit) { alert('Produit introuvable.'); return; }

  // Pré-remplir le formulaire
  document.getElementById('editId').value       = produit.id;
  document.getElementById('fieldRef').value     = produit.reference  || '';
  document.getElementById('fieldNom').value     = produit.nom        || '';
  document.getElementById('fieldDesc').value    = produit.description|| '';
  document.getElementById('fieldPrix').value    = produit.prix       || '';
  document.getElementById('fieldStock').value   = produit.stock      || '';
  document.getElementById('fieldCat').value     = produit.categorie  || '';

  // Titre du modal
  document.getElementById('modalTitleText').textContent = 'Modifier le produit';
  document.getElementById('btnSubmit').innerHTML =
    `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
       <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
       <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
     </svg> Enregistrer les modifications`;

  hideAlert();
  showModal();
}
