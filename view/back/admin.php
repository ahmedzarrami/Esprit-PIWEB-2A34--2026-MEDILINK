<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Administration — MediLink</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<style>
/* [KEEP ALL YOUR EXISTING STYLES - they remain the same] */
*, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
:root {
    --blue:#1a56db; --blue-dark:#1a46c4; --blue-light:#eff4ff; --blue-mid:#6694f8;
    --green:#0da271; --green-dark:#059669; --green-light:#ecfdf5;
    --navy:#0f1b2d; --navy2:#1e2f45; --navy3:#162236;
    --red:#dc2626; --red-light:#fee2e2;
    --orange:#ea580c; --orange-light:#fff7ed;
    --purple:#7c3aed; --purple-light:#f5f3ff;
    --gray-50:#f8fafc; --gray-100:#f1f5f9; --gray-200:#e2e8f0;
    --gray-400:#94a3b8; --gray-500:#64748b; --gray-600:#475569;
    --gray-700:#334155; --gray-900:#0f172a;
    --radius:12px; --radius-lg:18px; --radius-xl:24px;
    --shadow-sm:0 1px 3px rgba(0,0,0,0.06),0 1px 2px rgba(0,0,0,0.04);
    --shadow-md:0 4px 16px rgba(0,0,0,0.08);
    --sidebar-w:250px;
}
body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--gray-50); color:var(--gray-900); font-size:14px; display:flex; min-height:100vh; }

