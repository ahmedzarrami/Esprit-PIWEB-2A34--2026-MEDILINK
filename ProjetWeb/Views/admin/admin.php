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
  --navy:#0f1b2d;
  --navy2:#162336;
  --navy3:#1e2f45;
  --blue:#2563eb;
  --blue-l:#3b82f6;
  --blue-ll:#dbeafe;
  --teal:#0d9488;
  --teal-l:#ccfbf1;
  --amber:#d97706;
  --amber-l:#fef3c7;
  --red:#dc2626;
  --red-l:#fee2e2;
  --green:#059669;
  --green-l:#d1fae5;
  --purple:#7c3aed;
  --purple-l:#ede9fe;
  --gray-50:#f8fafc;
  --gray-100:#f1f5f9;
  --gray-200:#e2e8f0;
  --gray-400:#94a3b8;
  --gray-600:#475569;
  --gray-900:#0f172a;
  --white:#ffffff;
  --sidebar:260px;
  --radius:10px;
  --radius-lg:16px;
}

body{font-family:'DM Sans',sans-serif;background:var(--gray-50);color:var(--gray-900);font-size:14px;display:flex;min-height:100vh}

/* ── SIDEBAR ── */
.sidebar{
  width:var(--sidebar);
  background:var(--navy);
  display:flex;flex-direction:column;
  position:fixed;top:0;left:0;bottom:0;
  z-index:100;overflow-y:auto;
}
.sidebar-logo{
  padding:24px 20px 20px;
  border-bottom:1px solid rgba(255,255,255,.07);
}
.sidebar-logo img{height:38px;width:auto}
.sidebar-admin-tag{
  display:inline-block;margin-top:8px;
  background:rgba(37,99,235,.3);
  border:1px solid rgba(37,99,235,.4);
  border-radius:6px;padding:2px 10px;
  font-size:11px;font-weight:500;color:#93c5fd;letter-spacing:.04em;
}
.sidebar-nav{padding:16px 0;flex:1}
.nav-section-label{
  padding:8px 20px 4px;
  font-size:10px;font-weight:600;color:rgba(255,255,255,.3);
  text-transform:uppercase;letter-spacing:.08em;
}
.nav-item{
  display:flex;align-items:center;gap:10px;
  padding:10px 20px;margin:2px 10px;border-radius:8px;
  color:rgba(255,255,255,.6);font-size:13px;font-weight:500;
  cursor:pointer;transition:.15s;text-decoration:none;
  border:none;background:none;width:calc(100% - 20px);text-align:left;
}
.nav-item:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.9)}
.nav-item.active{background:rgba(37,99,235,.25);color:#93c5fd}
.nav-item .nav-icon{width:18px;height:18px;opacity:.7;flex-shrink:0}
.nav-item.active .nav-icon{opacity:1}
.nav-badge{margin-left:auto;background:var(--blue);color:#fff;border-radius:100px;padding:2px 8px;font-size:10px;font-weight:600}

.sidebar-footer{padding:16px 20px;border-top:1px solid rgba(255,255,255,.07)}
.sidebar-user{display:flex;align-items:center;gap:10px}
.user-avatar{width:34px;height:34px;border-radius:50%;background:var(--blue);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;flex-shrink:0}
.user-info{flex:1;min-width:0}
.user-name{font-size:13px;font-weight:500;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.user-role{font-size:11px;color:rgba(255,255,255,.4)}
.btn-logout{background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;padding:4px;border-radius:4px;transition:.15s;flex-shrink:0}
.btn-logout:hover{color:#f87171}

/* ── MAIN CONTENT ── */
.main{margin-left:var(--sidebar);flex:1;display:flex;flex-direction:column;min-height:100vh}

/* ── TOPBAR ── */
.topbar{
  background:#fff;border-bottom:1px solid var(--gray-200);
  padding:0 32px;height:60px;
  display:flex;align-items:center;justify-content:space-between;
  position:sticky;top:0;z-index:50;
}
.topbar-title{font-size:16px;font-weight:600;color:var(--gray-900)}
.topbar-subtitle{font-size:12px;color:var(--gray-400);margin-top:1px}
.topbar-right{display:flex;align-items:center;gap:12px}
.btn-refresh{
  display:flex;align-items:center;gap:6px;
  padding:7px 14px;border-radius:8px;
  background:var(--blue);color:#fff;border:none;
  font-size:13px;font-weight:500;cursor:pointer;
  font-family:'DM Sans',sans-serif;transition:.15s;
}
.btn-refresh:hover{background:#1d4ed8}
.btn-today{
  padding:7px 14px;border-radius:8px;
  background:var(--gray-100);color:var(--gray-600);border:1px solid var(--gray-200);
  font-size:13px;font-weight:500;cursor:pointer;
  font-family:'DM Sans',sans-serif;transition:.15s;
}
.btn-today:hover{background:var(--gray-200)}

/* ── PAGE SECTIONS ── */
.page-section{display:none;padding:28px 32px}
.page-section.active{display:block}

/* ── STAT CARDS ── */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px}
.stat-card{
  background:#fff;border:1px solid var(--gray-200);border-radius:var(--radius-lg);
  padding:20px 22px;display:flex;align-items:flex-start;gap:14px;
  transition:.15s;position:relative;overflow:hidden;
}
.stat-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px}
.stat-card.blue::before{background:var(--blue)}
.stat-card.teal::before{background:var(--teal)}
.stat-card.amber::before{background:var(--amber)}
.stat-card.purple::before{background:var(--purple)}
.stat-icon{
  width:42px;height:42px;border-radius:10px;
  display:flex;align-items:center;justify-content:center;
  font-size:18px;flex-shrink:0;
}
.stat-icon.blue{background:var(--blue-ll)}
.stat-icon.teal{background:var(--teal-l)}
.stat-icon.amber{background:var(--amber-l)}
.stat-icon.purple{background:var(--purple-l)}
.stat-body{flex:1}
.stat-label{font-size:12px;color:var(--gray-400);margin-bottom:4px;font-weight:500}
.stat-value{font-size:26px;font-weight:600;color:var(--gray-900);line-height:1}
.stat-sub{font-size:11px;color:var(--gray-400);margin-top:4px}

/* ── SECTION TITLE ── */
.section-hd{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
.section-hd h2{font-size:15px;font-weight:600;color:var(--gray-900);display:flex;align-items:center;gap:8px}
.section-hd h2::before{content:'';display:inline-block;width:3px;height:15px;background:var(--blue);border-radius:2px}

/* ── AGENDA HEBDOMADAIRE ── */
.agenda-nav{display:flex;align-items:center;gap:12px;margin-bottom:20px}
.agenda-period{font-size:15px;font-weight:600;color:var(--gray-900)}
.agenda-period span{font-family:'Playfair Display',serif;font-style:italic;color:var(--blue)}
.btn-nav{
  width:32px;height:32px;border-radius:8px;border:1px solid var(--gray-200);
  background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;
  font-size:14px;color:var(--gray-600);transition:.15s;
}
.btn-nav:hover{background:var(--blue);color:#fff;border-color:var(--blue)}

.agenda-grid{display:grid;grid-template-columns:64px repeat(6,1fr);border:1px solid var(--gray-200);border-radius:var(--radius-lg);overflow:hidden;background:#fff}

/* Entête jours */
.agenda-header{display:contents}
.agenda-corner{background:var(--gray-50);border-right:1px solid var(--gray-200);border-bottom:1px solid var(--gray-200);padding:12px 8px}
.agenda-day-head{
  background:var(--gray-50);border-bottom:1px solid var(--gray-200);
  border-right:1px solid var(--gray-200);padding:10px 8px;text-align:center;
}
.agenda-day-head:last-child{border-right:none}
.day-name{font-size:11px;font-weight:600;color:var(--gray-400);text-transform:uppercase;letter-spacing:.05em}
.day-num{font-size:20px;font-weight:600;color:var(--gray-900);line-height:1.2}
.day-head-today .day-name{color:var(--blue)}
.day-head-today .day-num{
  background:var(--blue);color:#fff;
  width:32px;height:32px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  margin:4px auto 0;font-size:15px;
}

/* Lignes horaires */
.agenda-body{display:contents}
.agenda-time-cell{
  background:var(--gray-50);border-right:1px solid var(--gray-200);
  border-bottom:1px solid var(--gray-200);
  padding:8px 6px;text-align:right;
  font-size:11px;color:var(--gray-400);font-weight:500;white-space:nowrap;
}
.agenda-slot{
  border-right:1px solid var(--gray-200);border-bottom:1px solid var(--gray-200);
  min-height:52px;padding:4px;position:relative;transition:.1s;
}
.agenda-slot:last-child{border-right:none}
.agenda-slot.pause{background:repeating-linear-gradient(45deg,#f8fafc,#f8fafc 4px,#f1f5f9 4px,#f1f5f9 8px)}
.agenda-slot.today-col{background:rgba(37,99,235,.02)}

.rdv-chip{
  background:var(--blue-ll);border:1px solid rgba(37,99,235,.2);
  border-left:3px solid var(--blue);border-radius:6px;
  padding:4px 6px;margin-bottom:3px;cursor:pointer;transition:.15s;
}
.rdv-chip:hover{background:#bfdbfe;transform:translateX(1px)}
.rdv-chip.teal{background:var(--teal-l);border-color:rgba(13,148,136,.2);border-left-color:var(--teal)}
.rdv-chip.teal:hover{background:#99f6e4}
.rdv-chip.coral{background:#fff7ed;border-color:rgba(234,88,12,.2);border-left-color:#ea580c}
.rdv-chip.coral:hover{background:#fed7aa}
.rdv-chip-time{font-size:10px;color:var(--gray-600);font-weight:500}
.rdv-chip-doc{font-size:11px;font-weight:600;color:var(--gray-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

.agenda-pause-label{
  font-size:10px;color:var(--gray-400);text-align:center;
  padding:4px 2px;font-style:italic;
}

/* ── LISTE TABLEAU ── */
.table-card{background:#fff;border:1px solid var(--gray-200);border-radius:var(--radius-lg);overflow:hidden}
.table-toolbar{
  padding:16px 20px;display:flex;align-items:center;gap:12px;
  border-bottom:1px solid var(--gray-200);
}
.search-box{
  display:flex;align-items:center;gap:8px;
  background:var(--gray-50);border:1px solid var(--gray-200);
  border-radius:8px;padding:7px 12px;flex:1;max-width:320px;
}
.search-box input{border:none;background:none;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--gray-900);outline:none;width:100%}
.filter-select{
  padding:7px 12px;border:1px solid var(--gray-200);border-radius:8px;
  font-size:13px;font-family:'DM Sans',sans-serif;color:var(--gray-600);
  background:#fff;outline:none;cursor:pointer;
}
.table-count{margin-left:auto;font-size:12px;color:var(--gray-400)}

table{width:100%;border-collapse:collapse}
thead th{
  padding:10px 16px;text-align:left;
  font-size:11px;font-weight:600;color:var(--gray-400);
  text-transform:uppercase;letter-spacing:.05em;
  background:var(--gray-50);border-bottom:1px solid var(--gray-200);
}
tbody tr{border-bottom:1px solid var(--gray-200);transition:.1s}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:var(--gray-50)}
tbody td{padding:12px 16px;font-size:13px;color:var(--gray-600)}
tbody td:first-child{color:var(--gray-900);font-weight:500}
.badge{display:inline-flex;align-items:center;gap:5px;border-radius:100px;padding:3px 10px;font-size:11px;font-weight:500}
.badge-blue{background:var(--blue-ll);color:#1e40af}
.badge-teal{background:var(--teal-l);color:#0f766e}
.badge-amber{background:var(--amber-l);color:#92400e}
.badge-dot{width:5px;height:5px;border-radius:50%}
.badge-dot.blue{background:var(--blue)}
.badge-dot.teal{background:var(--teal)}
.badge-dot.amber{background:var(--amber)}
.btn-del-row{
  padding:4px 10px;border-radius:6px;border:1px solid var(--red-l);
  background:#fff;color:var(--red);font-size:11px;cursor:pointer;
  font-family:'DM Sans',sans-serif;transition:.15s;
}
.btn-del-row:hover{background:var(--red-l)}

/* ── CHARTS SECTION ── */
.charts-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px}
.chart-card{background:#fff;border:1px solid var(--gray-200);border-radius:var(--radius-lg);padding:22px}
.chart-title{font-size:14px;font-weight:600;color:var(--gray-900);margin-bottom:18px}

/* Bar chart */
.bar-chart{display:flex;align-items:flex-end;gap:8px;height:140px}
.bar-group{flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;height:100%}
.bar-wrap{flex:1;display:flex;align-items:flex-end;width:100%}
.bar{
  width:100%;border-radius:6px 6px 0 0;
  transition:height .4s ease;min-height:3px;
  position:relative;cursor:default;
}
.bar.blue{background:var(--blue)}
.bar.teal{background:var(--teal)}
.bar.amber{background:var(--amber)}
.bar-label{font-size:10px;color:var(--gray-400);font-weight:500;text-align:center}
.bar-val{font-size:11px;color:var(--gray-600);font-weight:600;text-align:center}

/* Donut chart */
.donut-wrap{display:flex;align-items:center;gap:20px}
.donut-svg{flex-shrink:0}
.donut-legend{display:flex;flex-direction:column;gap:10px}
.legend-item{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gray-600)}
.legend-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}

/* Timeline */
.timeline{display:flex;flex-direction:column;gap:0}
.tl-item{display:flex;gap:14px;padding:10px 0;border-bottom:1px solid var(--gray-200);position:relative}
.tl-item:last-child{border-bottom:none}
.tl-time{font-size:11px;color:var(--gray-400);width:36px;flex-shrink:0;padding-top:2px;font-weight:500}
.tl-dot{width:8px;height:8px;border-radius:50%;background:var(--blue);flex-shrink:0;margin-top:4px}
.tl-dot.teal{background:var(--teal)}
.tl-dot.amber{background:var(--amber)}
.tl-body{flex:1}
.tl-doc{font-size:13px;font-weight:500;color:var(--gray-900)}
.tl-date{font-size:11px;color:var(--gray-400)}

/* Empty states */
.empty-agenda{text-align:center;padding:32px;color:var(--gray-400);font-size:12px;grid-column:1/-1}
.no-rdv{text-align:center;padding:48px 20px;color:var(--gray-400)}
.no-rdv-icon{font-size:32px;margin-bottom:10px;opacity:.4}
.no-rdv strong{display:block;font-size:15px;color:var(--gray-600);margin-bottom:4px}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  .stats-grid{grid-template-columns:repeat(2,1fr)}
  .charts-grid{grid-template-columns:1fr}
  .agenda-grid{grid-template-columns:56px repeat(6,1fr)}
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <img src="views/front/logo_medilink.jpg" alt="MediLink">
    <div class="sidebar-admin-tag">Backoffice Admin</div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Tableau de bord</div>
    <button class="nav-item active" onclick="showSection('dashboard')">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      Vue d'ensemble
    </button>
    <button class="nav-item" onclick="showSection('agenda')">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Agenda
      <span class="nav-badge" id="badgeAgenda">0</span>
    </button>
    <button class="nav-item" onclick="showSection('liste')">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="3" cy="6" r="1"/><circle cx="3" cy="12" r="1"/><circle cx="3" cy="18" r="1"/></svg>
      Liste des RDV
    </button>
    <div class="nav-section-label" style="margin-top:12px">Statistiques</div>
    <button class="nav-item" onclick="showSection('stats')">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      Statistiques
    </button>
    <div class="nav-section-label" style="margin-top:12px">Application</div>
    <a class="nav-item" href="/ProjetWeb/index.php?action=home">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Retour au site
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="user-avatar">AD</div>
      <div class="user-info">
        <div class="user-name">Administrateur</div>
        <div class="user-role">Accès complet</div>
      </div>
      <button class="btn-logout" title="Déconnexion">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      </button>
    </div>
  </div>
</aside>

<!-- MAIN -->
<div class="main">

  <!-- TOPBAR -->
  <div class="topbar">
    <div>
      <div class="topbar-title" id="topbarTitle">Vue d'ensemble</div>
      <div class="topbar-subtitle" id="topbarSub">Tableau de bord MediLink</div>
    </div>
    <div class="topbar-right">
      <button class="btn-today" onclick="goToday()">Aujourd'hui</button>
      <button class="btn-refresh" onclick="refreshAll()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
        Actualiser
      </button>
    </div>
  </div>

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
          <div class="stat-value" id="st-total">0</div>
          <div class="stat-sub">Tous les rendez-vous</div>
        </div>
      </div>
      <div class="stat-card teal">
        <div class="stat-icon teal">📆</div>
        <div class="stat-body">
          <div class="stat-label">Cette semaine</div>
          <div class="stat-value" id="st-week">0</div>
          <div class="stat-sub">Lun — Sam</div>
        </div>
      </div>
      <div class="stat-card amber">
        <div class="stat-icon amber">🕐</div>
        <div class="stat-body">
          <div class="stat-label">Aujourd'hui</div>
          <div class="stat-value" id="st-today">0</div>
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
    const response = await fetch('/ProjetWeb/api.php?action=list');
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
  
  fetch('/ProjetWeb/api.php?action=delete', {
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
  // Stat cards
  document.getElementById('st-total').textContent = rdvs.length;

  const weekRdvs  = rdvs.filter(r => isThisWeek(r.date));
  document.getElementById('st-week').textContent = weekRdvs.length;

  const todayStr  = toISO(new Date());
  const todayRdvs = rdvs.filter(r => r.date === todayStr);
  document.getElementById('st-today').textContent    = todayRdvs.length;
  const todayDate = new Date();
  document.getElementById('st-today-date').textContent =
    JOURS_LONG[todayDate.getDay()] + ' ' + todayDate.getDate() + ' ' + MOIS_FR[todayDate.getMonth()];

  // Top médecin
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

  // Timeline prochains RDV
  const now = new Date();
  const upcoming = rdvs
    .filter(r => parseDate(r.date) >= new Date(now.getFullYear(), now.getMonth(), now.getDate()))
    .slice(0,6);

  const tl = document.getElementById('timelineList');
  if (!upcoming.length) {
    tl.innerHTML = '<div style="text-align:center;padding:24px;color:var(--gray-400);font-size:12px">Aucun RDV à venir</div>';
  } else {
    tl.innerHTML = upcoming.map(r => `
      <div class="tl-item">
        <div class="tl-time">${r.heure}</div>
        <div class="tl-dot ${docColor(r.medecin)}"></div>
        <div class="tl-body">
          <div class="tl-doc">${r.medecin}</div>
          <div class="tl-date">${formatFR(r.date)}</div>
        </div>
      </div>`).join('');
  }

  // Donut
  renderDonut('donutSvg','donutLegend', docCount);
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