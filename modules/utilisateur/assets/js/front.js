/**
 * MediLink — Front Office JavaScript
 * Logique de navigation, inscription wizard (Patient uniquement), profil, et interactions
 */

// ===== PAGE NAVIGATION =====
function showPage(id) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.getElementById('page-' + id).classList.add('active');
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    if (id === 'home') document.querySelector('.nav-link').classList.add('active');
    window.scrollTo(0, 0);
}

function logout() {
    window.location.href = 'index.php?action=logout';
}

function afterRegister() {
    window.location.href = 'index.php?page=profile';
}

// ===== STEP WIZARD (3 étapes : Identité → Sécurité → Confirmation) =====
function gotoStep(n) {
    if (n > 1 && !validateStep(n - 1)) return;
    for (let i = 1; i <= 3; i++) {
        document.getElementById('rstep' + i).classList.toggle('hidden', i !== n);
        const sc = document.getElementById('sc' + i);
        const sl = document.getElementById('sl' + i);
        if (sc && sl) {
            sc.classList.remove('active', 'done');
            sl.classList.remove('active', 'done');
            if (i < n) { sc.classList.add('done'); sl.classList.add('done'); sc.textContent = '✓'; }
            else if (i === n) { sc.classList.add('active'); sl.classList.add('active'); }
        }
        if (i < 3) {
            const line = document.getElementById('sl' + i + (i + 1));
            if (line) line.classList.toggle('done', i < n);
        }
    }
    if (n === 3) {
        // Soumettre le formulaire d'inscription
        document.getElementById('registerForm').submit();
    }
}

function validateStep(s) {
    let ok = true;
    if (s === 1) {
        const prenom = document.getElementById('r-prenom').value.trim();
        const nom = document.getElementById('r-nom').value.trim();
        const email = document.getElementById('r-email').value.trim();
        const tel = document.getElementById('r-tel').value.trim();

        if (!Validator.isRequired(prenom) || !Validator.minLength(prenom, 2)) {
            Validator.showFieldError('r-prenom', 're-prenom', 'Prénom invalide (min. 2 caractères)');
            ok = false;
        } else if (!Validator.isValidName(prenom)) {
            Validator.showFieldError('r-prenom', 're-prenom', 'Le prénom ne doit contenir que des lettres');
            ok = false;
        }
        if (!Validator.isRequired(nom) || !Validator.minLength(nom, 2)) {
            Validator.showFieldError('r-nom', 're-nom', 'Nom invalide (min. 2 caractères)');
            ok = false;
        } else if (!Validator.isValidName(nom)) {
            Validator.showFieldError('r-nom', 're-nom', 'Le nom ne doit contenir que des lettres');
            ok = false;
        }
        if (!Validator.isRequired(email) || !Validator.isEmail(email)) {
            Validator.showFieldError('r-email', 're-email', 'Adresse email invalide');
            ok = false;
        }
        if (!Validator.isRequired(tel) || !Validator.isPhone(tel)) {
            Validator.showFieldError('r-tel', 're-tel', 'Le téléphone doit contenir exactement 8 chiffres');
            ok = false;
        }

        const dob = document.getElementById('r-dob').value;
        if (dob && !Validator.isValidBirthDate(dob)) {
            Validator.showFieldError('r-dob', 're-dob', 'Date de naissance invalide');
            ok = false;
        }
    }
    if (s === 2) {
        const pw = document.getElementById('r-pw').value;
        const cpw = document.getElementById('r-cpw').value;

        if (!Validator.isRequired(pw) || !Validator.minLength(pw, 8)) {
            Validator.showFieldError('r-pw', 're-pw', 'Minimum 8 caractères requis');
            ok = false;
        } else {
            const strength = Validator.passwordStrength(pw);
            if (strength.score < 2) {
                Validator.showFieldError('r-pw', 're-pw', 'Trop faible : ajoutez majuscule, chiffre ou symbole');
                ok = false;
            }
        }
        if (!Validator.passwordsMatch(pw, cpw)) {
            Validator.showFieldError('r-cpw', 're-cpw', 'Les mots de passe ne correspondent pas');
            ok = false;
        }
        if (!document.getElementById('r-terms').checked) {
            document.getElementById('re-terms').classList.remove('hidden');
            ok = false;
        }
    }
    return ok;
}

// ===== LOGIN =====
function doLogin() {
    const email = document.getElementById('l-email').value.trim();
    const pw = document.getElementById('l-password').value;
    let ok = true;
    document.getElementById('login-error').classList.add('hidden');

    if (!Validator.isRequired(email) || !Validator.isEmail(email)) {
        Validator.showFieldError('l-email', 'le-email', 'Email invalide');
        ok = false;
    }
    if (!Validator.isRequired(pw)) {
        Validator.showFieldError('l-password', 'le-password', 'Mot de passe requis');
        ok = false;
    }
    if (!ok) return;

    // Soumettre le formulaire
    document.getElementById('loginForm').submit();
}

