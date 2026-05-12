<?php
require_once __DIR__ . '/../../../../config/session.php';
require_role('Administrateur');
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Carte de Livraison — MediLink</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
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
    --shadow-lg:0 20px 40px rgba(0,0,0,0.12);
}
body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--gray-50); color:var(--gray-900); font-size:14px; display:flex; min-height:100vh; }

/* ════════ SIDEBAR ════════ */
.sidebar {
    width:250px; min-height:100vh; background:var(--navy);
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
.sb-nav {
    flex:1; padding:20px 0; overflow-y:auto;
}
.sb-nav-item {
    display:flex; align-items:center; gap:12px;
    padding:12px 20px; color:rgba(255,255,255,.7);
    text-decoration:none; font-size:13px; font-weight:500;
    transition:.2s; position:relative;
}
.sb-nav-item:hover, .sb-nav-item.active {
    background:rgba(255,255,255,.08); color:#fff;
}
.sb-nav-item.active::before {
    content:''; position:absolute; left:0; top:0; bottom:0;
    width:3px; background:var(--blue);
}
.sb-nav-icon {
    width:20px; height:20px; display:flex; align-items:center; justify-content:center;
}

/* ════════ MAIN CONTENT ════════ */
.main-content {
    flex:1; margin-left:250px; display:flex; flex-direction:column;
}
.page-header {
    background:#fff; padding:24px 32px; border-bottom:1px solid var(--gray-200);
    display:flex; align-items:center; justify-content:space-between;
}
.page-title {
    font-size:24px; font-weight:700; color:var(--gray-900);
    display:flex; align-items:center; gap:12px;
}
.page-subtitle {
    color:var(--gray-500); font-size:14px; font-weight:400; margin-top:4px;
}
.page-actions {
    display:flex; gap:12px; align-items:center;
}

/* ════════ MAP CONTAINER ════════ */
.map-container {
    flex:1; display:flex; flex-direction:column; padding:24px 32px; gap:24px;
}
.map-controls {
    background:#fff; border-radius:var(--radius-lg); padding:20px;
    box-shadow:var(--shadow-sm); display:flex; gap:16px; align-items:center;
    flex-wrap:wrap;
}
.control-group {
    display:flex; flex-direction:column; gap:8px;
}
.control-label {
    font-size:12px; font-weight:600; color:var(--gray-600); text-transform:uppercase;
    letter-spacing:0.04em;
}
.control-input {
    padding:8px 12px; border:1px solid var(--gray-200);
    border-radius:8px; font-size:13px; min-width:200px;
}
.control-btn {
    padding:10px 20px; background:var(--blue); color:#fff;
    border-radius:8px; font-size:13px; font-weight:600;
    border:none; cursor:pointer; transition:.2s;
}
.control-btn:hover { background:var(--blue-dark); transform:translateY(-1px); }
.control-btn.secondary {
    background:var(--gray-200); color:var(--gray-700);
}
.control-btn.secondary:hover { background:var(--gray-300); }

.map-wrapper {
    flex:1; background:#fff; border-radius:var(--radius-lg);
    box-shadow:var(--shadow-sm); overflow:hidden; position:relative;
}
#deliveryMap {
    width:100%; height:100%; min-height:500px;
}

/* ════════ DELIVERY INFO PANEL ════════ */
delivery-info {
    position:absolute; top:20px; right:20px;
    background:#fff; border-radius:var(--radius);
    box-shadow:var(--shadow-lg); padding:16px;
    min-width:280px; max-height:400px; overflow-y:auto;
    z-index:1000;
}
delivery-info h3 {
    font-size:14px; font-weight:700; margin-bottom:12px;
    color:var(--gray-900);
}
delivery-item {
    padding:12px 0; border-bottom:1px solid var(--gray-200);
}
delivery-item:last-child { border-bottom:none; }
delivery-customer {
    font-weight:600; font-size:13px; color:var(--gray-900);
    margin-bottom:4px;
}
delivery-address {
    font-size:12px; color:var(--gray-600); margin-bottom:8px;
}
delivery-status {
    display:inline-block; padding:4px 8px; border-radius:6px;
    font-size:11px; font-weight:600; text-transform:uppercase;
}
.status-pending { background:var(--orange-light); color:var(--orange); }
.status-delivered { background:var(--green-light); color:var(--green-dark); }
.status-in-progress { background:var(--blue-light); color:var(--blue-dark); }

