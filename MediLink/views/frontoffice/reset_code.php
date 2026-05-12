<!-- ===== PAGE: VÉRIFICATION DU CODE — Étape 2 ===== -->
<div id="page-reset-code" class="page active">
  <div class="auth-wrapper">
    <div class="auth-left">
      <div class="auth-brand">
        <img src="/medilink_medicament/MediLink/public/img/logo.png" alt="MediLink" class="auth-brand-img">
      </div>
      <h2 class="auth-headline">Vérification<br><em>du code</em></h2>
      <p class="auth-desc">Un code à 6 chiffres a été envoyé à <strong><?= htmlspecialchars($_SESSION['reset_email'] ?? '') ?></strong>. Saisissez-le ci-dessous.</p>
      <div class="auth-features">
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>Vérifiez vos spams si nécessaire</div>
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>Code valable 15 minutes</div>
        <div class="auth-feature"><div class="auth-feature-dot"><svg viewBox="0 0 10 10"><path d="M2 5l2 2 4-4" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg></div>5 tentatives maximum</div>
      </div>
    </div>
    <div class="auth-right">
      <div class="auth-form-card">
        <h1 class="auth-form-title">Code de vérification</h1>
        <p class="auth-form-sub"><a href="index.php?page=forgot_password">Renvoyer un nouveau code</a></p>

        <?php if (!empty($errors['global'])): ?>
        <div class="form-error-global">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span><?= htmlspecialchars($errors['global']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (!empty($devCode)): ?>
        <div style="background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px">
          <strong style="color:#92400e">Mode DEV (SMTP non configuré)</strong><br>
          <span style="color:#78350f">Votre code : <strong style="font-size:18px;letter-spacing:4px;color:#b45309"><?= htmlspecialchars($devCode) ?></strong></span>
        </div>
        <?php endif; ?>

        <form id="codeForm" method="POST" action="index.php?action=verify_reset_code" novalidate>
          <div class="form-group">
            <label class="form-label">Code à 6 chiffres <span class="req">*</span></label>
            <div class="otp-inputs" id="otpWrap">
              <input class="otp-digit" id="otp0" type="text" maxlength="1" oninput="otpMove(this,0)" onkeydown="otpBack(event,0)" autocomplete="off">
              <input class="otp-digit" id="otp1" type="text" maxlength="1" oninput="otpMove(this,1)" onkeydown="otpBack(event,1)" autocomplete="off">
              <input class="otp-digit" id="otp2" type="text" maxlength="1" oninput="otpMove(this,2)" onkeydown="otpBack(event,2)" autocomplete="off">
              <input class="otp-digit" id="otp3" type="text" maxlength="1" oninput="otpMove(this,3)" onkeydown="otpBack(event,3)" autocomplete="off">
              <input class="otp-digit" id="otp4" type="text" maxlength="1" oninput="otpMove(this,4)" onkeydown="otpBack(event,4)" autocomplete="off">
              <input class="otp-digit" id="otp5" type="text" maxlength="1" oninput="otpMove(this,5)" onkeydown="otpBack(event,5)" autocomplete="off">
            </div>
            <input type="hidden" id="rc-code" name="code">
            <div class="form-error hidden" id="rce-code"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span><?= htmlspecialchars($errors['code'] ?? '') ?></span></div>
          </div>

          <button type="button" class="btn btn-primary btn-block btn-lg" onclick="doVerifyCode()">Vérifier le code</button>
        </form>

        <div style="margin-top:20px;text-align:center;font-size:13px;color:var(--text3)">
          Vous n'avez pas reçu le code ?
          <a href="index.php?action=forgot_password&email=<?= urlencode($_SESSION['reset_email'] ?? '') ?>" style="color:var(--blue)">Renvoyer</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (!empty($errors['code'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('rce-code').classList.remove('hidden');
});
</script>
<?php endif; ?>

<script>
function otpMove(input, idx) {
  // Accepter uniquement les chiffres
  input.value = input.value.replace(/\D/g, '');
  if (input.value && idx < 5) {
    document.getElementById('otp' + (idx + 1)).focus();
  }
}

function otpBack(e, idx) {
  if (e.key === 'Backspace' && !document.getElementById('otp' + idx).value && idx > 0) {
    document.getElementById('otp' + (idx - 1)).focus();
  }
}

function doVerifyCode() {
  let code = '';
  for (let i = 0; i < 6; i++) {
    code += document.getElementById('otp' + i).value;
  }

  document.getElementById('rc-code').value = code;

  if (code.length !== 6 || !/^\d{6}$/.test(code)) {
    const errEl = document.getElementById('rce-code');
    errEl.classList.remove('hidden');
    errEl.querySelector('span').textContent = 'Veuillez saisir les 6 chiffres du code.';
    return;
  }

  document.getElementById('codeForm').submit();
}

// Prise en charge du collage du code depuis le presse-papier
document.addEventListener('paste', function(e) {
  const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
  if (text.length === 6) {
    for (let i = 0; i < 6; i++) {
      document.getElementById('otp' + i).value = text[i];
    }
    document.getElementById('otp5').focus();
  }
});
</script>
