<!-- ===== PAGE: NOUVEAU MOT DE PASSE — Étape 3 ===== -->
<div id="page-reset-pw" class="page active">
  <div class="auth-wrapper">
    <div class="auth-left">
      <div class="auth-brand">
        <img src="/medilink_medicament/MediLink/public/img/logo.png" alt="MediLink" class="auth-brand-img">
      </div>
      <h2 class="auth-headline">Nouveau<br><em>mot de passe</em></h2>
      <p class="auth-desc">Choisissez un mot de passe solide d'au moins 8 caractères avec majuscule, chiffre ou symbole.</p>
      <div class="auth-features">
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>8 caractères minimum</div>
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>Majuscule, chiffre ou symbole</div>
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>Confirmation requise</div>
      </div>
    </div>
    <div class="auth-right">
      <div class="auth-form-card">
        <h1 class="auth-form-title">Nouveau mot de passe</h1>
        <p class="auth-form-sub">Pour : <strong><?= htmlspecialchars($_SESSION['reset_email'] ?? '') ?></strong></p>

        <?php if (!empty($errors['global'])): ?>
        <div class="form-error-global">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span><?= htmlspecialchars($errors['global']) ?></span>
        </div>
        <?php endif; ?>

        <form id="resetPwForm" method="POST" action="index.php?action=reset_password" novalidate>
          <input type="hidden" name="code" value="<?= htmlspecialchars($_SESSION['reset_code_used'] ?? '') ?>">

          <div class="form-group">
            <label class="form-label">Nouveau mot de passe <span class="req">*</span></label>
            <div class="pw-wrap">
              <input class="form-input" id="rp-newpw" name="nouveau_mdp" type="password" placeholder="Min. 8 caractères" oninput="rpPwStrength()">
              <button class="pw-toggle" type="button" onclick="togglePw2('rp-newpw',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
            </div>
            <div class="strength-bar"><div class="strength-fill" id="rp-sf" style="width:0"></div></div>
            <div class="strength-label" id="rp-st" style="color:var(--text3)"></div>
            <div class="form-error hidden" id="rpe-newpw"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span><?= htmlspecialchars($errors['nouveau_mdp'] ?? '') ?></span></div>
          </div>

          <div class="form-group">
            <label class="form-label">Confirmer le mot de passe <span class="req">*</span></label>
            <div class="pw-wrap">
              <input class="form-input" id="rp-cpw" name="confirm_mdp" type="password" placeholder="Répétez le mot de passe" oninput="clearFieldError('rp-cpw')">
              <button class="pw-toggle" type="button" onclick="togglePw2('rp-cpw',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
            </div>
            <div class="form-error hidden" id="rpe-cpw"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span><?= htmlspecialchars($errors['confirm_mdp'] ?? '') ?></span></div>
          </div>

          <button type="button" class="btn btn-primary btn-block btn-lg" onclick="doResetPassword()">Enregistrer le nouveau mot de passe</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php if (!empty($errors['nouveau_mdp']) || !empty($errors['confirm_mdp'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  <?php if (!empty($errors['nouveau_mdp'])): ?>
  document.getElementById('rpe-newpw').classList.remove('hidden');
  <?php endif; ?>
  <?php if (!empty($errors['confirm_mdp'])): ?>
  document.getElementById('rpe-cpw').classList.remove('hidden');
  <?php endif; ?>
});
</script>
<?php endif; ?>

<script>
function rpPwStrength() {
  const v = document.getElementById('rp-newpw').value;
  let score = 0;
  if (v.length >= 8) score++;
  if (/[A-Z]/.test(v)) score++;
  if (/[0-9]/.test(v)) score++;
  if (/[^A-Za-z0-9]/.test(v)) score++;
  const labels = ['', 'Faible', 'Moyen', 'Fort', 'Très fort'];
  const colors = ['', '#ef4444', '#f59e0b', '#10b981', '#0ea5e9'];
  const fill   = document.getElementById('rp-sf');
  const label  = document.getElementById('rp-st');
  fill.style.width    = (score * 25) + '%';
  fill.style.background = colors[score] || '#e2e8f0';
  label.textContent   = v.length ? (labels[score] || '') : '';
  label.style.color   = colors[score] || 'var(--text3)';
  clearFieldError('rp-newpw');
}

function doResetPassword() {
  const newPw  = document.getElementById('rp-newpw').value;
  const confirm = document.getElementById('rp-cpw').value;
  let valid = true;

  if (!newPw) {
    Validator.showFieldError('rp-newpw', 'rpe-newpw', 'Le mot de passe est obligatoire.');
    valid = false;
  } else if (newPw.length < 8) {
    Validator.showFieldError('rp-newpw', 'rpe-newpw', 'Minimum 8 caractères requis.');
    valid = false;
  } else {
    let score = 0;
    if (/[A-Z]/.test(newPw)) score++;
    if (/[0-9]/.test(newPw)) score++;
    if (/[^A-Za-z0-9]/.test(newPw)) score++;
    if (score < 1) {
      Validator.showFieldError('rp-newpw', 'rpe-newpw', 'Mot de passe trop faible.');
      valid = false;
    }
  }

  if (newPw !== confirm) {
    Validator.showFieldError('rp-cpw', 'rpe-cpw', 'Les mots de passe ne correspondent pas.');
    valid = false;
  }

  if (valid) document.getElementById('resetPwForm').submit();
}
</script>
