<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/auth_guard.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getConnection();
$stmt = $db->query("SELECT id, client_id, nom_produit, quantite, total, status, mode_paiement, created_at FROM commandes ORDER BY created_at DESC LIMIT 100");
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!function_exists('e')) {
    function e(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
}
$pageTitle = 'Carte de Livraison';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Carte de Livraison — MediLink</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Plus Jakarta Sans',sans-serif;background:#f8fafc;display:flex;min-height:100vh}
</style>
</head>
<body>
<?php include __DIR__ . '/../../views/back/layouts/sidebar.php'; ?>

<div style="margin-left:280px;flex:1;display:flex;flex-direction:column;min-height:100vh">
  <!-- Topbar -->
  <div style="background:#fff;border-bottom:1px solid #e2e8f0;padding:0 40px;height:64px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50">
    <div>
      <div style="font-size:18px;font-weight:800;color:#0f172a">🗺️ Carte de Livraison</div>
      <div style="font-size:13px;color:#64748b">Visualisez les zones de livraison en temps réel</div>
    </div>
    <a href="commandes.php" style="padding:8px 16px;background:#f1f5f9;color:#334155;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">← Retour aux commandes</a>
  </div>

  <div style="display:grid;grid-template-columns:340px 1fr;flex:1;height:calc(100vh - 64px)">

    <!-- Panel commandes -->
    <div style="background:#fff;border-right:1px solid #e2e8f0;overflow-y:auto;padding:20px">
      <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:16px">📋 Commandes récentes (<?= count($commandes) ?>)</div>

      <?php
      $statusColors = ['En attente'=>'#f59e0b','Confirmée'=>'#10b981','Livrée'=>'#2563eb','Annulée'=>'#ef4444'];
      foreach ($commandes as $c):
        $color = $statusColors[$c['status']] ?? '#94a3b8';
      ?>
      <div style="background:#f8fafc;border:1px solid #e2e8f0;border-left:3px solid <?= $color ?>;border-radius:10px;padding:14px;margin-bottom:10px;cursor:pointer"
           onclick="focusOrder(<?= $c['id'] ?>)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
          <span style="font-size:12px;font-weight:700;color:#94a3b8">#<?= $c['id'] ?></span>
          <span style="font-size:11px;font-weight:700;color:<?= $color ?>;background:<?= $color ?>20;padding:2px 8px;border-radius:100px"><?= e($c['status']) ?></span>
        </div>
        <div style="font-weight:700;color:#0f172a;font-size:13px;margin-bottom:2px"><?= e($c['nom_produit']) ?></div>
        <div style="font-size:12px;color:#64748b">👤 <?= e($c['client_id']) ?> · <?= (int)$c['quantite'] ?> unité(s)</div>
        <div style="font-size:13px;font-weight:700;color:#059669;margin-top:4px"><?= number_format((float)$c['total'],3,',','') ?> DT</div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Map -->
    <div style="position:relative">
      <div id="map" style="width:100%;height:100%"></div>
      <div style="position:absolute;top:16px;right:16px;z-index:1000;background:#fff;border-radius:12px;padding:16px;box-shadow:0 4px 20px rgba(0,0,0,.1);min-width:200px">
        <div style="font-size:12px;font-weight:700;color:#64748b;margin-bottom:10px">LÉGENDE</div>
        <?php foreach ($statusColors as $s => $c): ?>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
          <div style="width:12px;height:12px;border-radius:50%;background:<?= $c ?>"></div>
          <span style="font-size:12px;color:#334155;font-weight:500"><?= $s ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<script>
const map = L.map('map').setView([36.8065, 10.1815], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors', maxZoom: 19
}).addTo(map);

// Zones de Tunis simulées pour la démonstration
const ZONES = [
  {name:'Centre Ville',    lat:36.8188, lng:10.1658},
  {name:'La Marsa',        lat:36.8789, lng:10.3247},
  {name:'Ariana',          lat:36.8662, lng:10.1939},
  {name:'Ben Arous',       lat:36.7531, lng:10.2281},
  {name:'Manouba',         lat:36.8092, lng:10.1011},
  {name:'Carthage',        lat:36.8527, lng:10.3244},
  {name:'La Soukra',       lat:36.8955, lng:10.2131},
  {name:'El Mourouj',      lat:36.7287, lng:10.2131},
];

const STATUS_COLORS = {
  'En attente':'#f59e0b','Confirmée':'#10b981','Livrée':'#2563eb','Annulée':'#ef4444'
};

const commandes = <?= json_encode($commandes, JSON_UNESCAPED_UNICODE) ?>;
const markers = {};

commandes.forEach((c, i) => {
  const zone  = ZONES[i % ZONES.length];
  const color = STATUS_COLORS[c.status] || '#94a3b8';
  const icon  = L.divIcon({
    html: `<div style="width:28px;height:28px;border-radius:50%;background:${color};border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.2);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff">${c.id}</div>`,
    iconSize:[28,28], iconAnchor:[14,14], className:''
  });
  const marker = L.marker([zone.lat + (Math.random()-.5)*.02, zone.lng + (Math.random()-.5)*.02], {icon})
    .addTo(map)
    .bindPopup(`
      <div style="font-family:'Plus Jakarta Sans',sans-serif;min-width:180px">
        <div style="font-weight:800;color:#0f172a;margin-bottom:6px">#${c.id} — ${c.nom_produit}</div>
        <div style="font-size:12px;color:#64748b;margin-bottom:4px">👤 ${c.client_id}</div>
        <div style="font-size:12px;color:#64748b;margin-bottom:4px">📍 ${zone.name}</div>
        <div style="font-size:12px;font-weight:700;color:${color};background:${color}20;padding:3px 8px;border-radius:6px;display:inline-block">${c.status}</div>
        <div style="font-size:14px;font-weight:700;color:#059669;margin-top:8px">${parseFloat(c.total).toFixed(3)} DT</div>
      </div>
    `);
  markers[c.id] = marker;
});

function focusOrder(id) {
  if (markers[id]) {
    markers[id].openPopup();
    map.setView(markers[id].getLatLng(), 15);
  }
}
</script>
</body>
</html>
