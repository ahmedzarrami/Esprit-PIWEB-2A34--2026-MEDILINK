<!-- ===== PAGE: REGISTER (WIZARD — Patient uniquement) ===== -->
<div id="page-register" class="page active">
  <div class="auth-wrapper">
    <div class="auth-left">
      <div class="auth-brand">
        <img src="assets/img/logo.png" alt="MediLink" class="auth-brand-img">
      </div>
      <h2 class="auth-headline">Rejoignez<br><em>notre communauté</em></h2>
      <p class="auth-desc">Créez votre compte patient en quelques minutes et accédez à tous nos services de santé digitale.</p>
      <div class="auth-features">
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>Inscription 100% gratuite</div>
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>Aucune carte bancaire requise</div>
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>Données protégées RGPD</div>
      </div>
    </div>
    <div class="auth-right">
      <div class="auth-form-card">
        <!-- STEP NAV (3 étapes) -->
        <div class="step-nav" id="stepNav">
          <div class="step-item">
            <div class="step-circle active" id="sc1">1</div>
            <div class="step-label active" id="sl1">Identité</div>
          </div>
          <div class="step-line" id="sl12"></div>
          <div class="step-item">
            <div class="step-circle" id="sc2">2</div>
            <div class="step-label" id="sl2">Sécurité</div>
          </div>
          <div class="step-line" id="sl23"></div>
          <div class="step-item">
            <div class="step-circle" id="sc3">3</div>
            <div class="step-label" id="sl3">Confirmation</div>
          </div>
        </div>

        <form id="registerForm" method="POST" action="index.php?action=register" novalidate>
        <input type="hidden" id="r-role" name="role" value="Patient">

        <!-- STEP 1: IDENTITY -->
        <div id="rstep1">
          <h2 class="auth-form-title">Créer votre compte patient</h2>
          <p class="auth-form-sub" style="margin-bottom:20px">Renseignez vos coordonnées personnelles</p>
          <div class="form-row-2">
            <div class="form-group">
              <label class="form-label">Prénom <span class="req">*</span></label>
              <input class="form-input" id="r-prenom" name="prenom" type="text" placeholder="Mohammed" oninput="Validator.filterTextOnly(this); clearFieldError('r-prenom')">
              <div class="form-error hidden" id="re-prenom"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
            </div>
            <div class="form-group">
              <label class="form-label">Nom <span class="req">*</span></label>
              <input class="form-input" id="r-nom" name="nom" type="text" placeholder="Ben Ali" oninput="Validator.filterTextOnly(this); clearFieldError('r-nom')">
              <div class="form-error hidden" id="re-nom"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Adresse email <span class="req">*</span></label>
            <div class="input-wrapper">
              <span class="input-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>
              <input class="form-input with-icon" id="r-email" name="email" type="text" placeholder="votre@email.com" oninput="clearFieldError('r-email')">
            </div>
            <div class="form-error hidden" id="re-email"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
          </div>
          <div class="form-row-2">
            <div class="form-group">
              <label class="form-label">Téléphone <span class="req">*</span></label>
              <input class="form-input" id="r-tel" name="telephone" type="text" placeholder="8 chiffres" maxlength="8" oninput="Validator.filterPhone(this); clearFieldError('r-tel')">
              <div class="form-hint">Exactement 8 chiffres</div>
              <div class="form-error hidden" id="re-tel"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
            </div>
            <div class="form-group">
              <label class="form-label">Date de naissance</label>
              <input class="form-input datepicker-input" id="r-dob" name="date_naissance" type="text" placeholder="Cliquez pour choisir">
              <div class="form-error hidden" id="re-dob"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
            </div>
          </div>
          <div style="display:flex;gap:10px;margin-top:8px">
            <button type="button" class="btn btn-primary btn-block btn-lg" onclick="gotoStep(2)">Continuer</button>
          </div>
          <div style="text-align:center;margin-top:14px;font-size:13px;color:var(--text3)">Déjà inscrit ? <a href="index.php?page=login" style="color:var(--blue);font-weight:600;text-decoration:none">Se connecter</a></div>
        </div>

        <!-- STEP 2: SECURITY -->
        <div id="rstep2" class="hidden">
          <h2 class="auth-form-title">Sécurisez votre compte</h2>
          <p class="auth-form-sub" style="margin-bottom:20px">Choisissez un mot de passe robuste</p>
          <div class="form-group">
            <label class="form-label">Mot de passe <span class="req">*</span></label>
            <div class="pw-wrap">
              <input class="form-input" id="r-pw" name="mot_de_passe" type="password" placeholder="Minimum 8 caractères" oninput="rPwStrength()">
              <button class="pw-toggle" type="button" onclick="togglePw2('r-pw',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
            </div>
            <div class="strength-bar"><div class="strength-fill" id="r-sf" style="width:0"></div></div>
            <div class="strength-label" id="r-st" style="color:var(--text3)"></div>
            <div class="form-error hidden" id="re-pw"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
          </div>
          <div class="form-group">
            <label class="form-label">Confirmer le mot de passe <span class="req">*</span></label>
            <div class="pw-wrap">
              <input class="form-input" id="r-cpw" name="confirm_mdp" type="password" placeholder="Répétez le mot de passe" oninput="clearFieldError('r-cpw')">
              <button class="pw-toggle" type="button" onclick="togglePw2('r-cpw',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
            </div>
            <div class="form-error hidden" id="re-cpw"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span></span></div>
          </div>
          <div class="form-group">
            <div class="checkbox-group" id="terms-group">
              <input type="checkbox" id="r-terms" name="terms" onchange="clearFieldError('r-terms')">
              <label for="r-terms">J'accepte les <a href="#">Conditions générales</a> et la <a href="#">Politique de confidentialité</a> de MediLink.</label>
            </div>
            <div class="form-error hidden" id="re-terms"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>Vous devez accepter les conditions</span></div>
          </div>
          <div style="display:flex;gap:10px;margin-top:8px">
            <button type="button" class="btn btn-outline btn-block" onclick="gotoStep(1)">Retour</button>
            <button type="button" class="btn btn-primary btn-block btn-lg" onclick="gotoStep(3)">Créer mon compte</button>
          </div>
        </div>

        <!-- STEP 3: SUCCESS -->
        <div id="rstep3" class="hidden" style="text-align:center;padding:16px 0">
          <div style="width:72px;height:72px;background:var(--green-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
            <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="var(--green)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          </div>
          <h2 style="font-size:22px;font-weight:700;margin-bottom:8px">Bienvenue sur MediLink !</h2>
          <p style="font-size:14px;color:var(--text2);line-height:1.6;margin-bottom:24px">Votre compte patient a été créé avec succès.</p>
          <a class="btn btn-primary btn-block btn-lg" href="index.php?page=profile">Accéder à mon espace</a>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>
