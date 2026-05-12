<style>
/* ── UNIFIED SIDEBAR CSS ── */
.sidebar{
  width:280px;
  background:linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
  display:flex;flex-direction:column;
  position:fixed;top:0;left:0;bottom:0;
  z-index:100;overflow-y:auto;
  border-right:1px solid rgba(255,255,255,0.05);
  font-family: 'Plus Jakarta Sans', 'DM Sans', sans-serif;
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
.nav-item.active{background:linear-gradient(90deg, rgba(37,99,235,.2) 0%, rgba(37,99,235,.0) 100%);color:#93c5fd;border-left:3px solid #2563eb;border-radius:0 12px 12px 0;}
.nav-item .nav-icon{width:20px;height:20px;opacity:.7;flex-shrink:0;transition:all .2s ease;}
.nav-item:hover .nav-icon{opacity:1;color:#fff;}
.nav-item.active .nav-icon{opacity:1;color:#3b82f6;}

.sidebar-footer{padding:20px 24px;border-top:1px solid rgba(255,255,255,.05);background:rgba(0,0,0,0.1);}
.sidebar-user{display:flex;align-items:center;gap:12px}
.user-avatar{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#8b5cf6);color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;box-shadow:0 4px 10px rgba(0,0,0,0.2);}
.user-info{flex:1;min-width:0}
.user-name{font-size:14px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:2px;}
.user-role{font-size:12px;color:rgba(255,255,255,.5);font-weight:500;}
.btn-logout{background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;padding:6px;border-radius:8px;transition:all .2s ease;flex-shrink:0;font-size:16px;}
.btn-logout:hover{color:#f87171;background:rgba(248,113,113,0.1);transform:scale(1.1);}

/* Remove main margin on smaller screens */
@media(max-width:768px){
  .sidebar{transform:translateX(-100%);transition:transform .3s ease;}
}
</style>

<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-text">MediLink</div>
    <div class="sidebar-admin-tag">Backoffice</div>
  </div>

  <nav class="sidebar-nav">
    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <?php $current_module = $_GET['module'] ?? ''; ?>

    <div class="nav-section-label">Médicaments</div>
    <a href="/medilink_medicament/MediLink/public/back/index.php" class="nav-item <?php echo ($current_module == '' && $current_page == 'index.php') ? 'active' : ''; ?>">
      <span class="nav-icon" style="font-size:16px;">💊</span>
      Gestion Médicaments
    </a>

    <div class="nav-section-label" style="margin-top:12px">Parapharmacie</div>
    <a href="/medilink_medicament/MediLink/public/back/parapharmacie.php" class="nav-item <?php echo ($current_page == 'parapharmacie.php') ? 'active' : ''; ?>">
      <span class="nav-icon" style="font-size:16px;">🧴</span>
      Produits
    </a>
    <a href="/medilink_medicament/MediLink/public/back/commandes.php" class="nav-item <?php echo ($current_page == 'commandes.php' && ($_GET['page']??'') !== 'ratings') ? 'active' : ''; ?>">
      <span class="nav-icon" style="font-size:16px;">🛒</span>
      Commandes
    </a>
    <a href="/medilink_medicament/MediLink/public/back/commandes.php?page=ratings" class="nav-item <?php echo ($current_page == 'commandes.php' && ($_GET['page']??'') === 'ratings') ? 'active' : ''; ?>">
      <span class="nav-icon" style="font-size:16px;">⭐</span>
      Avis Clients
    </a>
    <a href="/medilink_medicament/MediLink/public/back/delivery-map.php" class="nav-item <?php echo ($current_page == 'delivery-map.php') ? 'active' : ''; ?>">
      <span class="nav-icon" style="font-size:16px;">🗺️</span>
      Carte de Livraison
    </a>

    <div class="nav-section-label" style="margin-top:12px">Rendez-vous</div>
    <a href="/medilink_medicament/MediLink/index.php?module=rdv&action=admin" class="nav-item <?php echo ($current_module == 'rdv' && $current_page == 'index.php') ? 'active' : ''; ?>">
      <span class="nav-icon" style="font-size:16px;">📅</span>
      Gestion des RDV
    </a>
    <a href="/medilink_medicament/MediLink/index.php?module=rdv&action=admin" class="nav-item <?php echo ($current_module == 'rdv' && $current_page == 'index.php' && ($_GET['report']??'')=='fiches') ? 'active' : ''; ?>">
      <span class="nav-icon" style="font-size:16px;">📄</span>
      Fiches Patients
    </a>

    <div class="nav-section-label" style="margin-top:12px">Communauté</div>
    <a href="/medilink_medicament/MediLink/index.php?module=forum&controller=forum&action=adminList" class="nav-item <?php echo ($current_module == 'forum') ? 'active' : ''; ?>">
      <span class="nav-icon" style="font-size:16px;">💬</span>
      Forum Santé
    </a>

    <div class="nav-section-label" style="margin-top:12px">Utilisateurs</div>
    <a href="/medilink_medicament/MediLink/admin.php" class="nav-item <?php echo ($current_page == 'admin.php') ? 'active' : ''; ?>">
      <span class="nav-icon" style="font-size:16px;">👥</span>
      Gestion Utilisateurs
    </a>

    <div class="nav-section-label" style="margin-top:12px">Application</div>
    <a href="/medilink_medicament/MediLink/index.php" class="nav-item">
      <span class="nav-icon" style="font-size:16px;">🌍</span>
      Retour au site
    </a>
  </nav>

  <div class="sidebar-footer">
    <?php
      if (session_status() === PHP_SESSION_NONE) session_start();
      $adminNom  = $_SESSION['user_nom'] ?? 'Administrateur';
      $adminRole = ucfirst(strtolower($_SESSION['user_role'] ?? 'administrateur'));
      $initials  = '';
      foreach (explode(' ', $adminNom) as $part) { $initials .= strtoupper(mb_substr(trim($part), 0, 1)); }
      $initials  = mb_substr($initials, 0, 2) ?: 'AD';
    ?>
    <div class="sidebar-user">
      <div class="user-avatar"><?= htmlspecialchars($initials) ?></div>
      <div class="user-info">
        <div class="user-name"><?= htmlspecialchars($adminNom) ?></div>
        <div class="user-role"><?= htmlspecialchars($adminRole) ?></div>
      </div>
      <a href="/medilink_medicament/MediLink/index.php?action=logout" class="btn-logout" title="Déconnexion" style="display:flex;align-items:center;justify-content:center;text-decoration:none;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      </a>
    </div>
  </div>
</aside>
