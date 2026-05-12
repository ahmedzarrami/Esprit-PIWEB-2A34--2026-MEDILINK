<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$_rdvAdminRole = strtolower($_SESSION['user_role'] ?? '');
if (empty($_SESSION['user_id']) || $_rdvAdminRole !== 'administrateur') {
    header('Location: /medilink_medicament/MediLink/index.php?page=login&redirect=back');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediLink — Backoffice</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,700;1,500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --navy:#0f172a;
  --navy2:#1e293b;
  --navy3:#334155;
  --blue:#2563eb;
  --blue-l:#3b82f6;
  --blue-ll:#eff6ff;
  --teal:#0d9488;
  --teal-l:#ccfbf1;
  --amber:#f59e0b;
  --amber-l:#fef3c7;
  --red:#ef4444;
  --red-l:#fef2f2;
  --green:#10b981;
  --green-l:#ecfdf5;
  --purple:#8b5cf6;
  --purple-l:#f5f3ff;
  --gray-50:#f8fafc;
  --gray-100:#f1f5f9;
  --gray-200:#e2e8f0;
  --gray-400:#94a3b8;
  --gray-600:#475569;
  --gray-900:#0f172a;
  --white:#ffffff;
  --sidebar:280px;
  --radius:12px;
  --radius-lg:20px;
}

body{font-family:'Plus Jakarta Sans','DM Sans',sans-serif;background:var(--gray-50);color:var(--gray-900);font-size:14px;display:flex;min-height:100vh}

