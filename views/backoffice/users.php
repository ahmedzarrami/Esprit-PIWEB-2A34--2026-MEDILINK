  <header class="topbar">
    <div class="topbar-left">
      <div>
        <div class="page-title">Gestion des utilisateurs</div>
        <div class="breadcrumb">Accueil / <span>Utilisateurs</span></div>
      </div>
    </div>
    <div class="topbar-right">
      <button class="btn btn-secondary" onclick="exportUsers()">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Exporter
      </button>
      <button class="btn btn-primary" onclick="openCreateModal()">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvel utilisateur
      </button>
    </div>
  </header>

  <div class="content">
    <!-- STATS -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-label">Total utilisateurs</div>
        <div class="stat-value" id="stat-total"><?= $stats['total'] ?? 0 ?></div>
        <div class="stat-change up">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
          +12 ce mois
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Patients</div>
        <div class="stat-value" id="stat-patients"><?= $stats['patients'] ?? 0 ?></div>
        <div class="stat-change up"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>+8</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Professionnels</div>
        <div class="stat-value" id="stat-pros"><?= $stats['pros'] ?? 0 ?></div>
        <div class="stat-change up"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>+3</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Comptes actifs</div>
        <div class="stat-value" id="stat-active"><?= $stats['actifs'] ?? 0 ?></div>
        <div class="stat-change down"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>-2</div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="table-card">
      <div class="table-header">
        <div class="table-title">Liste des utilisateurs</div>
        <div class="search-box">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input type="text" id="searchInput" placeholder="Rechercher..." oninput="filterUsers()">
        </div>
        <select class="filter-select" id="roleFilter" onchange="filterUsers()">
          <option value="">Tous les rôles</option>
          <option value="Patient">Patient</option>
          <option value="Professionnel">Professionnel</option>
          <option value="Administrateur">Administrateur</option>
        </select>
        <select class="filter-select" id="statusFilter" onchange="filterUsers()">
          <option value="">Tous les statuts</option>
          <option value="Actif">Actif</option>
          <option value="Inactif">Inactif</option>
          <option value="Suspendu">Suspendu</option>
          <option value="En attente">En attente</option>
        </select>
      </div>

      <table>
        <thead>
          <tr>
            <th><input type="checkbox" class="checkbox" id="selectAll" onchange="toggleAll(this)"></th>
            <th class="sortable" onclick="sortBy('nom')">Utilisateur ↕</th>
            <th class="sortable" onclick="sortBy('role')">Rôle ↕</th>
            <th class="sortable" onclick="sortBy('statut_compte')">Statut ↕</th>
            <th>Téléphone</th>
            <th class="sortable" onclick="sortBy('date_creation')">Créé le ↕</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="usersTableBody"></tbody>
      </table>
      <div class="pagination">
        <div class="pagination-info" id="paginationInfo"></div>
        <div class="pagination-btns" id="paginationBtns"></div>
      </div>
    </div>
  </div>

