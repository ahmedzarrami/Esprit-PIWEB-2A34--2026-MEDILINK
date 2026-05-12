<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink — Accueil</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --blue: #2563eb; --blue-dark: #1d4ed8; --blue-light: #eff6ff;
            --green: #10b981; --green-light: #ecfdf5;
            --purple: #8b5cf6; --purple-light: #f5f3ff;
            --navy: #0f172a; --gray-50: #f8fafc; --gray-100: #f1f5f9;
            --gray-200: #e2e8f0; --gray-400: #94a3b8; --gray-600: #475569; --gray-900: #0f172a;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--gray-50); color: var(--gray-900); min-height: 100vh; overflow-x: hidden; }

        /* ── Navbar ── */
        .navbar { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-bottom: 1px solid rgba(255,255,255,0.3); padding: 0 40px; height: 72px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05); }
        .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-mark { width: 38px; height: 38px; background: linear-gradient(135deg, var(--blue), var(--purple)); color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 20px; box-shadow: 0 4px 12px rgba(37,99,235,0.3); }
        .logo-text { font-size: 20px; font-weight: 800; color: var(--gray-900); letter-spacing: -0.5px; }
        .logo-text span { background: linear-gradient(135deg, var(--blue), var(--purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .nav-links { display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.5); padding: 6px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.8); }
        .nav-links a { padding: 8px 18px; border-radius: 10px; font-size: 14px; font-weight: 600; color: var(--gray-600); text-decoration: none; transition: all .3s cubic-bezier(0.4, 0, 0.2, 1); }
        .nav-links a:hover, .nav-links a.active { background: #fff; color: var(--blue); box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .nav-badge { background: linear-gradient(135deg, var(--navy), #1e293b); color: #fff; padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all .3s ease; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 4px 15px rgba(15,23,42,0.2); }
        .nav-badge:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(15,23,42,0.3); }

        /* ── Hero ── */
        .hero { position: relative; background: var(--navy); color: #fff; padding: 120px 40px 140px; text-align: center; overflow: hidden; }
        .hero::before { content: ''; position: absolute; top: -50%; left: -10%; width: 60%; height: 200%; background: radial-gradient(circle, rgba(37,99,235,0.4) 0%, rgba(15,23,42,0) 60%); transform: rotate(-15deg); pointer-events: none; }
        .hero::after { content: ''; position: absolute; top: -20%; right: -10%; width: 50%; height: 150%; background: radial-gradient(circle, rgba(139,92,246,0.3) 0%, rgba(15,23,42,0) 60%); transform: rotate(15deg); pointer-events: none; }
        .hero-content { position: relative; z-index: 10; max-width: 800px; margin: 0 auto; }
        .hero-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); padding: 8px 20px; border-radius: 999px; font-size: 13px; font-weight: 600; margin-bottom: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .hero-badge span { display: inline-block; width: 8px; height: 8px; background: #4ade80; border-radius: 50%; box-shadow: 0 0 10px #4ade80; }
        .hero h1 { font-size: 64px; font-weight: 800; margin-bottom: 24px; line-height: 1.1; letter-spacing: -2px; }
        .hero h1 span { background: linear-gradient(135deg, #60a5fa, #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { font-size: 20px; color: rgba(255,255,255,.7); max-width: 600px; margin: 0 auto 48px; line-height: 1.6; font-weight: 400; }
        .hero-btn { display: inline-flex; align-items: center; gap: 10px; background: linear-gradient(135deg, var(--blue), var(--purple)); color: #fff; padding: 18px 40px; border-radius: 14px; font-weight: 700; font-size: 16px; text-decoration: none; transition: all .3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 10px 30px rgba(37,99,235,0.4); border: 1px solid rgba(255,255,255,0.2); }
        .hero-btn:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 20px 40px rgba(37,99,235,0.6); }

        /* ── Modules ── */
        .section { padding: 100px 40px; max-width: 1280px; margin: 0 auto; position: relative; }
        .section::before { content: ''; position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 100vw; height: 100%; background: radial-gradient(ellipse at top, rgba(255,255,255,0.8) 0%, rgba(248,250,252,1) 100%); z-index: -1; }
        .section-title { font-size: 40px; font-weight: 800; margin-bottom: 12px; text-align: center; letter-spacing: -1px; }
        .section-sub { color: var(--gray-600); font-size: 18px; margin-bottom: 60px; text-align: center; font-weight: 500; }
        .modules-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }

        .module-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.8); border-radius: 24px; padding: 40px 32px; text-decoration: none; color: inherit; transition: all .4s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; gap: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); position: relative; overflow: hidden; }
        .module-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(255,255,255,0.5) 0%, rgba(255,255,255,0) 100%); z-index: 1; pointer-events: none; }
        .module-card > * { position: relative; z-index: 2; }
        .module-card:hover { transform: translateY(-8px); box-shadow: 0 20px 50px rgba(0,0,0,0.08); border-color: #fff; }
        .module-card.blue:hover  { box-shadow: 0 20px 50px rgba(37,99,235,0.15); }
        .module-card.green:hover { box-shadow: 0 20px 50px rgba(16,185,129,0.15); }
        .module-card.purple:hover{ box-shadow: 0 20px 50px rgba(139,92,246,0.15); }

        .card-icon { width: 64px; height: 64px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: 0 8px 20px rgba(0,0,0,0.05); }
        .blue  .card-icon { background: linear-gradient(135deg, var(--blue-light), #dbeafe); color: var(--blue); }
        .green .card-icon { background: linear-gradient(135deg, var(--green-light), #d1fae5); color: var(--green); }
        .purple.card-icon, .purple .card-icon { background: linear-gradient(135deg, var(--purple-light), #ede9fe); color: var(--purple); }

        .card-title { font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
        .card-desc  { font-size: 15px; color: var(--gray-600); line-height: 1.7; flex: 1; font-weight: 500; }
        .card-links { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 12px; }
        .card-link  { padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 700; text-decoration: none; transition: all .3s ease; }
        .blue  .card-link-primary { background: var(--blue);   color: #fff; box-shadow: 0 4px 15px rgba(37,99,235,0.3); }
        .green .card-link-primary { background: var(--green);  color: #fff; box-shadow: 0 4px 15px rgba(16,185,129,0.3); }
        .purple.card-link-primary, .purple .card-link-primary { background: var(--purple); color: #fff; box-shadow: 0 4px 15px rgba(139,92,246,0.3); }
        .card-link-primary:hover { opacity: .9; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        .card-link-secondary { background: rgba(15,23,42,0.04); color: var(--gray-900); border: 1px solid rgba(15,23,42,0.05); }
        .card-link-secondary:hover { background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transform: translateY(-2px); }

        /* ── Stats ── */
        .stats { background: linear-gradient(135deg, var(--navy) 0%, #1e293b 100%); color: #fff; padding: 80px 40px; position: relative; overflow: hidden; }
        .stats::before { content: ''; position: absolute; inset: 0; background: url('data:image/svg+xml;utf8,<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="1" fill="rgba(255,255,255,0.05)"/></svg>') repeat; }
        .stats-inner { position: relative; z-index: 10; max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; text-align: center; }
        .stat-item { padding: 30px; border-radius: 20px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); backdrop-filter: blur(10px); }
        .stat-num  { font-size: 56px; font-weight: 800; background: linear-gradient(135deg, #60a5fa, #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; line-height: 1; margin-bottom: 12px; }
        .stat-label{ font-size: 16px; color: rgba(255,255,255,.7); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

        /* ── Footer ── */
        .footer { background: #fff; border-top: 1px solid var(--gray-200); padding: 32px 40px; text-align: center; color: var(--gray-400); font-size: 14px; font-weight: 500; }

        @media (max-width: 1024px) {
            .modules-grid { grid-template-columns: repeat(2, 1fr); }
            .hero h1 { font-size: 48px; }
        }
        @media (max-width: 768px) {
            .modules-grid { grid-template-columns: 1fr; }
            .hero h1 { font-size: 40px; }
            .stats-inner { grid-template-columns: 1fr; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="/medilink_medicament/MediLink/index.php" class="nav-logo">
        <div class="logo-mark">+</div>
        <div class="logo-text">Medi<span>Link</span></div>
    </a>
    <div class="nav-links">
        <a href="/medilink_medicament/MediLink/index.php" class="active"><i class="fas fa-home"></i> Accueil</a>
        <a href="/medilink_medicament/MediLink/public/front/index.php?action=medicaments"><i class="fas fa-pills"></i> Médicaments</a>
        <a href="/medilink_medicament/MediLink/index.php?module=forum&controller=forum&action=list"><i class="fas fa-comments"></i> Forum</a>
        <a href="/medilink_medicament/MediLink/index.php?module=rdv"><i class="fas fa-calendar-alt"></i> Gestion RDV</a>
    </div>
    <a href="/medilink_medicament/MediLink/public/back/index.php" class="nav-badge">
        <i class="fas fa-th-large"></i> Administration
    </a>
</nav>

<section class="hero">
    <div class="hero-content">
        <div class="hero-badge"><span></span> 🏥 Plateforme médicale intégrée</div>
        <h1>Bienvenue sur <span>MediLink</span></h1>
        <p>Gérez vos médicaments, échangez sur le forum santé et organisez vos rendez-vous médicaux en un seul endroit.</p>
        <a href="/medilink_medicament/MediLink/public/front/index.php" class="hero-btn">Accéder à la plateforme <i class="fas fa-arrow-right"></i></a>
    </div>
</section>

<div class="section">
    <div class="section-title">Nos modules</div>
    <div class="section-sub">Trois outils complets pour une gestion médicale optimale</div>

    <div class="modules-grid">

        <!-- Médicaments -->
        <div class="module-card blue">
            <div class="card-icon"><i class="fas fa-pills"></i></div>
            <div class="card-title">Gestion des Médicaments</div>
            <div class="card-desc">Consultez, recherchez et gérez les médicaments disponibles. Créez et suivez vos ordonnances en ligne avec l'aide de notre assistant IA.</div>
            <div class="card-links">
                <a href="/medilink_medicament/MediLink/public/front/index.php" class="card-link card-link-primary">Front Office</a>
                <a href="/medilink_medicament/MediLink/public/back/index.php"  class="card-link card-link-secondary">Back Office</a>
                <a href="/medilink_medicament/MediLink/public/front/index.php?action=assistant" class="card-link card-link-secondary">🤖 Assistant</a>
            </div>
        </div>

        <!-- Forum -->
        <div class="module-card green">
            <div class="card-icon"><i class="fas fa-comments"></i></div>
            <div class="card-title">Forum Santé</div>
            <div class="card-desc">Échangez avec des patients et des professionnels de santé. Posez vos questions, partagez vos expériences et obtenez des réponses.</div>
            <div class="card-links">
                <a href="/medilink_medicament/MediLink/index.php?module=forum&controller=forum&action=list" class="card-link card-link-primary">Voir les forums</a>
                <a href="/medilink_medicament/MediLink/index.php?module=forum&controller=forum&action=adminList" class="card-link card-link-secondary">Administration</a>
            </div>
        </div>

        <!-- RDV -->
        <div class="module-card purple">
            <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="card-title">Gestion des RDV</div>
            <div class="card-desc">Planifiez et gérez les rendez-vous médicaux. Espace dédié aux patients et aux médecins pour une organisation simplifiée.</div>
            <div class="card-links">
                <a href="/medilink_medicament/MediLink/index.php?module=rdv&action=patient" class="card-link card-link-primary">Espace Patient</a>
                <a href="/medilink_medicament/MediLink/index.php?module=rdv&action=medecin" class="card-link card-link-secondary">Espace Médecin</a>
                <a href="/medilink_medicament/MediLink/index.php?module=rdv&action=admin"   class="card-link card-link-secondary">Administration</a>
            </div>
        </div>

    </div>
</div>

<div class="stats">
    <div class="stats-inner">
        <div class="stat-item"><div class="stat-num">3</div><div class="stat-label">Modules intégrés</div></div>
        <div class="stat-item"><div class="stat-num">100%</div><div class="stat-label">En ligne</div></div>
        <div class="stat-item"><div class="stat-num">24/7</div><div class="stat-label">Disponible</div></div>
    </div>
</div>

<footer class="footer">
    <strong>MediLink</strong> — Plateforme médicale &copy; <?= date('Y') ?>
</footer>

</body>
</html>
