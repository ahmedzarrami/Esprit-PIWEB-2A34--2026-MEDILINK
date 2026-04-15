<!-- ===== PAGE: LOGIN ===== -->
<div id="page-login" class="page active">
  <div class="auth-wrapper">
    <div class="auth-left">
      <div class="auth-brand">
        <img src="assets/img/logo.png" alt="MediLink" class="auth-brand-img">
      </div>
      <h2 class="auth-headline">Bon retour<br><em>sur MediLink</em></h2>
      <p class="auth-desc">Accédez à votre espace personnel pour gérer vos rendez-vous et votre santé.</p>
      <div class="auth-features">
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>Accès 24h/24 à votre dossier</div>
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>Notifications de rendez-vous</div>
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>Données sécurisées et chiffrées</div>
      </div>
    </div>
    <div class="auth-right">
      <div class="auth-form-card">
        <h1 class="auth-form-title">Connexion</h1>
        <p class="auth-form-sub">Pas encore inscrit ? <a href="index.php?page=register">Créer un compte</a></p>

        <?php if (!empty($errors['global'])): ?>
        <div class="form-error-global" id="login-error">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span id="login-error-msg"><?= htmlspecialchars($errors['global']) ?></span>
        </div>
        <?php else: ?>
        <div class="form-error-global hidden" id="login-error">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span id="login-error-msg"></span>
        </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="index.php?action=login" novalidate>
        <div class="form-group">
          <label class="form-label">Adresse email <span class="req">*</span></label>
          <div class="input-wrapper">
            <span class="input-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>
            <input class="form-input with-icon" id="l-email" name="email" type="text" placeholder="votre@email.com" oninput="clearFieldError('l-email')" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
          <div class="form-error hidden" id="le-email"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
        </div>

        <div class="form-group">
          <label class="form-label" style="display:flex;justify-content:space-between">
            <span>Mot de passe <span class="req">*</span></span>
            <a href="#" style="font-size:12px;color:var(--blue);font-weight:400">Mot de passe oublié ?</a>
          </label>
          <div class="pw-wrap">
            <input class="form-input" id="l-password" name="mot_de_passe" type="password" placeholder="Votre mot de passe" oninput="clearFieldError('l-password')">
            <button class="pw-toggle" type="button" onclick="togglePw2('l-password',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
          </div>
          <div class="form-error hidden" id="le-password"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
        </div>

        <div style="margin-bottom:20px">
          <div class="checkbox-group">
            <input type="checkbox" id="l-remember" name="remember">
            <label for="l-remember">Se souvenir de moi pendant 30 jours</label>
          </div>
        </div>

        <button type="button" class="btn btn-primary btn-block btn-lg" onclick="doLogin()">Se connecter</button>
        </form>

        <div class="divider-or">ou continuer avec</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
          <button class="btn btn-outline" onclick="toast('Connexion sociale bientôt disponible','info')">
            <svg width="18" height="18" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            Google
          </button>
          <button class="btn btn-outline" onclick="toast('Connexion Facebook bientôt disponible','info')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            Facebook
          </button>
        </div>
        <div style="margin-top:16px;text-align:center;font-size:12px;color:var(--text3)">
          Compte de démo : <strong>patient@medilink.tn</strong> / <strong>Pass@1234</strong>
        </div>
      </div>
    </div>
  </div>
</div>
