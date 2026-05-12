<!-- ===== PAGE: MOT DE PASSE OUBLIÉ — Étape 1 ===== -->
<div id="page-forgot" class="page active">
  <div class="auth-wrapper">
    <div class="auth-left">
      <div class="auth-brand">
        <img src="/medilink_medicament/MediLink/public/img/logo.png" alt="MediLink" class="auth-brand-img">
      </div>
      <h2 class="auth-headline">Mot de passe<br><em>oublié ?</em></h2>
      <p class="auth-desc">Saisissez votre adresse email et nous vous enverrons un code de vérification pour réinitialiser votre mot de passe.</p>
      <div class="auth-features">
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>Code valable 15 minutes</div>
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>Envoyé sur votre email</div>
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>Procédure sécurisée</div>
      </div>
    </div>
    <div class="auth-right">
      <div class="auth-form-card">
        <h1 class="auth-form-title">Réinitialisation</h1>
        <p class="auth-form-sub">Retour à la <a href="index.php?page=login">connexion</a></p>

        <?php if (!empty($errors['global'])): ?>
        <div class="form-error-global">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span><?= htmlspecialchars($errors['global']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (!empty($flash) && $flash['type'] === 'info'): ?>
        <div class="form-success-global">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span><?= htmlspecialchars($flash['message']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (!empty($devCode)): ?>
        <div style="background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px">
          <strong style="color:#92400e">Mode DEV (SMTP non configuré)</strong><br>
          <span style="color:#78350f">Votre code : <strong style="font-size:18px;letter-spacing:4px;color:#b45309"><?= htmlspecialchars($devCode) ?></strong></span>
        </div>
        <?php endif; ?>

        <form id="forgotForm" method="POST" action="index.php?action=forgot_password" novalidate>
          <div class="form-group">
            <label class="form-label">Adresse email <span class="req">*</span></label>
            <div class="input-wrapper">
              <span class="input-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>
              <input class="form-input with-icon" id="fp-email" name="email" type="text" placeholder="votre@email.com" oninput="clearFieldError('fp-email')" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-error hidden" id="fpe-email"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span><?= htmlspecialchars($errors['email'] ?? '') ?></span></div>
          </div>

          <button type="button" class="btn btn-primary btn-block btn-lg" onclick="doForgotPassword()">Envoyer le code</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function doForgotPassword() {
  const email = document.getElementById('fp-email').value.trim();
  let valid = true;

  if (!email) {
    Validator.showFieldError('fp-email', 'fpe-email', 'L\'email est obligatoire.');
    valid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    Validator.showFieldError('fp-email', 'fpe-email', 'Format d\'email invalide.');
    valid = false;
  }

  if (valid) document.getElementById('forgotForm').submit();
}
</script>
