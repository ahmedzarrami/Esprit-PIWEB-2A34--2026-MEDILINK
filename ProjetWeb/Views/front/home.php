<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediLink — Rendez-vous médicaux</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --blue:        #1a56db;
    --blue-dark:   #1a46c4;
    --blue-light:  #eff4ff;
    --blue-mid:    #6694f8;
    --green:       #0da271;
    --green-light: #ecfdf5;
    --navy:        #0f1b2d;
    --navy2:       #1e2f45;
    --red:         #dc2626;
    --red-light:   #fee2e2;
    --orange-light:#fef3c7;
    --gray-50:     #f8fafc;
    --gray-100:    #f1f5f9;
    --gray-200:    #e2e8f0;
    --gray-400:    #94a3b8;
    --gray-600:    #475569;
    --gray-900:    #0f172a;
    --radius:      12px;
    --radius-lg:   18px;
    --radius-xl:   24px;
}
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--gray-50); color: var(--gray-900); font-size: 14px; line-height: 1.6; }

/* ── NAVBAR ── */
.navbar-medilink {
    background: #fff;
    border-bottom: 1px solid var(--gray-200);
    padding: 0 40px;
    height: 68px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 1000;
}
.nav-logo { display: flex; align-items: center; text-decoration: none; }
.nav-logo img { height: 44px; width: auto; }

.nav-links { display: flex; gap: 4px; }
.nav-links a {
    padding: 6px 16px;
    border-radius: 8px;
    color: var(--gray-600);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: .15s;
}
.nav-links a:hover, .nav-links a.active {
    background: var(--blue-light);
    color: var(--blue);
}

/* Bouton Administration */
.btn-admin {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    background: var(--navy);
    color: #fff;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: .15s;
    white-space: nowrap;
}
.btn-admin:hover { background: var(--navy2); color: #fff; }
.btn-admin svg { flex-shrink: 0; }

.nav-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: var(--blue);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 600;
}

