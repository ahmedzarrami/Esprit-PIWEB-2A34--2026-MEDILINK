<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediLink — Parapharmacie en ligne</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<style>
/* [KEEP ALL YOUR EXISTING STYLES - they remain the same] */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --blue:        #1a56db;
    --blue-dark:   #1a46c4;
    --blue-light:  #eff4ff;
    --blue-mid:    #6694f8;
    --green:       #0da271;
    --green-dark:  #059669;
    --green-light: #ecfdf5;
    --navy:        #0f1b2d;
    --navy2:       #1e2f45;
    --red:         #dc2626;
    --red-light:   #fee2e2;
    --orange:      #ea580c;
    --orange-light:#fff7ed;
    --gray-50:     #f8fafc;
    --gray-100:    #f1f5f9;
    --gray-200:    #e2e8f0;
    --gray-400:    #94a3b8;
    --gray-500:    #64748b;
    --gray-600:    #475569;
    --gray-700:    #334155;
    --gray-900:    #0f172a;
    --radius:      12px;
    --radius-lg:   18px;
    --radius-xl:   24px;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
    --shadow-lg: 0 20px 40px rgba(0,0,0,0.12);
}
body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--gray-50); color:var(--gray-900); font-size:14px; line-height:1.6; }

/* ── NAVBAR ── */
.navbar {
    background:#fff; border-bottom:1px solid var(--gray-200);
    padding:0 40px; height:68px;
    display:flex; align-items:center; justify-content:space-between;
    position:sticky; top:0; z-index:1000;
    box-shadow:var(--shadow-sm);
}
.nav-logo { display:flex; align-items:center; gap:10px; text-decoration:none; color:var(--gray-900); }
.nav-logo-icon {
    width:38px; height:38px; background:linear-gradient(135deg,var(--blue),#3b7ff7);
    border-radius:10px; display:flex; align-items:center; justify-content:center;
    box-shadow:0 4px 10px rgba(26,86,219,.3);
}
.nav-logo-icon svg { fill:#fff; }
.nav-logo-text { font-size:17px; font-weight:700; }
.nav-logo-text span { color:var(--blue); }
.nav-links { display:flex; gap:4px; }
.nav-links a {
    padding:7px 16px; border-radius:8px; color:var(--gray-600);
    text-decoration:none; font-size:13px; font-weight:500; transition:.15s;
}
.nav-links a:hover, .nav-links a.active { background:var(--blue-light); color:var(--blue); }
.nav-cart {
    display:flex; align-items:center; gap:8px; position:relative;
}
.cart-btn {
    display:flex; align-items:center; gap:8px;
    padding:8px 16px; background:var(--green-light); color:var(--green-dark);
    border-radius:10px; font-size:13px; font-weight:600;
    border:1px solid rgba(13,162,113,.2); cursor:pointer; transition:.2s;
}
.cart-btn:hover { background:var(--green); color:#fff; transform:translateY(-1px); }
.cart-badge {
    background:var(--green); color:#fff; font-size:10px; font-weight:700;
    width:18px; height:18px; border-radius:50%; display:flex; align-items:center; justify-content:center;
    position:absolute; top:-6px; right:-6px;
}
.btn-admin {
    display:flex; align-items:center; gap:7px;
    padding:8px 16px; background:var(--navy); color:#fff;
    border-radius:8px; font-size:13px; font-weight:500;
    text-decoration:none; transition:.2s;
}
.btn-admin:hover { background:var(--navy2); transform:translateY(-1px); }
.client-info {
    display:flex; align-items:center; gap:10px;
    background:var(--blue-light); padding:6px 14px;
    border-radius:100px; font-size:12px; font-weight:600;
    color:var(--blue);
}
.client-info span:first-child { opacity:0.7; font-weight:400; }
.btn-logout {
    background:none; border:none; color:var(--gray-500);
    cursor:pointer; font-size:11px; font-weight:500;
    margin-left:5px; padding:4px 8px; border-radius:6px;
}
.btn-logout:hover { background:var(--gray-200); }

/* ── HERO ── */
.hero {
    background:linear-gradient(135deg,#1a46c4 0%,#2563eb 55%,#3b7ff7 100%);
    padding:72px 40px 88px; position:relative; overflow:hidden;
}
.hero::before { content:''; position:absolute; top:-80px; right:-80px; width:350px; height:350px; background:rgba(255,255,255,.06); border-radius:50%; }
.hero::after  { content:''; position:absolute; bottom:-100px; left:42%; width:220px; height:220px; background:rgba(255,255,255,.04); border-radius:50%; }
.hero-inner { max-width:960px; margin:0 auto; position:relative; z-index:1; }
.hero-badge {
    display:inline-flex; align-items:center; gap:7px;
    background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);
    border-radius:100px; padding:5px 14px; font-size:12px; color:#fff; margin-bottom:22px;
}
.hero-badge-dot { width:7px; height:7px; background:#4ade80; border-radius:50%; flex-shrink:0; animation:pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.7;transform:scale(1.2)} }
.hero h1 { font-size:42px; font-weight:700; color:#fff; line-height:1.2; margin-bottom:14px; letter-spacing:-0.5px; }
.hero h1 em { font-family:'Instrument Serif',serif; font-style:italic; font-weight:400; }
.hero p { color:rgba(255,255,255,.85); font-size:15px; max-width:480px; line-height:1.75; }

/* ── STATS ── */
.stats-strip { background:#fff; border-bottom:1px solid var(--gray-200); }
.stats-inner { max-width:960px; margin:0 auto; display:grid; grid-template-columns:repeat(4,1fr); }
.stat-item { padding:20px 28px; border-right:1px solid var(--gray-200); }
.stat-item:last-child { border-right:none; }
.stat-num { font-size:22px; font-weight:800; color:var(--gray-900); }
.stat-label { font-size:12px; color:var(--gray-400); margin-top:3px; font-weight:500; }

/* ── MAIN ── */
.main-content { max-width:960px; margin:0 auto; padding:44px 40px 80px; }
.section-heading {
    font-size:15px; font-weight:700; color:var(--gray-900);
    margin-bottom:20px; display:flex; align-items:center; gap:10px;
}
.section-heading::before { content:''; display:inline-block; width:4px; height:18px; background:var(--blue); border-radius:3px; }

/* ── SEARCH ── */
.search-card {
    background:#fff; border:1px solid var(--gray-200);
    border-radius:var(--radius-xl); padding:28px; margin-bottom:36px;
    box-shadow:var(--shadow-sm);
}
.search-filters { display:grid; grid-template-columns:1fr 1fr auto; gap:14px; margin-bottom:16px; align-items:end; }
.form-group-inline { display:flex; flex-direction:column; gap:6px; }
.filter-label { font-size:11px; font-weight:600; color:var(--gray-600); text-transform:uppercase; letter-spacing:.05em; }
.filter-input {
    padding:10px 13px; border:1px solid var(--gray-200); border-radius:10px;
    font-size:14px; font-family:'Plus Jakarta Sans',sans-serif;
    color:var(--gray-900); background:#fff; outline:none; transition:.15s; height:42px;
}
.filter-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,86,219,.1); }
.btn-reset {
    height:42px; padding:0 18px; background:var(--gray-100); color:var(--gray-700);
    border:1px solid var(--gray-200); border-radius:10px; font-size:13px; font-weight:500; cursor:pointer;
    font-family:'Plus Jakarta Sans',sans-serif; transition:.2s;
}
.btn-reset:hover { background:var(--gray-200); }
.cat-tags { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
.cat-tag {
    padding:5px 15px; border-radius:100px; font-size:12px; font-weight:500; cursor:pointer;
    border:1px solid var(--gray-200); background:var(--gray-100); color:var(--gray-600);
    font-family:'Plus Jakarta Sans',sans-serif; transition:.15s;
}
.cat-tag:hover, .cat-tag.active { background:var(--blue-light); border-color:var(--blue-mid); color:var(--blue-dark); }
.results-info { font-size:12px; color:var(--gray-500); }
.results-info strong { color:var(--gray-900); font-weight:700; }

/* ── PRODUCTS GRID ── */
.products-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); gap:20px; }
.product-card {
    background:#fff; border:1px solid var(--gray-200);
    border-radius:var(--radius-lg); overflow:hidden;
    transition:all 0.25s ease; position:relative;
}
.product-card::before {
    content:''; position:absolute; top:0; left:0; right:0; height:3px;
    background:linear-gradient(90deg,var(--blue),#3b7ff7); transform:scaleX(0); transition:.25s; transform-origin:left;
}
.product-card:hover { border-color:var(--blue-mid); transform:translateY(-5px); box-shadow:0 16px 32px rgba(26,86,219,.13); }
.product-card:hover::before { transform:scaleX(1); }
.product-thumb {
    height:130px; background:linear-gradient(135deg,var(--gray-50),var(--blue-light));
    display:flex; align-items:center; justify-content:center; font-size:52px;
    border-bottom:1px solid var(--gray-200); position:relative; overflow:hidden;
}
.product-thumb::after {
    content:''; position:absolute; inset:0;
    background:radial-gradient(circle at 70% 30%, rgba(26,86,219,.06), transparent 60%);
}
.product-body { padding:18px; }
.product-ref { font-size:10px; font-weight:600; color:var(--gray-400); text-transform:uppercase; letter-spacing:.08em; margin-bottom:4px; }
.product-name { font-size:15px; font-weight:700; color:var(--gray-900); margin-bottom:6px; line-height:1.35; }
.product-desc { font-size:12px; color:var(--gray-500); margin-bottom:12px; line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.product-cat { display:inline-block; font-size:11px; font-weight:600; padding:3px 10px; border-radius:100px; margin-bottom:12px; background:var(--blue-light); color:var(--blue); }
.product-footer { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px; margin-bottom:14px; }
.product-price { font-size:20px; font-weight:800; color:var(--gray-900); }
.product-price small { font-size:11px; font-weight:500; color:var(--gray-400); }
.stock-badge { font-size:10px; font-weight:700; padding:3px 10px; border-radius:100px; display:inline-block; }
.stock-ok  { background:var(--green-light); color:#065f46; }
.stock-low { background:var(--orange-light); color:var(--orange); }
.stock-out { background:var(--red-light); color:var(--red); }
.btn-commander {
    width:100%; height:40px; border:none; border-radius:10px;
    font-size:13px; font-weight:700; cursor:pointer;
    font-family:'Plus Jakarta Sans',sans-serif; transition:.2s;
    display:flex; align-items:center; justify-content:center; gap:6px;
    background:linear-gradient(135deg,var(--blue),#3b7ff7); color:#fff;
    box-shadow:0 4px 12px rgba(26,86,219,.25);
}
.btn-commander:hover { background:linear-gradient(135deg,var(--blue-dark),var(--blue)); transform:translateY(-1px); box-shadow:0 6px 18px rgba(26,86,219,.35); }
.btn-commander:disabled { background:var(--gray-200); color:var(--gray-400); cursor:not-allowed; box-shadow:none; transform:none; }
.empty-state { grid-column:1/-1; text-align:center; padding:56px 20px; color:var(--gray-400); }
.empty-state-icon { font-size:40px; margin-bottom:14px; opacity:.5; }
.empty-state strong { display:block; font-size:16px; color:var(--gray-600); margin-bottom:6px; }

/* ── ORDER MODAL ── */
.modal-overlay {
    display:none; position:fixed; inset:0; background:rgba(15,27,45,.55);
    z-index:2000; align-items:center; justify-content:center; backdrop-filter:blur(5px);
}
.modal-overlay.open { display:flex; }
.modal {
    background:#fff; border-radius:var(--radius-xl); width:100%; max-width:480px;
    max-height:92vh; overflow-y:auto;
    box-shadow:0 32px 64px -12px rgba(0,0,0,0.3);
    animation:slideUp .25s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes slideUp { from{opacity:0;transform:translateY(24px) scale(.96)} to{opacity:1;transform:translateY(0) scale(1)} }

/* Modal product header */
.modal-product-header {
    background:linear-gradient(135deg,#1a46c4,#2563eb);
    padding:28px 28px 24px; border-radius:var(--radius-xl) var(--radius-xl) 0 0;
    position:relative; overflow:hidden;
}
.modal-product-header::before { content:''; position:absolute; top:-30px; right:-30px; width:130px; height:130px; background:rgba(255,255,255,.08); border-radius:50%; }
.mph-emoji { font-size:40px; margin-bottom:10px; display:block; }
.mph-ref { font-size:10px; font-weight:600; color:rgba(255,255,255,.6); text-transform:uppercase; letter-spacing:.1em; margin-bottom:4px; }
.mph-name { font-size:20px; font-weight:700; color:#fff; margin-bottom:6px; line-height:1.3; }
.mph-cat { display:inline-block; background:rgba(255,255,255,.2); color:#fff; font-size:11px; font-weight:600; padding:3px 10px; border-radius:100px; margin-bottom:16px; }
.mph-price-row { display:flex; align-items:center; gap:16px; }
.mph-price { font-size:28px; font-weight:800; color:#fff; }
.mph-price small { font-size:13px; font-weight:500; color:rgba(255,255,255,.7); }
.mph-stock-badge { font-size:11px; font-weight:600; padding:4px 12px; border-radius:100px; }
.mph-close {
    position:absolute; top:16px; right:16px; width:32px; height:32px;
    background:rgba(255,255,255,.15); border:none; border-radius:8px; color:#fff; font-size:18px;
    cursor:pointer; display:flex; align-items:center; justify-content:center; transition:.2s;
}
.mph-close:hover { background:rgba(255,255,255,.25); transform:rotate(90deg); }

/* Modal body */
.modal-body { padding:28px; }
.order-section-title {
    font-size:13px; font-weight:700; color:var(--gray-600); text-transform:uppercase;
    letter-spacing:.06em; margin-bottom:18px; padding-bottom:10px;
    border-bottom:1px solid var(--gray-100);
    display:flex; align-items:center; gap:8px;
}
.form-group { display:flex; flex-direction:column; gap:7px; margin-bottom:18px; }
.form-label { font-size:12px; font-weight:700; color:var(--gray-700); text-transform:uppercase; letter-spacing:.03em; }
.form-input, .form-select {
    padding:12px 14px; border:1.5px solid var(--gray-200); border-radius:12px;
    font-size:14px; font-family:'Plus Jakarta Sans',sans-serif; color:var(--gray-900);
    background:#fff; outline:none; transition:.15s;
}
.form-input:focus, .form-select:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,86,219,.1); }
.form-input.error, .form-select.error { border-color:var(--red); background:#fff5f5; }
.field-error { font-size:11px; color:var(--red); display:none; margin-top:2px; font-weight:500; }
.field-error.show { display:block; }

/* Quantity stepper */
.qty-stepper { display:flex; align-items:center; gap:0; border:1.5px solid var(--gray-200); border-radius:12px; overflow:hidden; height:46px; }
.qty-btn {
    width:46px; height:100%; background:var(--gray-50); border:none; border-radius:0; cursor:pointer;
    font-size:20px; font-weight:300; color:var(--gray-600); transition:.15s; flex-shrink:0;
    font-family:monospace; display:flex; align-items:center; justify-content:center;
}
.qty-btn:hover { background:var(--blue-light); color:var(--blue); }
.qty-input {
    flex:1; text-align:center; border:none; border-left:1.5px solid var(--gray-200); border-right:1.5px solid var(--gray-200);
    font-size:16px; font-weight:700; color:var(--gray-900); font-family:'Plus Jakarta Sans',sans-serif;
    outline:none; background:#fff; height:100%;
}
.qty-input:focus { background:var(--blue-light); }

/* Payment options */
.payment-options { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.payment-option { display:none; }
.payment-label {
    display:flex; flex-direction:column; align-items:center; gap:6px;
    padding:14px 10px; border:2px solid var(--gray-200); border-radius:12px;
    cursor:pointer; transition:.2s; text-align:center;
}
.payment-label:hover { border-color:var(--blue-mid); background:var(--blue-light); }
.payment-option:checked + .payment-label { border-color:var(--blue); background:var(--blue-light); color:var(--blue); }
.payment-icon { font-size:24px; }
.payment-text { font-size:12px; font-weight:600; }

/* Order total */
.order-total-box {
    background:linear-gradient(135deg,var(--gray-50),var(--blue-light));
    border:1px solid var(--gray-200); border-radius:14px; padding:16px 18px;
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:20px;
}
.otb-label { font-size:13px; font-weight:600; color:var(--gray-600); }
.otb-amount { font-size:24px; font-weight:800; color:var(--blue); }
.otb-amount small { font-size:13px; font-weight:500; color:var(--gray-500); }

/* Form alert */
.form-alert { display:none; padding:12px 16px; border-radius:12px; font-size:13px; margin-bottom:16px; align-items:center; gap:8px; font-weight:500; }
.form-alert.show { display:flex; }
.form-alert.success { background:var(--green-light); color:#065f46; border-left:4px solid var(--green); }
.form-alert.err { background:var(--red-light); color:#991b1b; border-left:4px solid var(--red); }

.btn-submit {
    width:100%; height:50px; background:linear-gradient(135deg,var(--blue),#3b7ff7); color:#fff;
    border:none; border-radius:14px; font-size:15px; font-weight:700;
    cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:.2s;
    display:flex; align-items:center; justify-content:center; gap:10px;
    box-shadow:0 6px 20px rgba(26,86,219,.3);
}
.btn-submit:hover { transform:translateY(-1px); box-shadow:0 10px 28px rgba(26,86,219,.4); }

/* ── SUCCESS OVERLAY ── */
.success-overlay {
    display:none; position:fixed; inset:0; background:rgba(15,27,45,.6);
    z-index:3000; align-items:center; justify-content:center; backdrop-filter:blur(6px);
}
.success-overlay.open { display:flex; }
.success-card {
    background:#fff; border-radius:var(--radius-xl); padding:48px 40px; text-align:center;
    max-width:360px; width:100%; margin:20px;
    box-shadow:var(--shadow-lg);
    animation:successPop .4s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes successPop { from{opacity:0;transform:scale(.8)} to{opacity:1;transform:scale(1)} }
.success-icon { font-size:64px; margin-bottom:16px; display:block; }
.success-title { font-size:22px; font-weight:700; color:var(--gray-900); margin-bottom:8px; }
.success-sub { font-size:14px; color:var(--gray-500); margin-bottom:28px; line-height:1.6; }
.success-id { display:inline-block; background:var(--green-light); color:var(--green-dark); font-size:12px; font-weight:700; padding:4px 14px; border-radius:100px; margin-bottom:24px; letter-spacing:.03em; }
.btn-success-close {
    width:100%; height:46px; background:var(--navy); color:#fff;
    border:none; border-radius:12px; font-size:14px; font-weight:700;
    cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:.2s;
}
.btn-success-close:hover { background:var(--navy2); }

/* ── FOOTER ── */
.footer { background:var(--navy); color:rgba(255,255,255,.5); text-align:center; padding:28px 20px; font-size:12px; margin-top:40px; }
.footer a { color:rgba(255,255,255,.7); text-decoration:none; }
.footer a:hover { color:#fff; }

@media (max-width:720px) {
    .navbar { padding:0 20px; }
    .hero { padding:48px 24px 60px; }
    .hero h1 { font-size:30px; }
    .main-content { padding:32px 20px 60px; }
    .search-filters { grid-template-columns:1fr; }
    .stats-inner { grid-template-columns:1fr 1fr; }
    .stat-item { padding:14px 20px; border-right:none; border-bottom:1px solid var(--gray-200); }
    .products-grid { gap:14px; }
    .modal { margin:12px; }
    .payment-options { grid-template-columns:1fr 1fr; }
}
</style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar">
    <a href="#" class="nav-logo" onclick="return false;">
        <div class="nav-logo-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
        </div>
        <span class="nav-logo-text">Medi<span>Link</span></span>
    </a>
    <div class="nav-links">
        <a href="#" class="active" onclick="return false;">Accueil</a>
        <a href="#liste" onclick="document.getElementById('liste').scrollIntoView({behavior:'smooth'}); return false;">Catalogue</a>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
        <div class="client-info" id="clientInfo">
            <span>👤 Client</span>
            <span id="clientIdDisplay">—</span>
            <button class="btn-logout" onclick="resetClient()" title="Nouveau client">⟳</button>
        </div>
        <a href="admin.php" class="btn-admin">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
            Administration
        </a>
    </div>
</nav>

<!-- ── HERO ── -->
<div class="hero">
    <div class="hero-inner">
        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Livraison disponible · Lundi – Samedi · 8h00 – 18h00
        </div>
        <h1>Votre parapharmacie<br><em>en ligne</em> de confiance</h1>
        <p>Découvrez notre catalogue de produits de santé, beauté et bien-être. Commandez en quelques clics avec livraison rapide.</p>
    </div>
</div>

<!-- ── STATS ── -->
<div class="stats-strip">
    <div class="stats-inner">
        <div class="stat-item"><div class="stat-num" id="statTotal">0</div><div class="stat-label">Produits en catalogue</div></div>
        <div class="stat-item"><div class="stat-num" id="statCats">—</div><div class="stat-label">Catégories</div></div>
        <div class="stat-item"><div class="stat-num" id="statDispo">0</div><div class="stat-label">En stock</div></div>
        <div class="stat-item"><div class="stat-num">24h – 48h</div><div class="stat-label">Délai livraison</div></div>
    </div>
</div>

<!-- ── MAIN ── -->
<div class="main-content" id="liste">
    <div class="section-heading">Rechercher un produit</div>
    <div class="search-card">
        <div class="search-filters">
            <div class="form-group-inline">
                <span class="filter-label">Recherche</span>
                <input type="text" id="searchInput" class="filter-input" placeholder="Nom, référence, description…">
            </div>
            <div class="form-group-inline">
                <span class="filter-label">Catégorie</span>
                <select id="filterCat" class="filter-input"><option value="">Toutes les catégories</option></select>
            </div>
            <button class="btn-reset" onclick="resetFilters()">↺ Réinitialiser</button>
        </div>
        <div class="cat-tags" id="catTags"></div>
        <div class="results-info" id="resultsInfo"></div>
    </div>

    <div class="section-heading">Catalogue des produits</div>
    <div class="products-grid" id="productsGrid"></div>
</div>

<!-- ══════════════════════════════════════
     ORDER MODAL
══════════════════════════════════════ -->
<div class="modal-overlay" id="orderOverlay">
    <div class="modal">
        <!-- Product header -->
        <div class="modal-product-header" id="orderProductHeader">
            <button class="mph-close" onclick="closeOrderModal()">✕</button>
            <span class="mph-emoji" id="orderEmoji">💊</span>
            <div class="mph-ref" id="orderRef">—</div>
            <div class="mph-name" id="orderName">—</div>
            <span class="mph-cat" id="orderCat">—</span>
            <div class="mph-price-row">
                <div class="mph-price"><span id="orderPrice">0.000</span> <small>DT</small></div>
                <span class="mph-stock-badge stock-ok" id="orderStockBadge">—</span>
            </div>
        </div>

        <!-- Body -->
        <div class="modal-body">
            <div class="form-alert" id="orderAlert"><span id="orderAlertMsg"></span></div>

            <div class="order-section-title">
                <span>🛒</span> Votre commande
            </div>

            <!-- User ID - NOW AUTO-FILLED WITH CLIENT ID -->
            <div class="form-group">
                <label class="form-label">Votre identifiant client</label>
                <input type="text" id="orderUserId" class="form-input" readonly style="background:#f1f5f9; font-weight:600;">
                <span style="font-size:10px; color:var(--gray-500);">✓ Identifiant automatique pour suivre vos commandes</span>
            </div>

            <!-- Quantity -->
            <div class="form-group">
                <label class="form-label">Quantité *</label>
                <div class="qty-stepper">
                    <button class="qty-btn" type="button" onclick="changeQty(-1)">−</button>
                    <input type="number" id="orderQty" class="qty-input" value="1" min="1" oninput="updateTotal()">
                    <button class="qty-btn" type="button" onclick="changeQty(1)">+</button>
                </div>
                <span class="field-error" id="errQty">Quantité invalide (min. 1)</span>
            </div>

            <!-- Payment -->
            <div class="form-group">
                <label class="form-label">Mode de paiement *</label>
                <div class="payment-options">
                    <div>
                        <input type="radio" name="payment" id="pay_virement" value="virement" class="payment-option" checked>
                        <label for="pay_virement" class="payment-label">
                            <span class="payment-icon">🏦</span>
                            <span class="payment-text">Virement</span>
                        </label>
                    </div>
                    <div>
                        <input type="radio" name="payment" id="pay_carte" value="carte_bancaire" class="payment-option">
                        <label for="pay_carte" class="payment-label">
                            <span class="payment-icon">💳</span>
                            <span class="payment-text">Carte bancaire</span>
                        </label>
                    </div>
                    <div>
                        <input type="radio" name="payment" id="pay_paypal" value="paypal" class="payment-option">
                        <label for="pay_paypal" class="payment-label">
                            <span class="payment-icon">🅿️</span>
                            <span class="payment-text">PayPal</span>
                        </label>
                    </div>
                    <div>
                        <input type="radio" name="payment" id="pay_especes" value="especes" class="payment-option">
                        <label for="pay_especes" class="payment-label">
                            <span class="payment-icon">💵</span>
                            <span class="payment-text">Espèces</span>
                        </label>
                    </div>
                </div>
                <span class="field-error" id="errPayment">Veuillez choisir un mode de paiement</span>
            </div>

            <!-- Total -->
            <div class="order-total-box">
                <div>
                    <div class="otb-label">Total à payer</div>
                    <div style="font-size:11px;color:var(--gray-400);margin-top:2px;" id="totalCalc">1 × 0.000 DT</div>
                </div>
                <div class="otb-amount"><span id="orderTotal">0.000</span> <small>DT</small></div>
            </div>

            <button class="btn-submit" onclick="submitOrder()">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                Commander maintenant
            </button>
        </div>
    </div>
</div>

<!-- SUCCESS -->
<div class="success-overlay" id="successOverlay">
    <div class="success-card">
        <span class="success-icon">🎉</span>
        <div class="success-title">Commande confirmée !</div>
        <div class="success-id" id="successOrderId">N° —</div>
        <div class="success-sub" id="successMsg">Votre commande a été enregistrée avec succès et sera traitée dans les plus brefs délais.</div>
        <button class="btn-success-close" onclick="closeSuccessOverlay()">
            Retour au catalogue
        </button>
    </div>
</div>

<!-- ── FOOTER ── -->
<footer class="footer">
    MediLink © 2026 · <a href="admin.php">Administration</a>
</footer>

<script>
// ════════════════════════════════════
//  STORAGE
// ════════════════════════════════════
const STORAGE_KEY  = 'pharma_products';
const ORDERS_KEY   = 'pharma_orders';
const CLIENT_KEY   = 'pharma_client_id';

const DEMO_PRODUCTS = [
    { id: 171000000001, reference:"PHM-VIS-01", nom:"Crème Hydra Éclat SPF30", description:"Protection UV et hydratation profonde, texture légère.", prix:42.500, stock:18, categorie:"Soins visage" },
    { id: 171000000002, reference:"PHM-COR-02", nom:"Beurre corporel karité",  description:"Beurre riche pour peaux sèches, 200ml.", prix:29.900, stock:6,  categorie:"Soins corps" },
    { id: 171000000003, reference:"PHM-HYG-03", nom:"Gel douche douceur",       description:"Sans savon, pH neutre, 500ml.", prix:12.300, stock:2,  categorie:"Hygiène" },
    { id: 171000000004, reference:"PHM-COMP-04", nom:"Vitamine C + Zinc",       description:"Immunité & vitalité, 30 comprimés.", prix:18.750, stock:0,  categorie:"Compléments alimentaires" },
    { id: 171000000005, reference:"PHM-BEB-05", nom:"Lait nettoyant bébé",      description:"Sans parfum, hypoallergénique.", prix:15.200, stock:4,  categorie:"Bébé & Maman" },
    { id: 171000000006, reference:"PHM-CAP-06", nom:"Shampoing sec réparateur", description:"Cheveux fragiles, 150ml.", prix:9.990,  stock:11, categorie:"Capillaire" }
];

const CAT_ICONS = {
    'Soins visage':'🧴','Soins corps':'🫧','Hygiène':'🪥',
    'Compléments alimentaires':'💊','Bébé & Maman':'🍼',
    'Capillaire':'💆','Solaire':'☀️','Minceur':'⚖️','Orthopédie':'🦴','Autre':'📦'
};

// ════════════════════════════════════
//  CLIENT MANAGEMENT - FIXED!
// ════════════════════════════════════
function getClientId() {
    let clientId = localStorage.getItem(CLIENT_KEY);
    if(!clientId) {
        // Generate a unique client ID
        clientId = 'CLT_' + Date.now() + '_' + Math.random().toString(36).substr(2, 8).toUpperCase();
        localStorage.setItem(CLIENT_KEY, clientId);
    }
    return clientId;
}

function getClientNumber() {
    // Extract a numeric representation for display
    const clientId = getClientId();
    // Use a simple hash to create a 4-6 digit number
    let hash = 0;
    for(let i = 0; i < clientId.length; i++) {
        hash = ((hash << 5) - hash) + clientId.charCodeAt(i);
        hash = hash & hash;
    }
    return Math.abs(hash % 90000) + 10000;
}

function updateClientDisplay() {
    const clientNum = getClientNumber();
    document.getElementById('clientIdDisplay').textContent = '#' + clientNum;
}

function resetClient() {
    if(confirm('Générer un nouvel identifiant client ? Vos commandes précédentes resteront visibles dans l\'admin.')) {
        localStorage.removeItem(CLIENT_KEY);
        updateClientDisplay();
        showToast('Nouvel identifiant client généré ✓', 'success');
    }
}

function getProducts() {
    const raw = localStorage.getItem(STORAGE_KEY);
    if(!raw || raw === '[]') { saveProducts(DEMO_PRODUCTS); return [...DEMO_PRODUCTS]; }
    try { return JSON.parse(raw); } catch(e) { return [...DEMO_PRODUCTS]; }
}
function saveProducts(list) { localStorage.setItem(STORAGE_KEY, JSON.stringify(list)); }
function getOrders() { try { return JSON.parse(localStorage.getItem(ORDERS_KEY) || '[]'); } catch(e) { return []; } }
function saveOrders(list) { localStorage.setItem(ORDERS_KEY, JSON.stringify(list)); }
function escH(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

function showToast(msg, type) {
    // Simple alert for now - can be enhanced
    const toast = document.createElement('div');
    toast.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#0f1b2d;color:#fff;padding:12px 20px;border-radius:12px;z-index:9999;font-size:13px;font-weight:500;box-shadow:0 4px 12px rgba(0,0,0,0.2);animation:fadeInOut 2.5s ease;';
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2500);
}

// ════════════════════════════════════
//  CATALOG UI
// ════════════════════════════════════
function loadFrontUI() {
    updateClientDisplay();
    const products = getProducts();
    const uniqueCats = [...new Set(products.map(p => p.categorie).filter(Boolean))];
    const inStock = products.filter(p => (p.stock||0) > 0).length;
    document.getElementById('statTotal').textContent = products.length;
    document.getElementById('statCats').textContent  = uniqueCats.length;
    document.getElementById('statDispo').textContent = inStock;

    const catSelect = document.getElementById('filterCat');
    const currentSel = catSelect.value;
    catSelect.innerHTML = '<option value="">Toutes les catégories</option>';
    uniqueCats.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c; opt.textContent = c;
        if(currentSel === c) opt.selected = true;
        catSelect.appendChild(opt);
    });

    const tagsDiv = document.getElementById('catTags');
    tagsDiv.innerHTML = uniqueCats.map(c =>
        `<button class="cat-tag" data-cat="${escH(c)}">${CAT_ICONS[c]||'📦'} ${escH(c)}</button>`
    ).join('');
    document.querySelectorAll('.cat-tag').forEach(btn => btn.addEventListener('click', () => {
        document.getElementById('filterCat').value = btn.dataset.cat; applyFilters();
    }));
    applyFilters();
}

function applyFilters() {
    const products = getProducts();
    const search   = document.getElementById('searchInput').value.toLowerCase().trim();
    const category = document.getElementById('filterCat').value;
    const filtered = products.filter(p => {
        const matchSearch = !search || p.nom.toLowerCase().includes(search) || (p.reference||'').toLowerCase().includes(search) || (p.description||'').toLowerCase().includes(search);
        const matchCat = !category || p.categorie === category;
        return matchSearch && matchCat;
    });
    renderCatalog(filtered, products.length);
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterCat').value = '';
    applyFilters();
}

function renderCatalog(filtered, total) {
    const grid = document.getElementById('productsGrid');
    document.getElementById('resultsInfo').innerHTML =
        `<strong>${filtered.length}</strong> résultat${filtered.length!==1?'s':''} sur ${total} produit${total!==1?'s':''}`;

    if(!filtered.length) {
        grid.innerHTML = `<div class="empty-state"><div class="empty-state-icon">🛒</div><strong>Aucun produit trouvé</strong><span>Modifiez vos critères de recherche</span></div>`;
        return;
    }

    grid.innerHTML = filtered.map(p => {
        const stock = parseInt(p.stock)||0;
        let stockClass = 'stock-ok', stockText = `✅ ${stock} unités`;
        if(stock === 0) { stockClass='stock-out'; stockText='Rupture'; }
        else if(stock <= 5) { stockClass='stock-low'; stockText=`⚠️ ${stock} restants`; }
        const emoji = CAT_ICONS[p.categorie] || '📦';
        const isOut = stock === 0;
        return `
        <div class="product-card">
            <div class="product-thumb">${emoji}</div>
            <div class="product-body">
                <div class="product-ref">${escH(p.reference||'—')}</div>
                <div class="product-name">${escH(p.nom)}</div>
                <div class="product-desc">${escH((p.description||'Aucune description').substring(0,80))}</div>
                <span class="product-cat">${escH(p.categorie||'Autre')}</span>
                <div class="product-footer">
                    <div class="product-price">${parseFloat(p.prix||0).toFixed(3)} <small>DT</small></div>
                    <span class="stock-badge ${stockClass}">${stockText}</span>
                </div>
                <button class="btn-commander" onclick="openOrderModal(${p.id})" ${isOut?'disabled':''}>
                    ${isOut
                        ? '❌ Rupture de stock'
                        : '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg> Commander'
                    }
                </button>
            </div>
        </div>`;
    }).join('');
}

// ════════════════════════════════════
//  ORDER MODAL - FIXED WITH CLIENT ID
// ════════════════════════════════════
let _currentProduct = null;

function openOrderModal(id) {
    const products = getProducts();
    const p = products.find(x => x.id == id);
    if(!p) return;
    _currentProduct = p;

    const emoji = CAT_ICONS[p.categorie] || '📦';
    const stock = parseInt(p.stock)||0;
    let stockClass = 'stock-ok', stockLabel = `En stock (${stock})`;
    if(stock <= 5 && stock > 0) { stockClass='stock-low'; stockLabel=`Stock faible (${stock})`; }

    document.getElementById('orderEmoji').textContent = emoji;
    document.getElementById('orderRef').textContent   = p.reference || '—';
    document.getElementById('orderName').textContent  = p.nom;
    document.getElementById('orderCat').textContent   = p.categorie || 'Autre';
    document.getElementById('orderPrice').textContent = parseFloat(p.prix||0).toFixed(3);
    const badge = document.getElementById('orderStockBadge');
    badge.textContent = stockLabel;
    badge.className = `mph-stock-badge ${stockClass}`;

    // FIXED: Use the client's unique ID
    const clientNum = getClientNumber();
    document.getElementById('orderUserId').value = 'Client #' + clientNum + ' (ID: ' + getClientId().slice(-8) + ')';
    
    document.getElementById('orderQty').value = '1';
    document.getElementById('pay_virement').checked = true;
    document.querySelectorAll('.field-error').forEach(e => e.classList.remove('show'));
    document.querySelectorAll('.form-input,.form-select').forEach(e => e.classList.remove('error'));
    document.getElementById('orderAlert').className = 'form-alert';

    updateTotal();
    document.getElementById('orderOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeOrderModal() {
    document.getElementById('orderOverlay').classList.remove('open');
    document.body.style.overflow = '';
    _currentProduct = null;
}
document.getElementById('orderOverlay').addEventListener('click', function(e) {
    if(e.target === this) closeOrderModal();
});

function changeQty(delta) {
    const input = document.getElementById('orderQty');
    const val = Math.max(1, (parseInt(input.value)||1) + delta);
    const maxStock = _currentProduct ? parseInt(_currentProduct.stock)||99 : 99;
    input.value = Math.min(val, maxStock);
    updateTotal();
}

function updateTotal() {
    if(!_currentProduct) return;
    const qty  = Math.max(1, parseInt(document.getElementById('orderQty').value)||1);
    const prix = parseFloat(_currentProduct.prix||0);
    const total = (qty * prix).toFixed(3);
    document.getElementById('orderTotal').textContent = total;
    document.getElementById('totalCalc').textContent  = `${qty} × ${prix.toFixed(3)} DT`;
}

function submitOrder() {
    let valid = true;

    // FIXED: Store the actual client ID with the order
    const clientId = getClientId();
    const clientNum = getClientNumber();

    // qty validation
    const qty = parseInt(document.getElementById('orderQty').value);
    const errQty = document.getElementById('errQty');
    const elQty  = document.getElementById('orderQty');
    const maxStk = parseInt(_currentProduct?.stock||99);
    if(isNaN(qty) || qty < 1 || qty > maxStk) {
        elQty.classList.add('error');
        errQty.textContent = qty > maxStk ? `Max disponible : ${maxStk}` : 'Quantité invalide (min. 1)';
        errQty.classList.add('show'); valid = false;
    } else { elQty.classList.remove('error'); errQty.classList.remove('show'); }

    // payment
    const payEl = document.querySelector('input[name="payment"]:checked');
    if(!payEl) {
        document.getElementById('errPayment').classList.add('show'); valid = false;
    } else { document.getElementById('errPayment').classList.remove('show'); }

    if(!valid) return;

    const order = {
        id:          Date.now(),
        productId:   _currentProduct.id,
        productName: _currentProduct.nom,
        productRef:  _currentProduct.reference || '—',
        userId:      clientId,  // Store the unique client ID
        userNumber:  clientNum,  // Store the readable client number
        quantity:    qty,
        unitPrice:   parseFloat(_currentProduct.prix||0),
        totalPrice:  parseFloat((qty * parseFloat(_currentProduct.prix||0)).toFixed(3)),
        paymentType: payEl.value,
        status:      'En attente',
        date:        new Date().toLocaleString('fr-FR', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' })
    };

    // Save order
    const orders = getOrders();
    orders.unshift(order);
    saveOrders(orders);

    // Update stock
    const products = getProducts();
    const idx = products.findIndex(p => p.id == _currentProduct.id);
    if(idx !== -1) {
        products[idx].stock = Math.max(0, (parseInt(products[idx].stock)||0) - qty);
        saveProducts(products);
    }

    closeOrderModal();

    // Show success
    document.getElementById('successOrderId').textContent = `Commande N° ${order.id}`;
    const payLabels = { virement:'Virement bancaire', carte_bancaire:'Carte bancaire', paypal:'PayPal', especes:'Espèces' };
    document.getElementById('successMsg').innerHTML =
        `<strong>${escH(order.productName)}</strong> — ${qty} unité${qty>1?'s':''}<br>
         Total : <strong>${order.totalPrice.toFixed(3)} DT</strong><br>
         Paiement : ${payLabels[order.paymentType]||order.paymentType}<br>
         <span style="font-size:11px;">Client #${clientNum}</span>`;
    document.getElementById('successOverlay').classList.add('open');

    loadFrontUI();
}

function closeSuccessOverlay() {
    document.getElementById('successOverlay').classList.remove('open');
}

// ════════════════════════════════════
//  INIT
// ════════════════════════════════════
window.addEventListener('load', () => {
    loadFrontUI();
    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.getElementById('filterCat').addEventListener('change', applyFilters);
    document.getElementById('orderQty').addEventListener('input', updateTotal);
});

// Add CSS animation for toast
const style = document.createElement('style');
style.textContent = `@keyframes fadeInOut { 0% { opacity: 0; transform: translateY(20px); } 15% { opacity: 1; transform: translateY(0); } 85% { opacity: 1; transform: translateY(0); } 100% { opacity: 0; transform: translateY(20px); } }`;
document.head.appendChild(style);
</script>
</body>
</html>