/* ════════ LEGEND ════════ */
map-legend {
    position:absolute; bottom:20px; left:20px;
    background:#fff; border-radius:var(--radius);
    box-shadow:var(--shadow-lg); padding:16px;
    z-index:1000;
}
legend-title {
    font-size:12px; font-weight:700; margin-bottom:8px;
    color:var(--gray-900);
}
legend-item {
    display:flex; align-items:center; gap:8px; margin-bottom:4px;
    font-size:11px; color:var(--gray-600);
}
legend-marker {
    width:12px; height:12px; border-radius:50%;
}
.marker-pending { background:var(--orange); }
.marker-delivered { background:var(--green); }
.marker-in-progress { background:var(--blue); }

/* ════════ RESPONSIVE ════════ */
@media (max-width:768px) {
    .sidebar { transform:translateX(-100%); }
    .main-content { margin-left:0; }
    .map-container { padding:16px; }
    .map-controls { flex-direction:column; align-items:stretch; }
    .control-input { min-width:auto; }
}
</style>
<link rel="stylesheet" href="/files40/assets/css/unified.css">
</head>
<body>

<!-- ════════ SIDEBAR ════════ -->
<aside class="sidebar">
    <div class="sb-logo">
        <div class="sb-logo-inner">
            <div class="nav-logo-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/></svg>
            </div>
            <div>
                <div class="sb-logo-text">Medi<span>Link</span></div>
                <div class="sb-logo-sub">Parapharmacie</div>
            </div>
        </div>
    </div>
    <nav class="sb-nav">
        <a href="admin.php" class="sb-nav-item">
            <span class="sb-nav-icon">📊</span>
            <span>Tableau de bord</span>
        </a>
        <a href="admin.php#products" class="sb-nav-item">
            <span class="sb-nav-icon">📦</span>
            <span>Produits</span>
        </a>
        <a href="admin.php#orders" class="sb-nav-item">
            <span class="sb-nav-icon">🛒</span>
            <span>Commandes</span>
        </a>
        <a href="delivery-map.php" class="sb-nav-item active">
            <span class="sb-nav-icon">🗺️</span>
            <span>Carte de livraison</span>
        </a>
        <a href="admin.php#settings" class="sb-nav-item">
            <span class="sb-nav-icon">⚙️</span>
            <span>Paramètres</span>
        </a>
    </nav>
</aside>

<!-- ════════ MAIN CONTENT ════════ -->
<main class="main-content">
    <header class="page-header">
        <div>
            <h1 class="page-title">
                🗺️ Carte de Livraison
            </h1>
            <p class="page-subtitle">Suivez vos livraisons en temps réel</p>
        </div>
        <div class="page-actions">
            <button class="control-btn secondary" onclick="refreshMap()">
                🔄 Actualiser
            </button>
            <button class="control-btn" onclick="optimizeRoutes()">
                🚚 Optimiser les routes
            </button>
        </div>
    </header>

    <div class="map-container">
        <div class="map-controls">
            <div class="control-group">
                <label class="control-label">Statut</label>
                <select class="control-input" id="statusFilter" onchange="filterDeliveries()">
                    <option value="all">Toutes les livraisons</option>
                    <option value="pending">En attente</option>
                    <option value="in_progress">En cours</option>
                    <option value="delivered">Livrées</option>
                </select>
            </div>
            <div class="control-group">
                <label class="control-label">Date</label>
                <input type="date" class="control-input" id="dateFilter" onchange="filterDeliveries()">
            </div>
            <div class="control-group">
                <label class="control-label">Livreur</label>
                <select class="control-input" id="deliveryPersonFilter" onchange="filterDeliveries()">
                    <option value="all">Tous les livreurs</option>
                    <option value="ahmed">Ahmed</option>
                    <option value="fatima">Fatima</option>
                    <option value="karim">Karim</option>
                </select>
            </div>
        </div>

        <div class="map-wrapper">
            <div id="deliveryMap"></div>
            
            <delivery-info>
                <h3>📦 Livraisons du jour</h3>
                <div id="deliveryList"></div>
            </delivery-info>

            <map-legend>
                <div class="legend-title">Légende</div>
                <div class="legend-item">
                    <div class="legend-marker marker-pending"></div>
                    <span>En attente</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker marker-in-progress"></div>
                    <span>En cours</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker marker-delivered"></div>
                    <span>Livrée</span>
                </div>
            </map-legend>
        </div>
    </div>
</main>

<script>
// ════════ MAP INITIALIZATION ════════
let map;
let deliveryMarkers = [];
let routeLines = [];

