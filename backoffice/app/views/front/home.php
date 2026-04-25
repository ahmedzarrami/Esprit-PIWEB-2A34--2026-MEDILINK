<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediLink — Rendez-vous médicaux</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --primary:      #0ea5e9;
    --primary-dark: #0284c7;
    --primary-light:#e0f2fe;
    --primary-glow: rgba(14,165,233,.18);
    --accent:       #06b6d4;
    --success:      #10b981;
    --success-light:#d1fae5;
    --danger:       #ef4444;
    --danger-light: #fee2e2;
    --warn-light:   #fef3c7;
    --white:        #ffffff;
    --bg:           #f0f7ff;
    --surface:      #ffffff;
    --border:       #e2eaf3;
    --text:         #0f172a;
    --text-2:       #475569;
    --text-3:       #94a3b8;
    --radius-sm:    8px;
    --radius:       14px;
    --radius-lg:    20px;
    --radius-xl:    28px;
    --shadow-sm:    0 1px 4px rgba(0,0,0,.06);
    --shadow:       0 4px 20px rgba(14,165,233,.10);
    --shadow-lg:    0 12px 40px rgba(14,165,233,.18);
}

body {
    font-family: 'Inter', sans-serif;
    background: var(--bg);
    color: var(--text);
    font-size: 14px;
    line-height: 1.6;
}

/* ── SCROLLBAR ── */
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: var(--bg); }
::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

/* ── NAVBAR ── */
.navbar {
    background: rgba(255,255,255,.85);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid var(--border);
    padding: 0 48px;
    height: 68px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 100;
}
.nav-logo { display:flex; align-items:center; text-decoration:none; gap:10px; }
.nav-logo img { height: 40px; width: auto; }
.nav-logo-text { font-size: 18px; font-weight: 700; color: var(--primary-dark); letter-spacing: -.5px; }

.nav-links { display: flex; gap: 2px; }
.nav-links a {
    padding: 7px 16px;
    border-radius: 10px;
    color: var(--text-2);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all .2s;
}
.nav-links a:hover { background: var(--primary-light); color: var(--primary-dark); }
.nav-links a.active { background: var(--primary-light); color: var(--primary-dark); font-weight: 600; }

.nav-right { display: flex; align-items: center; gap: 10px; }

.btn-admin {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 8px 18px;
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: #fff;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all .2s;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(15,23,42,.25);
}
.btn-admin:hover { background: linear-gradient(135deg, #1e293b, #334155); transform: translateY(-1px); }

.nav-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700;
    box-shadow: 0 2px 10px var(--primary-glow);
}

/* ── HERO ── */
.hero {
    position: relative;
    overflow: hidden;
    background: linear-gradient(145deg, #0c4a6e 0%, #0369a1 40%, #0ea5e9 75%, #38bdf8 100%);
    padding: 80px 48px 100px;
}
.hero-shapes {
    position: absolute; inset: 0; overflow: hidden; pointer-events: none;
}
.hero-shapes span {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
}
.hero-shapes span:nth-child(1) { width:400px; height:400px; top:-100px; right:-80px; }
.hero-shapes span:nth-child(2) { width:250px; height:250px; bottom:-80px; left:30%; background:rgba(255,255,255,.04); }
.hero-shapes span:nth-child(3) { width:180px; height:180px; top:40px; left:60%; background:rgba(255,255,255,.03); }

.hero-inner { max-width: 960px; margin: 0 auto; position: relative; z-index: 1; }

.hero-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 100px;
    padding: 6px 16px;
    font-size: 12px;
    color: rgba(255,255,255,.9);
    margin-bottom: 24px;
    backdrop-filter: blur(8px);
}
.hero-chip-dot {
    width: 7px; height: 7px;
    background: #4ade80;
    border-radius: 50%;
    box-shadow: 0 0 8px #4ade80;
    animation: pulse 2s infinite;
}
@keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:.5; } }

.hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: 48px;
    font-weight: 600;
    color: #fff;
    line-height: 1.15;
    margin-bottom: 16px;
    letter-spacing: -.5px;
}
.hero h1 em { font-style: italic; font-weight: 500; opacity: .9; }
.hero p {
    color: rgba(255,255,255,.7);
    font-size: 15px;
    max-width: 420px;
    line-height: 1.8;
}

