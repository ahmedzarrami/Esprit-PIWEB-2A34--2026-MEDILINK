<!-- ===== PAGE: LOGIN ===== -->
<div id="page-login" class="page active">
  <div class="auth-wrapper">
    <div class="auth-left">
      <div class="auth-brand">
        <span style="font-size:26px;font-weight:800;color:#fff;letter-spacing:-0.5px">Medi<span style="opacity:.75">Link</span></span>
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
            <a href="index.php?page=forgot_password" style="font-size:12px;color:var(--blue);font-weight:400">Mot de passe oublié ?</a>
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
        <div style="display:grid;grid-template-columns:1fr;gap:10px">
          <!-- Bouton Reconnaissance Faciale -->
          <button class="btn btn-outline face-login-btn" onclick="openFaceLoginModal()" title="Se connecter par reconnaissance faciale" style="display:flex;align-items:center;justify-content:center;gap:10px;padding:12px">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
              <circle cx="12" cy="10" r="3"/>
              <path d="M9 16c0-1.7 1.34-3 3-3s3 1.3 3 3"/>
            </svg>
            Connexion par reconnaissance faciale
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ===== MODAL: RECONNAISSANCE FACIALE ===== -->
<div id="faceLoginModal" class="face-modal-overlay hidden">
  <div class="face-modal">
    <div class="face-modal-header">
      <h3>Connexion par reconnaissance faciale</h3>
      <button class="face-modal-close" onclick="closeFaceModal()">&times;</button>
    </div>
    <div class="face-modal-body">
      <div id="faceLoginStatus" class="face-status face-status-info">
        <span id="faceLoginStatusText">Chargement des modèles IA...</span>
      </div>
      <div class="face-video-wrap">
        <video id="faceLoginVideo" autoplay muted playsinline class="face-video"></video>
        <canvas id="faceLoginCanvas" class="face-canvas"></canvas>
        <div id="faceLoginOverlay" class="face-scan-ring hidden"></div>
      </div>
      <div class="face-modal-actions">
        <button id="faceLoginCaptureBtn" class="btn btn-primary btn-block" onclick="captureFaceLogin()" disabled>
          Analyser mon visage
        </button>
        <button class="btn btn-outline btn-block" onclick="closeFaceModal()" style="margin-top:8px">Annuler</button>
      </div>
    </div>
  </div>
</div>
