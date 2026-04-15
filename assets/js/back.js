/**
 * MediLink — BackOffice JavaScript
 * Table, modals, CRUD, filtrage, pagination
 */

// ===== STATE =====
let users = [];
let editingId = null;
let deletingId = null;
let currentPage = 1;
const perPage = 6;
let sortField = 'date_creation';
let sortDir = -1;
let filteredUsers = [];

// ===== COLORS FOR AVATARS =====
const avatarColors = ['#1f6feb','#bc8cff','#3fb950','#d29922','#f85149','#58a6ff','#ff7b72','#56d364'];
function getAvatarColor(id) { return avatarColors[id % avatarColors.length]; }
function getInitials(u) { return ((u.prenom || '')[0] + (u.nom || '')[0]).toUpperCase(); }

// ===== INIT — Load users from PHP data =====
function initUsers(data) {
    users = data;
    filteredUsers = [...users];
    renderTable();
    updateStats();
}

// ===== RENDER TABLE =====
function renderTable() {
    const tbody = document.getElementById('usersTableBody');
    const start = (currentPage - 1) * perPage;
    const page = filteredUsers.slice(start, start + perPage);

    if (page.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg><p>Aucun utilisateur trouvé</p></div></td></tr>`;
        return;
    }

    tbody.innerHTML = page.map(u => `
    <tr>
      <td><input type="checkbox" class="checkbox" data-id="${u.id}"></td>
      <td>
        <div class="avatar-cell">
          <div class="table-avatar" style="background:${getAvatarColor(u.id)}">${getInitials(u)}</div>
          <div><div class="user-name">${u.prenom} ${u.nom}</div><div class="user-email">${u.email}</div></div>
        </div>
      </td>
      <td><span class="role-badge ${u.role==='Patient'?'role-patient':u.role==='Professionnel'?'role-pro':'role-admin'}">${u.role}</span></td>
      <td><span class="badge ${u.statut_compte==='Actif'?'badge-active':u.statut_compte==='Inactif'?'badge-inactive':u.statut_compte==='Suspendu'?'badge-suspended':'badge-pending'}">${u.statut_compte}</span></td>
      <td style="color:var(--text2);font-size:12px">${u.telephone}</td>
      <td style="color:var(--text3);font-size:12px">${formatDate(u.date_creation)}</td>
      <td>
        <div class="actions-cell">
          <button class="btn-icon" title="Voir" onclick="viewUser(${u.id})">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
          </button>
          <button class="btn-icon" title="Modifier" onclick="openEditModal(${u.id})">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          </button>
          <button class="btn-icon" title="Supprimer" onclick="openDeleteModal(${u.id})" style="border-color:var(--red-bg);color:var(--red)">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </div>
      </td>
    </tr>`).join('');
    renderPagination();
}

function renderPagination() {
    const total = filteredUsers.length;
    const pages = Math.ceil(total / perPage);
    const start = (currentPage - 1) * perPage + 1;
    const end = Math.min(currentPage * perPage, total);
    document.getElementById('paginationInfo').textContent = total ? `Affichage de ${start}-${end} sur ${total} utilisateurs` : 'Aucun résultat';
    const btns = document.getElementById('paginationBtns');
    btns.innerHTML = '';
    for (let i = 1; i <= pages; i++) {
        const b = document.createElement('button');
        b.className = 'page-btn' + (i === currentPage ? ' active' : '');
        b.textContent = i;
        b.onclick = () => { currentPage = i; renderTable(); };
        btns.appendChild(b);
    }
}

function formatDate(d) {
    if (!d) return '—';
    const parts = d.split(/[-T ]/);
    if (parts.length >= 3) return `${parts[2].substring(0,2)}/${parts[1]}/${parts[0]}`;
    return d;
}

// ===== SORT & FILTER =====
function sortBy(field) {
    if (sortField === field) sortDir *= -1; else { sortField = field; sortDir = 1; }
    filteredUsers.sort((a, b) => {
        const av = a[field] || '';
        const bv = b[field] || '';
        return av > bv ? sortDir : av < bv ? -sortDir : 0;
    });
    renderTable();
}

function filterUsers() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const role = document.getElementById('roleFilter').value;
    const status = document.getElementById('statusFilter').value;
    filteredUsers = users.filter(u => {
        const match = !q || (u.prenom + ' ' + u.nom + ' ' + u.email).toLowerCase().includes(q);
        const rMatch = !role || u.role === role;
        const sMatch = !status || u.statut_compte === status;
        return match && rMatch && sMatch;
    });
    currentPage = 1;
    renderTable();
}

// ===== MODAL HELPERS =====
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function closeOnOverlay(e, id) { if (e.target === document.getElementById(id)) closeModal(id); }
function toggleAll(cb) { document.querySelectorAll('tbody .checkbox').forEach(c => c.checked = cb.checked); }

// ===== FORM MODAL =====
function resetForm() {
    ['f-prenom','f-nom','f-email','f-telephone','f-dob','f-adresse','f-ordre','f-bio'].forEach(id => {
        const el = document.getElementById(id); if (el) el.value = '';
    });
    ['f-role','f-statut','f-sexe','f-specialite','f-groupe-sanguin'].forEach(id => {
        const el = document.getElementById(id); if (el) el.value = id === 'f-statut' ? 'Actif' : '';
    });
    document.getElementById('f-password').value = '';
    document.getElementById('f-confirm').value = '';
    clearErrors();
    resetAvatar();
    document.getElementById('pro-fields').style.display = 'none';
    document.getElementById('patient-fields').style.display = 'none';
    document.getElementById('strength-fill').style.width = '0%';
    document.getElementById('strength-text').textContent = '';
}

function clearErrors() {
    ['prenom','nom','email','telephone','dob','role','password','confirm','specialite','ordre'].forEach(f => {
        const e = document.getElementById('e-' + f);
        if (e) e.style.display = 'none';
        const inp = document.getElementById('f-' + f);
        if (inp) inp.classList.remove('error', 'success');
    });
}

function openCreateModal() {
    editingId = null;
    resetForm();
    document.getElementById('modalTitle').textContent = 'Nouvel utilisateur';
    document.getElementById('submitBtn').textContent = "Créer l'utilisateur";
    document.getElementById('edit-pw-hint').style.display = 'none';
    document.getElementById('pw-required').style.display = '';
    document.getElementById('cpw-required').style.display = '';
    openModal('formModal');
}

function openEditModal(id) {
    const u = users.find(x => x.id == id); if (!u) return;
    editingId = id;
    resetForm();
    document.getElementById('f-prenom').value = u.prenom || '';
    document.getElementById('f-nom').value = u.nom || '';
    document.getElementById('f-email').value = u.email || '';
    document.getElementById('f-telephone').value = u.telephone || '';
    document.getElementById('f-dob').value = u.date_naissance || '';
    document.getElementById('f-role').value = u.role || '';
    document.getElementById('f-statut').value = u.statut_compte || 'Actif';
    onRoleChange();
    if (u.role === 'Professionnel') {
        document.getElementById('f-specialite').value = u.specialite || '';
        document.getElementById('f-ordre').value = u.numero_ordre || '';
        document.getElementById('f-bio').value = u.biographie || '';
        updateBioCount();
    }
    if (u.role === 'Patient') {
        document.getElementById('f-sexe').value = u.sexe || '';
        document.getElementById('f-adresse').value = u.adresse || '';
        document.getElementById('f-groupe-sanguin').value = u.groupe_sanguin || '';
    }
    document.getElementById('modalTitle').textContent = "Modifier l'utilisateur";
    document.getElementById('submitBtn').textContent = 'Enregistrer les modifications';
    document.getElementById('edit-pw-hint').style.display = 'block';
    document.getElementById('pw-required').style.display = 'none';
    document.getElementById('cpw-required').style.display = 'none';
    closeModal('viewModal');
    openModal('formModal');
}

function onRoleChange() {
    const r = document.getElementById('f-role').value;
    document.getElementById('pro-fields').style.display = r === 'Professionnel' ? 'block' : 'none';
    document.getElementById('patient-fields').style.display = r === 'Patient' ? 'block' : 'none';
}

// ===== VALIDATION (using Validator class) =====
function showError(field, msg) {
    Validator.showFieldError('f-' + field, 'e-' + field, msg);
}
function clearError(field) {
    Validator.clearFieldError('f-' + field, 'e-' + field);
}

function validateField(fieldName) {
    const el = document.getElementById('f-' + fieldName);
    if (!el) return true;
    const val = el.value.trim();

    if (fieldName === 'prenom' || fieldName === 'nom') {
        if (!Validator.isRequired(val)) { showError(fieldName, 'Ce champ est obligatoire'); return false; }
        if (!Validator.minLength(val, 2)) { showError(fieldName, 'Minimum 2 caractères'); return false; }
        if (!Validator.isValidName(val)) { showError(fieldName, 'Ne doit contenir que des lettres'); return false; }
    }
    if (fieldName === 'email') {
        if (!Validator.isRequired(val)) { showError(fieldName, 'Email obligatoire'); return false; }
        if (!Validator.isEmail(val)) { showError(fieldName, 'Email invalide'); return false; }
    }
    if (fieldName === 'telephone') {
        if (!Validator.isRequired(val)) { showError(fieldName, 'Téléphone obligatoire'); return false; }
        if (!Validator.isPhone(val)) { showError(fieldName, 'Le téléphone doit contenir exactement 8 chiffres'); return false; }
    }
    if (fieldName === 'dob' && val) {
        if (!Validator.isValidBirthDate(val)) { showError(fieldName, 'Date invalide'); return false; }
    }
    if (fieldName === 'ordre' && document.getElementById('f-role').value === 'Professionnel') {
        if (!Validator.isRequired(val)) { showError(fieldName, "N° d'ordre obligatoire"); return false; }
        if (!Validator.isValidOrdre(val)) { showError(fieldName, 'Format: TN-MED-12345'); return false; }
    }
    clearError(fieldName);
    return true;
}

function validatePassword() {
    const pw = document.getElementById('f-password').value;
    const strength = Validator.passwordStrength(pw);
    document.getElementById('strength-fill').style.width = strength.percent;
    document.getElementById('strength-fill').style.background = strength.color;
    document.getElementById('strength-text').textContent = strength.label || '';

    if (!editingId || pw) {
        if (!pw && !editingId) { showError('password', 'Mot de passe obligatoire'); return false; }
        if (pw && !Validator.minLength(pw, 8)) { showError('password', 'Minimum 8 caractères'); return false; }
        if (pw && strength.score < 2) { showError('password', 'Trop faible : ajoutez majuscules, chiffres'); return false; }
        if (pw) clearError('password');
    }
    return true;
}

function validateConfirm() {
    const pw = document.getElementById('f-password').value;
    const cf = document.getElementById('f-confirm').value;
    if ((pw || !editingId) && !Validator.passwordsMatch(cf, pw)) {
        showError('confirm', 'Les mots de passe ne correspondent pas');
        return false;
    }
    if (cf) clearError('confirm');
    return true;
}

function updateBioCount() {
    const ta = document.getElementById('f-bio');
    document.getElementById('bio-count').textContent = `${ta.value.length} / 500 caractères`;
}

function validateAll() {
    let ok = true;
    ['prenom','nom','email','telephone'].forEach(f => { if (!validateField(f)) ok = false; });
    if (!Validator.isRequired(document.getElementById('f-role').value)) {
        showError('role', 'Rôle obligatoire'); ok = false;
    } else {
        clearError('role');
    }
    const dob = document.getElementById('f-dob').value;
    if (dob && !validateField('dob')) ok = false;
    if (!editingId || document.getElementById('f-password').value) {
        if (!validatePassword()) ok = false;
        if (!validateConfirm()) ok = false;
    }
    const role = document.getElementById('f-role').value;
    if (role === 'Professionnel') {
        if (!Validator.isRequired(document.getElementById('f-specialite').value)) {
            showError('specialite', 'Spécialité obligatoire'); ok = false;
        } else {
            clearError('specialite');
        }
        if (!validateField('ordre')) ok = false;
    }
    return ok;
}

// ===== CRUD (AJAX via fetch) =====
function submitForm() {
    if (!validateAll()) return;

    const formData = new FormData();
    formData.append('prenom', document.getElementById('f-prenom').value.trim());
    formData.append('nom', document.getElementById('f-nom').value.trim());
    formData.append('email', document.getElementById('f-email').value.trim());
    formData.append('telephone', document.getElementById('f-telephone').value.trim());
    formData.append('date_naissance', document.getElementById('f-dob').value);
    formData.append('role', document.getElementById('f-role').value);
    formData.append('statut', document.getElementById('f-statut').value);
    formData.append('mot_de_passe', document.getElementById('f-password').value);
    formData.append('confirm_mdp', document.getElementById('f-confirm').value);

    const role = document.getElementById('f-role').value;
    if (role === 'Professionnel') {
        formData.append('specialite', document.getElementById('f-specialite').value);
        formData.append('numero_ordre', document.getElementById('f-ordre').value.trim());
        formData.append('biographie', document.getElementById('f-bio').value.trim());
    }
    if (role === 'Patient') {
        formData.append('sexe', document.getElementById('f-sexe').value);
        formData.append('adresse', document.getElementById('f-adresse').value.trim());
        formData.append('groupe_sanguin', document.getElementById('f-groupe-sanguin').value);
    }

    if (editingId) {
        formData.append('action', 'update');
        formData.append('id', editingId);
    } else {
        formData.append('action', 'create');
    }

    fetch('admin.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toast(editingId ? 'Utilisateur modifié avec succès' : 'Utilisateur créé avec succès', 'success');
            closeModal('formModal');
            loadUsers();
        } else {
            if (data.errors) {
                Object.entries(data.errors).forEach(([field, msg]) => {
                    if (field === 'global') {
                        toast(msg, 'error');
                    } else {
                        showError(field, msg);
                    }
                });
            }
        }
    })
    .catch(() => toast('Erreur de connexion au serveur', 'error'));
}

function openDeleteModal(id) {
    const u = users.find(x => x.id == id);
    deletingId = id;
    document.getElementById('deleteUserName').textContent = `${u.prenom} ${u.nom}`;
    openModal('deleteModal');
}

function confirmDelete() {
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', deletingId);

    fetch('admin.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toast('Utilisateur supprimé', 'error');
            closeModal('deleteModal');
            loadUsers();
        } else {
            toast(data.errors?.global || 'Erreur lors de la suppression', 'error');
        }
    })
    .catch(() => toast('Erreur de connexion au serveur', 'error'));
}

function loadUsers() {
    fetch('admin.php?action=list')
    .then(r => r.json())
    .then(data => {
        users = data.users;
        filteredUsers = [...users];
        filterUsers();
        updateStatsFromData(data.stats);
    })
    .catch(() => toast('Erreur de chargement', 'error'));
}

function viewUser(id) {
    const u = users.find(x => x.id == id);
    const body = document.getElementById('viewModalBody');
    body.innerHTML = `
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px">
      <div style="width:64px;height:64px;border-radius:50%;background:${getAvatarColor(u.id)};display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:600;color:white;flex-shrink:0">${getInitials(u)}</div>
      <div>
        <div style="font-size:18px;font-weight:600">${u.prenom} ${u.nom}</div>
        <div style="font-size:13px;color:var(--text3)">${u.email}</div>
        <div style="margin-top:6px;display:flex;gap:6px">
          <span class="role-badge ${u.role==='Patient'?'role-patient':u.role==='Professionnel'?'role-pro':'role-admin'}">${u.role}</span>
          <span class="badge ${u.statut_compte==='Actif'?'badge-active':u.statut_compte==='Inactif'?'badge-inactive':u.statut_compte==='Suspendu'?'badge-suspended':'badge-pending'}">${u.statut_compte}</span>
        </div>
      </div>
    </div>
    <div class="detail-section">
      <div class="detail-section-title">Informations générales</div>
      <div class="detail-grid">
        <div class="detail-item"><label>Téléphone</label><span>${u.telephone||'—'}</span></div>
        <div class="detail-item"><label>Date de naissance</label><span>${u.date_naissance?formatDate(u.date_naissance):'—'}</span></div>
        <div class="detail-item"><label>Inscrit le</label><span>${formatDate(u.date_creation)}</span></div>
        ${u.adresse?`<div class="detail-item" style="grid-column:1/-1"><label>Adresse</label><span>${u.adresse}</span></div>`:''}
      </div>
    </div>
    ${u.role==='Professionnel'?`<div class="detail-section"><div class="detail-section-title">Professionnel de santé</div><div class="detail-grid"><div class="detail-item"><label>Spécialité</label><span>${u.specialite||'—'}</span></div><div class="detail-item"><label>N° Ordre</label><span>${u.numero_ordre||'—'}</span></div>${u.biographie?`<div class="detail-item" style="grid-column:1/-1"><label>Biographie</label><span>${u.biographie}</span></div>`:''}</div></div>`:''}
    ${u.role==='Patient'?`<div class="detail-section"><div class="detail-section-title">Patient</div><div class="detail-grid"><div class="detail-item"><label>Sexe</label><span>${u.sexe==='M'?'Masculin':u.sexe==='F'?'Féminin':'—'}</span></div><div class="detail-item"><label>Groupe sanguin</label><span>${u.groupe_sanguin||'—'}</span></div></div></div>`:''}
    `;
    document.getElementById('viewEditBtn').onclick = () => openEditModal(id);
    openModal('viewModal');
}

// ===== STATS =====
function updateStats() {
    const total = users.length;
    const patients = users.filter(u => u.role === 'Patient').length;
    const pros = users.filter(u => u.role === 'Professionnel').length;
    const actifs = users.filter(u => u.statut_compte === 'Actif').length;
    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-patients').textContent = patients;
    document.getElementById('stat-pros').textContent = pros;
    document.getElementById('stat-active').textContent = actifs;
}

function updateStatsFromData(stats) {
    document.getElementById('stat-total').textContent = stats.total;
    document.getElementById('stat-patients').textContent = stats.patients;
    document.getElementById('stat-pros').textContent = stats.pros;
    document.getElementById('stat-active').textContent = stats.actifs;
}

// ===== AVATAR =====
function previewAvatar(e) {
    const file = e.target.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
        document.getElementById('avatarPreview').innerHTML = `<img src="${ev.target.result}">`;
    };
    reader.readAsDataURL(file);
}
function resetAvatar() {
    document.getElementById('avatarPreview').innerHTML = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`;
    document.getElementById('avatarInput').value = '';
}

// ===== PASSWORD TOGGLE =====
function togglePw(id, btn) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.style.color = inp.type === 'text' ? 'var(--blue)' : 'var(--text3)';
}

// ===== TOAST =====
function toast(msg, type = 'info') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    const icons = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    };
    t.className = `toast toast-${type}`;
    t.innerHTML = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:16px;height:16px">${icons[type]}</svg><span class="toast-msg">${msg}</span>`;
    c.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

// ===== EXPORT (simulated) =====
function exportUsers() { toast('Export CSV en cours de génération…', 'info'); }

// ===== BIO COUNT LISTENER =====
document.addEventListener('DOMContentLoaded', function() {
    const bio = document.getElementById('f-bio');
    if (bio) bio.addEventListener('input', updateBioCount);
});