// Initialize map
function initMap() {
    map = L.map('deliveryMap').setView([36.8065, 10.1815], 12); // Tunis coordinates
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    loadDeliveries();
}

// ════════ DELIVERY DATA ════════
const mockDeliveries = [
    {
        id: 1,
        customer: 'Mohamed Ben Ali',
        address: 'Avenue Habib Bourguiba, Tunis',
        status: 'pending',
        lat: 36.8065,
        lng: 10.1815,
        products: ['Crème Hydra Éclat SPF30', 'Vitamine C + Zinc'],
        deliveryPerson: 'ahmed',
        estimatedTime: '14:30',
        phone: '+216 23 456 789'
    },
    {
        id: 2,
        customer: 'Fatma Trabelsi',
        address: 'Rue de la Palestine, Sousse',
        status: 'in_progress',
        lat: 35.8256,
        lng: 10.6369,
        products: ['Beurre corporel karité'],
        deliveryPerson: 'fatima',
        estimatedTime: '15:45',
        phone: '+216 98 765 432'
    },
    {
        id: 3,
        customer: 'Karim Jaziri',
        address: 'Boulevard du 2 Mars, Sfax',
        status: 'delivered',
        lat: 34.7406,
        lng: 10.7603,
        products: ['Gel douche douceur', 'Shampoing sec réparateur'],
        deliveryPerson: 'karim',
        estimatedTime: '12:00',
        phone: '+216 55 123 456'
    },
    {
        id: 4,
        customer: 'Sonia Mokhtar',
        address: 'Rue Farhat Hached, Ariana',
        status: 'pending',
        lat: 36.8625,
        lng: 10.1956,
        products: ['Lait nettoyant bébé'],
        deliveryPerson: 'ahmed',
        estimatedTime: '16:15',
        phone: '+216 71 234 567'
    }
];

// ════════ MAP FUNCTIONS ════════
function loadDeliveries() {
    clearMap();
    
    const filteredDeliveries = getFilteredDeliveries();
    
    filteredDeliveries.forEach(delivery => {
        addDeliveryMarker(delivery);
    });
    
    updateDeliveryList(filteredDeliveries);
    
    if (filteredDeliveries.length > 0) {
        fitMapToDeliveries(filteredDeliveries);
    }
}

function addDeliveryMarker(delivery) {
    const color = getStatusColor(delivery.status);
    
    const marker = L.circleMarker([delivery.lat, delivery.lng], {
        radius: 8,
        fillColor: color,
        color: '#fff',
        weight: 2,
        opacity: 1,
        fillOpacity: 0.8
    }).addTo(map);
    
    marker.bindPopup(`
        <div style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px;">
            <strong>${delivery.customer}</strong><br>
            📍 ${delivery.address}<br>
            📦 ${delivery.products.join(', ')}<br>
            📞 ${delivery.phone}<br>
            ⏰ ${delivery.estimatedTime}<br>
            👤 ${getDeliveryPersonName(delivery.deliveryPerson)}<br>
            <span style="display: inline-block; padding: 2px 6px; background: ${color}20; color: ${color}; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase;">
                ${getStatusName(delivery.status)}
            </span>
        </div>
    `);
    
    deliveryMarkers.push(marker);
}

function clearMap() {
    deliveryMarkers.forEach(marker => map.removeLayer(marker));
    routeLines.forEach(line => map.removeLayer(line));
    deliveryMarkers = [];
    routeLines = [];
}

function fitMapToDeliveries(deliveries) {
    const group = new L.featureGroup(deliveryMarkers);
    map.fitBounds(group.getBounds().pad(0.1));
}

// ════════ FILTERING ════════
function getFilteredDeliveries() {
    let filtered = [...mockDeliveries];
    
    const statusFilter = document.getElementById('statusFilter').value;
    if (statusFilter !== 'all') {
        filtered = filtered.filter(d => d.status === statusFilter);
    }
    
    const deliveryPersonFilter = document.getElementById('deliveryPersonFilter').value;
    if (deliveryPersonFilter !== 'all') {
        filtered = filtered.filter(d => d.deliveryPerson === deliveryPersonFilter);
    }
    
    return filtered;
}

function filterDeliveries() {
    loadDeliveries();
}