/* ── HERO ── */
.hero {
    background: linear-gradient(135deg, #1a46c4 0%, #2563eb 55%, #3b7ff7 100%);
    padding: 72px 40px 88px;
    position: relative;
    overflow: hidden;
}
.hero::before { content:''; position:absolute; top:-80px; right:-80px; width:350px; height:350px; background:rgba(255,255,255,.06); border-radius:50%; }
.hero::after  { content:''; position:absolute; bottom:-100px; left:42%; width:220px; height:220px; background:rgba(255,255,255,.04); border-radius:50%; }
.hero-inner { max-width:920px; margin:0 auto; position:relative; z-index:1; }
.hero-badge {
    display:inline-flex; align-items:center; gap:7px;
    background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);
    border-radius:100px; padding:5px 14px; font-size:12px; color:#fff; margin-bottom:22px;
}
.hero-badge-dot { width:6px; height:6px; background:#4ade80; border-radius:50%; flex-shrink:0; }
.hero h1 { font-size:40px; font-weight:600; color:#fff; line-height:1.2; margin-bottom:14px; }
.hero h1 em { font-family:'Instrument Serif',serif; font-style:italic; font-weight:400; }
.hero p { color:rgba(255,255,255,.75); font-size:15px; max-width:460px; line-height:1.75; }

/* ── STATS ── */
.stats-strip { background:#fff; border-bottom:1px solid var(--gray-200); }
.stats-inner { max-width:920px; margin:0 auto; display:grid; grid-template-columns:repeat(3,1fr); }
.stat-item { padding:20px 28px; border-right:1px solid var(--gray-200); }
.stat-item:last-child { border-right:none; }
.stat-num { font-size:22px; font-weight:600; color:var(--gray-900); }
.stat-label { font-size:12px; color:var(--gray-400); margin-top:3px; }

/* ── MAIN ── */
.main-content { max-width:920px; margin:0 auto; padding:44px 40px 60px; }
.section-heading {
    font-size:15px; font-weight:600; color:var(--gray-900);
    margin-bottom:20px; display:flex; align-items:center; gap:10px;
}
.section-heading::before {
    content:''; display:inline-block; width:3px; height:16px;
    background:var(--blue); border-radius:2px;
}

/* ── SEARCH ── */
.rech-card { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-xl); padding:28px; margin-bottom:44px; }
.rech-filters { display:grid; grid-template-columns:1fr 1fr auto; gap:14px; margin-bottom:18px; align-items:end; }
.rech-form-group { display:flex; flex-direction:column; gap:6px; }
.rech-label { font-size:11px; font-weight:500; color:var(--gray-600); text-transform:uppercase; letter-spacing:.05em; }
.rech-input { padding:10px 13px; border:1px solid var(--gray-200); border-radius:8px; font-size:14px; font-family:'Plus Jakarta Sans',sans-serif; color:var(--gray-900); background:#fff; outline:none; transition:.15s; height:40px; }
.rech-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,86,219,.1); }
.btn-rech-reset { height:40px; padding:0 16px; background:var(--gray-100); color:var(--gray-600); border:1px solid var(--gray-200); border-radius:8px; font-size:13px; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; white-space:nowrap; transition:.15s; }
.btn-rech-reset:hover { color:var(--gray-900); border-color:var(--gray-400); }
.rech-tags { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; }
.rech-tag { padding:4px 14px; border-radius:100px; font-size:12px; font-weight:500; cursor:pointer; border:1px solid var(--gray-200); background:var(--gray-100); color:var(--gray-600); font-family:'Plus Jakarta Sans',sans-serif; transition:.15s; }
.rech-tag:hover, .rech-tag.active { background:var(--blue-light); border-color:var(--blue-mid); color:var(--blue-dark, #1a46c4); }
.rech-info { font-size:12px; color:var(--gray-400); margin-bottom:14px; }
.rech-info strong { color:var(--gray-900); }
.rech-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:14px; }
.rech-doc-card { background:var(--gray-50); border:1px solid var(--gray-200); border-radius:var(--radius-lg); padding:20px 16px; text-align:center; transition:.2s ease; position:relative; overflow:hidden; }
.rech-doc-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--blue); transform:scaleX(0); transition:.2s; transform-origin:left; }
.rech-doc-card:hover { border-color:var(--blue-mid); transform:translateY(-2px); box-shadow:0 8px 24px rgba(26,86,219,.1); }
.rech-doc-card:hover::before { transform:scaleX(1); }
.rech-avatar { width:56px; height:56px; border-radius:50%; margin:0 auto 12px; display:flex; align-items:center; justify-content:center; font-size:16px; font-weight:600; }
.rech-doc-name { font-size:14px; font-weight:600; color:var(--gray-900); margin-bottom:3px; }
.rech-doc-spec { font-size:12px; color:var(--gray-400); margin-bottom:8px; }
.rech-doc-city { display:inline-flex; align-items:center; gap:4px; font-size:11px; color:var(--gray-600); background:#fff; border:1px solid var(--gray-200); border-radius:100px; padding:3px 10px; margin-bottom:12px; }
.rech-doc-meta { display:flex; justify-content:center; gap:16px; margin-bottom:14px; }
.rech-meta-item { font-size:11px; color:var(--gray-600); text-align:center; }
.rech-meta-item strong { display:block; font-size:13px; font-weight:600; color:var(--gray-900); margin-bottom:2px; }
.btn-rech-rdv { width:100%; background:var(--blue-light); color:var(--blue); border:1px solid rgba(26,86,219,.2); border-radius:8px; padding:8px; font-size:13px; font-weight:500; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:.15s; }
.btn-rech-rdv:hover { background:var(--blue); color:#fff; border-color:var(--blue); }
.rech-empty { grid-column:1/-1; text-align:center; padding:48px 20px; color:var(--gray-400); }
.rech-empty-icon { font-size:28px; margin-bottom:10px; opacity:.5; }
.rech-empty strong { display:block; font-size:15px; color:var(--gray-600); margin-bottom:4px; }

/* ── DOCTOR CARDS ── */
.doctors-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:44px; }
.doc-card { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); padding:26px 20px; text-align:center; cursor:pointer; transition:.2s ease; position:relative; overflow:hidden; }
.doc-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--blue); transform:scaleX(0); transition:.2s ease; transform-origin:left; }
.doc-card:hover { border-color:var(--blue-mid); transform:translateY(-3px); box-shadow:0 12px 28px rgba(26,86,219,.12); }
.doc-card:hover::before, .doc-card.selected-doctor::before { transform:scaleX(1); }
.doc-card.selected-doctor { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,86,219,.12); }
.doc-avatar { width:66px; height:66px; border-radius:50%; margin:0 auto 16px; display:flex; align-items:center; justify-content:center; font-size:21px; font-weight:600; color:#fff; }
.doc-avatar.av-blue  { background:linear-gradient(135deg,#1a56db,#6694f8); }
.doc-avatar.av-teal  { background:linear-gradient(135deg,#0da271,#34d399); }
.doc-avatar.av-coral { background:linear-gradient(135deg,#e05a2b,#fb923c); }
.doc-name { font-size:15px; font-weight:600; color:var(--gray-900); margin-bottom:4px; }
.doc-spec { font-size:12px; color:var(--gray-400); margin-bottom:16px; }
.doc-meta { display:flex; justify-content:center; gap:20px; margin-bottom:18px; }
.doc-meta-item { font-size:11px; color:var(--gray-600); }
.doc-meta-item strong { display:block; font-size:13px; font-weight:600; color:var(--gray-900); margin-bottom:2px; }
.btn-select-doc { width:100%; background:var(--blue-light); color:var(--blue); border:1px solid rgba(26,86,219,.2); border-radius:8px; padding:9px 12px; font-size:13px; font-weight:500; cursor:pointer; transition:.15s; font-family:'Plus Jakarta Sans',sans-serif; }
.btn-select-doc:hover { background:var(--blue); color:#fff; border-color:var(--blue); }

/* ── FORM ── */
.form-card { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-xl); padding:30px 28px; margin-bottom:44px; }
.medecin-badge { display:none; align-items:center; gap:9px; background:var(--green-light); border:1px solid rgba(13,162,113,.2); border-radius:8px; padding:11px 16px; margin-bottom:18px; font-size:13px; color:#065f46; }
.medecin-badge.visible { display:flex; }
.medecin-badge strong { color:#064e3b; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:18px; }
.form-group { display:flex; flex-direction:column; gap:6px; }
.form-group label { font-size:11px; font-weight:500; color:var(--gray-600); text-transform:uppercase; letter-spacing:.05em; }
.form-group input { padding:10px 14px; border:1px solid var(--gray-200); border-radius:8px; font-size:14px; font-family:'Plus Jakarta Sans',sans-serif; color:var(--gray-900); background:#fff; transition:.15s; outline:none; }
.form-group input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,86,219,.1); }
.form-group input.input-error { border-color:var(--red); box-shadow:0 0 0 3px rgba(220,38,38,.1); }
.field-hint  { font-size:11px; color:var(--gray-400); margin-top:4px; }
.field-error { font-size:11px; color:var(--red); margin-top:4px; display:none; }
.field-error.visible { display:block; }
.form-alert { display:none; align-items:flex-start; gap:10px; border-radius:8px; padding:12px 16px; margin-bottom:18px; font-size:13px; }
.form-alert.visible { display:flex; }
.form-alert.alert-error   { background:var(--red-light);    border:1px solid rgba(220,38,38,.25); color:#991b1b; }
.form-alert.alert-warning { background:var(--orange-light); border:1px solid rgba(217,119,6,.25);  color:#92400e; }
.form-alert.alert-success { background:var(--green-light);  border:1px solid rgba(13,162,113,.25); color:#065f46; }
.form-alert-icon { font-size:16px; flex-shrink:0; }
.horaires-info { display:flex; align-items:flex-start; gap:10px; background:var(--blue-light); border:1px solid rgba(26,86,219,.2); border-radius:8px; padding:12px 16px; margin-bottom:18px; font-size:12px; color:#1e40af; }
.horaires-info strong { display:block; font-size:13px; margin-bottom:4px; }
.btn-confirm { width:100%; background:var(--blue); color:#fff; border:none; border-radius:10px; padding:13px 20px; font-size:14px; font-weight:600; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:.15s; }
.btn-confirm:hover { background:var(--blue-dark); transform:translateY(-1px); box-shadow:0 6px 20px rgba(26,86,219,.3); }
.btn-confirm:active { transform:none; }

/* ── RDV LIST ── */
#rdvList { display:flex; flex-direction:column; gap:12px; }
.rdv-item { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); padding:18px 22px; display:flex; align-items:center; gap:16px; transition:.15s; }
.rdv-item:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); }
.rdv-icon { width:46px; height:46px; border-radius:10px; background:var(--blue-light); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:20px; }
.rdv-info { flex:1; }
.rdv-doc  { font-size:14px; font-weight:600; color:var(--gray-900); }
.rdv-time { font-size:12px; color:var(--gray-400); margin-top:3px; }
.rdv-status { display:inline-flex; align-items:center; gap:5px; background:var(--green-light); color:var(--green); border-radius:100px; padding:4px 12px; font-size:11px; font-weight:500; white-space:nowrap; }
.rdv-status-dot { width:5px; height:5px; border-radius:50%; background:var(--green); flex-shrink:0; }
.rdv-actions { display:flex; gap:8px; flex-shrink:0; }
.btn-edit { padding:7px 16px; border-radius:7px; border:1px solid var(--gray-200); background:#fff; font-size:12px; font-weight:500; color:var(--gray-600); cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:.15s; }
.btn-edit:hover { border-color:var(--blue); color:var(--blue); background:var(--blue-light); }
.btn-del  { padding:7px 16px; border-radius:7px; border:1px solid var(--red-light); background:#fff; font-size:12px; font-weight:500; color:var(--red); cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:.15s; }
.btn-del:hover { background:var(--red-light); }
.empty-state { text-align:center; padding:56px 20px; color:var(--gray-400); }
.empty-state strong { display:block; font-size:15px; color:var(--gray-600); margin:12px 0 4px; }

/* ── FOOTER ADMIN LINK ── */
.footer-admin { text-align:center; padding:24px 20px; border-top:1px solid var(--gray-200); }
.footer-admin a { font-size:12px; color:var(--gray-400); text-decoration:none; display:inline-flex; align-items:center; gap:5px; transition:.15s; }
.footer-admin a:hover { color:var(--gray-600); }

/* ── RESPONSIVE ── */
@media (max-width:700px) {
    .navbar-medilink { padding:0 16px; gap:8px; }
    .nav-links { display:none; }
    .hero { padding:48px 20px 60px; }
    .hero h1 { font-size:28px; }
    .main-content { padding:28px 20px 48px; }
    .rech-filters { grid-template-columns:1fr; }
    .doctors-grid { grid-template-columns:1fr; }
    .form-row { grid-template-columns:1fr; }
    .stats-inner { grid-template-columns:1fr; }
    .stat-item { border-right:none; border-bottom:1px solid var(--gray-200); }
    .rdv-item { flex-wrap:wrap; }
    .rdv-actions { width:100%; }
    .btn-admin span { display:none; }
}
</style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar-medilink">
    <a href="?action=home" class="nav-logo">
        <img src="./Views/front/logo_medilink.jpg" alt="MediLink">
    </a>

    <div class="nav-links">
        <a href="?action=home" class="active">Accueil</a>
        <a href="?action=home">Mes RDV</a>
        <a href="?action=home">Médecins</a>
    </div>

    <div style="display:flex;align-items:center;gap:10px">
        <!-- Bouton Administration -->
        <a href="?action=admin" class="btn-admin">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
                <path d="M12 2v2M12 20v2M2 12h2M20 12h2"/>
            </svg>
            <span>Administration</span>
        </a>
        <div class="nav-avatar">MA</div>
    </div>
</nav>

<!-- ── HERO ── -->
<div class="hero">
    <div class="hero-inner">
        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Service disponible du lundi au samedi · 8h00 – 18h00
        </div>
        <h1>Réservez votre<br>rendez-vous <em>médical</em></h1>
        <p>Choisissez votre médecin, sélectionnez un créneau et confirmez en quelques secondes.</p>
    </div>
</div>

<!-- ── STATS ── -->
<div class="stats-strip">
    <div class="stats-inner">
        <div class="stat-item">
            <div class="stat-num">3 médecins</div>
            <div class="stat-label">Disponibles</div>
        </div>
        <div class="stat-item">
            <div class="stat-num" id="statCount">0 RDV</div>
            <div class="stat-label">Réservés</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">&lt; 2 min</div>
            <div class="stat-label">Temps moyen</div>
        </div>
    </div>
</div>

<!-- ── MAIN ── -->
<div class="main-content">

    <!-- RECHERCHE -->
    <div class="section-heading">Rechercher un médecin</div>
    <div id="rechSection"></div>

    <!-- DOCTORS -->
    <div class="section-heading">Ou choisir directement</div>
    <div class="doctors-grid">

        <div class="doc-card" id="card-ahmed">
            <div class="doc-avatar av-blue">AH</div>
            <div class="doc-name">Dr. Ahmed</div>
            <div class="doc-spec">Cardiologue</div>
            <div class="doc-meta">
                <div class="doc-meta-item"><strong>4.9</strong>Note</div>
                <div class="doc-meta-item"><strong>12 ans</strong>Exp.</div>
            </div>
            <button class="btn-select-doc" onclick="selectMedecin('Dr. Ahmed','Cardiologue','card-ahmed')">Sélectionner</button>
        </div>

        <div class="doc-card" id="card-sara">
            <div class="doc-avatar av-teal">SA</div>
            <div class="doc-name">Dr. Sara</div>
            <div class="doc-spec">Dermatologue</div>
            <div class="doc-meta">
                <div class="doc-meta-item"><strong>4.8</strong>Note</div>
                <div class="doc-meta-item"><strong>8 ans</strong>Exp.</div>
            </div>
            <button class="btn-select-doc" onclick="selectMedecin('Dr. Sara','Dermatologue','card-sara')">Sélectionner</button>
        </div>

        <div class="doc-card" id="card-youssef">
            <div class="doc-avatar av-coral">YO</div>
            <div class="doc-name">Dr. Youssef</div>
            <div class="doc-spec">Dentiste</div>
            <div class="doc-meta">
                <div class="doc-meta-item"><strong>4.7</strong>Note</div>
                <div class="doc-meta-item"><strong>10 ans</strong>Exp.</div>
            </div>
            <button class="btn-select-doc" onclick="selectMedecin('Dr. Youssef','Dentiste','card-youssef')">Sélectionner</button>
        </div>

    </div>

    <!-- FORMULAIRE -->
    <div class="section-heading">Réserver un créneau</div>
    <div class="form-card">

        <div id="medecinBadge" class="medecin-badge">
            ✓ Médecin sélectionné : <strong id="badgeNom"></strong>
        </div>

        <div id="formAlert" class="form-alert">
            <span class="form-alert-icon" id="formAlertIcon"></span>
            <span id="formAlertMsg"></span>
        </div>

        <div class="horaires-info">
            <span style="font-size:16px;flex-shrink:0">🕐</span>
            <div>
                <strong>Horaires de disponibilité</strong>
                Lundi au samedi · 8h00–12h30 et 14h00–18h00 · Fermé le dimanche et de 12h30 à 14h00
            </div>
        </div>

        <form id="rdvForm">
            <input type="hidden" id="medecin">
            <div class="form-row">
                <div class="form-group">
                    <label>Date du rendez-vous</label>
                    <input type="date" id="date">
                    <span class="field-hint">Lundi – Samedi uniquement</span>
                    <span class="field-error" id="dateError"></span>
                </div>
                <div class="form-group">
                    <label>Heure du rendez-vous</label>
                    <input type="time" id="heure" min="08:00" max="18:00" step="900">
                    <span class="field-hint">8h00–12h30 ou 14h00–18h00</span>
                    <span class="field-error" id="heureError"></span>
                </div>
            </div>
            <button type="submit" class="btn-confirm">&#128197; Confirmer le rendez-vous</button>
        </form>

    </div>

    <!-- MES RDV -->
    <div class="section-heading">Mes rendez-vous</div>
    <div id="rdvList"></div>

</div>

<!-- ── FOOTER ── -->
<div class="footer-admin">
    <a href="?action=admin">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
        Accès administration
    </a>
</div>

<!-- ── SCRIPTS ── -->
<script src="./Views/front/rechRDV.js"></script>
<script src="./Views/front/addRDV.js"></script>
<script src="./Views/front/listRDV.js"></script>
<script src="./Views/front/modifRDV.js"></script>
<script src="./Views/front/suppRDV.js"></script>

</body>
</html>