/* ── STATS ── */
.stats-bar {
    background: var(--white);
    border-bottom: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
}
.stats-inner {
    max-width: 960px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
}
.stat-item {
    padding: 22px 32px;
    border-right: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 14px;
}
.stat-item:last-child { border-right: none; }
.stat-icon {
    width: 42px; height: 42px;
    border-radius: 12px;
    background: var(--primary-light);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.stat-text {}
.stat-num { font-size: 20px; font-weight: 700; color: var(--text); line-height: 1; }
.stat-label { font-size: 11px; color: var(--text-3); margin-top: 3px; font-weight: 500; }

/* ── MAIN ── */
.main { max-width: 960px; margin: 0 auto; padding: 48px 48px 72px; }

.section-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-2);
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}

/* ── SEARCH CARD ── */
.rech-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 28px;
    margin-bottom: 48px;
    box-shadow: var(--shadow-sm);
}
.rech-filters {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 14px;
    margin-bottom: 18px;
    align-items: end;
}
.rech-form-group { display: flex; flex-direction: column; gap: 6px; }
.rech-label { font-size: 11px; font-weight: 600; color: var(--text-2); text-transform: uppercase; letter-spacing: .06em; }
.rech-input {
    padding: 10px 14px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    color: var(--text);
    background: var(--bg);
    outline: none;
    transition: all .2s;
    height: 42px;
}
.rech-input:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px var(--primary-glow); }
.btn-rech-reset {
    height: 42px; padding: 0 18px;
    background: var(--bg); color: var(--text-2);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 13px; cursor: pointer;
    font-family: 'Inter', sans-serif;
    white-space: nowrap; transition: all .2s;
    font-weight: 500;
}
.btn-rech-reset:hover { border-color: var(--primary); color: var(--primary); }

.rech-tags { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
.rech-tag {
    padding: 5px 16px; border-radius: 100px; font-size: 12px; font-weight: 600;
    cursor: pointer; border: 1.5px solid var(--border);
    background: var(--bg); color: var(--text-2);
    font-family: 'Inter', sans-serif; transition: all .2s;
}
.rech-tag:hover, .rech-tag.active {
    background: var(--primary-light); border-color: var(--primary);
    color: var(--primary-dark);
}
.rech-info { font-size: 12px; color: var(--text-3); margin-bottom: 14px; }
.rech-info strong { color: var(--text); }

.rech-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px; }

.rech-doc-card {
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    padding: 20px 16px;
    text-align: center;
    transition: all .25s ease;
    position: relative;
    overflow: hidden;
}
.rech-doc-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
    transform: scaleX(0);
    transition: transform .25s;
    transform-origin: left;
}
.rech-doc-card:hover { border-color: var(--primary); transform: translateY(-3px); box-shadow: var(--shadow); }
.rech-doc-card:hover::after { transform: scaleX(1); }

.rech-avatar { width: 56px; height: 56px; border-radius: 50%; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; }
.rech-doc-name { font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 3px; }
.rech-doc-spec { font-size: 12px; color: var(--text-3); margin-bottom: 8px; }
.rech-doc-city { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: var(--text-2); background: #fff; border: 1px solid var(--border); border-radius: 100px; padding: 3px 10px; margin-bottom: 12px; }
.rech-doc-meta { display: flex; justify-content: center; gap: 16px; margin-bottom: 14px; }
.rech-meta-item { font-size: 11px; color: var(--text-2); text-align: center; }
.rech-meta-item strong { display: block; font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 2px; }
.btn-rech-rdv {
    width: 100%; background: var(--primary-light); color: var(--primary-dark);
    border: 1.5px solid rgba(14,165,233,.25); border-radius: var(--radius-sm);
    padding: 8px; font-size: 13px; font-weight: 600;
    cursor: pointer; font-family: 'Inter', sans-serif; transition: all .2s;
}
.btn-rech-rdv:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

.rech-empty { grid-column: 1/-1; text-align: center; padding: 48px 20px; color: var(--text-3); }
.rech-empty-icon { font-size: 32px; margin-bottom: 12px; opacity: .5; }
.rech-empty strong { display: block; font-size: 15px; color: var(--text-2); margin-bottom: 4px; }

/* ── DOCTOR CARDS ── */
.doctors-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 48px; }

.doc-card {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 28px 22px 22px;
    text-align: center;
    cursor: pointer;
    transition: all .25s ease;
    position: relative;
    overflow: hidden;
}
.doc-card-glow {
    position: absolute;
    top: -40px; left: 50%;
    transform: translateX(-50%);
    width: 120px; height: 120px;
    border-radius: 50%;
    opacity: 0;
    transition: opacity .3s;
    pointer-events: none;
}
.doc-card:hover .doc-card-glow, .doc-card.selected-doctor .doc-card-glow { opacity: 1; }

