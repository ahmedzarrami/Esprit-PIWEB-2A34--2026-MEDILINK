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

<!-- ===== PAGE: PROFILE ===== -->
<div id="page-profile" class="page active">
  <div class="profile-wrapper">
    <div class="profile-header">
      <div class="profile-avatar-big" onclick="document.getElementById('pAvatarInput').click()">
        <span id="profileInitials"><?= $initials ?></span>
        <div class="edit-overlay"><svg viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>
      </div>
      <input type="file" id="pAvatarInput" accept="image/*" style="display:none" onchange="previewProfileAvatar(event)">
      <div class="profile-header-info">
        <h2 id="profileName"><?= $prenom ?> <?= $nom ?></h2>
        <p id="profileEmail"><?= $email ?></p>
        <span class="profile-badge" id="profileRole">
          <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          <?= htmlspecialchars($role) ?>
        </span>
      </div>
      <div style="margin-left:auto">
        <span class="tag tag-active">Compte actif</span>
      </div>
    </div>

    <div class="profile-tabs">
      <div class="profile-tab active" onclick="showProfileTab('info',this)">Informations</div>
      <div class="profile-tab" onclick="showProfileTab('security',this)">Sécurité</div>
      <div class="profile-tab" onclick="showProfileTab('rdv',this)">Rendez-vous</div>
    </div>

    <!-- TAB: INFO -->
    <div id="ptab-info">
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
            <input class="form-input" id="p-tel" name="telephone" type="text" value="<?= $tel ?>" maxlength="8" oninput="Validator.filterPhone(this); clearFieldError('p-tel')">
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
          <button class="btn btn-primary btn-sm" style="margin-top:14px" onclick="toast('Module rendez-vous disponible bientôt','info')">Prendre un rendez-vous</button>
        </div>
      </div>
    </div>
  </div>
</div>