// ════════ UI FUNCTIONS ════════
function updateDeliveryList(deliveries) {
    const listContainer = document.getElementById('deliveryList');
    
    if (deliveries.length === 0) {
        listContainer.innerHTML = '<p style="text-align: center; color: var(--gray-500); padding: 20px;">Aucune livraison trouvée</p>';
        return;
    }
    
    listContainer.innerHTML = deliveries.map(delivery => `
        <div class="delivery-item">
            <div class="delivery-customer">${delivery.customer}</div>
            <div class="delivery-address">📍 ${delivery.address}</div>
            <div class="delivery-status status-${delivery.status}">
                ${getStatusName(delivery.status)}
            </div>
        </div>
    `).join('');
}

function getStatusColor(status) {
    const colors = {
        'pending': '#ea580c',
        'in_progress': '#1a56db',
        'delivered': '#0da271'
    };
    return colors[status] || '#64748b';
}

function getStatusName(status) {
    const names = {
        'pending': 'En attente',
        'in_progress': 'En cours',
        'delivered': 'Livrée'
    };
    return names[status] || status;
}

function getDeliveryPersonName(person) {
    const names = {
        'ahmed': 'Ahmed',
        'fatima': 'Fatima',
        'karim': 'Karim'
    };
    return names[person] || person;
}

// ════════ ACTIONS ════════
function refreshMap() {
    loadDeliveries();
    showToast('🔄 Carte actualisée', 'success');
}

function optimizeRoutes() {
    const pendingDeliveries = mockDeliveries.filter(d => d.status === 'pending');
    
    if (pendingDeliveries.length < 2) {
        showToast('📍 Pas assez de livraisons à optimiser', 'warning');
        return;
    }
    
    // Clear existing routes
    routeLines.forEach(line => map.removeLayer(line));
    routeLines = [];
    
    // Advanced route optimization using nearest neighbor algorithm
    const optimizedRoute = calculateOptimalRoute(pendingDeliveries);
    
    // Draw optimized route
    drawOptimizedRoute(optimizedRoute);
    
    // Calculate and display route statistics
    displayRouteStatistics(optimizedRoute);
    
    showToast('🚚 Routes optimisées avec succès', 'success');
}

function calculateOptimalRoute(deliveries) {
    if (deliveries.length === 0) return [];
    
    // Start from the first delivery as depot
    const route = [deliveries[0]];
    const unvisited = deliveries.slice(1);
    
    while (unvisited.length > 0) {
        const current = route[route.length - 1];
        let nearest = null;
        let minDistance = Infinity;
        
        // Find nearest unvisited delivery
        unvisited.forEach(delivery => {
            const distance = calculateDistance(
                current.lat, current.lng,
                delivery.lat, delivery.lng
            );
            
            if (distance < minDistance) {
                minDistance = distance;
                nearest = delivery;
            }
        });
        
        if (nearest) {
            route.push(nearest);
            unvisited.splice(unvisited.indexOf(nearest), 1);
        }
    }
    
    return route;
}

