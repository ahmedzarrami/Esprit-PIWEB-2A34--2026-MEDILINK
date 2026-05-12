/**
 * MediLink - barre de navigation flottante universelle.
 *
 * Injectee sur chaque page d'un module. Donne acces depuis n'importe ou a :
 *   - Accueil MediLink
 *   - BackOffice (si admin)
 *   - Profil
 *   - Deconnexion
 *
 * Le bandeau s'auto-positionne en bas (pour ne pas masquer les navbars
 * existantes des modules) avec un fond bleu MediLink + ombre.
 *
 * L'identite utilisateur est fournie par /files40/api/me.php (PHP).
 */
(function () {
  if (window.__ml_topbar_loaded) return;
  window.__ml_topbar_loaded = true;

  const BASE = '/files40';

  const css = `
    .ml-topbar{
      position:fixed; left:0; right:0; bottom:0;
      z-index:99999;
      background:linear-gradient(90deg,#1d4ed8 0%,#1e40af 100%);
      color:#fff;
      box-shadow:0 -4px 16px rgba(15,23,42,.15);
      font-family:'Plus Jakarta Sans','Inter',system-ui,sans-serif;
      font-size:13px;
      display:flex; align-items:center; gap:8px;
      padding:8px 18px;
      transition:transform .25s ease;
    }
    .ml-topbar--hidden{ transform:translateY(110%); }
    .ml-topbar__brand{
      font-weight:800; letter-spacing:.3px;
      display:flex; align-items:center; gap:6px;
      color:#fff; text-decoration:none;
      padding:4px 10px; border-radius:8px;
      background:rgba(255,255,255,.15);
    }
    .ml-topbar__brand:hover{ background:rgba(255,255,255,.25); color:#fff; text-decoration:none; }
    .ml-topbar__brand::before{ content:'+'; font-size:14px; }
    .ml-topbar__sep{ opacity:.5; padding:0 4px; }
    .ml-topbar__crumb{ font-weight:600; opacity:.9; }
    .ml-topbar__spacer{ flex:1; }
    .ml-topbar__btn{
      color:#fff; text-decoration:none;
      padding:5px 12px; border-radius:7px;
      font-weight:600;
      border:1px solid rgba(255,255,255,.25);
      background:transparent;
      cursor:pointer; font-size:12.5px;
      transition:background .12s, border-color .12s;
    }
    .ml-topbar__btn:hover{ background:rgba(255,255,255,.18); border-color:rgba(255,255,255,.45); color:#fff; text-decoration:none; }
    .ml-topbar__btn--solid{
      background:#fff; color:#1d4ed8; border-color:#fff;
    }
    .ml-topbar__btn--solid:hover{ background:#dbeafe; color:#1d4ed8; }
    .ml-topbar__btn--danger{ border-color:rgba(255,255,255,.4); }
    .ml-topbar__btn--danger:hover{ background:rgba(220,38,38,.6); border-color:rgba(220,38,38,.8); }
    .ml-topbar__user{
      display:flex; align-items:center; gap:6px;
      padding:3px 12px; border-radius:14px;
      background:rgba(255,255,255,.13);
      font-weight:600;
    }
    .ml-topbar__role{
      padding:2px 7px; border-radius:5px;
      background:rgba(255,255,255,.25); color:#fff;
      font-size:10.5px; font-weight:700;
      text-transform:uppercase; letter-spacing:.4px;
    }
    .ml-topbar__toggle{
      position:fixed; right:14px; bottom:14px;
      width:36px; height:36px; border-radius:50%;
      background:#1d4ed8; color:#fff; border:none;
      font-size:16px; cursor:pointer;
      box-shadow:0 4px 12px rgba(15,23,42,.25);
      display:none; z-index:99998;
    }
    .ml-topbar__toggle.show{ display:flex; align-items:center; justify-content:center; }
    body{ padding-bottom:48px !important; }
    @media (max-width:680px){
      .ml-topbar{ padding:6px 10px; gap:5px; font-size:11px; flex-wrap:wrap; }
      .ml-topbar__user{ display:none; }
      .ml-topbar__crumb{ display:none; }
    }
  `;

  const style = document.createElement('style');
  style.textContent = css;
  document.head.appendChild(style);

  // Determiner le "module courant" depuis l'URL
  function detectModule() {
    const p = window.location.pathname;
    if (p.includes('/modules/utilisateur/')) return 'Compte';
    if (p.includes('/modules/rdv/'))         return 'Rendez-vous';
    if (p.includes('/modules/medicaments/')) return 'Medicaments';
    if (p.includes('/modules/parapharmacie/')) return 'Parapharmacie';
    if (p.includes('/modules/forum/'))       return 'Forum';
    if (p.includes('/admin/'))               return 'BackOffice';
    return 'Accueil';
  }

  // Construire la barre
  function build(user) {
    const bar = document.createElement('nav');
    bar.className = 'ml-topbar';
    bar.id = '__ml_topbar';

    const moduleName = detectModule();
    const logged = !!user;
    const isAdmin = logged && user.role === 'Administrateur';

    let profileHref = BASE + '/modules/utilisateur/index.php?page=profile';
    if (logged && user.role === 'Professionnel') {
      profileHref = BASE + '/modules/utilisateur/index.php?page=professionnel';
    }

    bar.innerHTML =
      '<a class="ml-topbar__brand" href="' + BASE + '/index.php">MediLink</a>' +
      '<span class="ml-topbar__sep">|</span>' +
      '<span class="ml-topbar__crumb">' + moduleName + '</span>' +
      '<span class="ml-topbar__spacer"></span>' +
      (logged
        ? '<span class="ml-topbar__user">'
            + (user.prenom || '') + ' ' + (user.nom || '')
            + '<span class="ml-topbar__role">' + (user.role || '') + '</span>'
          + '</span>'
          + '<a class="ml-topbar__btn" href="' + BASE + '/index.php">Accueil</a>'
          + (isAdmin ? '<a class="ml-topbar__btn ml-topbar__btn--solid" href="' + BASE + '/admin/index.php">BackOffice</a>' : '')
          + '<a class="ml-topbar__btn" href="' + profileHref + '">Profil</a>'
          + '<a class="ml-topbar__btn ml-topbar__btn--danger" href="' + BASE + '/modules/utilisateur/index.php?action=logout">Deconnexion</a>'
        : '<a class="ml-topbar__btn ml-topbar__btn--solid" href="' + BASE + '/modules/utilisateur/index.php?page=login">Connexion</a>'
      );

    document.body.appendChild(bar);

    // Bouton flottant pour reveler la barre si masquee
    const toggle = document.createElement('button');
    toggle.className = 'ml-topbar__toggle';
    toggle.title = 'Afficher la barre MediLink';
    toggle.innerHTML = '&#x2630;';
    toggle.onclick = () => {
      bar.classList.toggle('ml-topbar--hidden');
      toggle.classList.toggle('show', bar.classList.contains('ml-topbar--hidden'));
    };
    document.body.appendChild(toggle);
  }

  // Recuperer l'utilisateur courant
  fetch(BASE + '/api/me.php', { credentials: 'same-origin' })
    .then(r => r.json())
    .then(d => build(d && d.user ? d.user : null))
    .catch(() => build(null));
})();
