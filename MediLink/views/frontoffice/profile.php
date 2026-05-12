<?php
// Données du profil utilisateur
$user = $profileData ?? [];
$prenom = htmlspecialchars($user['prenom'] ?? '');
$nom    = htmlspecialchars($user['nom'] ?? '');
$email  = htmlspecialchars($user['email'] ?? '');
$tel    = htmlspecialchars($user['telephone'] ?? '');
$dob    = htmlspecialchars($user['date_naissance'] ?? '');
$sexe   = $user['sexe'] ?? '';
$gs     = htmlspecialchars($user['groupe_sanguin'] ?? 'A+');
$adresse= htmlspecialchars($user['adresse'] ?? '');
$role   = $user['role'] ?? 'Patient';
$initials = strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
?>

<style>
.profile-layout{display:grid;grid-template-columns:260px 1fr;gap:24px;max-width:1100px;margin:32px auto;padding:0 20px}
.profile-sidebar{display:flex;flex-direction:column;gap:16px}
.profile-sidebar-card{background:#fff;border-radius:16px;border:1px solid #e8ecf0;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.05)}
.sidebar-profile-head{background:linear-gradient(135deg,#1a56db,#0ea5e9);padding:24px;text-align:center}
.sidebar-avatar{width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,0.25);border:3px solid rgba(255,255,255,0.5);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:#fff;margin:0 auto 12px;cursor:pointer;position:relative;overflow:hidden;transition:.2s}
.sidebar-avatar:hover{background:rgba(255,255,255,0.35)}
.sidebar-avatar .edit-overlay{position:absolute;inset:0;background:rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;opacity:0;transition:.2s}
.sidebar-avatar:hover .edit-overlay{opacity:1}
.sidebar-name{font-size:15px;font-weight:700;color:#fff;margin-bottom:4px}
.sidebar-email{font-size:12px;color:rgba(255,255,255,0.75);margin-bottom:10px;word-break:break-all}
.sidebar-role-badge{display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);border-radius:20px;padding:4px 12px;font-size:11px;font-weight:600;color:#fff}
.sidebar-status{display:flex;align-items:center;justify-content:center;gap:6px;margin-top:10px;font-size:11px;color:rgba(255,255,255,0.8)}
.sidebar-status-dot{width:7px;height:7px;border-radius:50%;background:#4ade80}
.sidebar-nav{padding:8px 0}
.sidebar-nav-item{display:flex;align-items:center;gap:12px;padding:12px 20px;cursor:pointer;transition:.15s;color:#64748b;font-size:14px;font-weight:500;border-left:3px solid transparent;user-select:none}
.sidebar-nav-item:hover{background:#f8fafc;color:#1e293b}
.sidebar-nav-item.active{background:#eff6ff;color:#1a56db;border-left-color:#1a56db;font-weight:600}
.sidebar-nav-item svg{flex-shrink:0;opacity:.7}
.sidebar-nav-item.active svg{opacity:1}
.sidebar-nav-divider{height:1px;background:#f1f5f9;margin:4px 0}
.sidebar-logout{display:flex;align-items:center;gap:12px;padding:12px 20px;cursor:pointer;color:#ef4444;font-size:14px;font-weight:500;transition:.15s}
.sidebar-logout:hover{background:#fff5f5}
.profile-main{min-width:0}
@media(max-width:768px){.profile-layout{grid-template-columns:1fr}.profile-sidebar{order:2}.profile-main{order:1}}
</style>

<!-- ===== PAGE: PROFILE ===== -->
<div id="page-profile" class="page active">
  <div class="profile-layout">

    <!-- SIDEBAR -->
    <aside class="profile-sidebar">
      <div class="profile-sidebar-card">
        <div class="sidebar-profile-head">
          <div class="sidebar-avatar" onclick="document.getElementById('pAvatarInput').click()">
            <span id="profileInitials"><?= $initials ?></span>
            <div class="edit-overlay"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M15.232 5.232l3.536 3.536M16.732 3.732a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></div>
          </div>
          <input type="file" id="pAvatarInput" accept="image/*" style="display:none" onchange="previewProfileAvatar(event)">
          <div class="sidebar-name" id="profileName"><?= $prenom ?> <?= $nom ?></div>
          <div class="sidebar-email" id="profileEmail"><?= $email ?></div>
          <div class="sidebar-role-badge" id="profileRole">
            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <?= htmlspecialchars($role) ?>
          </div>
          <div class="sidebar-status">
            <div class="sidebar-status-dot"></div> Compte actif
          </div>
        </div>

        <nav class="sidebar-nav">
          <div class="sidebar-nav-item active" data-tab="info">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Informations personnelles
          </div>
          <div class="sidebar-nav-item" data-tab="security">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Sécurité
          </div>
          <div class="sidebar-nav-item" data-tab="biometric">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/><circle cx="12" cy="10" r="3" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 16c0-1.7 1.34-3 3-3s3 1.3 3 3"/></svg>
            Biométrie
          </div>
          <div class="sidebar-nav-item" data-tab="rdv">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Rendez-vous
          </div>
          <div class="sidebar-nav-divider"></div>
          <a class="sidebar-logout" href="index.php?action=logout">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Déconnexion
          </a>
        </nav>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="profile-main">

    <!-- TAB: INFO -->
    <div id="ptab-info" style="animation:fadeIn .2s ease">
      <div class="profile-card">
        <div class="profile-card-title">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          Informations personnelles
        </div>
        <form id="profileForm" method="POST" action="index.php?action=update_profile" novalidate>
        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label">Prénom <span class="req">*</span></label>
            <input class="form-input" id="p-prenom" name="prenom" type="text" value="<?= $prenom ?>" oninput="Validator.filterTextOnly(this); clearFieldError('p-prenom')">
            <div class="form-error hidden" id="pe-prenom"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
          </div>
          <div class="form-group">
            <label class="form-label">Nom <span class="req">*</span></label>
            <input class="form-input" id="p-nom" name="nom" type="text" value="<?= $nom ?>" oninput="Validator.filterTextOnly(this); clearFieldError('p-nom')">
            <div class="form-error hidden" id="pe-nom"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Email <span class="req">*</span></label>
          <input class="form-input" id="p-email" name="email" type="text" value="<?= $email ?>" oninput="clearFieldError('p-email')">
          <div class="form-error hidden" id="pe-email"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
        </div>
        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label">Téléphone <span class="req">*</span></label>
            <input class="form-input" id="p-tel" name="telephone" type="text" value="<?= $tel ?>" oninput="Validator.filterPhone(this); clearFieldError('p-tel')">
            <div class="form-hint">Exactement 8 chiffres</div>
            <div class="form-error hidden" id="pe-tel"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
          </div>
          <div class="form-group">
            <label class="form-label">Date de naissance</label>
            <input class="form-input datepicker-input" id="p-dob" name="date_naissance" type="text" placeholder="Cliquez pour choisir" value="<?= $dob ?>">
          </div>
        </div>
        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label">Sexe</label>
            <select class="form-select" id="p-sexe" name="sexe">
              <option value="F" <?= $sexe === 'F' ? 'selected' : '' ?>>Féminin</option>
              <option value="M" <?= $sexe === 'M' ? 'selected' : '' ?>>Masculin</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Groupe sanguin</label>
            <select class="form-select" id="p-gs" name="groupe_sanguin">
              <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g): ?>
              <option <?= $gs === $g ? 'selected' : '' ?>><?= $g ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Adresse</label>
          <input class="form-input" id="p-adresse" name="adresse" type="text" value="<?= $adresse ?>">
        </div>
        <div class="save-bar">
          <a class="btn btn-outline btn-sm" href="index.php?page=profile">Annuler</a>
          <button type="button" class="btn btn-primary btn-sm" onclick="saveProfile()">Enregistrer les modifications</button>
        </div>
        </form>
      </div>
    </div>

    <!-- TAB: SECURITY -->
    <div id="ptab-security" class="hidden">
      <div class="profile-card">
        <div class="profile-card-title">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          Modifier le mot de passe
        </div>
        <form id="passwordForm" method="POST" action="index.php?action=change_password" novalidate>
        <div class="form-group">
          <label class="form-label">Mot de passe actuel <span class="req">*</span></label>
          <div class="pw-wrap">
            <input class="form-input" id="p-oldpw" name="old_password" type="password" placeholder="Votre mot de passe actuel" oninput="clearFieldError('p-oldpw')">
            <button class="pw-toggle" type="button" onclick="togglePw2('p-oldpw',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
          </div>
          <div class="form-error hidden" id="pe-oldpw"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
        </div>
        <div class="form-group">
          <label class="form-label">Nouveau mot de passe <span class="req">*</span></label>
          <div class="pw-wrap">
            <input class="form-input" id="p-newpw" name="new_password" type="password" placeholder="Min. 8 caractères" oninput="pPwStrength()">
            <button class="pw-toggle" type="button" onclick="togglePw2('p-newpw',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
          </div>
          <div class="strength-bar"><div class="strength-fill" id="p-sf" style="width:0"></div></div>
          <div class="strength-label" id="p-st" style="color:var(--text3)"></div>
          <div class="form-error hidden" id="pe-newpw"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
        </div>
        <div class="form-group">
          <label class="form-label">Confirmer le nouveau mot de passe <span class="req">*</span></label>
          <div class="pw-wrap">
            <input class="form-input" id="p-cpw" name="confirm_password" type="password" placeholder="Répétez le nouveau mot de passe" oninput="clearFieldError('p-cpw')">
            <button class="pw-toggle" type="button" onclick="togglePw2('p-cpw',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
          </div>
          <div class="form-error hidden" id="pe-cpw"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
        </div>
        <div class="save-bar">
          <button type="button" class="btn btn-primary btn-sm" onclick="savePassword()">Mettre à jour le mot de passe</button>
        </div>
        </form>
      </div>
      <div class="profile-card" style="border-color:#fca5a5">
        <div class="profile-card-title" style="color:var(--red)">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Zone dangereuse
        </div>
        <p style="font-size:13px;color:var(--text2);margin-bottom:16px">La suppression de votre compte est irréversible. Toutes vos données seront définitivement effacées.</p>
        <button class="btn btn-danger btn-sm" onclick="toast('Contactez le support pour supprimer votre compte','error')">Supprimer mon compte</button>
      </div>
    </div>

    <!-- TAB: BIOMÉTRIE (Reconnaissance Faciale) -->
    <div id="ptab-biometric" class="hidden">
      <?php
        require_once __DIR__ . '/../../controllers/FaceAuthController.php';
        $hasFace = FaceAuthController::aDescripteur((int)($_SESSION['user_id'] ?? 0));
      ?>
      <div class="profile-card">
        <div class="profile-card-title">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/><circle cx="12" cy="10" r="3" stroke-width="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 16c0-1.7 1.34-3 3-3s3 1.3 3 3"/></svg>
          Reconnaissance faciale
        </div>

        <div style="margin-bottom:20px">
          <?php if ($hasFace): ?>
          <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;margin-bottom:16px">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span style="font-size:14px;color:#15803d;font-weight:500">Reconnaissance faciale activée</span>
          </div>
          <?php else: ?>
          <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:#fafafa;border:1px solid #e2e8f0;border-radius:8px;margin-bottom:16px">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span style="font-size:14px;color:#64748b">Reconnaissance faciale non configurée</span>
          </div>
          <?php endif; ?>

          <p style="font-size:13px;color:var(--text2);line-height:1.6;margin-bottom:20px">
            Activez la connexion par reconnaissance faciale pour accéder à votre compte sans saisir de mot de passe.
            Votre descripteur facial est stocké de façon sécurisée et ne quitte jamais nos serveurs.
          </p>
        </div>

        <!-- Statut de chargement -->
        <div id="faceEnrollStatus" class="face-status face-status-info hidden">
          <span id="faceEnrollStatusText"></span>
        </div>

        <!-- Vidéo camera -->
        <div id="faceEnrollCameraWrap" class="face-video-wrap hidden">
          <video id="faceEnrollVideo" autoplay muted playsinline class="face-video"></video>
          <canvas id="faceEnrollCanvas" class="face-canvas"></canvas>
        </div>

        <div class="save-bar" style="flex-wrap:wrap;gap:10px">
          <button id="faceEnrollStartBtn" type="button" class="btn btn-primary btn-sm" onclick="startFaceEnrollment()">
            <?= $hasFace ? 'Mettre à jour mon visage' : 'Configurer la reconnaissance faciale' ?>
          </button>
          <button id="faceEnrollCaptureBtn" type="button" class="btn btn-primary btn-sm hidden" onclick="captureFaceEnrollment()" disabled>
            Enregistrer ce visage
          </button>
          <button id="faceEnrollCancelBtn" type="button" class="btn btn-outline btn-sm hidden" onclick="cancelFaceEnrollment()">
            Annuler
          </button>
          <?php if ($hasFace): ?>
          <button type="button" class="btn btn-danger btn-sm" onclick="deleteFaceDescriptor()" style="margin-left:auto">
            Supprimer mon visage
          </button>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- TAB: RDV -->
    <div id="ptab-rdv" class="hidden">
      <div class="profile-card">
        <div class="profile-card-title">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          Mes rendez-vous
        </div>
        <div style="text-align:center;padding:32px 0;color:var(--text3)">
          <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="margin:0 auto 12px;display:block;opacity:.35"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <p style="font-size:14px">Aucun rendez-vous planifié</p>
          <a class="btn btn-primary btn-sm" style="margin-top:14px;display:inline-block" href="/medilink_medicament/MediLink/index.php?module=rdv">Prendre un rendez-vous</a>
        </div>
      </div>
    </div>

    </div><!-- /.profile-main -->
  </div><!-- /.profile-layout -->
</div><!-- /#page-profile -->

<script>
if (typeof window.showProfileTab !== 'function') {
  window.showProfileTab = function(name, el) {
    ['info','security','biometric','rdv'].forEach(function(t) {
      var panel = document.getElementById('ptab-' + t);
      if (panel) panel.classList.add('hidden');
    });
    var target = document.getElementById('ptab-' + name);
    if (target) target.classList.remove('hidden');
    document.querySelectorAll('.sidebar-nav-item').forEach(function(item) {
      item.classList.remove('active');
    });
    if (el) el.classList.add('active');
  };

  document.querySelectorAll('.sidebar-nav-item[data-tab]').forEach(function(item) {
    item.addEventListener('click', function() {
      var tab = item.getAttribute('data-tab');
      if (!tab) return;
      showProfileTab(tab, item);
    });
  });
}
</script>