function calculateDistance(lat1, lng1, lat2, lng2) {
    // Haversine formula for calculating distance between two points
    const R = 6371; // Earth's radius in kilometers
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function drawOptimizedRoute(route) {
    if (route.length < 2) return;
    
    // Create route coordinates
    const routeCoords = route.map(d => [d.lat, d.lng]);
    
    // Draw main route line
    const mainRoute = L.polyline(routeCoords, {
        color: '#1a56db',
        weight: 4,
        opacity: 0.8,
        smoothFactor: 1
    }).addTo(map);
    
    routeLines.push(mainRoute);
    
    // Add route arrows to show direction
    for (let i = 0; i < routeCoords.length - 1; i++) {
        const start = routeCoords[i];
        const end = routeCoords[i + 1];
        const midPoint = [
            (start[0] + end[0]) / 2,
            (start[1] + end[1]) / 2
        ];
        
        // Add arrow marker
        const arrow = L.divIcon({
            html: '<div style="color: #1a56db; font-size: 16px; transform: rotate(' + calculateBearing(start, end) + 'deg);">➤</div>',
            iconSize: [20, 20],
            className: 'route-arrow'
        });
        
        L.marker(midPoint, { icon: arrow }).addTo(map);
    }
    
    // Add route number labels
    route.forEach((delivery, index) => {
        const label = L.divIcon({
            html: `<div style="
                background: #1a56db;
                color: white;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: bold;
                border: 2px solid white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            ">${index + 1}</div>`,
            iconSize: [24, 24],
            className: 'route-number'
        });
        
        L.marker([delivery.lat, delivery.lng], { icon: label }).addTo(map);
    });
}

function calculateBearing(start, end) {
    const lat1 = start[0] * Math.PI / 180;
    const lat2 = end[0] * Math.PI / 180;
    const lng1 = start[1] * Math.PI / 180;
    const lng2 = end[1] * Math.PI / 180;
    
    const bearing = Math.atan2(
        Math.sin(lng2 - lng1) * Math.cos(lat2),
        Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(lat2) * Math.cos(lng2 - lng1)
    ) * 180 / Math.PI;
    
    return bearing;
}

function displayRouteStatistics(route) {
    if (route.length < 2) return;
    
    let totalDistance = 0;
    let totalTime = 0;
    
    for (let i = 0; i < route.length - 1; i++) {
        const distance = calculateDistance(
            route[i].lat, route[i].lng,
            route[i + 1].lat, route[i + 1].lng
        );
        
        totalDistance += distance;
        // Assume average speed of 40 km/h in urban areas
        totalTime += (distance / 40) * 60; // Convert to minutes
    }
    
    // Create statistics popup
    const statsDiv = document.createElement('div');
    statsDiv.style.cssText = `
        position: absolute;
        bottom: 80px;
        left: 20px;
        background: white;
        border-radius: 8px;
        padding: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        min-width: 200px;
    `;
    
    statsDiv.innerHTML = `
        <h4 style="margin: 0 0 12px 0; font-size: 14px; color: #1a56db;">📊 Statistiques de route</h4>
        <div style="font-size: 12px; color: #64748b;">
            <div style="margin-bottom: 4px;">
                <strong>📍 Livraisons:</strong> ${route.length}
            </div>
            <div style="margin-bottom: 4px;">
                <strong>📏 Distance totale:</strong> ${totalDistance.toFixed(1)} km
            </div>
            <div style="margin-bottom: 4px;">
                <strong>⏱️ Temps estimé:</strong> ${Math.round(totalTime)} min
            </div>
            <div>
                <strong>🚐 Vitesse moyenne:</strong> 40 km/h
            </div>
        </div>
        <button onclick="this.parentElement.remove()" style="
            margin-top: 12px;
            padding: 4px 8px;
            background: #f1f5f9;
            border: none;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
            color: #64748b;
        ">Fermer</button>
    `;
    
    document.querySelector('.map-wrapper').appendChild(statsDiv);
    
    // Auto-remove after 10 seconds
    setTimeout(() => {
        if (statsDiv.parentElement) {
            statsDiv.remove();
        }
    }, 10000);
}

// ════════ TOAST NOTIFICATION ════════
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; top: 20px; right: 20px; z-index: 10000;
        padding: 12px 20px; border-radius: 8px; font-size: 13px; font-weight: 600;
        background: ${type === 'success' ? '#0da271' : type === 'warning' ? '#ea580c' : '#1a56db'};
        color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 3000);
}

// ════════ ANIMATIONS ════════
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// ════════ INIT ════════
document.addEventListener('DOMContentLoaded', initMap);

// Set today's date as default
document.getElementById('dateFilter').valueAsDate = new Date();

// ════════ REAL-TIME TRACKING ════════
let trackingInterval;
let isTrackingActive = false;

function startRealTimeTracking() {
    if (isTrackingActive) return;
    
    isTrackingActive = true;
    showToast('📍 Suivi en temps réel activé', 'success');
    
    trackingInterval = setInterval(() => {
        simulateDeliveryUpdates();
        loadDeliveries();
    }, 5000); // Update every 5 seconds
}

function stopRealTimeTracking() {
    if (!isTrackingActive) return;
    
    isTrackingActive = false;
    clearInterval(trackingInterval);
    showToast('⏸️ Suivi en temps réel arrêté', 'info');
}

function simulateDeliveryUpdates() {
    // Simulate random delivery status changes
    const statuses = ['pending', 'in_progress', 'delivered'];
    mockDeliveries.forEach(delivery => {
        if (delivery.status === 'pending' && Math.random() > 0.7) {
            delivery.status = 'in_progress';
            showToast(`🚚 Livraison de ${delivery.customer} en cours`, 'info');
        } else if (delivery.status === 'in_progress' && Math.random() > 0.8) {
            delivery.status = 'delivered';
            showToast(`✅ Livraison de ${delivery.customer} terminée`, 'success');
        }
    });
}

