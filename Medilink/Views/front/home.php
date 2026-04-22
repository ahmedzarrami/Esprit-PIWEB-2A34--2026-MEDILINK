<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediLink — Accueil</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
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
body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--gray-50);
    color: var(--gray-900);
    font-size: 14px;
    line-height: 1.6;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

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
.nav-logo {
    display: flex; align-items: center; text-decoration: none;
    font-size: 22px; font-weight: 700; letter-spacing: -0.5px;
}
.nav-logo span {
    background: linear-gradient(90deg, #1a56db 0%, #0da271 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.nav-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--green-light); border: 1px solid rgba(13,162,113,.2);
    border-radius: 100px; padding: 5px 12px; font-size: 12px; color: var(--green); font-weight: 500;
}
.nav-badge-dot { width: 6px; height: 6px; background: var(--green); border-radius: 50%; }

/* ── HERO ── */
.hero {
    background: linear-gradient(135deg, #0f1b2d 0%, #1a46c4 50%, #2563eb 100%);
    padding: 100px 40px 120px;
    position: relative;
    overflow: hidden;
    flex: 1;
}
.hero::before {
    content: ''; position: absolute; top: -100px; right: -100px;
    width: 500px; height: 500px; background: rgba(255,255,255,.04);
    border-radius: 50%;
}
.hero::after {
    content: ''; position: absolute; bottom: -80px; left: 38%;
    width: 300px; height: 300px; background: rgba(255,255,255,.03);
    border-radius: 50%;
}
.hero-inner {
    max-width: 960px; margin: 0 auto; position: relative; z-index: 1;
    display: flex; flex-direction: column; align-items: center; text-align: center;
}
.hero-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
    border-radius: 100px; padding: 6px 16px; font-size: 12px; color: #fff;
    margin-bottom: 28px; font-weight: 500;
}
.hero-badge-dot { width: 7px; height: 7px; background: #4ade80; border-radius: 50%; flex-shrink: 0; }
.hero h1 {
    font-size: 52px; font-weight: 700; color: #fff; line-height: 1.15;
    margin-bottom: 18px; letter-spacing: -1px;
}
.hero h1 em {
    font-family: 'Instrument Serif', serif; font-style: italic;
    font-weight: 400; color: rgba(255,255,255,.85);
}
.hero p {
    color: rgba(255,255,255,.7); font-size: 17px;
    max-width: 520px; line-height: 1.75; margin-bottom: 56px;
}

/* ── PORTAL CARDS ── */
.portals {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    width: 100%;
    max-width: 820px;
}
.portal-card {
    background: rgba(255,255,255,.1);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: var(--radius-xl);
    padding: 32px 24px 28px;
    text-align: center;
    cursor: pointer;
    transition: all .25s ease;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    position: relative;
    overflow: hidden;
}
.portal-card::before {
    content: '';
    position: absolute; inset: 0;
    background: rgba(255,255,255,.06);
    opacity: 0;
    transition: opacity .25s;
}
.portal-card:hover { transform: translateY(-6px); border-color: rgba(255,255,255,.35); box-shadow: 0 20px 40px rgba(0,0,0,.25); }
.portal-card:hover::before { opacity: 1; }

.portal-card.portal-patient .portal-icon-wrap { background: linear-gradient(135deg, #0da271, #34d399); }
.portal-card.portal-medecin .portal-icon-wrap { background: linear-gradient(135deg, #1a56db, #6694f8); }
.portal-card.portal-admin   .portal-icon-wrap { background: linear-gradient(135deg, #0f1b2d, #1e2f45); }

.portal-icon-wrap {
    width: 64px; height: 64px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; flex-shrink: 0;
    box-shadow: 0 8px 20px rgba(0,0,0,.2);
}
.portal-title {
    font-size: 17px; font-weight: 700; color: #fff; letter-spacing: -.3px;
}
.portal-desc {
    font-size: 12px; color: rgba(255,255,255,.65); line-height: 1.6;
}
.portal-btn {
    margin-top: 6px;
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 13px; font-weight: 600;
    color: #fff;
    border: 1px solid rgba(255,255,255,.3);
    background: rgba(255,255,255,.12);
    transition: .2s;
    white-space: nowrap;
}
.portal-card:hover .portal-btn {
    background: rgba(255,255,255,.22);
    border-color: rgba(255,255,255,.5);
}
.portal-btn svg { width: 14px; height: 14px; flex-shrink: 0; }

/* ── STATS STRIP ── */
.stats-strip {
    background: #fff;
    border-top: 1px solid var(--gray-200);
}
.stats-inner {
    max-width: 920px; margin: 0 auto;
    display: grid; grid-template-columns: repeat(3, 1fr);
}
.stat-item {
    padding: 22px 28px;
    border-right: 1px solid var(--gray-200);
    text-align: center;
}
.stat-item:last-child { border-right: none; }
.stat-num  { font-size: 22px; font-weight: 700; color: var(--gray-900); }
.stat-label { font-size: 12px; color: var(--gray-400); margin-top: 3px; }

/* ── FOOTER ── */
.footer {
    background: #fff;
    border-top: 1px solid var(--gray-200);
    padding: 20px 40px;
    text-align: center;
    font-size: 12px;
    color: var(--gray-400);
}
.footer a { color: var(--gray-600); text-decoration: none; }
.footer a:hover { color: var(--blue); }

/* ── RESPONSIVE ── */
@media (max-width: 700px) {
    .navbar-medilink { padding: 0 20px; }
    .hero { padding: 64px 20px 80px; }
    .hero h1 { font-size: 34px; }
    .hero p { font-size: 15px; margin-bottom: 36px; }
    .portals { grid-template-columns: 1fr; max-width: 340px; gap: 14px; }
    .stats-inner { grid-template-columns: 1fr; }
    .stat-item { border-right: none; border-bottom: 1px solid var(--gray-200); }
}
</style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar-medilink">
    <a href="index.php" class="nav-logo">
        <span>MediLink</span>
    </a>
    <div class="nav-badge">
        <span class="nav-badge-dot"></span>
        Service disponible · Lun–Sam 8h–18h
    </div>
</nav>

<!-- ── HERO ── -->
<div class="hero">
    <div class="hero-inner">
        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Plateforme de gestion des rendez-vous médicaux
        </div>

        <h1>Votre santé,<br>simplifiée <em>en quelques clics</em></h1>
        <p>Réservez, modifiez et gérez vos consultations médicales en toute simplicité. Accédez à votre espace en choisissant votre profil.</p>

        <!-- ── PORTAILS ── -->
        <div class="portals">

            <!-- Patient -->
            <a href="Views/front/homePatient.php" class="portal-card portal-patient">
                <div class="portal-icon-wrap">🧑‍⚕️</div>
                <div class="portal-title">Espace Patient</div>
                <div class="portal-desc">Réservez vos rendez-vous, consultez votre historique et gérez vos consultations.</div>
                <div class="portal-btn">
                    Accéder
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </div>
            </a>

            <!-- Médecin -->
            <a href="Views/front/loginMedecin.php" class="portal-card portal-medecin">
                <div class="portal-icon-wrap">⚕️</div>
                <div class="portal-title">Espace Médecin</div>
                <div class="portal-desc">Consultez vos patients, gérez vos plannings et accédez aux fiches médicales.</div>
                <div class="portal-btn">
                    Accéder
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </div>
            </a>

            <!-- Admin -->
            <a href="Views/admin/admin.php" class="portal-card portal-admin">
                <div class="portal-icon-wrap">⚙️</div>
                <div class="portal-title">Administration</div>
                <div class="portal-desc">Gérez les utilisateurs, les médecins et la configuration de la plateforme.</div>
                <div class="portal-btn">
                    Accéder
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </div>
            </a>

        </div>
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
            <div class="stat-num">&lt; 2 min</div>
            <div class="stat-label">Temps de réservation</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">Lun – Sam</div>
            <div class="stat-label">8h00 – 18h00</div>
        </div>
    </div>
</div>

<!-- ── FOOTER ── -->
<div class="footer">
    MediLink &copy; <?php echo date('Y'); ?> &nbsp;·&nbsp;
    <a href="Views/admin/admin.php">Administration</a>
</div>

</body>
</html>