// ===== PROFILE SAVE =====
function saveProfile() {
    const prenom = document.getElementById('p-prenom').value.trim();
    const nom = document.getElementById('p-nom').value.trim();
    const email = document.getElementById('p-email').value.trim();
    const tel = document.getElementById('p-tel').value.trim();
    let ok = true;

    if (!Validator.isRequired(prenom) || !Validator.minLength(prenom, 2)) {
        Validator.showFieldError('p-prenom', 'pe-prenom', 'Prénom invalide');
        ok = false;
    } else if (!Validator.isValidName(prenom)) {
        Validator.showFieldError('p-prenom', 'pe-prenom', 'Le prénom ne doit contenir que des lettres');
        ok = false;
    }
    if (!Validator.isRequired(nom) || !Validator.minLength(nom, 2)) {
        Validator.showFieldError('p-nom', 'pe-nom', 'Nom invalide');
        ok = false;
    } else if (!Validator.isValidName(nom)) {
        Validator.showFieldError('p-nom', 'pe-nom', 'Le nom ne doit contenir que des lettres');
        ok = false;
    }
    if (!Validator.isRequired(email) || !Validator.isEmail(email)) {
        Validator.showFieldError('p-email', 'pe-email', 'Email invalide');
        ok = false;
    }
    if (!Validator.isRequired(tel) || !Validator.isPhone(tel)) {
        Validator.showFieldError('p-tel', 'pe-tel', 'Le téléphone doit contenir exactement 8 chiffres');
        ok = false;
    }
    if (!ok) return;

    document.getElementById('profileForm').submit();
}

function savePassword() {
    const old = document.getElementById('p-oldpw').value;
    const npw = document.getElementById('p-newpw').value;
    const cpw = document.getElementById('p-cpw').value;
    let ok = true;

    if (!Validator.isRequired(old)) {
        Validator.showFieldError('p-oldpw', 'pe-oldpw', 'Mot de passe actuel requis');
        ok = false;
    }
    if (!Validator.isRequired(npw) || !Validator.minLength(npw, 8)) {
        Validator.showFieldError('p-newpw', 'pe-newpw', 'Minimum 8 caractères');
        ok = false;
    }
    if (!Validator.passwordsMatch(npw, cpw)) {
        Validator.showFieldError('p-cpw', 'pe-cpw', 'Les mots de passe ne correspondent pas');
        ok = false;
    }
    if (!ok) return;

    document.getElementById('passwordForm').submit();
}

// ===== PROFILE TABS =====
function showProfileTab(name, el) {
    ['info','security','rdv'].forEach(t => document.getElementById('ptab-' + t).classList.add('hidden'));
    document.getElementById('ptab-' + name).classList.remove('hidden');
    document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}

// ===== PASSWORD STRENGTH =====
function rPwStrength() { updatePwStrength('r-pw', 'r-sf', 'r-st'); }
function pPwStrength() { updatePwStrength('p-newpw', 'p-sf', 'p-st'); }

function updatePwStrength(inputId, barId, textId) {
    const pw = document.getElementById(inputId).value;
    const strength = Validator.passwordStrength(pw);
    document.getElementById(barId).style.cssText = `width:${strength.percent};background:${strength.color}`;
    document.getElementById(textId).textContent = strength.label;
    document.getElementById(textId).style.color = strength.color;
}

// ===== AVATAR =====
function previewProfileAvatar(e) {
    const f = e.target.files[0]; if (!f) return;
    const r = new FileReader();
    r.onload = ev => {
        const el = document.getElementById('profileInitials');
        el.style.display = 'none';
        const existingImg = document.querySelector('.profile-avatar-big img');
        if (existingImg) existingImg.remove();
        const img = document.createElement('img');
        img.src = ev.target.result;
        img.style.cssText = 'width:100%;height:100%;object-fit:cover;border-radius:50%';
        document.querySelector('.profile-avatar-big').insertBefore(img, el);
    };
    r.readAsDataURL(f);
    toast('Photo de profil mise à jour', 'success');
}

// ===== FIELD ERROR CLEAR =====
function clearFieldError(id) {
    const inp = document.getElementById(id);
    if (inp) { inp.classList.remove('err', 'error'); inp.classList.add('ok'); }
    // Essayer de trouver l'élément d'erreur correspondant
    const prefixes = ['e-','le-','re-','pe-'];
    const field = id.replace(/^[^-]+-/, '');
    prefixes.forEach(p => {
        const err = document.getElementById(p + field);
        if (err) { err.classList.add('hidden'); err.style.display = 'none'; }
    });
}

// ===== PASSWORD TOGGLE =====
function togglePw2(id, btn) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.style.color = inp.type === 'text' ? 'var(--blue)' : 'var(--text3)';
}

// ===== TOAST =====
function toast(msg, type = 'info') {
    const wrap = document.getElementById('toastWrap');
    const icons = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    };
    const t = document.createElement('div');
    t.className = `toast toast-${type}`;
    t.innerHTML = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor">${icons[type]}</svg><span>${msg}</span>`;
    wrap.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}