<!-- MODAL CREATE/EDIT -->
<div class="modal-overlay" id="formModal" onclick="closeOnOverlay(event,'formModal')">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modalTitle">Nouvel utilisateur</div>
      <button class="btn-icon" onclick="closeModal('formModal')">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="form-row full" style="margin-bottom:20px">
        <div class="form-group">
          <label class="form-label">Photo de profil</label>
          <div class="avatar-upload">
            <div class="avatar-preview" id="avatarPreview" onclick="document.getElementById('avatarInput').click()">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <input type="file" id="avatarInput" accept="image/*" style="display:none" onchange="previewAvatar(event)">
            <div class="avatar-actions">
              <button class="avatar-btn" onclick="document.getElementById('avatarInput').click()">Choisir une image</button>
              <button class="avatar-btn" style="color:var(--red)" onclick="resetAvatar()">Supprimer</button>
              <div class="avatar-hint">JPG, PNG — max 2MB</div>
            </div>
          </div>
        </div>
      </div>

      <div class="section-divider"><span>Informations personnelles</span></div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Prénom <span class="required">*</span></label>
          <input class="form-input" id="f-prenom" type="text" placeholder="Ex: Mohammed" oninput="Validator.filterTextOnly(this); validateField('prenom')">
          <div class="form-error" id="e-prenom" style="display:none">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span></span>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Nom <span class="required">*</span></label>
          <input class="form-input" id="f-nom" type="text" placeholder="Ex: Ben Salem" oninput="Validator.filterTextOnly(this); validateField('nom')">
          <div class="form-error" id="e-nom" style="display:none">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span></span>
          </div>
        </div>
      </div>

      <div class="form-row full">
        <div class="form-group">
          <label class="form-label">Email <span class="required">*</span></label>
          <input class="form-input" id="f-email" type="text" placeholder="exemple@email.com" oninput="validateField('email')">
          <div class="form-error" id="e-email" style="display:none">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span></span>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Téléphone <span class="required">*</span></label>
          <input class="form-input" id="f-telephone" type="text" placeholder="8 chiffres" maxlength="8" oninput="Validator.filterPhone(this); validateField('telephone')">
          <div class="form-hint">Exactement 8 chiffres</div>
          <div class="form-error" id="e-telephone" style="display:none">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span></span>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Date de naissance</label>
          <input class="form-input datepicker-input" id="f-dob" type="text" placeholder="Cliquez pour choisir">
          <div class="form-error" id="e-dob" style="display:none">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span></span>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Rôle <span class="required">*</span></label>
          <select class="form-select" id="f-role" onchange="onRoleChange()">
            <option value="">Sélectionner un rôle</option>
            <option value="Patient">Patient</option>
            <option value="Professionnel">Professionnel de santé</option>
            <option value="Administrateur">Administrateur</option>
          </select>
          <div class="form-error" id="e-role" style="display:none">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span></span>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Statut <span class="required">*</span></label>
          <select class="form-select" id="f-statut">
            <option value="Actif">Actif</option>
            <option value="Inactif">Inactif</option>
            <option value="Suspendu">Suspendu</option>
            <option value="En attente">En attente</option>
          </select>
        </div>
      </div>

      <!-- PRO FIELDS -->
      <div id="pro-fields" style="display:none">
        <div class="section-divider"><span>Professionnel de santé</span></div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Spécialité <span class="required">*</span></label>
            <select class="form-select" id="f-specialite">
              <option value="">Choisir</option>
              <option>Médecine générale</option><option>Cardiologie</option><option>Dermatologie</option>
              <option>Gynécologie</option><option>Neurologie</option><option>Ophtalmologie</option>
              <option>Pédiatrie</option><option>Radiologie</option><option>Chirurgie</option><option>Pharmacie</option>
            </select>
            <div class="form-error" id="e-specialite" style="display:none">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span></span>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">N° Ordre <span class="required">*</span></label>
            <input class="form-input" id="f-ordre" type="text" placeholder="Ex: TN-MED-12345" oninput="validateField('ordre')">
            <div class="form-error" id="e-ordre" style="display:none">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span></span>
            </div>
          </div>
        </div>
        <div class="form-row full">
          <div class="form-group">
            <label class="form-label">Biographie</label>
            <textarea class="form-textarea" id="f-bio" placeholder="Décrivez l'expertise du professionnel..."></textarea>
            <div class="form-hint" id="bio-count">0 / 500 caractères</div>
          </div>
        </div>
      </div>

      <!-- PATIENT FIELDS -->
      <div id="patient-fields" style="display:none">
        <div class="section-divider"><span>Informations patient</span></div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Sexe</label>
            <select class="form-select" id="f-sexe">
              <option value="">Choisir</option>
              <option value="M">Masculin</option>
              <option value="F">Féminin</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Groupe sanguin</label>
            <select class="form-select" id="f-groupe-sanguin">
              <option value="">Inconnu</option>
              <option>A+</option><option>A-</option>
              <option>B+</option><option>B-</option>
              <option>AB+</option><option>AB-</option>
              <option>O+</option><option>O-</option>
            </select>
          </div>
        </div>
        <div class="form-row full">
          <div class="form-group">
            <label class="form-label">Adresse</label>
            <input class="form-input" id="f-adresse" type="text" placeholder="Adresse complète">
          </div>
        </div>
      </div>

      <div class="section-divider"><span>Sécurité</span></div>

      <div class="form-row" id="password-row">
        <div class="form-group">
          <label class="form-label">Mot de passe <span class="required" id="pw-required">*</span></label>
          <div class="password-wrapper">
            <input class="form-input" id="f-password" type="password" placeholder="Min. 8 caractères" oninput="validatePassword()">
            <button class="password-toggle" type="button" onclick="togglePw('f-password',this)" tabindex="-1">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
          <div class="strength-bar"><div class="strength-fill" id="strength-fill" style="width:0%"></div></div>
          <div class="strength-text" id="strength-text"></div>
          <div class="form-error" id="e-password" style="display:none">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span></span>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Confirmer <span class="required" id="cpw-required">*</span></label>
          <div class="password-wrapper">
            <input class="form-input" id="f-confirm" type="password" placeholder="Répéter le mot de passe" oninput="validateConfirm()">
            <button class="password-toggle" type="button" onclick="togglePw('f-confirm',this)" tabindex="-1">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
          <div class="form-error" id="e-confirm" style="display:none">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span></span>
          </div>
        </div>
      </div>
      <div id="edit-pw-hint" style="display:none">
        <div class="form-hint" style="margin-bottom:12px">Laissez vide pour conserver l'ancien mot de passe.</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('formModal')">Annuler</button>
      <button class="btn btn-primary" id="submitBtn" onclick="submitForm()">Créer l'utilisateur</button>
    </div>
  </div>
</div>

<!-- MODAL VIEW -->
<div class="modal-overlay" id="viewModal" onclick="closeOnOverlay(event,'viewModal')">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Détails utilisateur</div>
      <button class="btn-icon" onclick="closeModal('viewModal')">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal-body" id="viewModalBody"></div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('viewModal')">Fermer</button>
      <button class="btn btn-primary" id="viewEditBtn">Modifier</button>
    </div>
  </div>
</div>

<!-- MODAL DELETE -->
<div class="modal-overlay" id="deleteModal" onclick="closeOnOverlay(event,'deleteModal')">
  <div class="modal" style="width:420px">
    <div class="modal-body" style="padding:32px 24px">
      <div class="delete-icon">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
      </div>
      <div class="delete-title">Supprimer l'utilisateur</div>
      <div class="delete-desc">Êtes-vous sûr de vouloir supprimer <span class="delete-name" id="deleteUserName"></span> ? Cette action est irréversible.</div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('deleteModal')">Annuler</button>
      <button class="btn btn-danger" onclick="confirmDelete()">Supprimer définitivement</button>
    </div>
  </div>
</div>
