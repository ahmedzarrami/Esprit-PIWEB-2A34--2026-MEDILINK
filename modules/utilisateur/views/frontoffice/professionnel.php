<?php
$user     = $profileData ?? [];
$prenom   = htmlspecialchars($user['prenom'] ?? '');
$nom      = htmlspecialchars($user['nom'] ?? '');
$email    = htmlspecialchars($user['email'] ?? '');
$tel      = htmlspecialchars($user['telephone'] ?? '');
$spec     = htmlspecialchars($user['specialite'] ?? '');
$ordre    = htmlspecialchars($user['numero_ordre'] ?? '');
$bio      = htmlspecialchars($user['biographie'] ?? '');
$initials = strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));

$specialites = [
    'Médecine générale','Cardiologie','Dermatologie','Gynécologie','Neurologie',
    'Ophtalmologie','Orthopédie','Pédiatrie','Psychiatrie','Radiologie',
    'Rhumatologie','Urologie','Endocrinologie','Gastro-entérologie','Pneumologie',
    'ORL','Chirurgie générale','Anesthésiologie','Oncologie','Néphrologie',
];
?>

<!-- ===== PAGE: PROFESSIONNEL DASHBOARD ===== -->
<div id="page-professionnel" class="page active">
  <div class="profile-wrapper">

    <!-- En-tête profil -->
    <div class="profile-header pro-header">
      <div class="profile-avatar-big pro-avatar">
        <span><?= $initials ?></span>
      </div>
      <div class="profile-header-info">
        <h2><?= $prenom ?> <?= $nom ?></h2>
        <p><?= $email ?></p>
        <span class="profile-badge pro-badge">
          <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          Professionnel de santé
        </span>
        <?php if (!empty($spec)): ?>
        <span class="profile-badge" style="background:var(--teal-light);color:var(--teal);margin-left:6px">
          <?= $spec ?>
        </span>
        <?php endif; ?>
      </div>
      <div style="margin-left:auto;display:flex;flex-direction:column;align-items:flex-end;gap:8px">
        <span class="tag tag-active">Compte actif</span>
        <?php if (!empty($ordre)): ?>
        <span style="font-size:11px;color:var(--text3);font-weight:500">N° Ordre : <?= $ordre ?></span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="pro-stats-bar">
      <div class="pro-stat-item">
        <div class="pro-stat-num">0</div>
        <div class="pro-stat-label">Rendez-vous aujourd'hui</div>
      </div>
      <div class="pro-stat-item">
        <div class="pro-stat-num">0</div>
        <div class="pro-stat-label">En attente</div>
      </div>
      <div class="pro-stat-item">
        <div class="pro-stat-num">0</div>
        <div class="pro-stat-label">Total patients</div>
      </div>
      <div class="pro-stat-item">
        <div class="pro-stat-num">0</div>
        <div class="pro-stat-label">Ce mois</div>
      </div>
    </div>

    <!-- Onglets -->
    <div class="profile-tabs">
      <div class="profile-tab active" onclick="showProfileTab('pro-info', this)">Informations</div>
      <div class="profile-tab" onclick="showProfileTab('pro-agenda', this)">Agenda</div>
      <div class="profile-tab" onclick="showProfileTab('pro-patients', this)">Patients</div>
      <div class="profile-tab" onclick="showProfileTab('pro-security', this)">Sécurité</div>
    </div>

    <!-- TAB: INFORMATIONS -->
    <div id="ptab-pro-info">
      <div class="profile-card">
        <div class="profile-card-title">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          Informations personnelles
        </div>
        <form id="proProfileForm" method="POST" action="index.php?action=update_pro_profile" novalidate>
          <div class="form-row-2">
            <div class="form-group">
              <label class="form-label">Prénom <span class="req">*</span></label>
              <input class="form-input" id="pp-prenom" name="prenom" type="text" value="<?= $prenom ?>"
                     oninput="Validator.filterTextOnly(this); clearFieldError('pp-prenom')">
              <div class="form-error hidden" id="ppe-prenom">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span></span>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Nom <span class="req">*</span></label>
              <input class="form-input" id="pp-nom" name="nom" type="text" value="<?= $nom ?>"
                     oninput="Validator.filterTextOnly(this); clearFieldError('pp-nom')">
              <div class="form-error hidden" id="ppe-nom">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Email <span class="req">*</span></label>
            <input class="form-input" id="pp-email" name="email" type="text" value="<?= $email ?>"
                   oninput="clearFieldError('pp-email')">
            <div class="form-error hidden" id="ppe-email">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span></span>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Téléphone <span class="req">*</span></label>
            <input class="form-input" id="pp-tel" name="telephone" type="text" value="<?= $tel ?>"
                   oninput="Validator.filterPhone(this); clearFieldError('pp-tel')">
            <div class="form-hint">Exactement 8 chiffres</div>
            <div class="form-error hidden" id="ppe-tel">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span></span>
            </div>
          </div>
        </form>
      </div>

      <div class="profile-card">
        <div class="profile-card-title">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          Informations professionnelles
        </div>
        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label">Spécialité <span class="req">*</span></label>
            <select class="form-select" id="pp-spec" name="specialite" form="proProfileForm"
                    onchange="clearFieldError('pp-spec')">
              <option value="">— Choisir —</option>
              <?php foreach ($specialites as $s): ?>
              <option <?= $spec === $s ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
              <?php endforeach; ?>
            </select>
            <div class="form-error hidden" id="ppe-spec">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span></span>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Numéro d'ordre <span class="req">*</span></label>
            <input class="form-input" id="pp-ordre" name="numero_ordre" type="text"
                   value="<?= $ordre ?>" placeholder="Ex: TN-MED-12345" form="proProfileForm"
                   oninput="clearFieldError('pp-ordre')">
            <div class="form-hint">Format : TN-MED-XXXXX</div>
            <div class="form-error hidden" id="ppe-ordre">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span></span>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Biographie / Présentation</label>
          <textarea class="form-input" id="pp-bio" name="biographie" rows="4"
                    placeholder="Décrivez votre parcours, vos expertises, votre approche…"
                    form="proProfileForm" style="resize:vertical"><?= $bio ?></textarea>
        </div>
        <div class="save-bar">
          <a class="btn btn-outline btn-sm" href="index.php?page=professionnel">Annuler</a>
          <button type="submit" form="proProfileForm" class="btn btn-primary btn-sm">
            Enregistrer les modifications
          </button>
        </div>
      </div>
    </div>

    <!-- TAB: AGENDA -->
    <div id="ptab-pro-agenda" class="hidden">
      <div class="profile-card">
        <div class="profile-card-title">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          Mon agenda
        </div>
        <div class="pro-empty-state">
          <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <p>Aucun rendez-vous planifié</p>
          <span>Le module agenda sera disponible prochainement</span>
        </div>
      </div>
    </div>

    <!-- TAB: PATIENTS -->
    <div id="ptab-pro-patients" class="hidden">
      <div class="profile-card">
        <div class="profile-card-title">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          Mes patients
        </div>
        <div class="pro-empty-state">
          <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <p>Aucun patient pour l'instant</p>
          <span>Vos patients apparaîtront ici après le premier rendez-vous</span>
        </div>
      </div>
    </div>

    <!-- TAB: SÉCURITÉ -->
    <div id="ptab-pro-security" class="hidden">
      <div class="profile-card">
        <div class="profile-card-title">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          Modifier le mot de passe
        </div>
        <form id="proPasswordForm" method="POST" action="index.php?action=change_pro_password" novalidate>
          <div class="form-group">
            <label class="form-label">Mot de passe actuel <span class="req">*</span></label>
            <div class="pw-wrap">
              <input class="form-input" id="pp-oldpw" name="old_password" type="password"
                     placeholder="Votre mot de passe actuel" oninput="clearFieldError('pp-oldpw')">
              <button class="pw-toggle" type="button" onclick="togglePw2('pp-oldpw',this)">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </button>
            </div>
            <div class="form-error hidden" id="ppe-oldpw">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span></span>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Nouveau mot de passe <span class="req">*</span></label>
            <div class="pw-wrap">
              <input class="form-input" id="pp-newpw" name="new_password" type="password"
                     placeholder="Min. 8 caractères" oninput="pPwStrength2()">
              <button class="pw-toggle" type="button" onclick="togglePw2('pp-newpw',this)">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </button>
            </div>
            <div class="strength-bar"><div class="strength-fill" id="pp-sf" style="width:0"></div></div>
            <div class="strength-label" id="pp-st" style="color:var(--text3)"></div>
            <div class="form-error hidden" id="ppe-newpw">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span></span>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Confirmer le nouveau mot de passe <span class="req">*</span></label>
            <div class="pw-wrap">
              <input class="form-input" id="pp-cpw" name="confirm_password" type="password"
                     placeholder="Répétez le nouveau mot de passe" oninput="clearFieldError('pp-cpw')">
              <button class="pw-toggle" type="button" onclick="togglePw2('pp-cpw',this)">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </button>
            </div>
            <div class="form-error hidden" id="ppe-cpw">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span></span>
            </div>
          </div>
          <div class="save-bar">
            <button type="submit" class="btn btn-primary btn-sm">Mettre à jour le mot de passe</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<script>
function pPwStrength2() {
  const v = document.getElementById('pp-newpw').value;
  const sf = document.getElementById('pp-sf');
  const st = document.getElementById('pp-st');
  let score = 0;
  if (v.length >= 8) score++;
  if (/[A-Z]/.test(v)) score++;
  if (/[0-9]/.test(v)) score++;
  if (/[^A-Za-z0-9]/.test(v)) score++;
  const levels = [
    {w:'0%',   c:'var(--red)',   l:''},
    {w:'25%',  c:'var(--red)',   l:'Très faible'},
    {w:'50%',  c:'#f59e0b',     l:'Faible'},
    {w:'75%',  c:'#3b82f6',     l:'Moyen'},
    {w:'100%', c:'var(--green)', l:'Fort'},
  ];
  const lv = levels[score] || levels[0];
  sf.style.width = lv.w;
  sf.style.background = lv.c;
  st.textContent = lv.l;
  st.style.color = lv.c;
}
</script>