/* ════════ SIDEBAR ════════ */
.sidebar {
    width:var(--sidebar-w); min-height:100vh; background:var(--navy);
    display:flex; flex-direction:column;
    position:fixed; top:0; left:0; bottom:0; z-index:200;
    box-shadow:4px 0 24px rgba(0,0,0,.2);
}
.sb-logo {
    padding:24px 20px 20px; border-bottom:1px solid rgba(255,255,255,.08);
}
.sb-logo-inner { display:flex; align-items:center; gap:11px; }
.sb-logo-icon {
    width:40px; height:40px; min-width:40px;
    background:linear-gradient(135deg,var(--blue),#3b7ff7);
    border-radius:10px; display:flex; align-items:center; justify-content:center;
    box-shadow:0 4px 12px rgba(26,86,219,.4);
}
.sb-logo-icon svg { fill:#fff; }
.sb-logo-text { font-size:17px; font-weight:700; color:#fff; line-height:1.2; }
.sb-logo-text span { color:var(--blue-mid); }
.sb-logo-sub { font-size:10px; color:rgba(255,255,255,.4); font-weight:500; margin-top:2px; letter-spacing:.04em; }
.sb-badge { display:inline-block; background:var(--orange); color:#fff; font-size:9px; font-weight:700; padding:2px 7px; border-radius:100px; letter-spacing:.04em; margin-top:6px; }

.sb-nav { padding:20px 12px; flex:1; }
.sb-section-label { font-size:9px; font-weight:700; color:rgba(255,255,255,.3); text-transform:uppercase; letter-spacing:.12em; padding:0 8px; margin-bottom:8px; margin-top:20px; }
.sb-section-label:first-child { margin-top:0; }
.sb-nav-item {
    display:flex; align-items:center; gap:11px;
    padding:10px 12px; border-radius:10px; cursor:pointer;
    color:rgba(255,255,255,.6); font-size:13px; font-weight:500;
    transition:.2s; margin-bottom:3px; border:none; background:none;
    width:100%; text-align:left; font-family:'Plus Jakarta Sans',sans-serif;
    text-decoration:none;
}
.sb-nav-item:hover { background:rgba(255,255,255,.07); color:rgba(255,255,255,.9); }
.sb-nav-item.active { background:var(--blue); color:#fff; box-shadow:0 4px 12px rgba(26,86,219,.3); }
.sb-nav-item svg { flex-shrink:0; opacity:.7; }
.sb-nav-item.active svg { opacity:1; }
.sb-nav-badge { margin-left:auto; background:rgba(255,255,255,.15); color:#fff; font-size:10px; font-weight:700; padding:2px 8px; border-radius:100px; min-width:22px; text-align:center; }
.sb-nav-item.active .sb-nav-badge { background:rgba(255,255,255,.25); }

.sb-footer { padding:16px 20px; border-top:1px solid rgba(255,255,255,.08); }
.sb-footer-text { font-size:10px; color:rgba(255,255,255,.25); font-weight:500; }

/* ════════ MAIN CONTENT ════════ */
.main-wrapper { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; min-height:100vh; }

/* Topbar */
.topbar {
    background:#fff; border-bottom:1px solid var(--gray-200);
    padding:0 32px; height:64px;
    display:flex; align-items:center; justify-content:space-between;
    position:sticky; top:0; z-index:100; box-shadow:var(--shadow-sm);
}
.topbar-title { font-size:16px; font-weight:700; color:var(--gray-900); }
.topbar-title span { color:var(--gray-400); font-weight:400; font-size:13px; margin-left:8px; }
.topbar-actions { display:flex; align-items:center; gap:10px; }
.btn-front {
    display:flex; align-items:center; gap:7px;
    padding:8px 16px; background:var(--blue-light); color:var(--blue);
    border-radius:9px; font-size:12px; font-weight:600; text-decoration:none; transition:.2s;
    border:1px solid rgba(26,86,219,.15);
}
.btn-front:hover { background:var(--blue); color:#fff; }

/* Page sections */
.page-section { display:none; }
.page-section.active { display:block; }

/* Section hero */
.section-hero {
    padding:32px 32px 0;
}
.section-hero-inner {
    background:linear-gradient(135deg,var(--navy),var(--navy2));
    border-radius:var(--radius-xl); padding:32px 36px;
    display:flex; align-items:center; justify-content:space-between; gap:20px;
    position:relative; overflow:hidden; margin-bottom:32px;
}
.section-hero-inner::before { content:''; position:absolute; top:-40px; right:-40px; width:180px; height:180px; background:rgba(255,255,255,.04); border-radius:50%; }
.section-hero-tag { display:inline-block; background:rgba(255,255,255,.12); color:rgba(255,255,255,.8); font-size:11px; font-weight:600; padding:4px 12px; border-radius:100px; margin-bottom:10px; letter-spacing:.04em; }
.section-hero h2 { font-size:26px; font-weight:700; color:#fff; margin-bottom:6px; letter-spacing:-0.2px; }
.section-hero h2 em { font-family:'Instrument Serif',serif; font-style:italic; font-weight:400; }
.section-hero p { color:rgba(255,255,255,.6); font-size:13px; }
.section-hero-icon { font-size:48px; flex-shrink:0; opacity:.8; }

/* Stats row */
.stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; padding:0 32px 24px; }
.stat-card {
    background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); padding:20px 22px;
    box-shadow:var(--shadow-sm); position:relative; overflow:hidden; transition:.2s;
}
.stat-card:hover { border-color:var(--blue-mid); box-shadow:var(--shadow-md); transform:translateY(-2px); }
.stat-card::before { content:''; position:absolute; top:0; left:0; width:3px; height:100%; border-radius:3px 0 0 3px; }
.stat-card.blue::before  { background:var(--blue); }
.stat-card.green::before { background:var(--green); }
.stat-card.orange::before{ background:var(--orange); }
.stat-card.red::before   { background:var(--red); }
.stat-card.purple::before{ background:var(--purple); }
.sc-icon { font-size:28px; margin-bottom:10px; }
.sc-num { font-size:28px; font-weight:800; letter-spacing:-0.5px; }
.sc-num.blue  { color:var(--blue); }
.sc-num.green { color:var(--green-dark); }
.sc-num.orange{ color:var(--orange); }
.sc-num.red   { color:var(--red); }
.sc-num.purple{ color:var(--purple); }
.sc-label { font-size:11px; color:var(--gray-400); font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin-top:4px; }

/* Toolbar */
.toolbar-wrap { padding:0 32px 20px; }
.toolbar {
    display:flex; gap:12px; align-items:center; flex-wrap:wrap;
    background:#fff; padding:14px 18px; border-radius:var(--radius-lg);
    border:1px solid var(--gray-200); box-shadow:var(--shadow-sm);
}
.tb-input {
    padding:9px 13px; border:1.5px solid var(--gray-200); border-radius:10px;
    font-size:13px; font-family:'Plus Jakarta Sans',sans-serif; color:var(--gray-900);
    outline:none; transition:.15s; min-width:200px; background:#fff;
}
.tb-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,86,219,.1); }
.tb-select {
    padding:9px 13px; border:1.5px solid var(--gray-200); border-radius:10px;
    font-size:13px; font-family:'Plus Jakarta Sans',sans-serif; color:var(--gray-900);
    outline:none; background:#fff; cursor:pointer;
}
.tb-select:focus { border-color:var(--blue); }
.btn-add-main {
    display:flex; align-items:center; gap:7px; padding:9px 20px;
    background:linear-gradient(135deg,var(--blue),#3b7ff7); color:#fff;
    border:none; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer;
    font-family:'Plus Jakarta Sans',sans-serif; transition:.2s; margin-left:auto;
    box-shadow:0 4px 12px rgba(26,86,219,.25);
}
.btn-add-main:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(26,86,219,.35); }

/* Table */
.table-wrap-outer { padding:0 32px 40px; }
.table-wrap {
    background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg);
    overflow:hidden; box-shadow:var(--shadow-sm);
}
table { width:100%; border-collapse:collapse; min-width:600px; }
thead tr { background:var(--gray-50); }
th {
    padding:13px 16px; text-align:left; font-size:11px; font-weight:700;
    color:var(--gray-500); text-transform:uppercase; letter-spacing:.06em;
    border-bottom:2px solid var(--gray-200);
}
td { padding:13px 16px; border-bottom:1px solid var(--gray-100); vertical-align:middle; }
tr:last-child td { border-bottom:none; }
tbody tr { transition:.15s; }
tbody tr:hover td { background:var(--gray-50); }
.td-ref { font-size:10px; color:var(--gray-500); font-weight:600; font-family:monospace; background:var(--gray-100); display:inline-block; padding:2px 7px; border-radius:6px; }
.td-nom { font-weight:700; color:var(--gray-900); }
.td-desc { font-size:12px; color:var(--gray-500); max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.td-prix { font-weight:700; white-space:nowrap; }
.td-cat { display:inline-block; background:var(--blue-light); color:var(--blue); font-size:11px; font-weight:600; padding:4px 10px; border-radius:100px; white-space:nowrap; }
.stock-badge { font-size:11px; font-weight:700; padding:4px 10px; border-radius:100px; white-space:nowrap; display:inline-block; min-width:60px; }
.stock-ok   { background:var(--green-light); color:#065f46; }
.stock-low  { background:var(--orange-light); color:var(--orange); }
.stock-out  { background:var(--red-light); color:var(--red); }
.td-actions { display:flex; gap:7px; flex-wrap:wrap; }
.btn-te, .btn-td {
    height:30px; padding:0 12px; border:none; border-radius:7px;
    font-size:12px; font-weight:600; cursor:pointer;
    font-family:'Plus Jakarta Sans',sans-serif; transition:.2s; white-space:nowrap;
}
.btn-te { background:var(--blue-light); color:var(--blue); }
.btn-te:hover { background:var(--blue); color:#fff; }
.btn-td { background:var(--red-light); color:var(--red); }
.btn-td:hover { background:var(--red); color:#fff; }

.table-footer {
    padding:12px 18px; border-top:1px solid var(--gray-200);
    font-size:12px; color:var(--gray-500); display:flex; align-items:center; justify-content:space-between;
    background:var(--gray-50);
}
.table-empty { text-align:center; padding:48px 24px; color:var(--gray-400); }
.table-empty-icon { font-size:36px; margin-bottom:10px; opacity:.5; }
.table-empty strong { display:block; font-size:15px; color:var(--gray-600); margin-bottom:4px; }

/* ── ORDERS TABLE specific ── */
.order-status-select {
    padding:4px 8px; border-radius:7px; border:1.5px solid transparent;
    font-size:11px; font-weight:700; cursor:pointer; outline:none;
    font-family:'Plus Jakarta Sans',sans-serif; transition:.15s;
}
.status-en_attente  { background:#fef9c3; color:#854d0e; border-color:#fde047; }
.status-confirmee   { background:var(--green-light); color:#065f46; border-color:var(--green); }
.status-livree      { background:var(--blue-light); color:var(--blue-dark); border-color:var(--blue-mid); }
.status-annulee     { background:var(--red-light); color:var(--red); border-color:var(--red); }

.order-pay-badge {
    display:inline-flex; align-items:center; gap:5px;
    font-size:11px; font-weight:600; padding:4px 10px; border-radius:100px;
    background:var(--gray-100); color:var(--gray-600);
}
.td-user { font-size:12px; font-weight:600; color:var(--gray-700); background:var(--purple-light); padding:3px 9px; border-radius:100px; display:inline-block; color:var(--purple); }
.td-total { font-weight:800; color:var(--gray-900); font-size:13px; }
.td-date { font-size:11px; color:var(--gray-500); white-space:nowrap; }

/* ── MODALS ── */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,27,45,.55); z-index:2000; align-items:center; justify-content:center; backdrop-filter:blur(5px); }
.modal-overlay.open { display:flex; }
.modal { background:#fff; border-radius:var(--radius-xl); width:100%; max-width:560px; max-height:90vh; overflow-y:auto; padding:32px; box-shadow:0 32px 64px rgba(0,0,0,.25); animation:slideUp .25s cubic-bezier(0.34,1.56,0.64,1); }
@keyframes slideUp { from{opacity:0;transform:translateY(20px) scale(.96)} to{opacity:1;transform:translateY(0) scale(1)} }
.modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.modal-title { font-size:20px; font-weight:700; color:var(--gray-900); display:flex; align-items:center; gap:12px; }
.modal-title-icon { width:42px; height:42px; border-radius:12px; background:var(--blue-light); display:flex; align-items:center; justify-content:center; }
.modal-close { width:36px; height:36px; border:none; background:var(--gray-100); border-radius:10px; cursor:pointer; font-size:18px; color:var(--gray-600); display:flex; align-items:center; justify-content:center; transition:.2s; }
.modal-close:hover { background:var(--gray-200); transform:rotate(90deg); }
.form-alert { display:none; padding:12px 16px; border-radius:12px; font-size:13px; margin-bottom:20px; align-items:center; gap:10px; font-weight:500; }
.form-alert.show { display:flex; }
.form-alert.success { background:var(--green-light); color:#065f46; border-left:4px solid var(--green); }
.form-alert.err { background:var(--red-light); color:#991b1b; border-left:4px solid var(--red); }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.form-group { display:flex; flex-direction:column; gap:7px; margin-bottom:18px; }
.form-label { font-size:12px; font-weight:700; color:var(--gray-700); text-transform:uppercase; letter-spacing:.03em; }
.form-input, .form-select, .form-textarea { padding:11px 14px; border:1.5px solid var(--gray-200); border-radius:12px; font-size:14px; font-family:'Plus Jakarta Sans',sans-serif; color:var(--gray-900); background:#fff; outline:none; transition:.15s; }
.form-input:focus, .form-select:focus, .form-textarea:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,86,219,.1); }
.form-textarea { resize:vertical; min-height:90px; }
.form-input.error, .form-select.error { border-color:var(--red); background:#fff5f5; }
.field-error { font-size:11px; color:var(--red); display:none; margin-top:2px; font-weight:500; }
.field-error.show { display:block; }
.btn-submit { width:100%; height:48px; background:linear-gradient(135deg,var(--blue),#3b7ff7); color:#fff; border:none; border-radius:14px; font-size:15px; font-weight:700; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:.2s; margin-top:12px; display:flex; align-items:center; justify-content:center; gap:10px; box-shadow:0 4px 14px rgba(26,86,219,.25); }
.btn-submit:hover { transform:translateY(-1px); box-shadow:0 8px 22px rgba(26,86,219,.35); }

/* Confirm modal */
.confirm-modal { max-width:400px; text-align:center; padding:36px 32px; }

/* Order details modal */
.order-details { }
.order-info-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px; }
.order-info-item { display:flex; flex-direction:column; gap:4px; }
.order-info-item label { font-size:12px; font-weight:600; color:var(--gray-500); text-transform:uppercase; letter-spacing:0.5px; }
.order-info-item span { font-size:14px; color:var(--gray-900); }
.order-status-badge { display:inline-block; padding:4px 8px; border-radius:6px; font-size:11px; font-weight:600; text-transform:uppercase; }
.order-total { font-weight:700; font-size:16px; color:var(--blue-600); }
.order-products-section h4 { margin:0 0 16px 0; font-size:16px; font-weight:700; color:var(--gray-900); }
.order-products-list { display:flex; flex-direction:column; gap:12px; }
.order-detail-product { padding:12px; background:var(--gray-50); border-radius:8px; border:1px solid var(--gray-200); }
.order-product-name { font-weight:600; font-size:14px; color:var(--gray-900); }
.order-product-ref { font-size:12px; color:var(--gray-500); margin:2px 0; }
.order-product-qty { font-size:12px; color:var(--gray-600); margin-bottom:4px; }
.order-product-price { font-size:13px; color:var(--gray-700); }
.confirm-icon { font-size:52px; margin-bottom:14px; }
.confirm-title { font-size:19px; font-weight:700; margin-bottom:8px; }
.confirm-msg { font-size:13px; color:var(--gray-600); margin-bottom:28px; line-height:1.6; }
.confirm-actions { display:flex; gap:12px; }
.btn-cancel { flex:1; height:44px; background:var(--gray-100); color:var(--gray-700); border:1.5px solid var(--gray-200); border-radius:12px; font-size:14px; font-weight:600; cursor:pointer; transition:.2s; font-family:'Plus Jakarta Sans',sans-serif; }
.btn-cancel:hover { background:var(--gray-200); }
.btn-confirm-del { flex:1; height:44px; background:var(--red); color:#fff; border:none; border-radius:12px; font-size:14px; font-weight:600; cursor:pointer; transition:.2s; font-family:'Plus Jakarta Sans',sans-serif; }
.btn-confirm-del:hover { background:#b91c1c; }

/* Toast notification */
.toast {
    position:fixed; bottom:28px; right:28px; z-index:9000;
    background:var(--navy); color:#fff; border-radius:14px;
    padding:14px 20px; font-size:13px; font-weight:600;
    display:flex; align-items:center; gap:10px; max-width:320px;
    box-shadow:0 10px 30px rgba(0,0,0,.25);
    transform:translateY(80px); opacity:0; transition:.35s cubic-bezier(0.34,1.56,0.64,1);
}
.toast.show { transform:translateY(0); opacity:1; }
.toast.success .toast-icon::before { content:'✓'; background:var(--green); }
.toast.err .toast-icon::before { content:'✕'; background:var(--red); }
.toast-icon::before { display:inline-flex; width:22px; height:22px; border-radius:50%; color:#fff; font-size:12px; font-weight:800; align-items:center; justify-content:center; }

@media (max-width:900px) {
    .sidebar { display:none; }
    .main-wrapper { margin-left:0; }
    .stats-row { grid-template-columns:1fr 1fr; }
}
</style>
</head>
<body>

<!-- ════════ SIDEBAR ════════ -->
<aside class="sidebar">
    <div class="sb-logo">
        <div class="sb-logo-inner">
            <div class="sb-logo-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
            </div>
            <div>
                <div class="sb-logo-text">Medi<span>Link</span></div>
                <div class="sb-logo-sub">BACKOFFICE</div>
            </div>
        </div>
        <div class="sb-badge">ADMIN</div>
    </div>

    <nav class="sb-nav">
        <div class="sb-section-label">Catalogue</div>
        <button class="sb-nav-item active" onclick="showSection('products')" id="nav-products">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            Produits
            <span class="sb-nav-badge" id="sbBadgeProducts">0</span>
        </button>
        <button class="sb-nav-item" onclick="showSection('add-product')" id="nav-add-product">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Ajouter Produit
        </button>

        <div class="sb-section-label">Ventes</div>
        <button class="sb-nav-item" onclick="showSection('orders')" id="nav-orders">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            Commandes
            <span class="sb-nav-badge" id="sbBadgeOrders">0</span>
        </button>

        <div class="sb-section-label">Navigation</div>
        <a class="sb-nav-item" href="../front/home.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Voir Front Office
        </a>
    </nav>

    <div class="sb-footer">
        <div class="sb-footer-text">MediLink © 2026</div>
    </div>
</aside>

<!-- ════════ MAIN ════════ -->
<div class="main-wrapper">

    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-title" id="topbarTitle">
            Gestion des produits <span>Catalogue</span>
        </div>
        <div class="topbar-actions">
            <a href="../front/home.php" class="btn-front">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Vue publique
            </a>
        </div>
    </div>

    <!-- ══════ SECTION PRODUCTS ══════ -->
    <div class="page-section active" id="section-products">
        <div class="section-hero">
            <div class="section-hero-inner">
                <div>
                    <div class="section-hero-tag">📦 CATALOGUE</div>
                    <h2>Gestion des <em>produits</em></h2>
                    <p>Consultez, modifiez et gérez l'ensemble de votre catalogue parapharmacie.</p>
                </div>
                <div class="section-hero-icon">🧴</div>
            </div>
        </div>

        <div class="stats-row" id="productStats">
            <div class="stat-card blue"><div class="sc-icon">📦</div><div class="sc-num blue" id="aStatTotal">0</div><div class="sc-label">Total produits</div></div>
            <div class="stat-card green"><div class="sc-icon">✅</div><div class="sc-num green" id="aStatDispo">0</div><div class="sc-label">En stock (&gt;5)</div></div>
            <div class="stat-card orange"><div class="sc-icon">⚠️</div><div class="sc-num orange" id="aStatLow">0</div><div class="sc-label">Stock faible (≤5)</div></div>
            <div class="stat-card red"><div class="sc-icon">❌</div><div class="sc-num red" id="aStatOut">0</div><div class="sc-label">En rupture</div></div>
        </div>

        <div class="toolbar-wrap">
            <div class="toolbar">
                <input type="text" id="aSearch" class="tb-input" placeholder="🔍 Rechercher (nom, réf, description)">
                <select id="aFilterCat" class="tb-select">
                    <option value="">📂 Toutes catégories</option>
                </select>
                <select id="aFilterStock" class="tb-select">
                    <option value="">📊 Tout le stock</option>
                    <option value="ok">✅ En stock (&gt;5)</option>
                    <option value="low">⚠️ Stock faible (≤5)</option>
                    <option value="out">❌ Rupture (0)</option>
                </select>
                <button class="btn-add-main" onclick="openAddModal()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    Ajouter un produit
                </button>
            </div>
        </div>

        <div class="table-wrap-outer">
            <div class="table-wrap">
                <table id="adminTable">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Catégorie</th>
                            <th>Prix (DT)</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="adminTableBody"></tbody>
                </table>
                <div class="table-footer">
                    <span id="tableInfo">—</span>
                    <span id="tableDate" style="font-size:11px;color:var(--gray-400)"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════ SECTION ADD PRODUCT (quick form) ══════ -->
    <div class="page-section" id="section-add-product">
        <div class="section-hero">
            <div class="section-hero-inner">
                <div>
                    <div class="section-hero-tag">➕ NOUVEAU</div>
                    <h2>Ajouter un <em>produit</em></h2>
                    <p>Remplissez le formulaire pour ajouter un nouveau produit au catalogue.</p>
                </div>
                <div class="section-hero-icon">📝</div>
            </div>
        </div>
        <div style="padding:0 32px 40px;max-width:680px;">
            <div style="background:#fff;border:1px solid var(--gray-200);border-radius:var(--radius-xl);padding:32px;box-shadow:var(--shadow-sm);">
                <div class="form-alert" id="quickAlert"><span id="quickAlertMsg"></span></div>
                <input type="hidden" id="quickEditId">
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Référence *</label><input type="text" id="quickRef" class="form-input" placeholder="PHM-001"><span class="field-error" id="qerrRef"></span></div>
                    <div class="form-group"><label class="form-label">Catégorie *</label><select id="quickCat" class="form-select"><option value="">— Sélectionner —</option><option>Soins visage</option><option>Soins corps</option><option>Hygiène</option><option>Compléments alimentaires</option><option>Bébé &amp; Maman</option><option>Capillaire</option><option>Solaire</option><option>Minceur</option><option>Orthopédie</option><option>Autre</option></select><span class="field-error" id="qerrCat"></span></div>
                </div>
                <div class="form-group"><label class="form-label">Nom du produit *</label><input type="text" id="quickNom" class="form-input" placeholder="Ex : Crème hydratante SPF30"><span class="field-error" id="qerrNom"></span></div>
                <div class="form-group"><label class="form-label">Description</label><textarea id="quickDesc" class="form-textarea" placeholder="Description, bienfaits, conseils..."></textarea></div>
                <div class="form-group"><label class="form-label">URL de l'image</label><input type="text" id="quickImage" class="form-input" placeholder="https://..." /></div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Prix (DT) *</label><input type="number" id="quickPrix" class="form-input" placeholder="0.000" min="0" step="0.001"><span class="field-error" id="qerrPrix"></span></div>
                    <div class="form-group"><label class="form-label">Stock (unités) *</label><input type="number" id="quickStock" class="form-input" placeholder="0" min="0"><span class="field-error" id="qerrStock"></span></div>
                </div>
                <button class="btn-submit" onclick="submitQuickForm()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Enregistrer le produit
                </button>
            </div>
        </div>
    </div>

    <!-- ══════ SECTION ORDERS ══════ -->
    <div class="page-section" id="section-orders">
        <div class="section-hero">
            <div class="section-hero-inner">
                <div>
                    <div class="section-hero-tag">📋 SUIVI</div>
                    <h2>Gestion des <em>commandes</em></h2>
                    <p>Consultez et gérez toutes les commandes passées par vos clients.</p>
                </div>
                <div class="section-hero-icon">🛒</div>
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-card blue"><div class="sc-icon">📋</div><div class="sc-num blue" id="oStatTotal">0</div><div class="sc-label">Total commandes</div></div>
            <div class="stat-card green"><div class="sc-icon">💰</div><div class="sc-num green" id="oStatRevenu" style="font-size:18px">0.000</div><div class="sc-label">Revenu total (DT)</div></div>
            <div class="stat-card orange"><div class="sc-icon">⏳</div><div class="sc-num orange" id="oStatPending">0</div><div class="sc-label">En attente</div></div>
            <div class="stat-card purple"><div class="sc-icon">✅</div><div class="sc-num purple" id="oStatConfirmed" style="color:var(--purple)">0</div><div class="sc-label">Confirmées</div></div>
        </div>

        <div class="toolbar-wrap">
            <div class="toolbar">
                <input type="text" id="oSearch" class="tb-input" placeholder="🔍 Rechercher (produit, utilisateur, N°)">
                <select id="oFilterStatus" class="tb-select">
                    <option value="">📊 Tous les statuts</option>
                    <option value="En attente">⏳ En attente</option>
                    <option value="Confirmée">✅ Confirmée</option>
                    <option value="Livrée">🚚 Livrée</option>
                    <option value="Annulée">❌ Annulée</option>
                </select>
                <select id="oFilterPayment" class="tb-select">
                    <option value="">💳 Tous paiements</option>
                    <option value="virement">🏦 Virement</option>
                    <option value="carte_bancaire">💳 Carte bancaire</option>
                    <option value="paypal">🅿️ PayPal</option>
                    <option value="especes">💵 Espèces</option>
                </select>
                <button class="btn-add-main" style="background:linear-gradient(135deg,var(--red),#ef4444);" onclick="confirmClearOrders()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                    Vider les commandes
                </button>
            </div>
        </div>

        <div class="table-wrap-outer">
            <div class="table-wrap">
                <table id="ordersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Produit</th>
                            <th>Qté</th>
                            <th>Prix Total</th>
                            <th>Statut</th>
                            <th>Paiement</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody"></tbody>
                </table>
                <div class="table-footer">
                    <span id="ordersTableInfo">—</span>
                    <span style="font-size:11px;color:var(--gray-400)">Mis à jour en temps réel</span>
                </div>
            </div>
        </div>
    </div>

</div><!-- end main-wrapper -->

<!-- ══ MODAL PRODUCT ADD/EDIT ══ -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">
                <div class="modal-title-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1a56db" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg></div>
                <span id="modalTitleText">Ajouter un produit</span>
            </div>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="form-alert" id="formAlert"><span id="formAlertMsg"></span></div>
        <input type="hidden" id="editId">
        <div class="form-row">
            <div class="form-group"><label class="form-label">Référence *</label><input type="text" id="fieldRef" class="form-input" placeholder="PHM-001"><span class="field-error" id="errRef"></span></div>
            <div class="form-group"><label class="form-label">Catégorie *</label><select id="fieldCat" class="form-select"><option value="">— Sélectionner —</option><option>Soins visage</option><option>Soins corps</option><option>Hygiène</option><option>Compléments alimentaires</option><option>Bébé &amp; Maman</option><option>Capillaire</option><option>Solaire</option><option>Minceur</option><option>Orthopédie</option><option>Autre</option></select><span class="field-error" id="errCat"></span></div>
        </div>
        <div class="form-group"><label class="form-label">Nom du produit *</label><input type="text" id="fieldNom" class="form-input" placeholder="Crème hydratante SPF30"><span class="field-error" id="errNom"></span></div>
        <div class="form-group"><label class="form-label">Description</label><textarea id="fieldDesc" class="form-textarea" placeholder="Description, bienfaits..."></textarea></div>
        <div class="form-group"><label class="form-label">URL de l'image</label><input type="text" id="fieldImage" class="form-input" placeholder="https://..." /></div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Prix (DT) *</label><input type="number" id="fieldPrix" class="form-input" placeholder="0.000" min="0" step="0.001"><span class="field-error" id="errPrix"></span></div>
            <div class="form-group"><label class="form-label">Stock (unités) *</label><input type="number" id="fieldStock" class="form-input" placeholder="0" min="0"><span class="field-error" id="errStock"></span></div>
        </div>
        <button class="btn-submit" id="btnSubmit" onclick="submitForm()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/></svg>
            Enregistrer
        </button>
    </div>
</div>

<!-- ══ CONFIRM MODAL ══ -->
<div class="modal-overlay" id="confirmOverlay">
    <div class="modal confirm-modal">
        <div class="confirm-icon" id="confirmIcon">🗑️</div>
        <div class="confirm-title" id="confirmTitle">Supprimer ?</div>
        <div class="confirm-msg" id="confirmMsg">Cette action est irréversible.</div>
        <div class="confirm-actions">
            <button class="btn-cancel" onclick="closeConfirm()">Annuler</button>
            <button class="btn-confirm-del" id="btnConfirmDel">Confirmer</button>
        </div>
    </div>
</div>

<!-- ══ ORDER DETAILS MODAL ══ -->
<div class="modal-overlay" id="orderDetailsOverlay">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">
                <div class="modal-title-icon">📋</div>
                <span>Détails de la commande</span>
            </div>
            <button class="modal-close" onclick="closeOrderDetails()">✕</button>
        </div>
        <div id="orderDetailsContent">
            <!-- Content will be populated by JS -->
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
    <span class="toast-icon"></span>
    <span id="toastMsg">—</span>
</div>

<script>
// ════════════════════════════════════
//  STORAGE
// ════════════════════════════════════
const STORAGE_KEY = 'pharma_products';
const ORDERS_KEY  = 'pharma_orders';
const API_URL      = new URL('../../api.php', window.location.href).href;

async function apiRequest(resource, method, body = null) {
    const options = { method, headers: { 'Content-Type': 'application/json' } };
    if (body !== null) {
        options.body = JSON.stringify(body);
    }
    const response = await fetch(`${API_URL}?resource=${encodeURIComponent(resource)}`, options);
    const result = await response.json();
    if (!response.ok || !result.success) {
        throw new Error(result.message || 'Erreur API');
    }
    return result.data ?? result;
}

const DEMO_PRODUCTS = [
    { id:171000000001, reference:"PHM-VIS-01", nom:"Crème Hydra Éclat SPF30",    description:"Protection UV et hydratation profonde, texture légère.", prix:42.500, stock:18, categorie:"Soins visage", image:"https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=600&q=80" },
    { id:171000000002, reference:"PHM-COR-02", nom:"Beurre corporel karité",      description:"Beurre riche pour peaux sèches, 200ml.",              prix:29.900, stock:6,  categorie:"Soins corps", image:"https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=600&q=80" },
    { id:171000000003, reference:"PHM-HYG-03", nom:"Gel douche douceur",          description:"Sans savon, pH neutre, 500ml.",                       prix:12.300, stock:2,  categorie:"Hygiène", image:"https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=600&q=80" },
    { id:171000000004, reference:"PHM-COMP-04",nom:"Vitamine C + Zinc",           description:"Immunité & vitalité, 30 comprimés.",                  prix:18.750, stock:0,  categorie:"Compléments alimentaires", image:"https://images.unsplash.com/photo-1580281657521-98da17d80bd3?auto=format&fit=crop&w=600&q=80" },
    { id:171000000005, reference:"PHM-BEB-05", nom:"Lait nettoyant bébé",         description:"Sans parfum, hypoallergénique.",                      prix:15.200, stock:4,  categorie:"Bébé & Maman", image:"https://images.unsplash.com/photo-1580542970540-e80b54a7f363?auto=format&fit=crop&w=600&q=80" },
    { id:171000000006, reference:"PHM-CAP-06", nom:"Shampoing sec réparateur",    description:"Cheveux fragiles, 150ml.",                            prix:9.990,  stock:11, categorie:"Capillaire", image:"https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=600&q=80" }
];

function getProducts() { const r=localStorage.getItem(STORAGE_KEY); if(!r||r==='[]'){saveProducts(DEMO_PRODUCTS);return[...DEMO_PRODUCTS];}try{return JSON.parse(r);}catch{return[...DEMO_PRODUCTS];} }
function saveProducts(list) { localStorage.setItem(STORAGE_KEY,JSON.stringify(list)); refreshAll(); }
function getOrders() { try{return JSON.parse(localStorage.getItem(ORDERS_KEY)||'[]');}catch{return[];} }
function saveOrders(list) { localStorage.setItem(ORDERS_KEY,JSON.stringify(list)); refreshOrders(); }
function escH(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// ════════════════════════════════════
//  NAVIGATION
// ════════════════════════════════════
const SECTIONS = { products:'Gestion des produits', 'add-product':'Ajouter un produit', orders:'Gestion des commandes' };
const SUBTITLES = { products:'Catalogue', 'add-product':'Nouveau', orders:'Commandes' };

function showSection(name) {
    document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.sb-nav-item').forEach(b => b.classList.remove('active'));
    document.getElementById('section-'+name).classList.add('active');
    const navBtn = document.getElementById('nav-'+name);
    if(navBtn) navBtn.classList.add('active');
    document.getElementById('topbarTitle').innerHTML =
        `${SECTIONS[name]||name} <span>${SUBTITLES[name]||''}</span>`;
    if(name === 'orders') refreshOrders();
}

// ════════════════════════════════════
//  PRODUCTS — RENDER
// ════════════════════════════════════
function refreshAll() {
    updateProductStats();
    applyProductFilters();
    updateSidebarBadges();
}

function updateProductStats() {
    const products = getProducts();
    const dispo = products.filter(p => (parseInt(p.stock)||0) > 5).length;
    const low   = products.filter(p => { const s=parseInt(p.stock)||0; return s>0&&s<=5; }).length;
    const out   = products.filter(p => (parseInt(p.stock)||0) === 0).length;
    document.getElementById('aStatTotal').textContent = products.length;
    document.getElementById('aStatDispo').textContent = dispo;
    document.getElementById('aStatLow').textContent   = low;
    document.getElementById('aStatOut').textContent   = out;

    const catSelect = document.getElementById('aFilterCat');
    const current = catSelect.value;
    const cats = [...new Set(products.map(p=>p.categorie).filter(Boolean))];
    catSelect.innerHTML = '<option value="">📂 Toutes catégories</option>';
    cats.forEach(c => {
        const o=document.createElement('option');
        o.value=c; o.textContent=c;
        if(c===current) o.selected=true;
        catSelect.appendChild(o);
    });
}

function applyProductFilters() {
    const search = document.getElementById('aSearch').value.toLowerCase().trim();
    const cat    = document.getElementById('aFilterCat').value;
    const stock  = document.getElementById('aFilterStock').value;
    const products = getProducts();

    const filtered = products.filter(p => {
        const s = parseInt(p.stock)||0;
        const matchQ = !search || (p.nom&&p.nom.toLowerCase().includes(search)) || (p.reference&&p.reference.toLowerCase().includes(search)) || (p.description&&p.description.toLowerCase().includes(search));
        const matchC = !cat || p.categorie===cat;
        let matchS = true;
        if(stock==='ok')  matchS = s>5;
        if(stock==='low') matchS = s>0&&s<=5;
        if(stock==='out') matchS = s===0;
        return matchQ&&matchC&&matchS;
    });

    renderProductTable(filtered, products.length);
}

function renderProductTable(list, total) {
    const tbody = document.getElementById('adminTableBody');
    document.getElementById('tableInfo').innerHTML = `<strong>${list.length}</strong> produit${list.length!==1?'s':''} affichés sur ${total}`;
    document.getElementById('tableDate').textContent = new Date().toLocaleString('fr-FR',{day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'});

    if(!list.length) {
        tbody.innerHTML = `<tr><td colspan="7"><div class="table-empty"><div class="table-empty-icon">📦</div><strong>Aucun produit trouvé</strong><span>Modifiez les filtres ou ajoutez un produit.</span></div></td></tr>`;
        return;
    }
    tbody.innerHTML = '';
    list.forEach(p => {
        const s = parseInt(p.stock)||0;
        let sc='stock-ok',st=`${s}`;
        if(s===0){sc='stock-out';st='Rupture';}
        else if(s<=5){sc='stock-low';st=`⚠️ ${s}`;}
        const desc = p.description ? (p.description.length>50?p.description.substring(0,48)+'…':p.description):'—';
        const tr=document.createElement('tr');
        tr.innerHTML=`
            <td><span class="td-ref">${escH(p.reference||'—')}</span></td>
            <td><span class="td-nom">${escH(p.nom)}</span></td>
            <td><span class="td-desc" title="${escH(p.description||'')}">${escH(desc)}</span></td>
            <td><span class="td-cat">${escH(p.categorie||'Autre')}</span></td>
            <td class="td-prix">${parseFloat(p.prix||0).toFixed(3)} DT</td>
            <td><span class="stock-badge ${sc}">${st}</span></td>
            <td><div class="td-actions">
                <button class="btn-te" onclick="openEditModal(${p.id})">✏️ Modifier</button>
                <button class="btn-td" onclick="confirmDeleteProduct(${p.id},'${escH(p.nom).replace(/'/g,"\\'")}')">🗑️ Supprimer</button>
            </div></td>`;
        tbody.appendChild(tr);
    });
}

// ════════════════════════════════════
//  ORDERS — RENDER (FIXED to show client numbers)
// ════════════════════════════════════
function refreshOrders() {
    const orders = getOrders();
    // Stats
    const revenue  = orders.reduce((acc,o) => acc+(o.totalPrice||0), 0);
    const pending  = orders.filter(o => o.status==='En attente').length;
    const confirmed= orders.filter(o => o.status==='Confirmée').length;
    document.getElementById('oStatTotal').textContent     = orders.length;
    document.getElementById('oStatRevenu').textContent    = revenue.toFixed(3);
    document.getElementById('oStatPending').textContent   = pending;
    document.getElementById('oStatConfirmed').textContent = confirmed;
    updateSidebarBadges();

    applyOrderFilters();
}

function applyOrderFilters() {
    const search  = document.getElementById('oSearch').value.toLowerCase().trim();
    const status  = document.getElementById('oFilterStatus').value;
    const payment = document.getElementById('oFilterPayment').value;
    const orders  = getOrders();

    const filtered = orders.filter(o => {
        const matchQ = !search || (o.productName&&o.productName.toLowerCase().includes(search)) || 
                       String(o.userNumber||o.userId).includes(search) || 
                       String(o.id).includes(search) || 
                       (o.productRef&&o.productRef.toLowerCase().includes(search));
        const matchS = !status  || o.status===status;
        const matchP = !payment || o.paymentType===payment;
        return matchQ&&matchS&&matchP;
    });
    renderOrdersTable(filtered, orders.length);
}

const PAY_ICONS = { virement:'🏦', carte_bancaire:'💳', paypal:'🅿️', especes:'💵' };
const PAY_LABELS = { virement:'Virement', carte_bancaire:'Carte bancaire', paypal:'PayPal', especes:'Espèces' };
const STATUS_CLASS = { 'En attente':'status-en_attente','Confirmée':'status-confirmee','Livrée':'status-livree','Annulée':'status-annulee' };

function renderOrdersTable(list, total) {
    const tbody = document.getElementById('ordersTableBody');
    document.getElementById('ordersTableInfo').innerHTML = `<strong>${list.length}</strong> commande${list.length!==1?'s':''} affichées sur ${total}`;

    if(!list.length) {
        tbody.innerHTML = `<tr><td colspan="9"><div class="table-empty"><div class="table-empty-icon">🛒</div><strong>Aucune commande trouvée</strong><span>Les commandes passées depuis le front office apparaîtront ici.</span></div></td></tr>`;
        return;
    }
    tbody.innerHTML='';

    // Group orders by id to handle multiple products
    const orderMap = new Map();
    list.forEach(o => {
        if (!orderMap.has(o.id)) {
            orderMap.set(o.id, {
                id: o.id,
                clientId: o.clientId,
                clientNumber: o.clientNumber || o.userNumber,
                userId: o.userId,
                paymentType: o.paymentType,
                status: o.status,
                date: o.date,
                totalPrice: o.totalPrice || 0,
                products: []
            });
        }
        const order = orderMap.get(o.id);
        if (o.products) {
            // New format with products array
            order.products = o.products;
            order.totalPrice = o.totalPrice;
        } else {
            // Old format single product
            order.products.push({
                productId: o.productId,
                productName: o.productName,
                productRef: o.productRef,
                unitPrice: o.unitPrice,
                quantity: o.quantity,
                totalPrice: o.totalPrice
            });
        }
    });

    Array.from(orderMap.values()).forEach(order => {
        const sc = STATUS_CLASS[order.status] || 'status-en_attente';
        const payIcon = PAY_ICONS[order.paymentType]||'💳';
        const payLabel = PAY_LABELS[order.paymentType]||order.paymentType;
        const shortId = String(order.id).slice(-6);
        const clientDisplay = order.clientNumber ? `#${order.clientNumber}` : (typeof order.clientId === 'string' ? order.clientId.slice(-8) : `U${order.clientId}`);

        // For multiple products, show first product and indicate more
        const firstProduct = order.products[0] || {};
        const productDisplay = order.products.length === 1
            ? `<div style="font-weight:700;font-size:13px;">${escH(firstProduct.productName||'—')}</div><div style="font-size:10px;color:var(--gray-400);margin-top:2px;">${escH(firstProduct.productRef||'')}</div>`
            : `<div style="font-weight:700;font-size:13px;">${order.products.length} produits</div><div style="font-size:10px;color:var(--gray-400);margin-top:2px;">${escH(firstProduct.productName||'')} + ${order.products.length - 1} autres</div>`;

        const totalQty = order.products.reduce((sum, p) => sum + (p.quantity || 1), 0);

        const tr=document.createElement('tr');
        tr.innerHTML=`
            <td style="font-family:monospace;font-size:11px;color:var(--gray-400);">#${escH(shortId)}</td>
            <td><span class="td-user">👤 ${escH(clientDisplay)}</span></td>
            <td>${productDisplay}</td>
            <td style="font-weight:700;text-align:center;">${totalQty}</td>
            <td class="td-total">${parseFloat(order.totalPrice).toFixed(3)} DT</td>
            <td>
                <select class="order-status-select ${sc}" onchange="updateOrderStatus('${order.id}', this.value, this)">
                    <option ${order.status==='En attente'?'selected':''}>En attente</option>
                    <option ${order.status==='Confirmée'?'selected':''}>Confirmée</option>
                    <option ${order.status==='Livrée'?'selected':''}>Livrée</option>
                    <option ${order.status==='Annulée'?'selected':''}>Annulée</option>
                </select>
            </td>
            <td><span class="order-pay-badge">${payIcon} ${escH(payLabel)}</span></td>
            <td class="td-date">${escH(order.date||'—')}</td>
            <td>
                <button class="btn-td" onclick="viewOrderDetails('${order.id}')">👁️ Détails</button>
                <button class="btn-td" onclick="confirmDeleteOrder('${order.id}')">🗑️</button>
            </td>`;
        tbody.appendChild(tr);
    });
}

function updateOrderStatus(orderId, newStatus, selectEl) {
    const orders = getOrders();
    const idx = orders.findIndex(o => String(o.id) === String(orderId));
    if(idx === -1) return;
    orders[idx].status = newStatus;
    const sc = STATUS_CLASS[newStatus] || 'status-en_attente';
    selectEl.className = `order-status-select ${sc}`;
    saveOrders(orders);
    showToast(`Statut mis à jour : ${newStatus}`, 'success');
}

function updateSidebarBadges() {
    document.getElementById('sbBadgeProducts').textContent = getProducts().length;
    document.getElementById('sbBadgeOrders').textContent   = getOrders().length;
}

// ════════════════════════════════════
//  MODAL PRODUCT ADD/EDIT
// ════════════════════════════════════
function openAddModal() {
    document.getElementById('modalTitleText').textContent = 'Ajouter un produit';
    document.getElementById('editId').value = '';
    ['fieldRef','fieldNom','fieldDesc','fieldImage','fieldPrix','fieldStock'].forEach(id => document.getElementById(id).value='');
    document.getElementById('fieldCat').value = '';
    clearModalErrors();
    document.getElementById('modalOverlay').classList.add('open');
    document.body.style.overflow='hidden';
}
function openEditModal(id) {
    const p = getProducts().find(x => x.id===id);
    if(!p){ showToast('Produit introuvable','err'); return; }
    document.getElementById('modalTitleText').textContent = 'Modifier le produit';
    document.getElementById('editId').value  = p.id;
    document.getElementById('fieldRef').value   = p.reference||'';
    document.getElementById('fieldNom').value   = p.nom||'';
    document.getElementById('fieldDesc').value  = p.description||'';
    document.getElementById('fieldImage').value = p.image||'';
    document.getElementById('fieldPrix').value  = p.prix||'';
    document.getElementById('fieldStock').value = p.stock||0;
    document.getElementById('fieldCat').value   = p.categorie||'';
    clearModalErrors();
    document.getElementById('modalOverlay').classList.add('open');
    document.body.style.overflow='hidden';
}
function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
    document.body.style.overflow='';
}
function clearModalErrors() {
    document.getElementById('formAlert').className='form-alert';
    ['fieldRef','fieldNom','fieldCat','fieldImage','fieldPrix','fieldStock'].forEach(id => {
        const el=document.getElementById(id); if(el) el.classList.remove('error');
    });
    ['errRef','errNom','errCat','errPrix','errStock'].forEach(id => {
        const el=document.getElementById(id); if(el){el.classList.remove('show');el.textContent='';}
    });
}
function showFieldErr(inputId,errId,msg) { document.getElementById(inputId).classList.add('error'); const e=document.getElementById(errId); e.textContent=msg; e.classList.add('show'); }
function clearFieldErr(inputId,errId)    { document.getElementById(inputId).classList.remove('error'); document.getElementById(errId).classList.remove('show'); }

async function submitForm() {
    document.getElementById('formAlert').className='form-alert';
    let ok=true;
    const ref=document.getElementById('fieldRef').value.trim();
    const nom=document.getElementById('fieldNom').value.trim();
    const cat=document.getElementById('fieldCat').value;
    const prix=parseFloat(document.getElementById('fieldPrix').value);
    const stock=parseInt(document.getElementById('fieldStock').value);
    const image=document.getElementById('fieldImage').value.trim();
    if(!ref){showFieldErr('fieldRef','errRef','Référence obligatoire');ok=false;}else clearFieldErr('fieldRef','errRef');
    if(!nom){showFieldErr('fieldNom','errNom','Nom requis');ok=false;}else clearFieldErr('fieldNom','errNom');
    if(!cat){showFieldErr('fieldCat','errCat','Catégorie requise');ok=false;}else clearFieldErr('fieldCat','errCat');
    if(isNaN(prix)||prix<0){showFieldErr('fieldPrix','errPrix','Prix invalide (≥0)');ok=false;}else clearFieldErr('fieldPrix','errPrix');
    if(isNaN(stock)||stock<0){showFieldErr('fieldStock','errStock','Stock invalide');ok=false;}else clearFieldErr('fieldStock','errStock');
    if(!ok) return;
    const editIdRaw=document.getElementById('editId').value;
    const products=getProducts();
    const newP={reference:ref,nom,description:document.getElementById('fieldDesc').value.trim(),image,prix,stock,categorie:cat};

    try {
        if(editIdRaw) {
            const idNum=parseInt(editIdRaw);
            if(products.some(p=>p.id!==idNum&&p.reference&&p.reference.toLowerCase()===ref.toLowerCase())){showFieldErr('fieldRef','errRef','Référence déjà utilisée');return;}
            await apiRequest('produits','PUT',{ id:idNum, ...newP });
            newP.id=idNum;
            const idx=products.findIndex(p=>p.id===idNum);
            if(idx!==-1){ products[idx]=newP; saveProducts(products); showToast('✓ Produit modifié','success'); setTimeout(closeModal,700); }
        } else {
            if(products.some(p=>p.reference&&p.reference.toLowerCase()===ref.toLowerCase())){showFieldErr('fieldRef','errRef','Référence déjà existante');return;}
            const created = await apiRequest('produits','POST',newP);
            newP.id = created.id ?? Date.now();
            products.push(newP);
            saveProducts(products);
            showToast('✓ Produit ajouté','success');
            setTimeout(closeModal,700);
        }
    } catch(error) {
        document.getElementById('formAlert').className = 'form-alert show err';
        document.getElementById('formAlertMsg').textContent = error.message;
    }
}

// ════════════════════════════════════
//  QUICK FORM (section add-product)
// ════════════════════════════════════
async function submitQuickForm() {
    document.getElementById('quickAlert').className='form-alert';
    let ok=true;
    const ref=document.getElementById('quickRef').value.trim();
    const nom=document.getElementById('quickNom').value.trim();
    const cat=document.getElementById('quickCat').value;
    const prix=parseFloat(document.getElementById('quickPrix').value);
    const stock=parseInt(document.getElementById('quickStock').value);
    const image=document.getElementById('quickImage').value.trim();
    const qErr = (inputId,errId,msg) => { document.getElementById(inputId).classList.add('error'); const e=document.getElementById(errId); e.textContent=msg; e.classList.add('show'); ok=false; };
    const qOk  = (inputId,errId) => { document.getElementById(inputId).classList.remove('error'); document.getElementById(errId).classList.remove('show'); };
    if(!ref){qErr('quickRef','qerrRef','Référence obligatoire');}else qOk('quickRef','qerrRef');
    if(!nom){qErr('quickNom','qerrNom','Nom requis');}else qOk('quickNom','qerrNom');
    if(!cat){qErr('quickCat','qerrCat','Catégorie requise');}else qOk('quickCat','qerrCat');
    if(isNaN(prix)||prix<0){qErr('quickPrix','qerrPrix','Prix invalide');}else qOk('quickPrix','qerrPrix');
    if(isNaN(stock)||stock<0){qErr('quickStock','qerrStock','Stock invalide');}else qOk('quickStock','qerrStock');
    if(!ok) return;
    const products=getProducts();
    if(products.some(p=>p.reference&&p.reference.toLowerCase()===ref.toLowerCase())){
        qErr('quickRef','qerrRef','Référence déjà existante'); return;
    }
    const newP={reference:ref,nom,description:document.getElementById('quickDesc').value.trim(),image,prix,stock,categorie:cat};
    try {
        const created = await apiRequest('produits','POST',newP);
        newP.id = created.id ?? Date.now();
        products.push(newP);
        saveProducts(products);
        ['quickRef','quickNom','quickDesc','quickImage','quickPrix','quickStock'].forEach(id=>document.getElementById(id).value='');
        document.getElementById('quickCat').value='';
        const a=document.getElementById('quickAlert');
        a.className='form-alert show success';
        document.getElementById('quickAlertMsg').textContent='✓ Produit ajouté avec succès !';
        setTimeout(()=>{a.className='form-alert';},2500);
        showToast('✓ Produit ajouté','success');
    } catch(error) {
        document.getElementById('quickAlert').className = 'form-alert show err';
        document.getElementById('quickAlertMsg').textContent = error.message;
    }
}

// ════════════════════════════════════
//  DELETE CONFIRM
// ════════════════════════════════════
let _pendingConfirm = null;

function confirmDeleteProduct(id, nom) {
    document.getElementById('confirmIcon').textContent = '🗑️';
    document.getElementById('confirmTitle').textContent = 'Supprimer ce produit ?';
    document.getElementById('confirmMsg').innerHTML = `Supprimer définitivement <strong>${escH(nom)}</strong> ?<br>Cette action est irréversible.`;
    _pendingConfirm = () => {
        const products = getProducts().filter(p => p.id !== id);
        saveProducts(products);
        showToast('Produit supprimé', 'success');
        closeConfirm();
    };
    openConfirm();
}

function confirmDeleteOrder(id) {
    document.getElementById('confirmIcon').textContent = '📋';
    document.getElementById('confirmTitle').textContent = 'Supprimer cette commande ?';
    document.getElementById('confirmMsg').textContent = 'Cette action est irréversible.';
    _pendingConfirm = () => {
        const orders = getOrders().filter(o => String(o.id) !== String(id));
        saveOrders(orders);
        showToast('Commande supprimée', 'success');
        closeConfirm();
    };
    openConfirm();
}

function confirmClearOrders() {
    document.getElementById('confirmIcon').textContent = '🗑️';
    document.getElementById('confirmTitle').textContent = 'Vider toutes les commandes ?';
    document.getElementById('confirmMsg').textContent = 'Toutes les commandes seront supprimées. Cette action est irréversible.';
    _pendingConfirm = () => {
        saveOrders([]);
        showToast('Commandes vidées', 'success');
        closeConfirm();
    };
    openConfirm();
}

function openConfirm() {
    document.getElementById('confirmOverlay').classList.add('open');
    document.body.style.overflow='hidden';
}
function closeConfirm() {
    _pendingConfirm=null;
    document.getElementById('confirmOverlay').classList.remove('open');
    document.body.style.overflow='';
}
document.getElementById('btnConfirmDel').addEventListener('click', () => { if(_pendingConfirm) _pendingConfirm(); });

// Click outside to close
document.getElementById('confirmOverlay').addEventListener('click', function(e){ if(e.target===this) closeConfirm(); });
document.getElementById('modalOverlay').addEventListener('click', function(e){ if(e.target===this) closeModal(); });
document.getElementById('orderDetailsOverlay').addEventListener('click', function(e){ if(e.target===this) closeOrderDetails(); });

function viewOrderDetails(orderId) {
    const orders = getOrders();
    const order = orders.find(o => String(o.id) === String(orderId));
    if (!order) return;

    const content = document.getElementById('orderDetailsContent');
    const payIcon = PAY_ICONS[order.paymentType] || '💳';
    const payLabel = PAY_LABELS[order.paymentType] || order.paymentType;
    const clientDisplay = order.clientNumber ? `#${order.clientNumber}` : (typeof order.clientId === 'string' ? order.clientId.slice(-8) : `U${order.clientId}`);

    let productsHtml = '';
    if (order.products && order.products.length) {
        productsHtml = order.products.map(p => `
            <div class="order-detail-product">
                <div class="order-product-name">${escH(p.productName || '—')}</div>
                <div class="order-product-ref">${escH(p.productRef || '')}</div>
                <div class="order-product-qty">Qté: ${p.quantity || 1}</div>
                <div class="order-product-price">${parseFloat(p.unitPrice || 0).toFixed(3)} DT × ${p.quantity || 1} = ${parseFloat(p.totalPrice || 0).toFixed(3)} DT</div>
            </div>
        `).join('');
    } else {
        // Fallback for old format
        productsHtml = `
            <div class="order-detail-product">
                <div class="order-product-name">${escH(order.productName || '—')}</div>
                <div class="order-product-ref">${escH(order.productRef || '')}</div>
                <div class="order-product-qty">Qté: ${order.quantity || 1}</div>
                <div class="order-product-price">${parseFloat(order.unitPrice || 0).toFixed(3)} DT × ${order.quantity || 1} = ${parseFloat(order.totalPrice || 0).toFixed(3)} DT</div>
            </div>
        `;
    }

    content.innerHTML = `
        <div class="order-details">
            <div class="order-info-grid">
                <div class="order-info-item">
                    <label>ID Commande:</label>
                    <span>#${String(order.id).slice(-6)}</span>
                </div>
                <div class="order-info-item">
                    <label>Client:</label>
                    <span>${escH(clientDisplay)}</span>
                </div>
                <div class="order-info-item">
                    <label>Paiement:</label>
                    <span>${payIcon} ${escH(payLabel)}</span>
                </div>
                <div class="order-info-item">
                    <label>Statut:</label>
                    <span class="order-status-badge ${STATUS_CLASS[order.status] || 'status-en_attente'}">${escH(order.status || 'En attente')}</span>
                </div>
                <div class="order-info-item">
                    <label>Date:</label>
                    <span>${escH(order.date || '—')}</span>
                </div>
                <div class="order-info-item">
                    <label>Total:</label>
                    <span class="order-total">${parseFloat(order.totalPrice || 0).toFixed(3)} DT</span>
                </div>
            </div>
            <div class="order-products-section">
                <h4>Produits commandés:</h4>
                <div class="order-products-list">
                    ${productsHtml}
                </div>
            </div>
        </div>
    `;

    document.getElementById('orderDetailsOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeOrderDetails() {
    document.getElementById('orderDetailsOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

// ════════════════════════════════════
//  TOAST
// ════════════════════════════════════
let _toastTimer = null;
function showToast(msg, type='success') {
    const t=document.getElementById('toast');
    document.getElementById('toastMsg').textContent=msg;
    t.className=`toast ${type}`;
    requestAnimationFrame(()=>{ t.classList.add('show'); });
    clearTimeout(_toastTimer);
    _toastTimer=setTimeout(()=>{ t.classList.remove('show'); },2800);
}

// ════════════════════════════════════
//  LISTENERS & INIT
// ════════════════════════════════════
document.getElementById('aSearch').addEventListener('input', applyProductFilters);
document.getElementById('aFilterCat').addEventListener('change', applyProductFilters);
document.getElementById('aFilterStock').addEventListener('change', applyProductFilters);
document.getElementById('oSearch').addEventListener('input', applyOrderFilters);
document.getElementById('oFilterStatus').addEventListener('change', applyOrderFilters);
document.getElementById('oFilterPayment').addEventListener('change', applyOrderFilters);

// Init
refreshAll();
</script>
</body>
</html>