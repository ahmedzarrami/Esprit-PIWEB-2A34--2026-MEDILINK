<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediLink — Parapharmacie en ligne</title>
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
    --orange:      #ea580c;
    --orange-light:#fff7ed;
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
.navbar {
    background: #fff; border-bottom: 1px solid var(--gray-200);
    padding: 0 40px; height: 68px;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; z-index: 1000;
}
.nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--gray-900); }
.nav-logo-icon {
    width: 36px; height: 36px; background: var(--blue);
    border-radius: 9px; display: flex; align-items: center; justify-content: center;
}
.nav-logo-icon svg { fill: #fff; }
.nav-logo-text { font-size: 16px; font-weight: 600; }
.nav-logo-text span { color: var(--blue); }
.nav-links { display: flex; gap: 4px; }
.nav-links a {
    padding: 6px 16px; border-radius: 8px; color: var(--gray-600);
    text-decoration: none; font-size: 13px; font-weight: 500; transition: .15s;
}
.nav-links a:hover, .nav-links a.active { background: var(--blue-light); color: var(--blue); }
.btn-admin {
    display: flex; align-items: center; gap: 7px;
    padding: 8px 16px; background: var(--navy); color: #fff;
    border-radius: 8px; font-size: 13px; font-weight: 500;
    text-decoration: none; transition: .15s;
}
.btn-admin:hover { background: var(--navy2); color: #fff; }
.nav-cart .cart-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 14px; background: var(--green-light); color: var(--green-dark);
    border: 1px solid rgba(13,162,113,.25); border-radius: 10px;
    font-size: 13px; font-weight: 600; cursor: pointer; transition: .15s;
}
.nav-cart .cart-btn:hover { background: var(--green); color: #fff; }
.cart-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 20px; height: 20px; border-radius: 50%; background: var(--green); color: #fff;
    font-size: 11px; font-weight: 700;
}

/* ── HERO ── */
.hero {
    background: linear-gradient(135deg, #0f3a9c 0%, #1a46c4 25%, #2563eb 55%, #3b7ff7 85%, #4ca5ff 100%);
    padding: 88px 40px 120px; position: relative; overflow: hidden;
    min-height: 520px; display: flex; align-items: center;
}
.hero::before { 
    content:''; position:absolute; top:-80px; right:-80px; width:400px; height:400px; 
    background:radial-gradient(circle, rgba(255,255,255,.12) 0%, rgba(255,255,255,.04) 70%, transparent 100%); 
    border-radius:50%; animation: float 6s ease-in-out infinite;
}
.hero::after  { 
    content:''; position:absolute; bottom:-120px; left:35%; width:300px; height:300px; 
    background:radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%);
    border-radius:50%; animation: float 8s ease-in-out infinite reverse;
}
@keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(20px); } }
.hero::before { animation: float 6s ease-in-out infinite; }
.hero::after { animation: float 8s ease-in-out infinite reverse; }
.hero-inner { max-width:960px; margin:0 auto; position:relative; z-index:1; }
.hero-badge {
    display:inline-flex; align-items:center; gap:8px;
    background:rgba(255,255,255,.12); border:1.5px solid rgba(255,255,255,.35);
    border-radius:100px; padding:7px 18px; font-size:12px; font-weight:500; color:#fff; 
    margin-bottom:28px; backdrop-filter:blur(10px); box-shadow:0 8px 32px rgba(0,0,0,.15);
    animation: slideInDown .8s ease-out;
}
@keyframes slideInDown { from { opacity:0; transform: translateY(-20px); } to { opacity:1; transform: translateY(0); } }
.hero-badge-dot { width:7px; height:7px; background:#4ade80; border-radius:50%; flex-shrink:0; animation: pulse 2s infinite; }
@keyframes pulse { 0%, 100% { opacity:1; transform:scale(1); } 50% { opacity:.6; transform:scale(1.3); } }
.hero h1 { 
    font-size:52px; font-weight:700; color:#fff; line-height:1.15; margin-bottom:16px;
    letter-spacing:-.5px; animation: slideInUp .8s ease-out .1s backwards;
    text-shadow: 0 2px 10px rgba(0,0,0,.2);
}
.hero h1 em { font-family:'Instrument Serif',serif; font-style:italic; font-weight:400; color:#e0eeff; }
.hero p { 
    color:rgba(255,255,255,.85); font-size:16px; max-width:520px; line-height:1.8;
    animation: slideInUp .8s ease-out .2s backwards; font-weight:400;
}
@keyframes slideInUp { from { opacity:0; transform: translateY(20px); } to { opacity:1; transform: translateY(0); } }

/* ── STATS ── */
.stats-strip { background:#fff; border-bottom:1px solid var(--gray-200); box-shadow:0 4px 12px rgba(0,0,0,.05); }
.stats-inner { max-width:960px; margin:0 auto; display:grid; grid-template-columns:repeat(4,1fr); }
.stat-item { padding:24px 28px; border-right:1px solid var(--gray-200); transition:.3s; }
.stat-item:hover { background:var(--gray-50); transform:translateY(-2px); }
.stat-item:last-child { border-right:none; }
.stat-num { font-size:26px; font-weight:700; color:var(--blue); letter-spacing:-.5px; }
.stat-label { font-size:12px; color:var(--gray-400); margin-top:4px; font-weight:500; text-transform:uppercase; letter-spacing:.03em; }

/* ── MAIN ── */
.main-content { max-width:960px; margin:0 auto; padding:44px 40px 80px; }
.section-heading {
    font-size:15px; font-weight:600; color:var(--gray-900);
    margin-bottom:20px; display:flex; align-items:center; gap:10px;
}
.section-heading::before { content:''; display:inline-block; width:3px; height:16px; background:var(--blue); border-radius:2px; }

/* ── SEARCH CARD ── */
.search-card {
    background:#fff; border:1px solid var(--gray-200);
    border-radius:var(--radius-xl); padding:28px; margin-bottom:36px;
}
.search-filters { display:grid; grid-template-columns:1fr 1fr auto; gap:14px; margin-bottom:16px; align-items:end; }
.form-group-inline { display:flex; flex-direction:column; gap:6px; }
.filter-label { font-size:11px; font-weight:500; color:var(--gray-600); text-transform:uppercase; letter-spacing:.05em; }
.filter-input {
    padding:10px 13px; border:1px solid var(--gray-200); border-radius:8px;
    font-size:14px; font-family:'Plus Jakarta Sans',sans-serif;
    color:var(--gray-900); background:#fff; outline:none; transition:.15s; height:40px;
}
.filter-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,86,219,.1); }
.btn-reset {
    height:40px; padding:0 16px; background:var(--gray-100); color:var(--gray-600);
    border:1px solid var(--gray-200); border-radius:8px; font-size:13px; cursor:pointer;
    font-family:'Plus Jakarta Sans',sans-serif; transition:.15s;
}
.btn-reset:hover { color:var(--gray-900); border-color:var(--gray-400); }
.cat-tags { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
.cat-tag {
    padding:4px 14px; border-radius:100px; font-size:12px; font-weight:500; cursor:pointer;
    border:1px solid var(--gray-200); background:var(--gray-100); color:var(--gray-600);
    font-family:'Plus Jakarta Sans',sans-serif; transition:.15s;
}
.cat-tag:hover, .cat-tag.active { background:var(--blue-light); border-color:var(--blue-mid); color:var(--blue-dark); }
.results-info { font-size:12px; color:var(--gray-400); }
.results-info strong { color:var(--gray-900); }

/* ── PRODUCTS GRID ── */
.products-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:16px; }
.product-card {
    background:#fff; border:1px solid var(--gray-200);
    border-radius:var(--radius-lg); overflow:hidden;
    transition:.2s ease; position:relative;
}
.product-card::before {
    content:''; position:absolute; top:0; left:0; right:0; height:3px;
    background:var(--blue); transform:scaleX(0); transition:.2s; transform-origin:left;
}
.product-card:hover { border-color:var(--blue-mid); transform:translateY(-3px); box-shadow:0 12px 28px rgba(26,86,219,.12); }
.product-card:hover::before { transform:scaleX(1); }
.product-thumb {
    height:130px; background:var(--gray-50);
    display:flex; align-items:center; justify-content:center; font-size:44px;
    border-bottom:1px solid var(--gray-200); overflow:hidden;
}
.product-thumb img {
    width:100%; height:100%; object-fit:cover;
}
.product-body { padding:16px; }
.product-ref { font-size:10px; font-weight:500; color:var(--gray-400); text-transform:uppercase; letter-spacing:.08em; margin-bottom:4px; }
.product-name { font-size:14px; font-weight:600; color:var(--gray-900); margin-bottom:4px; line-height:1.35; }
.product-desc { font-size:12px; color:var(--gray-400); margin-bottom:10px; line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.product-rating { 
  display:flex; 
  align-items:center; 
  gap:8px; 
  margin-bottom:10px; 
  position:relative;
  padding: 6px 12px;
  background: linear-gradient(135deg, #f8fafc, #f1f5f9);
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.star-rating { 
  display:flex; 
  gap:1px; 
  position:relative;
}

.star { 
  font-size:13px; 
  color:#cbd5e1; 
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  transform-origin: center;
  filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
}

.star.filled { 
  background: linear-gradient(135deg, #059669, #047857);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  filter: drop-shadow(0 0 6px rgba(5, 150, 105, 0.3));
  animation: medicalPulse 3s ease-in-out infinite;
}

.star.filled::before {
  content: '★';
  position: absolute;
  top: 0;
  left: 0;
  background: linear-gradient(135deg, #059669, #047857);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  z-index: -1;
  filter: blur(6px);
  opacity: 0.4;
  animation: medicalGlow 3s ease-in-out infinite;
}

.star.half-star {
  background: linear-gradient(90deg, #059669 50%, #cbd5e1 50%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  position: relative;
}

.star.half-star::after {
  content: '★';
  position: absolute;
  top: 0;
  left: 0;
  background: linear-gradient(90deg, #059669 50%, #cbd5e1 50%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  filter: drop-shadow(0 0 3px rgba(5, 150, 105, 0.2));
}

.rating-text { 
  font-size:10px; 
  color:#64748b; 
  font-weight: 600;
  padding: 3px 8px;
  background: linear-gradient(135deg, #ffffff, #f8fafc);
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  gap: 4px;
}

.rating-text::before {
  content: '💊';
  font-size: 9px;
  opacity: 0.7;
}

@keyframes medicalPulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.03); }
}

@keyframes medicalGlow {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 0.6; transform: scale(1.05); }
}

.product-rating:hover .star {
  transform: scale(1.08);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.product-rating:hover .star.filled {
  filter: drop-shadow(0 0 10px rgba(5, 150, 105, 0.5));
  transform: scale(1.12);
}

.product-rating:hover .rating-text {
  background: linear-gradient(135deg, #ecfdf5, #d1fae5);
  color: #065f46;
  transform: translateY(-1px);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  border-color: #10b981;
}

/* Professional medical rating badge for high ratings */
.product-rating[data-rating="4"] .rating-text,
.product-rating[data-rating="5"] .rating-text {
  background: linear-gradient(135deg, #ecfdf5, #d1fae5);
  color: #065f46;
  border: 1px solid #10b981;
  box-shadow: 0 2px 6px rgba(5, 150, 105, 0.2);
  animation: certifiedPulse 4s ease-in-out infinite;
}

.product-rating[data-rating="4"] .rating-text::before,
.product-rating[data-rating="5"] .rating-text::before {
  content: '⭐';
  color: #059669;
  opacity: 1;
}

@keyframes certifiedPulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.03); }
}

/* Add professional seal for 5-star ratings */
.product-rating[data-rating="5"]::after {
  content: '✓';
  position: absolute;
  top: -4px;
  right: -4px;
  width: 16px;
  height: 16px;
  background: linear-gradient(135deg, #059669, #047857);
  color: white;
  border-radius: 50%;
  font-size: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  box-shadow: 0 1px 3px rgba(5, 150, 105, 0.3);
}
.product-cat { display:inline-block; font-size:11px; font-weight:500; padding:2px 9px; border-radius:100px; margin-bottom:10px; background:var(--blue-light); color:var(--blue); }
.product-footer { display:flex; align-items:center; justify-content:space-between; }
.product-price { font-size:17px; font-weight:700; color:var(--gray-900); }
.product-price small { font-size:11px; font-weight:400; color:var(--gray-400); }
.stock-badge { font-size:10px; font-weight:600; padding:3px 9px; border-radius:100px; }
.stock-ok  { background:var(--green-light); color:#065f46; }
.stock-low { background:var(--orange-light); color:var(--orange); }
.stock-out { background:var(--red-light); color:var(--red); }
.product-actions { display:flex; gap:6px; margin-top:12px; }
.btn-commander {
    width:100%; height:36px; border:none; border-radius:8px;
    font-size:13px; font-weight:600; cursor:pointer;
    font-family:'Plus Jakarta Sans',sans-serif; transition:.15s;
    display:flex; align-items:center; justify-content:center; gap:6px;
    background:var(--blue); color:#fff;
}
.btn-commander:hover { background:var(--blue-dark); transform:translateY(-1px); }
.btn-commander:disabled { background:var(--gray-200); color:var(--gray-400); cursor:not-allowed; }
.btn-add-cart {
    width:36px; height:36px; border:none; border-radius:8px;
    font-size:16px; font-weight:600; cursor:pointer;
    font-family:'Plus Jakarta Sans',sans-serif; transition:.15s;
    background:var(--green-light); color:var(--green-dark);
}
.btn-add-cart:hover { background:var(--green); color:#fff; }
.btn-add-cart:disabled { background:var(--gray-200); color:var(--gray-400); cursor:not-allowed; }
.btn-rate {
    width:36px; height:36px; border:none; border-radius:8px;
    font-size:14px; cursor:pointer;
    font-family:'Plus Jakarta Sans',sans-serif; transition:.15s;
    background:var(--orange-light); color:var(--orange);
}
.btn-rate:hover { background:var(--orange); color:#fff; }
.empty-state { grid-column:1/-1; text-align:center; padding:56px 20px; color:var(--gray-400); }
.empty-state-icon { font-size:32px; margin-bottom:12px; opacity:.5; }
.empty-state strong { display:block; font-size:15px; color:var(--gray-600); margin-bottom:6px; }

/* ── RATING STYLES ── */
.star-input { display:flex; gap:4px; font-size:24px; cursor:pointer; }
.star-option { color:#ddd; transition:.15s; }
.star-option:hover, .star-option.filled { color:#fbbf24; }

/* ── FAB ── */
.fab-add {
    position:fixed; bottom:32px; right:32px; width:56px; height:56px; border-radius:50%;
    background:var(--blue); color:#fff; display:flex; align-items:center; justify-content:center;
    box-shadow:0 8px 24px rgba(26,86,219,.35); cursor:pointer; font-size:26px;
    border:none; transition:.2s; z-index:500;
}
.fab-add:hover { background:var(--blue-dark); transform:scale(1.08); }

/* ── MODAL ── */
.modal-overlay {
    display:none; position:fixed; inset:0; background:rgba(15,27,45,.45);
    z-index:2000; align-items:center; justify-content:center; backdrop-filter:blur(3px);
}
.modal-overlay.open { display:flex; }
.modal {
    background:#fff; border-radius:var(--radius-xl); width:100%; max-width:540px;
    max-height:92vh; overflow-y:auto; padding:36px;
    box-shadow:0 24px 60px rgba(15,27,45,.2);
    animation: slideUp .25s ease;
}
@keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
.modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; }
.modal-title { font-size:18px; font-weight:600; color:var(--gray-900); display:flex; align-items:center; gap:10px; }
.modal-title-icon { width:36px; height:36px; border-radius:9px; background:var(--blue-light); display:flex; align-items:center; justify-content:center; }
.modal-close { width:32px; height:32px; border:none; background:var(--gray-100); border-radius:8px; cursor:pointer; font-size:18px; color:var(--gray-600); display:flex; align-items:center; justify-content:center; transition:.15s; }
.modal-close:hover { background:var(--gray-200); }
.rating-modal {
    padding: 28px 28px 24px;
    background: linear-gradient(180deg, #ffffff 0%, #eef6ff 100%);
    border: 1px solid rgba(59,130,246,.18);
    border-radius: 28px;
}
.rating-modal .modal-title-icon {
    background: rgba(59,130,246,.12);
    color: #2563eb;
}
.rating-modal .modal-title span {
    font-size: 18px;
    font-weight: 700;
}
.rating-modal .form-group {
    margin-bottom: 18px;
}
.rating-modal .form-label {
    font-size: 11px; font-weight: 700; color: #475569;
    letter-spacing: .08em; text-transform: uppercase;
}
.rating-modal .product-chip {
    display: inline-flex; align-items:center; gap:8px;
    width:100%; padding:14px 16px; border-radius:18px;
    border:1px solid rgba(59,130,246,.18); background:#eff6ff;
    color:#1d4ed8; font-weight:700; font-size:14px;
}
.star-input { display:flex; gap:8px; font-size:28px; }
.star-option {
    width:46px; height:46px; display:inline-flex; align-items:center; justify-content:center;
    border-radius:14px; cursor:pointer; background:#f8fafc; color:#94a3b8;
    transition:.18s;
    border:1px solid transparent;
}
.star-option:hover { background:#eff6ff; color:#2563eb; }
.star-option.filled { background:#fef3c7; color:#f59e0b; border-color:#fbbf24; }
.rating-modal .form-textarea {
    min-height:120px; border-radius:18px; padding:18px;
    border:1px solid rgba(148,163,184,.3);
    background:#f8fafc;
}
.rating-modal .btn-submit {
    width:100%; min-height:50px; border-radius:16px;
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    box-shadow:0 14px 28px rgba(59,130,246,.18);
    font-size:15px; letter-spacing:.02em;
}
.rating-modal .btn-submit:hover { background: linear-gradient(135deg, #1d4ed8, #2563eb); }
.form-alert {
    display:none; padding:14px 16px; border-radius:16px; font-size:14px; margin-bottom:20px;
    align-items:center; gap:10px; font-weight:600;
}
.form-alert.show { display:flex; }
.form-alert.success { background: #dcfce7; color: #166534; }
.form-alert.err { background: #fee2e2; color: #9b1234; }
.form-row { display:grid; grid-template-columns:1fr; gap:16px; }

/* ── CONFIRM MODAL ── */
.confirm-modal { max-width:400px; text-align:center; }
.confirm-icon { font-size:40px; margin-bottom:12px; }
.confirm-title { font-size:17px; font-weight:600; margin-bottom:8px; }
.confirm-msg { font-size:13px; color:var(--gray-600); margin-bottom:24px; }
.confirm-actions { display:flex; gap:10px; }
.btn-cancel { flex:1; height:40px; background:var(--gray-100); color:var(--gray-600); border:1px solid var(--gray-200); border-radius:9px; font-size:13px; font-weight:500; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:.15s; }
.btn-cancel:hover { background:var(--gray-200); }
.btn-confirm-del { flex:1; height:40px; background:var(--red); color:#fff; border:none; border-radius:9px; font-size:13px; font-weight:500; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:.15s; }
.btn-confirm-del:hover { background:#b91c1c; }

/* ── FOOTER ── */
.footer-admin { text-align:center; padding:20px; border-top:1px solid var(--gray-200); background:#fff; margin-top:40px; }
.footer-admin a { display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--gray-400); text-decoration:none; padding:8px 16px; border-radius:8px; transition:.15s; }
.footer-admin a:hover { background:var(--gray-100); color:var(--gray-600); }
</style>
<link rel="stylesheet" href="/files40/assets/css/unified.css">
<script src="/files40/assets/js/medilink_topbar.js" defer></script>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar">
    <a href="home.php" class="nav-logo">
        <div class="nav-logo-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/></svg>
        </div>
        <span class="nav-logo-text">Medi<span>Link</span></span>
    </a>

    <div class="nav-links">
        <a href="home.php" class="active">Accueil</a>
        <a href="#liste">Catalogue</a>
    </div>

    <div style="display:flex;align-items:center;gap:10px">
        <button class="cart-btn" onclick="refreshProducts()" style="background: var(--blue-light); color: var(--blue-dark); border: 1px solid rgba(26,86,219,.25);" title="Actualiser les produits">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6M1 20v-6h6"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
            Actualiser
        </button>
        <div class="nav-cart">
            <button class="cart-btn" onclick="openCart()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
                Panier
                <span class="cart-badge" id="cartBadge">0</span>
            </button>
        </div>
        <a href="../back/admin.php" class="btn-admin">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
            </svg>
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
        <p>Découvrez notre catalogue de produits de santé, beauté et bien-être. Ajoutez, gérez et commandez en quelques secondes.</p>
    </div>
</div>

<!-- ── STATS ── -->
<div class="stats-strip">
    <div class="stats-inner">
        <div class="stat-item">
            <div class="stat-num" id="statTotal">0 produits</div>
            <div class="stat-label">En catalogue</div>
        </div>
        <div class="stat-item">
            <div class="stat-num" id="statCats">—</div>
            <div class="stat-label">Catégories</div>
        </div>
        <div class="stat-item">
            <div class="stat-num" id="statDispo">0</div>
            <div class="stat-label">En stock</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">24h – 48h</div>
            <div class="stat-label">Délai livraison</div>
        </div>
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
                <select id="filterCat" class="filter-input">
                    <option value="">Toutes les catégories</option>
                </select>
            </div>
            <button class="btn-reset" onclick="resetFilters()">↺ Réinitialiser</button>
        </div>
        <div class="cat-tags" id="catTags"></div>
        <div class="results-info" id="resultsInfo"></div>
    </div>

    <div class="section-heading">Catalogue des produits</div>
    <div class="products-grid" id="productsGrid"></div>

</div>

<!-- ── MODAL AJOUT / ÉDITION ── -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">
                <div class="modal-title-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1a56db" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                </div>
                <span id="modalTitleText">Ajouter un produit</span>
            </div>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>

        <div class="form-alert" id="formAlert">
            <span id="formAlertMsg"></span>
        </div>

        <input type="hidden" id="editId">

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Référence *</label>
                <input type="text" id="fieldRef" class="form-input" placeholder="PRD-001">
                <span class="field-error" id="errRef">Champ requis</span>
            </div>
            <div class="form-group">
                <label class="form-label">Catégorie *</label>
                <select id="fieldCat" class="form-select">
                    <option value="">— Sélectionner —</option>
                    <option>Soins visage</option>
                    <option>Soins corps</option>
                    <option>Hygiène</option>
                    <option>Compléments alimentaires</option>
                    <option>Bébé &amp; Maman</option>
                    <option>Capillaire</option>
                    <option>Solaire</option>
                    <option>Minceur</option>
                    <option>Orthopédie</option>
                    <option>Autre</option>
                </select>
                <span class="field-error" id="errCat">Champ requis</span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Nom du produit *</label>
            <input type="text" id="fieldNom" class="form-input" placeholder="Ex : Crème hydratante SPF 30">
            <span class="field-error" id="errNom">Champ requis</span>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea id="fieldDesc" class="form-textarea" placeholder="Description, ingrédients, bienfaits…"></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Prix (DT) *</label>
                <input type="number" id="fieldPrix" class="form-input" placeholder="0.000" min="0" step="0.001">
                <span class="field-error" id="errPrix">Prix invalide</span>
            </div>
            <div class="form-group">
                <label class="form-label">Stock (unités) *</label>
                <input type="number" id="fieldStock" class="form-input" placeholder="0" min="0">
                <span class="field-error" id="errStock">Stock invalide</span>
            </div>
        </div>

        <button class="btn-submit" id="btnSubmit" onclick="submitForm()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Enregistrer
        </button>
    </div>
</div>

<!-- ── ORDER MODAL ── -->
<div class="modal-overlay" id="orderOverlay">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">
                <div class="modal-title-icon">🛒</div>
                <span id="orderModalTitle">Commander un produit</span>
            </div>
            <button class="modal-close" onclick="closeOrderModal()">✕</button>
        </div>
        <div class="form-alert" id="orderAlert"><span id="orderAlertMsg"></span></div>
        <div id="orderSingleSection">
          <div class="form-group">
              <label class="form-label">Produit</label>
              <div id="orderProductInfo" style="padding:12px;background:var(--blue-light);border-radius:9px;color:var(--blue);font-weight:600;">—</div>
          </div>
          <div class="form-group">
              <label class="form-label">Quantité *</label>
              <input type="number" id="orderQty" class="form-input" value="1" min="1">
              <span class="field-error" id="errQty">Quantité invalide</span>
          </div>
        </div>
        <div id="orderCartSection" style="display:none;">
          <div class="form-group">
              <label class="form-label">Panier</label>
              <div id="orderCartList" style="padding:12px;background:var(--gray-50);border-radius:9px;min-height:90px;">Aucun produit</div>
          </div>
        </div>
        <div class="form-group">
            <label class="form-label">Adresse de livraison 📍 *</label>
            <input type="text" id="orderAddress" class="form-input" placeholder="Ex: 123 Rue Habib Bourguiba, Tunis">
            <span class="field-error" id="errAddress">Adresse requise</span>
        </div>
        <div class="form-group">
            <label class="form-label">Mode de paiement *</label>
            <select id="orderPayment" class="form-select">
                <option value="">— Sélectionner —</option>
                <option value="virement">🏦 Virement bancaire</option>
                <option value="carte_bancaire">💳 Carte bancaire</option>
                <option value="paypal">🅿️ PayPal</option>
                <option value="especes">💵 Espèces</option>
            </select>
            <span class="field-error" id="errPayment">Mode de paiement requis</span>
        </div>
        <div style="background:var(--gray-50);padding:14px;border-radius:10px;margin-bottom:18px;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:12px;color:var(--gray-600);">Total à payer</span>
            <span style="font-size:18px;font-weight:700;color:var(--blue);"><span id="orderTotal">0.000</span> DT</span>
        </div>
        <button class="btn-submit" onclick="submitOrder()">Passer la commande</button>
    </div>
</div>

<!-- ── FOOTER ── -->
<div class="footer-admin">
    <a href="../back/admin.php">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
        Accès administration
    </a>
</div>

<script>
// ═══════════════════════════════════════════
//  SHARED HELPERS
// ═══════════════════════════════════════════
const CAT_ICONS = {
  'Soins visage':'🧴','Soins corps':'🫧','Hygiène':'🪥',
  'Compléments alimentaires':'💊','Bébé & Maman':'🍼',
  'Capillaire':'💆','Solaire':'☀️','Minceur':'⚖️','Orthopédie':'🦴','Autre':'📦'
};
const CATEGORY_IMAGES = {
  'Soins visage': [
    'https://images.unsplash.com/photo-1596462502278-27d3814a9fd9?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1620916566398-215f721f815c?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1612810280033-3e1b1e0c9bed?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1556228720-195a90dd30a8?auto=format&fit=crop&w=600&q=80'
  ],
  'Soins corps': [
    'https://images.unsplash.com/photo-1556228720-195a90dd30a8?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1570172619644-dfd03ed7d9d0?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1559597219-e00e898cb492?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=600&q=80'
  ],
  'Hygiène': [
    'https://images.unsplash.com/photo-1556228720-195a90dd30a8?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1579413121661-d8d8cb5f599d?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1559827260-dc66d52bef19?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=600&q=80'
  ],
  'Compléments alimentaires': [
    'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1598106848310-4a4ba4106a81?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1554465385-e3b0b08c72ea?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?auto=format&fit=crop&w=600&q=80'
  ],
  'Bébé & Maman': [
    'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1579413121661-d8d8cb5f599d?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=600&q=80'
  ],
  'Capillaire': [
    'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1560066161-48c89d2545db?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1596462502278-27d3814a9fd9?auto=format&fit=crop&w=600&q=80'
  ],
  'Solaire': [
    'https://images.unsplash.com/photo-1596462502278-27d3814a9fd9?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1612810280033-3e1b1e0c9bed?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1559827260-dc66d52bef19?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1570172619644-dfd03ed7d9d0?auto=format&fit=crop&w=600&q=80'
  ],
  'Minceur': [
    'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1598106848310-4a4ba4106a81?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1554465385-e3b0b08c72ea?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?auto=format&fit=crop&w=600&q=80'
  ],
  'Orthopédie': [
    'https://images.unsplash.com/photo-1559757148-5c350a060e0b?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1559827260-dc66d52bef19?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1570172619644-dfd03ed7d9d0?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=600&q=80'
  ],
  'Autre': [
    'https://images.unsplash.com/photo-1556228720-195a90dd30a8?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1596462502278-27d3814a9fd9?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=600&q=80'
  ]
};

const DEFAULT_PRODUCT_IMAGES = {
  'PHM-VIS-01':'https://images.unsplash.com/photo-1596462502278-27d3814a9fd9?auto=format&fit=crop&w=600&q=80',
  'PHM-COR-02':'https://images.unsplash.com/photo-1556228720-195a90dd30a8?auto=format&fit=crop&w=600&q=80',
  'PHM-HYG-03':'https://images.unsplash.com/photo-1579413121661-d8d8cb5f599d?auto=format&fit=crop&w=600&q=80',
  'PHM-COMP-04':'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=600&q=80',
  'PHM-BEB-05':'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=600&q=80',
  'PHM-CAP-06':'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=600&q=80'
};
const CART_KEY = 'pharma_cart';
let _currentOrderProduct = null;
let _cartItems = [];
let _orderMode = 'single';

function getProducts() { return JSON.parse(localStorage.getItem('pharma_products')||'[]'); }
function saveProducts(l){ localStorage.setItem('pharma_products',JSON.stringify(l)); }
function getCart() { try { return JSON.parse(localStorage.getItem(CART_KEY)||'[]'); } catch(e) { return []; } }
function saveCart(c){ localStorage.setItem(CART_KEY,JSON.stringify(c)); }
function addToCart(productId, qty=1) {
  const products = getProducts();
  const product = products.find(p=>p.id==productId);
  if(!product || parseInt(product.stock)<=0) {
    showToast('Produit indisponible', 'error');
    return;
  }
  const cart = getCart();
  const item = cart.find(i=>i.productId==productId);
  if(item) {
    if(item.quantity + qty > parseInt(product.stock)) {
      showToast('Stock insuffisant', 'error');
      return;
    }
    item.quantity += qty;
  } else {
    cart.push({ productId, quantity: qty });
  }
  saveCart(cart);
  updateCartBadge();
  showToast('Produit ajouté au panier ✓', 'success');
}
function updateCartBadge(){
  const cart = getCart();
  const total = cart.reduce((sum,item)=>sum + item.quantity,0);
  const badge = document.getElementById('cartBadge');
  if(badge) badge.textContent = total;
}
function openCart(){
  _cartItems = getCart();
  if(!_cartItems.length) {
    showToast('Votre panier est vide', 'info');
    return;
  }
  _orderMode = 'cart';
  _currentOrderProduct = null;
  renderOrderModal();
  document.getElementById('orderOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function showToast(msg,type){
  const toast = document.createElement('div');
  toast.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#0f1b2d;color:#fff;padding:12px 18px;border-radius:12px;z-index:9999;font-size:13px;font-weight:600;box-shadow:0 10px 24px rgba(0,0,0,.2);';
  toast.textContent = msg;
  document.body.appendChild(toast);
  setTimeout(()=>toast.remove(),2500);
}
function escH(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

async function fetchProductsFromApi() {
  try {
    const products = await apiRequest('produits','GET');
    if (Array.isArray(products) && products.length > 0) {
      saveProducts(products);
      return products;
    }
  } catch (e) {
    console.warn('Impossible de charger les produits depuis l\'API', e.message);
  }
  return getProducts();
}

async function initFrontProducts() {
  await fetchProductsFromApi();
  updateFrontStats();
  populateCatSelectAndTags();
  applyFilters();
  updateCartBadge();
}

window.addEventListener('storage', (event) => {
  if (event.key === 'pharma_products') {
    updateFrontStats();
    populateCatSelectAndTags();
    applyFilters();
    updateCartBadge();
    showToast('📦 Produits mis à jour', 'success');
  } else if (event.key === 'pharma_products_updated') {
    // Force refresh when products are updated from admin
    setTimeout(() => {
      updateFrontStats();
      populateCatSelectAndTags();
      applyFilters();
      updateCartBadge();
      showToast('🖼️ Images des produits mises à jour', 'success');
    }, 100);
  }
});

// Also listen for visibility changes to refresh when tab becomes active
document.addEventListener('visibilitychange', function() {
  if (!document.hidden) {
    // Tab became active, check for updates
    setTimeout(() => {
      updateFrontStats();
      populateCatSelectAndTags();
      applyFilters();
      updateCartBadge();
    }, 100);
  }
});

// Manual refresh function
function refreshProducts() {
  // Force refresh from localStorage
  const products = getProducts();
  saveProducts(products); // This will trigger storage events
  
  // Update all displays
  updateFrontStats();
  populateCatSelectAndTags();
  applyFilters();
  updateCartBadge();
  
  showToast('🔄 Produits actualisés', 'success');
}

// ── STATS ──
function updateFrontStats(){
  const products = getProducts();
  const total = products.length;
  const dispo = products.filter(p=>parseInt(p.stock)>5).length;
  const uniqueCats = new Set(products.map(p=>p.categorie).filter(Boolean));
  document.getElementById('statTotal').textContent = total + (total===1?' produit':' produits');
  document.getElementById('statCats').textContent = uniqueCats.size;
  document.getElementById('statDispo').textContent = dispo;
}
initFrontProducts();

// ── FILTRES ──
function populateCatSelectAndTags(){
  const products = getProducts();
  const cats = [...new Set(products.map(p=>p.categorie).filter(Boolean))];
  const select = document.getElementById('filterCat');
  const cur = select.value;
  select.innerHTML = '<option value="">Toutes les catégories</option>';
  cats.forEach(c=>{ const o=document.createElement('option'); o.value=c; o.textContent=c; if(c===cur)o.selected=true; select.appendChild(o); });
  
  const tagsDiv = document.getElementById('catTags');
  tagsDiv.innerHTML = '';
  cats.forEach(c=>{
    const tag = document.createElement('button');
    tag.className = 'cat-tag';
    tag.textContent = c;
    tag.onclick = ()=>{ select.value = c; applyFilters(); };
    tagsDiv.appendChild(tag);
  });
}

function applyFilters(){
  const q = document.getElementById('searchInput').value.toLowerCase().trim();
  const cat = document.getElementById('filterCat').value;
  const products = getProducts();
  const filtered = products.filter(p=>{
    const matchQ = !q || p.nom.toLowerCase().includes(q) || (p.reference||'').toLowerCase().includes(q) || (p.description||'').toLowerCase().includes(q);
    const matchC = !cat || p.categorie===cat;
    return matchQ && matchC;
  });
  renderFrontGrid(filtered, products.length);
  document.getElementById('resultsInfo').innerHTML = `<strong>${filtered.length}</strong> produit${filtered.length!==1?'s':''} trouvé${filtered.length!==1?'s':''}`;
}

function renderFrontGrid(products, total){
  const grid = document.getElementById('productsGrid');
  if(!products.length){
    grid.innerHTML = `<div class="empty-state"><div class="empty-state-icon">📦</div><strong>Aucun produit trouvé</strong>Essayez de modifier votre recherche ou ajoutez un produit.</div>`;
    return;
  }
  // Load ratings for all products
  loadAllRatings(products).then(ratingsMap => {
    grid.innerHTML = products.map(p=>{
      const s = parseInt(p.stock)||0;
      const sc = s===0?'stock-out':s<=5?'stock-low':'stock-ok';
      const sl = s===0?'Rupture':s<=5?`⚠ ${s}`:`${s} en stock`;
      const icon = CAT_ICONS[p.categorie] || '📦';
      const isOut = s === 0;
      // Get image: custom image first, then category-based image, then default reference image, then icon
      let imageUrl = '';
      if (p.image && p.image.trim()) {
        imageUrl = String(p.image).trim();
        console.log(`Product ${p.nom} (${p.reference}) using custom image:`, imageUrl);
      } else if (CATEGORY_IMAGES[p.categorie] && CATEGORY_IMAGES[p.categorie].length > 0) {
        // Use category-based image with some randomness
        const categoryImages = CATEGORY_IMAGES[p.categorie];
        const randomIndex = Math.abs(p.id || p.nom.charCodeAt(0)) % categoryImages.length;
        imageUrl = categoryImages[randomIndex];
        console.log(`Product ${p.nom} (${p.reference}) using category image:`, imageUrl);
      } else if (DEFAULT_PRODUCT_IMAGES[p.reference]) {
        imageUrl = DEFAULT_PRODUCT_IMAGES[p.reference];
        console.log(`Product ${p.nom} (${p.reference}) using default image:`, imageUrl);
      }
      
      const thumb = imageUrl ? `<img src="${escH(imageUrl)}" alt="${escH(p.nom)}" onerror="this.parentNode.textContent='📦'; console.warn('Image failed to load:', '${imageUrl}');">` : icon;
      const ratingData = ratingsMap[p.id] || { average_rating: 0, total_ratings: 0 };
      const stars = renderStars(ratingData.average_rating);
      return `
        <div class="product-card">
          <div class="product-thumb">${thumb}</div>
          <div class="product-body">
            <div class="product-ref">${escH(p.reference||'N/R')}</div>
            <div class="product-name">${escH(p.nom)}</div>
            <div class="product-desc">${escH(p.description||'Aucune description')}</div>
            <div class="product-rating" data-rating="${Math.floor(ratingData.average_rating)}">
              <div class="star-rating">${stars}</div>
              <span class="rating-text">${ratingData.total_ratings} avis</span>
            </div>
            <div class="product-cat">${escH(p.categorie||'Autre')}</div>
            <div class="product-footer">
              <div class="product-price">${parseFloat(p.prix||0).toFixed(3)} <small>DT</small></div>
              <div class="stock-badge ${sc}">${sl}</div>
            </div>
            <div class="product-actions">
              <button class="btn-commander" onclick="openOrderModal(${p.id})" ${isOut?'disabled':''}>🛒 Commander</button>
              <button class="btn-add-cart" onclick="addToCart(${p.id})" ${isOut?'disabled':''}>+</button>
              <button class="btn-rate" onclick="openRatingModal(${p.id}, '${escH(p.nom)}')">⭐ Noter</button>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }).catch(err => {
    console.error('Erreur chargement ratings:', err);
    // Render without ratings
    grid.innerHTML = products.map(p=>{
      const s = parseInt(p.stock)||0;
      const sc = s===0?'stock-out':s<=5?'stock-low':'stock-ok';
      const sl = s===0?'Rupture':s<=5?`⚠ ${s}`:`${s} en stock`;
      const icon = CAT_ICONS[p.categorie] || '📦';
      const isOut = s === 0;
      // Get image: custom image first, then category-based image, then default reference image, then icon
      let imageUrl = '';
      if (p.image && p.image.trim()) {
        imageUrl = String(p.image).trim();
        console.log(`Product ${p.nom} (${p.reference}) using custom image:`, imageUrl);
      } else if (CATEGORY_IMAGES[p.categorie] && CATEGORY_IMAGES[p.categorie].length > 0) {
        // Use category-based image with some randomness
        const categoryImages = CATEGORY_IMAGES[p.categorie];
        const randomIndex = Math.abs(p.id || p.nom.charCodeAt(0)) % categoryImages.length;
        imageUrl = categoryImages[randomIndex];
        console.log(`Product ${p.nom} (${p.reference}) using category image:`, imageUrl);
      } else if (DEFAULT_PRODUCT_IMAGES[p.reference]) {
        imageUrl = DEFAULT_PRODUCT_IMAGES[p.reference];
        console.log(`Product ${p.nom} (${p.reference}) using default image:`, imageUrl);
      }
      
      const thumb = imageUrl ? `<img src="${escH(imageUrl)}" alt="${escH(p.nom)}" onerror="this.parentNode.textContent='📦'; console.warn('Image failed to load:', '${imageUrl}');">` : icon;
      const defaultStars = renderStars(0);
      return `
        <div class="product-card">
          <div class="product-thumb">${thumb}</div>
          <div class="product-body">
            <div class="product-ref">${escH(p.reference||'N/R')}</div>
            <div class="product-name">${escH(p.nom)}</div>
            <div class="product-desc">${escH(p.description||'Aucune description')}</div>
            <div class="product-rating" data-rating="0">
              <div class="star-rating">${defaultStars}</div>
              <span class="rating-text">0 avis</span>
            </div>
            <div class="product-cat">${escH(p.categorie||'Autre')}</div>
            <div class="product-footer">
              <div class="product-price">${parseFloat(p.prix||0).toFixed(3)} <small>DT</small></div>
              <div class="stock-badge ${sc}">${sl}</div>
            </div>
            <div class="product-actions">
              <button class="btn-commander" onclick="openOrderModal(${p.id})" ${isOut?'disabled':''}>🛒 Commander</button>
              <button class="btn-add-cart" onclick="addToCart(${p.id})" ${isOut?'disabled':''}>+</button>
              <button class="btn-rate" onclick="openRatingModal(${p.id}, '${escH(p.nom)}')">⭐ Noter</button>
            </div>
          </div>
        </div>
      `;
    }).join('');
  });
}

function resetFilters(){
  document.getElementById('searchInput').value = '';
  document.getElementById('filterCat').value = '';
  applyFilters();
}

document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('filterCat').addEventListener('change', applyFilters);
populateCatSelectAndTags();
applyFilters();

// ── RATINGS ──
async function loadAllRatings(products) {
  const ratingsMap = {};
  const promises = products.map(async (p) => {
    try {
      const data = await apiRequest('ratings', 'GET', null, { produit_id: p.id });
      ratingsMap[p.id] = data;
    } catch (err) {
      console.warn(`Erreur chargement ratings pour ${p.id}:`, err);
      ratingsMap[p.id] = { average_rating: 0, total_ratings: 0 };
    }
  });
  await Promise.all(promises);
  return ratingsMap;
}

function renderStars(rating) {
  const fullStars = Math.floor(rating);
  const hasHalf = rating % 1 >= 0.5;
  let stars = '';
  for (let i = 1; i <= 5; i++) {
    if (i <= fullStars) {
      stars += `<span class="star filled" style="animation-delay: ${(i-1) * 0.1}s">★</span>`;
    } else if (i === fullStars + 1 && hasHalf) {
      stars += `<span class="star filled half-star" style="animation-delay: ${(i-1) * 0.1}s">★</span>`;
    } else {
      stars += `<span class="star" style="animation-delay: ${(i-1) * 0.1}s">☆</span>`;
    }
  }
  return stars;
}

function openRatingModal(productId, productName) {
  const modal = document.createElement('div');
  modal.className = 'modal-overlay';
  modal.innerHTML = `
    <div class="modal rating-modal">
      <div class="modal-header">
        <div class="modal-title">
          <div class="modal-title-icon">⭐</div>
          <span>Noter le produit</span>
        </div>
        <button class="modal-close" onclick="closeRatingModal()">✕</button>
      </div>
      <div class="form-alert" id="ratingAlert"><span id="ratingAlertMsg"></span></div>
      <div class="form-group">
        <label class="form-label">Produit</label>
        <div class="product-chip">${escH(productName)}</div>
      </div>
      <div class="form-group">
        <label class="form-label">Votre note *</label>
        <div class="star-input" id="starInput">
          <span class="star-option" data-rating="1">☆</span>
          <span class="star-option" data-rating="2">☆</span>
          <span class="star-option" data-rating="3">☆</span>
          <span class="star-option" data-rating="4">☆</span>
          <span class="star-option" data-rating="5">☆</span>
        </div>
        <span class="field-error" id="errRating">Veuillez sélectionner une note</span>
      </div>
      <div class="form-group">
        <label class="form-label">Commentaire (optionnel)</label>
        <textarea id="ratingComment" class="form-textarea" placeholder="Partagez votre expérience..."></textarea>
      </div>
      <button class="btn-submit" onclick="submitRating(${productId})">Envoyer la note</button>
    </div>
  `;
  document.body.appendChild(modal);
  modal.classList.add('open');
  document.body.style.overflow = 'hidden';

  let selectedRating = 0;
  const stars = modal.querySelectorAll('.star-option');
  stars.forEach((star, index) => {
    star.addEventListener('click', () => {
      selectedRating = index + 1;
      stars.forEach((s, i) => {
        s.classList.toggle('filled', i < selectedRating);
        s.textContent = i < selectedRating ? '★' : '☆';
      });
    });
  });
}

function closeRatingModal() {
  const modal = document.querySelector('.modal-overlay');
  if (modal) {
    modal.remove();
    document.body.style.overflow = '';
  }
}

async function submitRating(productId) {
  const rating = document.querySelectorAll('.star-option.filled').length;
  const comment = document.getElementById('ratingComment').value.trim();
  const alert = document.getElementById('ratingAlert');

  if (rating === 0) {
    document.getElementById('errRating').classList.add('show');
    return;
  }
  document.getElementById('errRating').classList.remove('show');

  try {
    await apiRequest('ratings', 'POST', {
      produit_id: productId,
      client_id: getClientId(),
      rating: rating,
      comment: comment
    });
    alert.className = 'form-alert show success';
    document.getElementById('ratingAlertMsg').textContent = 'Merci pour votre note !';
    setTimeout(() => {
      closeRatingModal();
      applyFilters(); // Refresh to show updated ratings
    }, 1500);
  } catch (error) {
    alert.className = 'form-alert show err';
    document.getElementById('ratingAlertMsg').textContent = error.message;
  }
}

const ORDERS_KEY = 'pharma_orders';
const CLIENT_KEY = 'pharma_client_id';
const API_URL    = new URL('../../api.php', window.location.href).href;

async function apiRequest(resource, method, body = null, queryParams = {}) {
  const options = { method, headers: { 'Content-Type': 'application/json' } };
  if (body !== null) options.body = JSON.stringify(body);
  let url = `${API_URL}?resource=${encodeURIComponent(resource)}`;
  if (Object.keys(queryParams).length > 0) {
    const params = new URLSearchParams(queryParams);
    url += '&' + params.toString();
  }
  const response = await fetch(url, options);
  const result = await response.json();
  if (!response.ok || !result.success) {
      throw new Error(result.message || 'Erreur API');
  }
  return result.data ?? result;
}

function getClientId() {
  let clientId = localStorage.getItem(CLIENT_KEY);
  if(!clientId) {
    clientId = 'CLT_' + Date.now() + '_' + Math.random().toString(36).substr(2, 8).toUpperCase();
    localStorage.setItem(CLIENT_KEY, clientId);
  }
  return clientId;
}

function getOrders() { try { return JSON.parse(localStorage.getItem(ORDERS_KEY) || '[]'); } catch(e) { return []; } }
function saveOrders(list) { localStorage.setItem(ORDERS_KEY, JSON.stringify(list)); }

function openOrderModal(id) {
  const products = getProducts();
  const p = products.find(x => x.id == id);
  if(!p) return;
  _orderMode = 'single';
  _currentOrderProduct = p;
  _cartItems = [];
  renderOrderModal();
  document.getElementById('orderOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function renderOrderModal() {
  const title = document.getElementById('orderModalTitle');
  const singleSection = document.getElementById('orderSingleSection');
  const cartSection = document.getElementById('orderCartSection');
  const alert = document.getElementById('orderAlert');
  title.textContent = _orderMode === 'cart' ? 'Commander le panier' : 'Commander un produit';
  alert.className = 'form-alert';
  ['errQty','errPayment'].forEach(id => document.getElementById(id).classList.remove('show'));
  document.getElementById('orderPayment').value = '';
  if(_orderMode === 'single') {
    singleSection.style.display = 'block';
    cartSection.style.display = 'none';
    document.getElementById('orderProductInfo').textContent = `${_currentOrderProduct.nom} - ${parseFloat(_currentOrderProduct.prix||0).toFixed(3)} DT`;
    document.getElementById('orderQty').value = '1';
  } else {
    singleSection.style.display = 'none';
    cartSection.style.display = 'block';
    renderCartList();
  }
  updateOrderTotal();
}

function renderCartList() {
  const list = document.getElementById('orderCartList');
  const products = getProducts();
  if(!_cartItems.length) {
    list.innerHTML = 'Aucun produit';
    return;
  }
  list.innerHTML = _cartItems.map(item => {
    const p = products.find(prod => prod.id == item.productId);
    if(!p) return '';
    return `<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(15,23,42,.09);">
      <div>
        <div style="font-weight:600;color:#0f172a;">${escH(p.nom)}</div>
        <div style="font-size:12px;color:#64748b;">${item.quantity} × ${parseFloat(p.prix||0).toFixed(3)} DT</div>
      </div>
      <div style="font-weight:700;color:#0f172a;">${(item.quantity * p.prix).toFixed(3)} DT</div>
    </div>`;
  }).join('');
}

function closeOrderModal() {
  document.getElementById('orderOverlay').classList.remove('open');
  document.body.style.overflow = '';
  _currentOrderProduct = null;
  _orderMode = 'single';
}

function updateOrderTotal() {
  if(_orderMode === 'single' && _currentOrderProduct) {
    const qty = parseInt(document.getElementById('orderQty').value) || 0;
    const total = (parseFloat(_currentOrderProduct.prix||0) * qty).toFixed(3);
    document.getElementById('orderTotal').textContent = total;
  } else if(_orderMode === 'cart') {
    const products = getProducts();
    const total = _cartItems.reduce((sum,item) => {
      const p = products.find(prod => prod.id == item.productId);
      return sum + (p ? item.quantity * parseFloat(p.prix||0) : 0);
    }, 0);
    document.getElementById('orderTotal').textContent = total.toFixed(3);
  }
}

document.getElementById('orderQty')?.addEventListener('input', updateOrderTotal);

async function submitOrder() {
  const payment = document.getElementById('orderPayment').value;
  const address = document.getElementById('orderAddress').value.trim();
  const orderAlert = document.getElementById('orderAlert');
  let ok = true;
  
  if(_orderMode === 'single') {
    const qty = parseInt(document.getElementById('orderQty').value);
    if(!qty || qty < 1) { document.getElementById('errQty').classList.add('show'); ok = false; }
    else { document.getElementById('errQty').classList.remove('show'); }
  }
  if(!address) { document.getElementById('errAddress').classList.add('show'); ok = false; }
  else { document.getElementById('errAddress').classList.remove('show'); }
  if(!payment) { document.getElementById('errPayment').classList.add('show'); ok = false; }
  else { document.getElementById('errPayment').classList.remove('show'); }
  if(!ok) return;

  const clientId = getClientId();
  const orders = getOrders();
  try {
    if(_orderMode === 'single') {
      const qty = parseInt(document.getElementById('orderQty').value);
      const order = {
        id: Date.now(),
        clientId: clientId,
        productId: _currentOrderProduct.id,
        productRef: _currentOrderProduct.reference || '',
        productNom: _currentOrderProduct.nom,
        productPrix: parseFloat(_currentOrderProduct.prix||0),
        qty: qty,
        total: parseFloat(_currentOrderProduct.prix||0) * qty,
        payment: payment,
        status: 'En attente',
        adresse: address,
        date: new Date().toLocaleString('fr-FR')
      };
      await apiRequest('commandes','POST', order);
      orders.push(order);
      saveOrders(orders);
      orderAlert.className = 'form-alert show success';
      document.getElementById('orderAlertMsg').textContent = '✓ Commande créée avec succès !';
    } else {
      for(const item of _cartItems) {
        const p = getProducts().find(prod => prod.id == item.productId);
        if(!p) continue;
        const order = {
          id: Date.now() + Math.random(),
          clientId: clientId,
          productId: p.id,
          productRef: p.reference || '',
          productNom: p.nom,
          productPrix: parseFloat(p.prix||0),
          qty: item.quantity,
          total: parseFloat(p.prix||0) * item.quantity,
          payment: payment,
          status: 'En attente',
          adresse: address,
          date: new Date().toLocaleString('fr-FR')
        };
        await apiRequest('commandes','POST', order);
        orders.push(order);
      }
      saveOrders(orders);
      saveCart([]);
      updateCartBadge();
      orderAlert.className = 'form-alert show success';
      document.getElementById('orderAlertMsg').textContent = '✓ Panier commandé avec succès !';
    }
  } catch(error) {
    orderAlert.className = 'form-alert show err';
    document.getElementById('orderAlertMsg').textContent = error.message;
    return;
  }

  setTimeout(() => {
    closeOrderModal();
    orderAlert.className = 'form-alert';
  }, 2000);
}
</script>
</body>
</html>