// Add tracking controls to the map
function addTrackingControls() {
    const controlDiv = document.createElement('div');
    controlDiv.style.cssText = `
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        border-radius: 8px;
        padding: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        z-index: 1000;
        display: flex;
        gap: 8px;
        align-items: center;
        font-size: 12px;
    `;
    
    controlDiv.innerHTML = `
        <button id="startTracking" onclick="startRealTimeTracking()" style="
            padding: 6px 12px;
            background: #0da271;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
        ">▶️ Démarrer</button>
        <button id="stopTracking" onclick="stopRealTimeTracking()" style="
            padding: 6px 12px;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
            display: none;
        ">⏸️ Arrêter</button>
        <span id="trackingStatus" style="color: #64748b; font-size: 11px;">Suivi inactif</span>
    `;
    
    document.querySelector('.map-wrapper').appendChild(controlDiv);
    
    // Update button visibility and status
    setInterval(() => {
        const startBtn = document.getElementById('startTracking');
        const stopBtn = document.getElementById('stopTracking');
        const status = document.getElementById('trackingStatus');
        
        if (isTrackingActive) {
            startBtn.style.display = 'none';
            stopBtn.style.display = 'block';
            status.textContent = 'Suivi actif';
            status.style.color = '#0da271';
        } else {
            startBtn.style.display = 'block';
            stopBtn.style.display = 'none';
            status.textContent = 'Suivi inactif';
            status.style.color = '#64748b';
        }
    }, 1000);
}

// Enhanced delivery info with live updates
function updateDeliveryInfoWithTracking(deliveries) {
    const listContainer = document.getElementById('deliveryList');
    
    if (deliveries.length === 0) {
        listContainer.innerHTML = '<p style="text-align: center; color: var(--gray-500); padding: 20px;">Aucune livraison trouvée</p>';
        return;
    }
    
    listContainer.innerHTML = deliveries.map(delivery => {
        const timeAgo = getTimeAgo(delivery.lastUpdate || new Date());
        const trackingDot = isTrackingActive && delivery.status === 'in_progress' ? 
            '<span style="display: inline-block; width: 8px; height: 8px; background: #0da271; border-radius: 50%; margin-left: 8px; animation: pulse 2s infinite;"></span>' : '';
        
        return `
            <div class="delivery-item">
                <div class="delivery-customer">${delivery.customer}${trackingDot}</div>
                <div class="delivery-address">📍 ${delivery.address}</div>
                <div class="delivery-status status-${delivery.status}">
                    ${getStatusName(delivery.status)}
                </div>
                <div style="font-size: 10px; color: var(--gray-400); margin-top: 4px;">
                    🕒 ${timeAgo}
                </div>
            </div>
        `;
    }).join('');
}

function getTimeAgo(date) {
    const now = new Date();
    const past = new Date(date);
    const diffMs = now - past;
    const diffMins = Math.floor(diffMs / 60000);
    
    if (diffMins < 1) return 'À l\'instant';
    if (diffMins < 60) return `Il y a ${diffMins} min`;
    
    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `Il y a ${diffHours}h`;
    
    const diffDays = Math.floor(diffHours / 24);
    return `Il y a ${diffDays}j`;
}

// Add pulse animation
const pulseStyle = document.createElement('style');
pulseStyle.textContent = `
    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
        100% { opacity: 1; transform: scale(1); }
    }
`;
document.head.appendChild(pulseStyle);

// ════════ ENHANCED MAP LOADING ════════
function loadDeliveriesWithTracking() {
    clearMap();
    
    const filteredDeliveries = getFilteredDeliveries();
    
    filteredDeliveries.forEach(delivery => {
        addDeliveryMarker(delivery);
        
        // Add live tracking circle for active deliveries
        if (delivery.status === 'in_progress' && isTrackingActive) {
            const trackingCircle = L.circle([delivery.lat, delivery.lng], {
                radius: 500,
                fillColor: '#0da271',
                color: '#0da271',
                weight: 2,
                opacity: 0.3,
                fillOpacity: 0.1
            }).addTo(map);
            
            routeLines.push(trackingCircle);
        }
    });
    
    updateDeliveryInfoWithTracking(filteredDeliveries);
    
    if (filteredDeliveries.length > 0) {
        fitMapToDeliveries(filteredDeliveries);
    }
}

// Override the original loadDeliveries function
const originalLoadDeliveries = loadDeliveries;
loadDeliveries = loadDeliveriesWithTracking;

// Initialize tracking controls when map loads
document.addEventListener('DOMContentLoaded', () => {
    initMap();
    addTrackingControls();
});
</script>

</body>
</html>