/* ── SIDEBAR ── */
.sidebar{
  width:var(--sidebar);
  background:linear-gradient(180deg, var(--navy) 0%, var(--navy2) 100%);
  display:flex;flex-direction:column;
  position:fixed;top:0;left:0;bottom:0;
  z-index:100;overflow-y:auto;
  border-right:1px solid rgba(255,255,255,0.05);
}
.sidebar-logo{
  padding:32px 24px 24px;
  border-bottom:1px solid rgba(255,255,255,.05);
}
.sidebar-logo-text{
  font-size:24px;font-weight:800;letter-spacing:-0.5px;
  background:linear-gradient(135deg,#60a5fa 0%,#a78bfa 100%);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  background-clip:text;
}
.sidebar-logo img{height:38px;width:auto}
.sidebar-admin-tag{
  display:inline-block;margin-top:8px;
  background:rgba(37,99,235,.15);
  border:1px solid rgba(37,99,235,.3);
  border-radius:6px;padding:4px 12px;
  font-size:11px;font-weight:600;color:#93c5fd;letter-spacing:.05em;
  text-transform:uppercase;
}
.sidebar-nav{padding:24px 0;flex:1}
.nav-section-label{
  padding:12px 24px 8px;
  font-size:11px;font-weight:700;color:rgba(255,255,255,.4);
  text-transform:uppercase;letter-spacing:.08em;
}
.nav-item{
  display:flex;align-items:center;gap:12px;
  padding:12px 24px;margin:4px 12px;border-radius:12px;
  color:rgba(255,255,255,.6);font-size:14px;font-weight:600;
  cursor:pointer;transition:all .2s ease;text-decoration:none;
  border:none;background:transparent;width:calc(100% - 24px);text-align:left;
}
.nav-item:hover{background:rgba(255,255,255,.08);color:#fff;transform:translateX(4px);}
.nav-item.active{background:linear-gradient(90deg, rgba(37,99,235,.2) 0%, rgba(37,99,235,.0) 100%);color:#93c5fd;border-left:3px solid var(--blue);border-radius:0 12px 12px 0;}
.nav-item .nav-icon{width:20px;height:20px;opacity:.7;flex-shrink:0;transition:all .2s ease;}
.nav-item:hover .nav-icon{opacity:1;color:#fff;}
.nav-item.active .nav-icon{opacity:1;color:var(--blue-l);}
.nav-badge{margin-left:auto;background:var(--blue);color:#fff;border-radius:100px;padding:4px 10px;font-size:11px;font-weight:700;box-shadow:0 2px 5px rgba(37,99,235,0.3);}

.sidebar-footer{padding:20px 24px;border-top:1px solid rgba(255,255,255,.05);background:rgba(0,0,0,0.1);}
.sidebar-user{display:flex;align-items:center;gap:12px}
.user-avatar{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--purple));color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;box-shadow:0 4px 10px rgba(0,0,0,0.2);}
.user-info{flex:1;min-width:0}
.user-name{font-size:14px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:2px;}
.user-role{font-size:12px;color:rgba(255,255,255,.5);font-weight:500;}
.btn-logout{background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;padding:6px;border-radius:8px;transition:all .2s ease;flex-shrink:0;font-size:16px;}
.btn-logout:hover{color:#f87171;background:rgba(248,113,113,0.1);transform:scale(1.1);}

/* ── MAIN CONTENT ── */
.main{margin-left:var(--sidebar);flex:1;display:flex;flex-direction:column;min-height:100vh;background:var(--gray-50);}

/* ── TOPBAR ── */
.topbar{
  background:rgba(255,255,255,0.9);backdrop-filter:blur(10px);border-bottom:1px solid rgba(0,0,0,0.05);
  padding:0 40px;height:72px;
  display:flex;align-items:center;justify-content:space-between;
  position:sticky;top:0;z-index:50;
  box-shadow:0 4px 20px rgba(0,0,0,0.02);
}
.topbar-title{font-size:18px;font-weight:800;color:var(--gray-900);letter-spacing:-0.3px;}
.topbar-subtitle{font-size:13px;color:var(--gray-500);margin-top:2px;font-weight:500;}
.topbar-right{display:flex;align-items:center;gap:16px}
.btn-refresh{
  display:flex;align-items:center;gap:8px;
  padding:10px 18px;border-radius:10px;
  background:var(--blue-ll);color:var(--blue);border:none;
  font-size:13px;font-weight:700;cursor:pointer;
  font-family:inherit;transition:all .2s ease;
}
.btn-refresh:hover{background:var(--blue);color:#fff;box-shadow:0 4px 12px rgba(37,99,235,0.2);transform:translateY(-1px);}
.btn-today{
  padding:10px 18px;border-radius:10px;
  background:#fff;color:var(--gray-700);border:1px solid var(--gray-200);
  font-size:13px;font-weight:600;cursor:pointer;
  font-family:inherit;transition:all .2s ease;
  box-shadow:0 2px 5px rgba(0,0,0,0.02);
}
.btn-today:hover{background:var(--gray-50);border-color:var(--gray-300);transform:translateY(-1px);box-shadow:0 4px 10px rgba(0,0,0,0.04);}

/* ── PAGE SECTIONS ── */
.page-section{display:none;padding:32px 40px;animation:fadeIn .3s ease;}
.page-section.active{display:block}
@keyframes fadeIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

/* ── STAT CARDS ── */
.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:32px}
.stat-card{
  background:#fff;border:1px solid rgba(0,0,0,0.05);border-radius:var(--radius-lg);
  padding:24px;display:flex;align-items:flex-start;gap:16px;
  transition:all .3s cubic-bezier(0.4, 0, 0.2, 1);position:relative;overflow:hidden;
  box-shadow:0 4px 15px rgba(0,0,0,0.02);
}
.stat-card:hover{transform:translateY(-4px);box-shadow:0 12px 30px rgba(0,0,0,0.06);}
.stat-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px}
.stat-card.blue::before{background:var(--blue)}
.stat-card.teal::before{background:var(--teal)}
.stat-card.amber::before{background:var(--amber)}
.stat-card.purple::before{background:var(--purple)}
.stat-card.green::before{background:var(--green)}
.stat-card.coral::before{background:#ea580c}

.stat-icon.green{background:var(--green-l);color:var(--green)}
.stat-icon.coral{background:#fff1ee;color:#ea580c}

.doc-bar-wrap{display:flex;flex-direction:column;gap:6px}
.doc-bar-label{display:flex;justify-content:space-between;font-size:13px;color:var(--gray-600);font-weight:500;}
.doc-bar-label strong{color:var(--gray-900);font-weight:700}
.doc-bar-track{height:8px;background:var(--gray-100);border-radius:100px;overflow:hidden;box-shadow:inset 0 1px 2px rgba(0,0,0,0.05);}
.doc-bar-fill{height:100%;border-radius:100px;transition:width 1s cubic-bezier(0.4, 0, 0.2, 1)}

.stat-icon{
  width:48px;height:48px;border-radius:14px;
  display:flex;align-items:center;justify-content:center;
  font-size:20px;flex-shrink:0;box-shadow:0 4px 10px rgba(0,0,0,0.05);
}
.stat-icon.blue{background:var(--blue-ll);color:var(--blue)}
.stat-icon.teal{background:var(--teal-l);color:var(--teal)}
.stat-icon.amber{background:var(--amber-l);color:var(--amber)}
.stat-icon.purple{background:var(--purple-l);color:var(--purple)}
.stat-body{flex:1}
.stat-label{font-size:13px;color:var(--gray-500);margin-bottom:6px;font-weight:600;letter-spacing:0.02em;text-transform:uppercase;}
.stat-value{font-size:32px;font-weight:800;color:var(--gray-900);line-height:1;letter-spacing:-1px;}
.stat-sub{font-size:12px;color:var(--gray-500);margin-top:8px;font-weight:500;}

/* ── SECTION TITLE ── */
.section-hd{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px}
.section-hd h2{font-size:18px;font-weight:800;color:var(--gray-900);display:flex;align-items:center;gap:12px;letter-spacing:-0.5px;}
.section-hd h2::before{content:'';display:inline-block;width:4px;height:20px;background:linear-gradient(180deg,var(--blue),var(--purple));border-radius:4px}

/* ── AGENDA HEBDOMADAIRE ── */
.agenda-nav{display:flex;align-items:center;gap:16px;margin-bottom:24px}
.agenda-period{font-size:16px;font-weight:700;color:var(--gray-900)}
.agenda-period span{font-family:'Playfair Display',serif;font-style:italic;color:var(--blue);font-weight:600;}
.btn-nav{
  width:36px;height:36px;border-radius:10px;border:1px solid var(--gray-200);
  background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;
  font-size:14px;color:var(--gray-600);transition:all .2s ease;
  box-shadow:0 2px 5px rgba(0,0,0,0.02);
}
.btn-nav:hover{background:var(--blue);color:#fff;border-color:var(--blue);transform:translateY(-1px);box-shadow:0 4px 10px rgba(37,99,235,0.2);}

.agenda-grid{display:grid;grid-template-columns:70px repeat(6,1fr);border:1px solid rgba(0,0,0,0.05);border-radius:var(--radius-lg);overflow:hidden;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,0.02);}

/* Entête jours */
.agenda-header{display:contents}
.agenda-corner{background:var(--gray-50);border-right:1px solid rgba(0,0,0,0.05);border-bottom:1px solid rgba(0,0,0,0.05);padding:16px 12px}
.agenda-day-head{
  background:var(--gray-50);border-bottom:1px solid rgba(0,0,0,0.05);
  border-right:1px solid rgba(0,0,0,0.05);padding:14px 12px;text-align:center;
}
.agenda-day-head:last-child{border-right:none}
.day-name{font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em}
.day-num{font-size:24px;font-weight:800;color:var(--gray-900);line-height:1.2;letter-spacing:-0.5px;}
.day-head-today .day-name{color:var(--blue)}
.day-head-today .day-num{
  background:linear-gradient(135deg,var(--blue),var(--purple));color:#fff;
  width:38px;height:38px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  margin:6px auto 0;font-size:18px;box-shadow:0 4px 10px rgba(37,99,235,0.3);
}

/* Lignes horaires */
.agenda-body{display:contents}
.agenda-time-cell{
  background:var(--gray-50);border-right:1px solid rgba(0,0,0,0.05);
  border-bottom:1px solid rgba(0,0,0,0.05);
  padding:12px 10px;text-align:right;
  font-size:12px;color:var(--gray-500);font-weight:600;white-space:nowrap;
}
.agenda-slot{
  border-right:1px solid rgba(0,0,0,0.05);border-bottom:1px solid rgba(0,0,0,0.05);
  min-height:60px;padding:6px;position:relative;transition:background .2s ease;
}
.agenda-slot:last-child{border-right:none}
.agenda-slot.pause{background:repeating-linear-gradient(45deg,#f8fafc,#f8fafc 4px,#f1f5f9 4px,#f1f5f9 8px)}
.agenda-slot.today-col{background:rgba(37,99,235,.02)}

.rdv-chip{
  background:var(--blue-ll);border:1px solid rgba(37,99,235,.1);
  border-left:3px solid var(--blue);border-radius:8px;
  padding:6px 8px;margin-bottom:4px;cursor:pointer;transition:all .2s ease;
  box-shadow:0 2px 5px rgba(37,99,235,0.05);
}
.rdv-chip:hover{background:#bfdbfe;transform:translateY(-1px);box-shadow:0 4px 8px rgba(37,99,235,0.15);}
.rdv-chip.teal{background:var(--teal-l);border-color:rgba(13,148,136,.1);border-left-color:var(--teal)}
.rdv-chip.teal:hover{background:#99f6e4}
.rdv-chip.coral{background:#fff7ed;border-color:rgba(234,88,12,.1);border-left-color:#ea580c}
.rdv-chip.coral:hover{background:#fed7aa}
.rdv-chip-time{font-size:11px;color:var(--gray-600);font-weight:600}
.rdv-chip-doc{font-size:12px;font-weight:700;color:var(--gray-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;}

.agenda-pause-label{
  font-size:11px;color:var(--gray-400);text-align:center;
  padding:6px 4px;font-style:italic;font-weight:500;
}

/* ── LISTE TABLEAU ── */
.table-card{background:#fff;border:1px solid rgba(0,0,0,0.05);border-radius:var(--radius-lg);overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.02);}
.table-toolbar{
  padding:20px 24px;display:flex;align-items:center;gap:16px;
  border-bottom:1px solid rgba(0,0,0,0.05);background:var(--gray-50);
}
.search-box{
  display:flex;align-items:center;gap:10px;
  background:#fff;border:1px solid var(--gray-200);
  border-radius:10px;padding:10px 14px;flex:1;max-width:360px;
  transition:all .2s ease;box-shadow:inset 0 2px 4px rgba(0,0,0,0.02);
}
.search-box:focus-within{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,0.1);}
.search-box input{border:none;background:none;font-family:inherit;font-size:14px;color:var(--gray-900);outline:none;width:100%}
.filter-select{
  padding:10px 14px;border:1px solid var(--gray-200);border-radius:10px;
  font-size:14px;font-family:inherit;font-weight:600;color:var(--gray-700);
  background:#fff;outline:none;cursor:pointer;transition:all .2s ease;
}
.filter-select:hover{border-color:var(--gray-300);}
.filter-select:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,0.1);}
.table-count{margin-left:auto;font-size:13px;color:var(--gray-500);font-weight:600;}

table{width:100%;border-collapse:separate;border-spacing:0}
thead th{
  padding:16px;text-align:left;
  font-size:12px;font-weight:700;color:var(--gray-500);
  text-transform:uppercase;letter-spacing:.05em;
  background:var(--gray-50);border-bottom:1px solid rgba(0,0,0,0.05);
}
thead th:first-child{border-top-left-radius:10px;}
thead th:last-child{border-top-right-radius:10px;}
tbody tr{transition:background .2s ease}
tbody tr td{border-bottom:1px solid rgba(0,0,0,0.05);}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:var(--gray-50)}
tbody td{padding:16px;font-size:14px;color:var(--gray-600)}
tbody td:first-child{color:var(--gray-900);font-weight:600}
.badge{display:inline-flex;align-items:center;gap:6px;border-radius:100px;padding:4px 12px;font-size:12px;font-weight:600;box-shadow:0 2px 5px rgba(0,0,0,0.02);}
.badge-blue{background:var(--blue-ll);color:var(--blue);border:1px solid rgba(37,99,235,0.1);}
.badge-teal{background:var(--teal-l);color:var(--teal);border:1px solid rgba(13,148,136,0.1);}
.badge-amber{background:var(--amber-l);color:#b45309;border:1px solid rgba(245,158,11,0.1);}
.badge-dot{width:6px;height:6px;border-radius:50%}
.badge-dot.blue{background:var(--blue)}
.badge-dot.teal{background:var(--teal)}
.badge-dot.amber{background:var(--amber)}
.btn-del-row{
  padding:6px 12px;border-radius:8px;border:1px solid rgba(239,68,68,0.2);
  background:var(--red-l);color:var(--red);font-size:12px;cursor:pointer;font-weight:600;
  font-family:inherit;transition:all .2s ease;
}
.btn-del-row:hover{background:var(--red);color:#fff;transform:translateY(-1px);box-shadow:0 4px 10px rgba(239,68,68,0.2);}

/* ── CHARTS SECTION ── */
.charts-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:32px}
.chart-card{background:#fff;border:1px solid rgba(0,0,0,0.05);border-radius:var(--radius-lg);padding:28px;box-shadow:0 4px 20px rgba(0,0,0,0.02);}
.chart-title{font-size:16px;font-weight:700;color:var(--gray-900);margin-bottom:24px;letter-spacing:-0.3px;}

/* Bar chart */
.bar-chart{display:flex;align-items:flex-end;gap:12px;height:160px}
.bar-group{flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;height:100%}
.bar-wrap{flex:1;display:flex;align-items:flex-end;width:100%}
.bar{
  width:100%;border-radius:8px 8px 0 0;
  transition:height 1s cubic-bezier(0.4, 0, 0.2, 1);min-height:4px;
  position:relative;cursor:default;box-shadow:0 4px 10px rgba(0,0,0,0.05);
}
.bar.blue{background:linear-gradient(180deg, var(--blue) 0%, var(--blue-l) 100%);}
.bar.teal{background:linear-gradient(180deg, var(--teal) 0%, #14b8a6 100%);}
.bar.amber{background:linear-gradient(180deg, var(--amber) 0%, #fbbf24 100%);}
.bar-label{font-size:11px;color:var(--gray-500);font-weight:600;text-align:center;text-transform:uppercase;letter-spacing:0.05em;}
.bar-val{font-size:12px;color:var(--gray-700);font-weight:700;text-align:center}

/* Donut chart */
.donut-wrap{display:flex;align-items:center;gap:24px}
.donut-svg{flex-shrink:0;filter:drop-shadow(0 4px 10px rgba(0,0,0,0.05));}
.donut-legend{display:flex;flex-direction:column;gap:12px}
.legend-item{display:flex;align-items:center;gap:10px;font-size:13px;color:var(--gray-600);font-weight:500;}
.legend-dot{width:12px;height:12px;border-radius:50%;flex-shrink:0;box-shadow:inset 0 1px 2px rgba(0,0,0,0.1);}

/* Timeline */
.timeline{display:flex;flex-direction:column;gap:0}
.tl-item{display:flex;gap:16px;padding:12px 0;border-bottom:1px solid rgba(0,0,0,0.05);position:relative}
.tl-item:last-child{border-bottom:none}
.tl-time{font-size:12px;color:var(--gray-500);width:40px;flex-shrink:0;padding-top:2px;font-weight:600}
.tl-dot{width:10px;height:10px;border-radius:50%;background:var(--blue);flex-shrink:0;margin-top:6px;box-shadow:0 0 0 4px var(--blue-ll);}
.tl-dot.teal{background:var(--teal);box-shadow:0 0 0 4px var(--teal-l);}
.tl-dot.amber{background:var(--amber);box-shadow:0 0 0 4px var(--amber-l);}
.tl-body{flex:1}
.tl-doc{font-size:14px;font-weight:600;color:var(--gray-900)}
.tl-date{font-size:12px;color:var(--gray-500);margin-top:2px;}

/* Empty states */
.empty-agenda{text-align:center;padding:40px;color:var(--gray-500);font-size:14px;grid-column:1/-1;background:rgba(248,250,252,0.5);font-weight:500;}
.no-rdv{text-align:center;padding:60px 20px;color:var(--gray-500)}
.no-rdv-icon{font-size:40px;margin-bottom:16px;opacity:.3;filter:grayscale(1);}
.no-rdv strong{display:block;font-size:16px;color:var(--gray-700);margin-bottom:6px;font-weight:700;}

/* ── RESPONSIVE ── */
@media(max-width:1024px){
  .stats-grid{grid-template-columns:repeat(2,1fr)}
  .charts-grid{grid-template-columns:1fr}
}
@media(max-width:768px){
  .sidebar{transform:translateX(-100%);transition:transform .3s ease;}
  .main{margin-left:0;}
  .stats-grid{grid-template-columns:1fr}
  .agenda-grid{grid-template-columns:60px repeat(6,1fr)}
  .page-section{padding:20px;}
}
</style>
</head>
<body>

<?php include dirname(dirname(dirname(__DIR__))) . '/views/back/layouts/sidebar.php'; ?>

<!-- MAIN -->
<div class="main">

  <!-- TOPBAR -->
  <div class="topbar" style="height:auto; padding: 16px 40px; flex-direction:column; align-items:flex-start; gap: 16px;">
    <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
      <div>
        <div class="topbar-title" id="topbarTitle">Gestion des RDV</div>
        <div class="topbar-subtitle" id="topbarSub">Gérez les rendez-vous de la clinique</div>
      </div>
      <div class="topbar-right">
        <button class="btn-today" onclick="goToday()">Aujourd'hui</button>
        <button class="btn-refresh" onclick="refreshAll()">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
          Actualiser
        </button>
      </div>
    </div>
    
    <!-- SUB TABS -->
    <div style="display:flex; gap: 8px; border-bottom: 1px solid var(--gray-200); width: 100%;">
      <button class="nav-item active" style="padding: 10px 16px; margin: 0; border-radius: 8px 8px 0 0; border-bottom: 3px solid transparent;" onclick="showSection('dashboard', this)">
        Vue d'ensemble
      </button>
      <button class="nav-item" style="padding: 10px 16px; margin: 0; border-radius: 8px 8px 0 0; border-bottom: 3px solid transparent;" onclick="showSection('agenda', this)">
        Agenda <span class="nav-badge" id="badgeAgenda" style="margin-left:8px;">0</span>
      </button>
      <button class="nav-item" style="padding: 10px 16px; margin: 0; border-radius: 8px 8px 0 0; border-bottom: 3px solid transparent;" onclick="showSection('liste', this)">
        Liste des RDV
      </button>
      <button class="nav-item" style="padding: 10px 16px; margin: 0; border-radius: 8px 8px 0 0; border-bottom: 3px solid transparent;" onclick="showSection('stats', this)">
        Statistiques
      </button>
    </div>
  </div>
  
  <script>
    // Override the showSection function slightly to handle the new tab clicks
    const originalShowSection = window.showSection;
    window.showSection = function(secId, btn) {
        document.querySelectorAll('.topbar .nav-item').forEach(b => {
            b.classList.remove('active');
            b.style.borderBottomColor = 'transparent';
        });
        if (btn) {
            btn.classList.add('active');
            btn.style.borderBottomColor = 'var(--blue)';
        }
        
        // Hide all sections
        document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
        // Show target
        const target = document.getElementById('sec-' + secId);
        if(target) target.classList.add('active');
    };
    
    // Initialize first tab
    document.addEventListener('DOMContentLoaded', () => {
        const firstTab = document.querySelector('.topbar .nav-item');
        if (firstTab) {
            firstTab.classList.add('active');
            firstTab.style.borderBottomColor = 'var(--blue)';
        }
    });
  </script>

  <!-- ════════════════════════════════
       SECTION : DASHBOARD
  ════════════════════════════════ -->
  <section class="page-section active" id="sec-dashboard">

    <!-- Stat cards -->
    <div class="stats-grid">
      <div class="stat-card blue">
        <div class="stat-icon blue">📅</div>
        <div class="stat-body">
          <div class="stat-label">Total RDV</div>
          <div class="stat-value" id="st-total">—</div>
          <div class="stat-sub">Tous les rendez-vous</div>
        </div>
      </div>
      <div class="stat-card teal">
        <div class="stat-icon teal">📆</div>
        <div class="stat-body">
          <div class="stat-label">Cette semaine</div>
          <div class="stat-value" id="st-week">—</div>
          <div class="stat-sub" id="st-week-sub">Lun — Sam</div>
        </div>
      </div>
      <div class="stat-card amber">
        <div class="stat-icon amber">🕐</div>
        <div class="stat-body">
          <div class="stat-label">Aujourd'hui</div>
          <div class="stat-value" id="st-today">—</div>
          <div class="stat-sub" id="st-today-date">—</div>
        </div>
      </div>
      <div class="stat-card purple">
        <div class="stat-icon purple">👨‍⚕️</div>
        <div class="stat-body">
          <div class="stat-label">Médecin le + sollicité</div>
          <div class="stat-value" style="font-size:15px;margin-top:4px" id="st-top-doc">—</div>
          <div class="stat-sub" id="st-top-doc-sub">—</div>
        </div>
      </div>
      <div class="stat-card green">
        <div class="stat-icon green">✅</div>
        <div class="stat-body">
          <div class="stat-label">Confirmés</div>
          <div class="stat-value" id="st-confirmed">—</div>
          <div class="stat-sub" id="st-confirmed-pct">—</div>
        </div>
      </div>
      <div class="stat-card coral">
        <div class="stat-icon coral">⏭️</div>
        <div class="stat-body">
          <div class="stat-label">Prochain RDV</div>
          <div class="stat-value" style="font-size:14px;margin-top:4px" id="st-next">—</div>
          <div class="stat-sub" id="st-next-sub">—</div>
        </div>
      </div>
    </div>

    <!-- Barres de progression par médecin -->
    <div class="chart-card" style="margin-bottom:20px">
      <div class="chart-title">📊 Répartition des RDV par médecin</div>
      <div id="docBars" style="display:flex;flex-direction:column;gap:12px;margin-top:12px"></div>
    </div>

    <!-- Prochains RDV + Répartition -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

      <div class="chart-card">
        <div class="chart-title">📋 Prochains rendez-vous</div>
        <div class="timeline" id="timelineList"></div>
      </div>

      <div class="chart-card">
        <div class="chart-title">🏥 Répartition par médecin</div>
        <div class="donut-wrap">
          <svg class="donut-svg" width="120" height="120" viewBox="0 0 120 120" id="donutSvg">
            <circle cx="60" cy="60" r="46" fill="none" stroke="#f1f5f9" stroke-width="18"/>
          </svg>
          <div class="donut-legend" id="donutLegend"></div>
        </div>
      </div>

    </div>
  </section>

  <!-- ════════════════════════════════
       SECTION : AGENDA
  ════════════════════════════════ -->
  <section class="page-section" id="sec-agenda">

    <div class="section-hd">
      <h2>Agenda hebdomadaire</h2>
      <div class="agenda-nav">
        <button class="btn-nav" onclick="changeWeek(-1)">&#8592;</button>
        <div class="agenda-period">Semaine du <span id="agendaPeriod">—</span></div>
        <button class="btn-nav" onclick="changeWeek(1)">&#8594;</button>
      </div>
    </div>

    <div class="agenda-grid" id="agendaGrid"></div>

  </section>

  <!-- ════════════════════════════════
       SECTION : LISTE
  ════════════════════════════════ -->
  <section class="page-section" id="sec-liste">

    <div class="table-card">
      <div class="table-toolbar">
        <div class="search-box">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" id="searchInput" placeholder="Rechercher un médecin ou une date…" oninput="renderTable()">
        </div>
        <select class="filter-select" id="filterDoc" onchange="renderTable()">
          <option value="">Tous les médecins</option>
        </select>
        <select class="filter-select" id="filterPeriod" onchange="renderTable()">
          <option value="">Toute période</option>
          <option value="today">Aujourd'hui</option>
          <option value="week">Cette semaine</option>
          <option value="month">Ce mois</option>
        </select>
        <div class="table-count" id="tableCount">0 RDV</div>
      </div>
      <table>
        <thead>
          <tr>
            <th>Médecin</th>
            <th>Spécialité</th>
            <th>Date</th>
            <th>Heure</th>
            <th>Statut</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="tableBody"></tbody>
      </table>
      <div id="tableEmpty" class="no-rdv" style="display:none">
        <div class="no-rdv-icon">🗂️</div>
        <strong>Aucun rendez-vous trouvé</strong>
        Modifiez les filtres ou ajoutez des RDV depuis le site.
      </div>
    </div>

  </section>

  <!-- ════════════════════════════════
       SECTION : STATISTIQUES
  ════════════════════════════════ -->
  <section class="page-section" id="sec-stats">

    <div class="stats-grid" style="margin-bottom:24px">
      <div class="stat-card blue">
        <div class="stat-icon blue">📅</div>
        <div class="stat-body">
          <div class="stat-label">Total RDV</div>
          <div class="stat-value" id="st2-total">0</div>
        </div>
      </div>
      <div class="stat-card teal">
        <div class="stat-icon teal">⏰</div>
        <div class="stat-body">
          <div class="stat-label">Heure de pointe</div>
          <div class="stat-value" style="font-size:18px;margin-top:4px" id="st2-peak">—</div>
        </div>
      </div>
      <div class="stat-card amber">
        <div class="stat-icon amber">📅</div>
        <div class="stat-body">
          <div class="stat-label">Jour le plus chargé</div>
          <div class="stat-value" style="font-size:18px;margin-top:4px" id="st2-day">—</div>
        </div>
      </div>
      <div class="stat-card purple">
        <div class="stat-icon purple">📈</div>
        <div class="stat-body">
          <div class="stat-label">Moy. / semaine</div>
          <div class="stat-value" id="st2-avg">0</div>
        </div>
      </div>
    </div>

    <div class="charts-grid">
      <div class="chart-card">
        <div class="chart-title">RDV par médecin</div>
        <div class="bar-chart" id="barByDoc"></div>
      </div>
      <div class="chart-card">
        <div class="chart-title">RDV par jour de la semaine</div>
        <div class="bar-chart" id="barByDay"></div>
      </div>
      <div class="chart-card">
        <div class="chart-title">RDV par tranche horaire</div>
        <div class="bar-chart" id="barByHour"></div>
      </div>
      <div class="chart-card">
        <div class="chart-title">Répartition par médecin</div>
        <div class="donut-wrap">
          <svg class="donut-svg" width="120" height="120" viewBox="0 0 120 120" id="donutSvg2">
            <circle cx="60" cy="60" r="46" fill="none" stroke="#f1f5f9" stroke-width="18"/>
          </svg>
          <div class="donut-legend" id="donutLegend2"></div>
        </div>
      </div>
    </div>

  </section>

</div><!-- end .main -->

<script>
/* ══════════════════════════════════════════
   CONFIG
══════════════════════════════════════════ */
const DOC_COLORS  = { default:'blue', 0:'blue', 1:'teal', 2:'coral' };
const DOC_SPECS   = {
  'Dr. Ahmed':'Cardiologue',
  'Dr. Sara':'Dermatologue',
  'Dr. Youssef':'Dentiste'
};
const JOURS_FR    = ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'];
const JOURS_LONG  = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
const MOIS_FR     = ['jan','fév','mar','avr','mai','juin','juil','août','sep','oct','nov','déc'];

// Tranches horaires agenda (08:00-12:30 + 14:00-18:00)
const SLOTS_MATIN = ['08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00'];
const SLOT_PAUSE  = 'PAUSE';
const SLOTS_APM   = ['14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30'];
const ALL_SLOTS   = [...SLOTS_MATIN, SLOT_PAUSE, ...SLOTS_APM];

/* ══════════════════════════════════════════
   STATE
══════════════════════════════════════════ */
let currentSection = 'dashboard';
let agendaOffset   = 0; // semaines depuis aujourd'hui
let cachedRDVs     = []; // Cache global des RDVs

/* ══════════════════════════════════════════
   NAVIGATION
══════════════════════════════════════════ */
function showSection(id) {
  currentSection = id;
  document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
  document.getElementById('sec-' + id).classList.add('active');
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  event.currentTarget.classList.add('active');

  const titles = {
    dashboard: ['Vue d\'ensemble','Tableau de bord MediLink'],
    agenda:    ['Agenda','Rendez-vous hebdomadaire'],
    liste:     ['Liste des RDV','Tous les rendez-vous'],
    stats:     ['Statistiques','Analyse des rendez-vous'],
  };
  document.getElementById('topbarTitle').textContent = titles[id][0];
  document.getElementById('topbarSub').textContent   = titles[id][1];

  renderSection(id);
}

async function renderSection(id) {
  // Charger et mettre en cache les RDVs
  cachedRDVs = await getRDVs();
  updateBadge();
  
  if (id === 'dashboard') renderDashboard(cachedRDVs);
  if (id === 'agenda')    renderAgenda(cachedRDVs);
  if (id === 'liste')     { populateDocFilter(cachedRDVs); renderTable(); }
  if (id === 'stats')     renderStats(cachedRDVs);
}

function refreshAll() {
  renderSection(currentSection);
}

function goToday() {
  agendaOffset = 0;
  if (currentSection === 'agenda') renderSection('agenda');
}

/* ══════════════════════════════════════════
   DATA
══════════════════════════════════════════ */
async function getRDVs() {
  try {
    const response = await fetch('api.php?action=list');
    const result = await response.json();
    
    if (result.success && result.data) {
      // Transformer les données de la BD pour correspondre au format attendu
      return result.data.map(rdv => ({
        id: rdv.id,
        medecin: rdv.medecin_nom,
        specialite: rdv.specialite,
        medecin_id: rdv.medecin_id,
        date: rdv.date_rdv,
        heure: rdv.heure_rdv,
        statut: rdv.statut || 'confirmé'
      })).sort((a,b) => (a.date+a.heure).localeCompare(b.date+b.heure));
    }
    return [];
  } catch (error) {
    console.error('Erreur lors du chargement des RDV:', error);
    return [];
  }
}

function deleteRDVAdmin(id) {
  if (!confirm('Supprimer ce rendez-vous ?')) return;
  
  fetch('api.php?action=delete', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: id })
  })
  .then(r => r.json())
  .then(result => {
    if (result.success) {
      alert('✅ Rendez-vous supprimé');
      reloadAdmin();
    } else {
      alert('❌ ' + (result.message || 'Erreur'));
    }
  })
  .catch(err => {
    console.error('Erreur:', err);
    alert('❌ Erreur de suppression');
  });
}

/* ══════════════════════════════════════════
   HELPERS DATE
══════════════════════════════════════════ */
function parseDate(str) {
  const [y,m,d] = str.split('-').map(Number);
  return new Date(y, m-1, d);
}

function toISO(date) {
  const y = date.getFullYear();
  const m = String(date.getMonth()+1).padStart(2,'0');
  const d = String(date.getDate()).padStart(2,'0');
  return `${y}-${m}-${d}`;
}

function formatFR(str) {
  if (!str) return '—';
  const d = parseDate(str);
  return JOURS_FR[d.getDay()] + ' ' + d.getDate() + ' ' + MOIS_FR[d.getMonth()] + '. ' + d.getFullYear();
}

function getWeekStart(offset = 0) {
  const today = new Date();
  const day   = today.getDay(); // 0=dim
  // Aller au lundi
  const diff  = (day === 0) ? -6 : 1 - day;
  const monday = new Date(today);
  monday.setDate(today.getDate() + diff + offset * 7);
  monday.setHours(0,0,0,0);
  return monday;
}

function isToday(dateStr) {
  return dateStr === toISO(new Date());
}

function isThisWeek(dateStr) {
  const ws = getWeekStart(0);
  const we = new Date(ws); we.setDate(ws.getDate() + 5); // sam
  const d  = parseDate(dateStr);
  return d >= ws && d <= we;
}

function docColor(docName) {
  const docs = [...new Set(cachedRDVs.map(r => r.medecin))];
  const idx  = docs.indexOf(docName);
  return ['blue','teal','coral'][idx % 3] || 'blue';
}

/* ══════════════════════════════════════════
   BADGE SIDEBAR
══════════════════════════════════════════ */
function updateBadge() {
  const n = cachedRDVs.length;
  document.getElementById('badgeAgenda').textContent = n;
}

/* ══════════════════════════════════════════
   DASHBOARD
══════════════════════════════════════════ */
function renderDashboard(rdvs) {
  const todayStr  = toISO(new Date());
  const todayDate = new Date();

  // ── Total ──
  animCount('st-total', rdvs.length);

  // ── Cette semaine ──
  const weekRdvs = rdvs.filter(r => isThisWeek(r.date));
  animCount('st-week', weekRdvs.length);
  const pctWeek = rdvs.length ? Math.round(weekRdvs.length / rdvs.length * 100) : 0;
  document.getElementById('st-week-sub').textContent = pctWeek + ' % du total';

  // ── Aujourd'hui ──
  const todayRdvs = rdvs.filter(r => r.date === todayStr);
  animCount('st-today', todayRdvs.length);
  document.getElementById('st-today-date').textContent =
    JOURS_LONG[todayDate.getDay()] + ' ' + todayDate.getDate() + ' ' + MOIS_FR[todayDate.getMonth()];

  // ── Médecin le + sollicité ──
  const docCount = {};
  rdvs.forEach(r => { docCount[r.medecin] = (docCount[r.medecin] || 0) + 1; });
  const topDoc = Object.entries(docCount).sort((a,b) => b[1]-a[1])[0];
  if (topDoc) {
    document.getElementById('st-top-doc').textContent     = topDoc[0];
    document.getElementById('st-top-doc-sub').textContent = topDoc[1] + ' rendez-vous';
  } else {
    document.getElementById('st-top-doc').textContent     = '—';
    document.getElementById('st-top-doc-sub').textContent = 'Aucune donnée';
  }

  // ── Confirmés ──
  const confirmed = rdvs.filter(r => (r.statut || '').toLowerCase() === 'confirmé').length;
  animCount('st-confirmed', confirmed);
  const pctConf = rdvs.length ? Math.round(confirmed / rdvs.length * 100) : 0;
  document.getElementById('st-confirmed-pct').textContent = pctConf + ' % des RDV';

  // ── Prochain RDV ──
  const now = new Date();
  const nowStr = todayStr + ' ' + String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0');
  const upcoming = rdvs
    .filter(r => (r.date + ' ' + r.heure) >= nowStr)
    .sort((a,b) => (a.date+a.heure).localeCompare(b.date+b.heure));
  if (upcoming.length) {
    const next = upcoming[0];
    document.getElementById('st-next').textContent     = next.heure + ' · ' + formatFR(next.date);
    document.getElementById('st-next-sub').textContent = next.medecin;
  } else {
    document.getElementById('st-next').textContent     = 'Aucun';
    document.getElementById('st-next-sub').textContent = 'Pas de RDV à venir';
  }

  // ── Barres de progression par médecin ──
  const COLORS = ['#2563eb','#0d9488','#ea580c','#7c3aed','#d97706'];
  const total  = rdvs.length || 1;
  const bars   = Object.entries(docCount).sort((a,b) => b[1]-a[1]);
  const docBars = document.getElementById('docBars');
  docBars.innerHTML = bars.map(([doc, cnt], i) => {
    const pct = Math.round(cnt / total * 100);
    const col = COLORS[i % COLORS.length];
    return `<div class="doc-bar-wrap">
      <div class="doc-bar-label">
        <span>${doc}</span>
        <strong>${cnt} RDV (${pct} %)</strong>
      </div>
      <div class="doc-bar-track">
        <div class="doc-bar-fill" style="width:0%;background:${col}"
             data-target="${pct}"></div>
      </div>
    </div>`;
  }).join('');
  // Animer les barres après rendu
  setTimeout(() => {
    docBars.querySelectorAll('.doc-bar-fill').forEach(bar => {
      bar.style.width = bar.dataset.target + '%';
    });
  }, 50);

  // ── Timeline prochains RDV ──
  const upcomingList = rdvs
    .filter(r => parseDate(r.date) >= new Date(now.getFullYear(), now.getMonth(), now.getDate()))
    .slice(0, 6);
  const tl = document.getElementById('timelineList');
  if (!upcomingList.length) {
    tl.innerHTML = '<div style="text-align:center;padding:24px;color:var(--gray-400);font-size:12px">Aucun RDV à venir</div>';
  } else {
    tl.innerHTML = upcomingList.map(r => `
      <div class="tl-item">
        <div class="tl-time">${r.heure}</div>
        <div class="tl-dot ${docColor(r.medecin)}"></div>
        <div class="tl-body">
          <div class="tl-doc">${r.medecin}</div>
          <div class="tl-date">${formatFR(r.date)}</div>
        </div>
      </div>`).join('');
  }

  // ── Donut ──
  renderDonut('donutSvg','donutLegend', docCount);
}

// Animation compteur
function animCount(id, target) {
  const el = document.getElementById(id);
  if (!el) return;
  let current = 0;
  const step  = Math.ceil(target / 20);
  const timer = setInterval(() => {
    current += step;
    if (current >= target) { current = target; clearInterval(timer); }
    el.textContent = current;
  }, 30);
}

/* ══════════════════════════════════════════
   DONUT CHART
══════════════════════════════════════════ */
function renderDonut(svgId, legendId, docCount) {
  const svg    = document.getElementById(svgId);
  const legend = document.getElementById(legendId);
  const COLORS = ['#2563eb','#0d9488','#ea580c','#7c3aed','#d97706'];
  const CX = 60, CY = 60, R = 46, SW = 18;
  const CIRC = 2 * Math.PI * R;

  const entries = Object.entries(docCount);
  const total   = entries.reduce((s,[,v]) => s+v, 0);

  if (!total) {
    svg.innerHTML = `<circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="#f1f5f9" stroke-width="${SW}"/>
      <text x="${CX}" y="${CY+5}" text-anchor="middle" fill="#94a3b8" font-size="11" font-family="DM Sans">Aucun RDV</text>`;
    legend.innerHTML = '';
    return;
  }

  let offset = 0;
  let circles = `<circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="#f1f5f9" stroke-width="${SW}"/>`;
  let legendHTML = '';

  entries.forEach(([doc, count], i) => {
    const pct  = count / total;
    const dash = pct * CIRC;
    const gap  = CIRC - dash;
    const col  = COLORS[i % COLORS.length];
    // Rotation: start from top (-90°), add cumulative offset
    const rot  = -90 + (offset / CIRC) * 360;
    circles += `<circle cx="${CX}" cy="${CY}" r="${R}" fill="none" stroke="${col}" stroke-width="${SW}"
      stroke-dasharray="${dash} ${gap}"
      transform="rotate(${rot} ${CX} ${CY})" stroke-linecap="butt"/>`;
    legendHTML += `<div class="legend-item">
      <span class="legend-dot" style="background:${col}"></span>
      <span>${doc} <strong style="color:var(--gray-900)">${count}</strong></span>
    </div>`;
    offset += dash;
  });

  // Total au centre
  circles += `<text x="${CX}" y="${CY-4}" text-anchor="middle" fill="#0f172a" font-size="18" font-weight="600" font-family="DM Sans">${total}</text>
    <text x="${CX}" y="${CY+12}" text-anchor="middle" fill="#94a3b8" font-size="9" font-family="DM Sans">RDV TOTAL</text>`;

  svg.innerHTML = circles;
  legend.innerHTML = legendHTML;
}

/* ══════════════════════════════════════════
   AGENDA HEBDOMADAIRE
══════════════════════════════════════════ */
function changeWeek(dir) { agendaOffset += dir; renderAgenda(cachedRDVs); }

function renderAgenda(rdvs) {
  const ws  = getWeekStart(agendaOffset);
  const days = [];
  for (let i=0;i<6;i++) {
    const d = new Date(ws); d.setDate(ws.getDate()+i);
    days.push(d);
  }

  // Period label
  const we = days[5];
  document.getElementById('agendaPeriod').textContent =
    days[0].getDate() + ' ' + MOIS_FR[days[0].getMonth()] +
    ' – ' + we.getDate() + ' ' + MOIS_FR[we.getMonth()] + ' ' + we.getFullYear();

  const grid = document.getElementById('agendaGrid');
  let html = '';

  // ── HEADER ──
  html += `<div class="agenda-corner"></div>`;
  days.forEach(d => {
    const todayCls = isToday(toISO(d)) ? 'day-head-today' : '';
    html += `<div class="agenda-day-head ${todayCls}">
      <div class="day-name">${JOURS_FR[d.getDay()]}</div>
      <div class="day-num">${d.getDate()}</div>
    </div>`;
  });

  // ── ROWS ──
  ALL_SLOTS.forEach(slot => {
    if (slot === SLOT_PAUSE) {
      // Ligne pause
      html += `<div class="agenda-time-cell" style="color:#f59e0b;font-weight:600">12:30</div>`;
      days.forEach(d => {
        html += `<div class="agenda-slot pause"><div class="agenda-pause-label">🚫 Pause</div></div>`;
      });
      return;
    }

    html += `<div class="agenda-time-cell">${slot}</div>`;
    days.forEach(d => {
      const dateStr = toISO(d);
      const todayCls = isToday(dateStr) ? 'today-col' : '';
      // RDV dans ce créneau (heure == slot ou dans les 30 min)
      const slotMin = toMinutes(slot);
      const slotRdvs = rdvs.filter(r => {
        if (r.date !== dateStr) return false;
        const rm = toMinutes(r.heure);
        return rm >= slotMin && rm < slotMin + 30;
      });

      html += `<div class="agenda-slot ${todayCls}">`;
      slotRdvs.forEach(r => {
        const col = docColor(r.medecin);
        html += `<div class="rdv-chip ${col}" title="${r.medecin} — ${r.heure}">
          <div class="rdv-chip-time">${r.heure}</div>
          <div class="rdv-chip-doc">${r.medecin}</div>
        </div>`;
      });
      html += `</div>`;
    });
  });

  grid.innerHTML = html;
}

function toMinutes(hStr) {
  const [h,m] = hStr.split(':').map(Number);
  return h*60+m;
}

/* ══════════════════════════════════════════
   LISTE / TABLEAU
══════════════════════════════════════════ */
function populateDocFilter(rdvs) {
  const sel  = document.getElementById('filterDoc');
  const docs = [...new Set(rdvs.map(r => r.medecin))];
  const cur  = sel.value;
  sel.innerHTML = '<option value="">Tous les médecins</option>' +
    docs.map(d => `<option value="${d}" ${d===cur?'selected':''}>${d}</option>`).join('');
}

function renderTable() {
  const rdvs   = cachedRDVs;
  const q      = document.getElementById('searchInput').value.toLowerCase();
  const doc    = document.getElementById('filterDoc').value;
  const period = document.getElementById('filterPeriod').value;
  const todayStr = toISO(new Date());

  const filtered = rdvs.filter(r => {
    const matchQ   = !q || r.medecin.toLowerCase().includes(q) || r.date.includes(q) || r.heure.includes(q);
    const matchDoc = !doc || r.medecin === doc;
    let matchP = true;
    if (period === 'today') matchP = r.date === todayStr;
    if (period === 'week')  matchP = isThisWeek(r.date);
    if (period === 'month') {
      const d = parseDate(r.date); const t = new Date();
      matchP = d.getMonth() === t.getMonth() && d.getFullYear() === t.getFullYear();
    }
    return matchQ && matchDoc && matchP;
  });

  document.getElementById('tableCount').textContent = filtered.length + ' RDV';

  const COLORS_DOC = {};
  const docList = [...new Set(rdvs.map(r=>r.medecin))];
  docList.forEach((d,i) => { COLORS_DOC[d] = ['blue','teal','amber'][i%3]; });

  const tbody = document.getElementById('tableBody');
  const empty = document.getElementById('tableEmpty');

  if (!filtered.length) {
    tbody.innerHTML = '';
    empty.style.display = 'block';
    return;
  }
  empty.style.display = 'none';

  tbody.innerHTML = filtered.map(r => {
    const col  = COLORS_DOC[r.medecin] || 'blue';
    const spec = DOC_SPECS[r.medecin] || 'Médecin';
    const isPast = parseDate(r.date) < new Date(new Date().setHours(0,0,0,0));
    const status = isPast
      ? `<span class="badge badge-amber"><span class="badge-dot amber"></span>Passé</span>`
      : `<span class="badge badge-blue"><span class="badge-dot blue"></span>À venir</span>`;
    return `<tr>
      <td>${r.medecin}</td>
      <td><span class="badge badge-${col}"><span class="badge-dot ${col}"></span>${spec}</span></td>
      <td>${formatFR(r.date)}</td>
      <td><strong>${r.heure}</strong></td>
      <td>${status}</td>
      <td><button class="btn-del-row" onclick="deleteRDVAdmin(${r.id})">Supprimer</button></td>
    </tr>`;
  }).join('');
}

/* ══════════════════════════════════════════
   STATISTIQUES
══════════════════════════════════════════ */
function renderStats(rdvs) {
  document.getElementById('st2-total').textContent = rdvs.length;

  // Heure de pointe
  const hCount = {};
  rdvs.forEach(r => {
    const h = r.heure.split(':')[0] + 'h';
    hCount[h] = (hCount[h]||0)+1;
  });
  const peakH = Object.entries(hCount).sort((a,b)=>b[1]-a[1])[0];
  document.getElementById('st2-peak').textContent = peakH ? peakH[0] : '—';

  // Jour le plus chargé
  const dCount = {};
  rdvs.forEach(r => { const d=parseDate(r.date); const j=JOURS_LONG[d.getDay()]; dCount[j]=(dCount[j]||0)+1; });
  const peakD = Object.entries(dCount).sort((a,b)=>b[1]-a[1])[0];
  document.getElementById('st2-day').textContent = peakD ? peakD[0] : '—';

  // Moy / semaine
  if (rdvs.length) {
    const dates = rdvs.map(r=>r.date).sort();
    const first = parseDate(dates[0]);
    const last  = parseDate(dates[dates.length-1]);
    const weeks = Math.max(1, Math.ceil((last-first)/(7*86400000))+1);
    document.getElementById('st2-avg').textContent = (rdvs.length/weeks).toFixed(1);
  } else {
    document.getElementById('st2-avg').textContent = '0';
  }

  // Bar: par médecin
  const docCount = {};
  rdvs.forEach(r => { docCount[r.medecin]=(docCount[r.medecin]||0)+1; });
  renderBarChart('barByDoc', docCount, ['blue','teal','coral']);

  // Bar: par jour
  const dayLabels = ['Lun','Mar','Mer','Jeu','Ven','Sam'];
  const dayCount  = {Lun:0,Mar:0,Mer:0,Jeu:0,Ven:0,Sam:0};
  rdvs.forEach(r => { const j = JOURS_FR[parseDate(r.date).getDay()]; if (dayCount[j]!==undefined) dayCount[j]++; });
  renderBarChart('barByDay', dayCount, ['blue','blue','blue','blue','blue','teal']);

  // Bar: par tranche horaire
  const hourBuckets = {'8h-9h':0,'9h-10h':0,'10h-11h':0,'11h-12h':0,'14h-15h':0,'15h-16h':0,'16h-17h':0,'17h-18h':0};
  rdvs.forEach(r => {
    const m = toMinutes(r.heure);
    if (m>=480&&m<540)  hourBuckets['8h-9h']++;
    else if(m>=540&&m<600) hourBuckets['9h-10h']++;
    else if(m>=600&&m<660) hourBuckets['10h-11h']++;
    else if(m>=660&&m<750) hourBuckets['11h-12h']++;
    else if(m>=840&&m<900) hourBuckets['14h-15h']++;
    else if(m>=900&&m<960) hourBuckets['15h-16h']++;
    else if(m>=960&&m<1020) hourBuckets['16h-17h']++;
    else if(m>=1020&&m<1080) hourBuckets['17h-18h']++;
  });
  renderBarChart('barByHour', hourBuckets, ['blue','blue','blue','blue','teal','teal','teal','teal']);

  // Donut 2
  renderDonut('donutSvg2','donutLegend2', docCount);
}

function renderBarChart(containerId, dataObj, colors) {
  const container = document.getElementById(containerId);
  const entries   = Object.entries(dataObj);
  const maxVal    = Math.max(...entries.map(([,v])=>v), 1);
  const BAR_COLORS = ['#2563eb','#0d9488','#ea580c','#7c3aed','#d97706','#059669'];

  container.innerHTML = entries.map(([label, val], i) => {
    const pct = (val / maxVal) * 100;
    const col = Array.isArray(colors) ? (colors[i]||'blue') : 'blue';
    const hexCol = col==='blue'?'#2563eb':col==='teal'?'#0d9488':col==='coral'?'#ea580c':col==='amber'?'#d97706':'#2563eb';
    return `<div class="bar-group">
      <div class="bar-val">${val||''}</div>
      <div class="bar-wrap">
        <div class="bar" style="height:${pct}%;background:${hexCol};width:100%"></div>
      </div>
      <div class="bar-label">${label}</div>
    </div>`;
  }).join('');
}

/* ══════════════════════════════════════════
   INIT
══════════════════════════════════════════ */
(async function init() {
  await reloadAdmin();
})();

// Fonction pour recharger le tableau de bord
async function reloadAdmin() {
  cachedRDVs = await getRDVs();
  updateBadge();
  renderDashboard(cachedRDVs);
}
</script>

</body>
</html>