.doc-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
    transform: scaleX(0);
    transition: transform .3s ease;
    transform-origin: left;
}
.doc-card:hover { border-color: var(--primary); transform: translateY(-4px); box-shadow: var(--shadow-lg); }
.doc-card:hover::after, .doc-card.selected-doctor::after { transform: scaleX(1); }
.doc-card.selected-doctor { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-glow); }

.doc-avatar {
    width: 72px; height: 72px;
    border-radius: 50%;
    margin: 0 auto 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; font-weight: 700; color: #fff;
    position: relative; z-index: 1;
}
.doc-avatar.av-blue  { background: linear-gradient(135deg, #0369a1, #38bdf8); box-shadow: 0 6px 20px rgba(14,165,233,.4); }
.doc-avatar.av-teal  { background: linear-gradient(135deg, #047857, #34d399); box-shadow: 0 6px 20px rgba(16,185,129,.4); }
.doc-avatar.av-coral { background: linear-gradient(135deg, #c2410c, #fb923c); box-shadow: 0 6px 20px rgba(251,146,60,.4); }

.doc-card-glow.glow-blue  { background: radial-gradient(circle, rgba(14,165,233,.2), transparent 70%); }
.doc-card-glow.glow-teal  { background: radial-gradient(circle, rgba(16,185,129,.2), transparent 70%); }
.doc-card-glow.glow-coral { background: radial-gradient(circle, rgba(251,146,60,.2), transparent 70%); }

.doc-name { font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.doc-spec {
    font-size: 12px; color: var(--primary);
    background: var(--primary-light);
    border-radius: 100px; padding: 3px 12px;
    display: inline-block; margin-bottom: 18px; font-weight: 600;
}
.doc-meta { display: flex; justify-content: center; gap: 24px; margin-bottom: 20px; }
.doc-meta-item { font-size: 11px; color: var(--text-2); }
.doc-meta-item strong { display: block; font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 2px; }
.btn-select-doc {
    width: 100%;
    background: var(--primary-light);
    color: var(--primary-dark);
    border: 1.5px solid rgba(14,165,233,.25);
    border-radius: var(--radius-sm);
    padding: 10px 12px;
    font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all .2s;
    font-family: 'Inter', sans-serif;
}
.btn-select-doc:hover { background: var(--primary); color: #fff; border-color: var(--primary); box-shadow: 0 4px 14px var(--primary-glow); }

/* ── FORM CARD ── */
.form-card {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 32px 30px;
    margin-bottom: 48px;
    box-shadow: var(--shadow-sm);
}
.medecin-badge {
    display: none; align-items: center; gap: 10px;
    background: var(--success-light); border: 1.5px solid rgba(16,185,129,.25);
    border-radius: var(--radius-sm); padding: 12px 18px;
    margin-bottom: 20px; font-size: 13px; color: #065f46; font-weight: 500;
}
.medecin-badge.visible { display: flex; }
.medecin-badge strong { color: #064e3b; font-weight: 700; }

.form-alert { display: none; align-items: flex-start; gap: 10px; border-radius: var(--radius-sm); padding: 13px 16px; margin-bottom: 20px; font-size: 13px; }
.form-alert.visible { display: flex; }
.form-alert.alert-error   { background: var(--danger-light); border: 1.5px solid rgba(239,68,68,.25); color: #991b1b; }
.form-alert.alert-warning { background: var(--warn-light);   border: 1.5px solid rgba(217,119,6,.25); color: #92400e; }
.form-alert-icon { font-size: 16px; flex-shrink: 0; }

.horaires-info {
    display: flex; align-items: flex-start; gap: 12px;
    background: var(--primary-light);
    border: 1.5px solid rgba(14,165,233,.2);
    border-radius: var(--radius-sm);
    padding: 13px 16px; margin-bottom: 22px;
    font-size: 12px; color: #075985;
}
.horaires-info strong { display: block; font-size: 13px; margin-bottom: 3px; font-weight: 600; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 22px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 11px; font-weight: 700; color: var(--text-2); text-transform: uppercase; letter-spacing: .06em; }
.form-group input {
    padding: 11px 15px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 14px; font-family: 'Inter', sans-serif;
    color: var(--text); background: var(--bg);
    transition: all .2s; outline: none;
}
.form-group input:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px var(--primary-glow); }
.form-group input.input-error { border-color: var(--danger); box-shadow: 0 0 0 3px rgba(239,68,68,.1); }

.field-hint  { font-size: 11px; color: var(--text-3); margin-top: 4px; }
.field-error { font-size: 11px; color: var(--danger); margin-top: 4px; display: none; }
.field-error.visible { display: block; }

.btn-confirm {
    width: 100%;
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
    color: #fff; border: none;
    border-radius: var(--radius);
    padding: 14px 20px;
    font-size: 14px; font-weight: 700;
    cursor: pointer; font-family: 'Inter', sans-serif;
    transition: all .25s;
    box-shadow: 0 4px 16px var(--primary-glow);
    letter-spacing: .01em;
}
.btn-confirm:hover {
    background: linear-gradient(135deg, #0c4a6e, var(--primary-dark));
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(14,165,233,.35);
}
.btn-confirm:active { transform: none; }

/* ── RDV LIST ── */
#rdvList { display: flex; flex-direction: column; gap: 12px; }

.rdv-item {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 22px;
    display: flex; align-items: center; gap: 16px;
    transition: all .2s;
}
.rdv-item:hover { box-shadow: var(--shadow); border-color: #c7dff7; }
.rdv-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--primary-light), #bae6fd);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 20px;
}
.rdv-info { flex: 1; }
.rdv-doc  { font-size: 14px; font-weight: 700; color: var(--text); }
.rdv-time { font-size: 12px; color: var(--text-3); margin-top: 3px; }
.rdv-status {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--success-light); color: var(--success);
    border-radius: 100px; padding: 5px 14px;
    font-size: 11px; font-weight: 600; white-space: nowrap;
}
.rdv-status-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--success); flex-shrink: 0; animation: pulse 2s infinite; }
.rdv-actions { display: flex; gap: 8px; flex-shrink: 0; }
.btn-edit {
    padding: 7px 16px; border-radius: var(--radius-sm);
    border: 1.5px solid var(--border); background: #fff;
    font-size: 12px; font-weight: 600; color: var(--text-2);
    cursor: pointer; font-family: 'Inter', sans-serif; transition: all .2s;
}
.btn-edit:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
.btn-del  {
    padding: 7px 16px; border-radius: var(--radius-sm);
    border: 1.5px solid rgba(239,68,68,.25); background: #fff;
    font-size: 12px; font-weight: 600; color: var(--danger);
    cursor: pointer; font-family: 'Inter', sans-serif; transition: all .2s;
}
.btn-del:hover { background: var(--danger-light); border-color: var(--danger); }

.empty-state { text-align: center; padding: 64px 20px; color: var(--text-3); }
.empty-state-icon { font-size: 40px; opacity: .35; margin-bottom: 16px; }
.empty-state strong { display: block; font-size: 15px; color: var(--text-2); margin: 0 0 6px; font-weight: 600; }

/* ── FOOTER ── */
.footer {
    text-align: center;
    padding: 24px 20px;
    border-top: 1px solid var(--border);
    margin-top: 8px;
}
.footer a {
    font-size: 12px; color: var(--text-3); text-decoration: none;
    display: inline-flex; align-items: center; gap: 6px;
    transition: color .2s; font-weight: 500;
}
.footer a:hover { color: var(--primary); }

/* ── RESPONSIVE ── */
@media (max-width: 720px) {
    .navbar { padding: 0 20px; }
    .nav-links { display: none; }
    .hero { padding: 52px 24px 68px; }
    .hero h1 { font-size: 32px; }
    .main { padding: 32px 20px 56px; }
    .rech-filters { grid-template-columns: 1fr; }
    .doctors-grid { grid-template-columns: 1fr; }
    .form-row { grid-template-columns: 1fr; }
    .stats-inner { grid-template-columns: 1fr; }
    .stat-item { border-right: none; border-bottom: 1px solid var(--border); }
    .rdv-item { flex-wrap: wrap; }
    .rdv-actions { width: 100%; }
    .btn-admin span { display: none; }
}
</style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar">
    <a href="index.php" class="nav-logo">
        <img src="views/front/logo_medilink.jpg" alt="MediLink">
    </a>

    <div class="nav-links">
        <a href="index.php" class="active">Accueil</a>
        <a href="#">Mes RDV</a>
        <a href="#">Médecins</a>
    </div>

    <div class="nav-right">
        <a href="index.php?action=admin" class="btn-admin">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
            </svg>
            <span>Administration</span>
        </a>
        <div class="nav-avatar">MA</div>
    </div>
</nav>

<!-- ── HERO ── -->
<div class="hero">
    <div class="hero-shapes">
        <span></span><span></span><span></span>
    </div>
    <div class="hero-inner">
        <div class="hero-chip">
            <span class="hero-chip-dot"></span>
            Service disponible du lundi au samedi · 8h00 – 18h00
        </div>
        <h1>Réservez votre<br>rendez-vous <em>médical</em></h1>
        <p>Choisissez votre médecin, sélectionnez un créneau et confirmez en quelques secondes.</p>
    </div>
</div>

<!-- ── STATS ── -->
<div class="stats-bar">
    <div class="stats-inner">
        <div class="stat-item">
            <div class="stat-icon">🏥</div>
            <div class="stat-text">
                <div class="stat-num">3 médecins</div>
                <div class="stat-label">Disponibles</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">📅</div>
            <div class="stat-text">
                <div class="stat-num" id="statCount">0 RDV</div>
                <div class="stat-label">Réservés</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">⚡</div>
            <div class="stat-text">
                <div class="stat-num">&lt; 2 min</div>
                <div class="stat-label">Temps moyen</div>
            </div>
        </div>
    </div>
</div>

<!-- ── MAIN ── -->
<div class="main">

    <!-- RECHERCHE -->
    <div class="section-title">Rechercher un médecin</div>
    <div id="rechSection"></div>

    <!-- DOCTORS -->
    <div class="section-title">Choisir un médecin</div>
    <div class="doctors-grid">

        <div class="doc-card" id="card-ahmed">
            <div class="doc-card-glow glow-blue"></div>
            <div class="doc-avatar av-blue">AH</div>
            <div class="doc-name">Dr. Ahmed</div>
            <div class="doc-spec">Cardiologue</div>
            <div class="doc-meta">
                <div class="doc-meta-item"><strong>4.9 ★</strong>Note</div>
                <div class="doc-meta-item"><strong>12 ans</strong>Expérience</div>
            </div>
            <button class="btn-select-doc" onclick="selectMedecin('Dr. Ahmed','Cardiologue','card-ahmed')">Sélectionner</button>
        </div>

        <div class="doc-card" id="card-sara">
            <div class="doc-card-glow glow-teal"></div>
            <div class="doc-avatar av-teal">SA</div>
            <div class="doc-name">Dr. Sara</div>
            <div class="doc-spec">Dermatologue</div>
            <div class="doc-meta">
                <div class="doc-meta-item"><strong>4.8 ★</strong>Note</div>
                <div class="doc-meta-item"><strong>8 ans</strong>Expérience</div>
            </div>
            <button class="btn-select-doc" onclick="selectMedecin('Dr. Sara','Dermatologue','card-sara')">Sélectionner</button>
        </div>

        <div class="doc-card" id="card-youssef">
            <div class="doc-card-glow glow-coral"></div>
            <div class="doc-avatar av-coral">YO</div>
            <div class="doc-name">Dr. Youssef</div>
            <div class="doc-spec">Dentiste</div>
            <div class="doc-meta">
                <div class="doc-meta-item"><strong>4.7 ★</strong>Note</div>
                <div class="doc-meta-item"><strong>10 ans</strong>Expérience</div>
            </div>
            <button class="btn-select-doc" onclick="selectMedecin('Dr. Youssef','Dentiste','card-youssef')">Sélectionner</button>
        </div>

    </div>

    <!-- FORMULAIRE -->
    <div class="section-title">Réserver un créneau</div>
    <div class="form-card">

        <div id="medecinBadge" class="medecin-badge">
            ✓ Médecin sélectionné : <strong id="badgeNom"></strong>
        </div>

        <div id="formAlert" class="form-alert">
            <span class="form-alert-icon" id="formAlertIcon"></span>
            <span id="formAlertMsg"></span>
        </div>

        <div class="horaires-info">
            <span style="font-size:18px;flex-shrink:0">🕐</span>
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
            <button type="submit" class="btn-confirm">📅 Confirmer le rendez-vous</button>
        </form>

    </div>

    <!-- MES RDV -->
    <div class="section-title">Mes rendez-vous</div>
    <div id="rdvList"></div>

</div>

<!-- ── FOOTER ── -->
<div class="footer">
    <a href="index.php?action=admin">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="3"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
        Accès administration
    </a>
</div>

<!-- ── SCRIPTS ── -->
<script src="views/front/rechRDV.js"></script>
<script src="views/front/addRDV.js"></script>
<script src="views/front/listRDV.js"></script>
<script src="views/front/modifRDV.js"></script>
<script src="views/front/suppRDV.js"></script>

</body>
</html>
