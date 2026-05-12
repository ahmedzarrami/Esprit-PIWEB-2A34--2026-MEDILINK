<?php
session_start();

// ── Vérifier que le patient est connecté ──
if (!isset($_SESSION['patient_id']) || empty($_SESSION['patient_id'])) {
    header('Location: loginPatient.php');
    exit;
}

$basePath = dirname(__DIR__) . '/..';
require_once $basePath . '/config.php';
require_once $basePath . '/controllers/rendezvousC.php';
require_once $basePath . '/models/rendezvous.php';
require_once $basePath . '/controllers/evaluationC.php';
require_once $basePath . '/models/evaluation.php';

$rendezvousC    = new RendezvousC();
$evaluationC    = new EvaluationC();
$patient_id     = $_SESSION['patient_id'];
$patient_nom    = $_SESSION['patient_nom']    ?? '';
$patient_prenom = $_SESSION['patient_prenom'] ?? '';

$alert_message = '';
$alert_type    = '';

// ══════════════════════════════════════════
// TRAITEMENT DES ACTIONS POST
// ══════════════════════════════════════════

// ── SUPPRESSION ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $rdv_id = intval($_POST['rdv_id'] ?? 0);
    if ($rdv_id > 0) {
        $rdv = $rendezvousC->getRendezvousById($rdv_id);
        if ($rdv && $rdv['patient_id'] == $patient_id) {
            $rendezvousC->deleteRendezvous($rdv_id);
            $alert_message = '✅ Rendez-vous supprimé avec succès.';
            $alert_type    = 'success';
        } else {
            $alert_message = '❌ Accès refusé ou rendez-vous introuvable.';
            $alert_type    = 'error';
        }
    }
}

// ── MODIFICATION ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $rdv_id    = intval($_POST['rdv_id']  ?? 0);
    $date_rdv  = trim($_POST['date_rdv']  ?? '');
    $heure_rdv = trim($_POST['heure_rdv'] ?? '');
    $errors    = [];

    if (empty($date_rdv)) {
        $errors[] = 'Veuillez choisir une date.';
    } else {
        [$yr,$mo,$da] = array_map('intval', explode('-', $date_rdv));
        $ts  = mktime(0,0,0,$mo,$da,$yr);
        $dow = (int) date('w', $ts);
        if ($dow === 0)            $errors[] = 'Le cabinet est fermé le dimanche.';
        if ($ts < mktime(0,0,0))   $errors[] = 'Impossible de choisir une date passée.';
    }
    if (empty($heure_rdv)) {
        $errors[] = 'Veuillez choisir un créneau.';
    } else {
        [$h,$m]  = array_map('intval', explode(':', $heure_rdv));
        $tot     = $h*60+$m;
        if (!( ($tot>=480&&$tot<750) || ($tot>=840&&$tot<1080) ))
            $errors[] = 'Heure invalide. Consultations : 8h00–12h30 et 14h00–18h00.';
    }

    if (empty($errors) && $rdv_id > 0) {
        $rdv = $rendezvousC->getRendezvousById($rdv_id);
        if ($rdv && $rdv['patient_id'] == $patient_id) {
            $updated = new Rendezvous($rdv_id, $rdv['medecin_id'], $patient_id, $date_rdv, $heure_rdv, $rdv['statut']);
            $rendezvousC->updateRendezvous($updated);
            $alert_message = '✅ Rendez-vous modifié avec succès.';
            $alert_type    = 'success';
        } else {
            $alert_message = '❌ Accès refusé ou rendez-vous introuvable.';
            $alert_type    = 'error';
        }
    } elseif (!empty($errors)) {
        $alert_message = '❌ ' . implode(' ', $errors);
        $alert_type    = 'error';
    }
}

// ── AJOUT ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $medecin_id = intval($_POST['medecin_id'] ?? 0);
    $date_rdv   = trim($_POST['date_rdv']     ?? '');
    $heure_rdv  = trim($_POST['heure_rdv']    ?? '');
    $errors     = [];

    if ($medecin_id <= 0) $errors[] = 'Veuillez sélectionner un médecin.';

    if (empty($date_rdv)) {
        $errors[] = 'Veuillez choisir une date.';
    } else {
        [$yr,$mo,$da] = array_map('intval', explode('-', $date_rdv));
        $ts  = mktime(0,0,0,$mo,$da,$yr);
        $dow = (int) date('w', $ts);
        if ($dow === 0)           $errors[] = 'Le cabinet est fermé le dimanche.';
        if ($ts < mktime(0,0,0))  $errors[] = 'Impossible de choisir une date passée.';
    }
    if (empty($heure_rdv)) {
        $errors[] = 'Veuillez choisir un créneau.';
    } else {
        [$h,$m] = array_map('intval', explode(':', $heure_rdv));
        $tot    = $h*60+$m;
        if (!( ($tot>=480&&$tot<750) || ($tot>=840&&$tot<1080) ))
            $errors[] = 'Heure invalide. Consultations : 8h00–12h30 et 14h00–18h00.';
    }

    if (empty($errors)) {
        $rdv = new Rendezvous(null, $medecin_id, $patient_id, $date_rdv, $heure_rdv, 'confirmé');
        $rendezvousC->addRendezvous($rdv);
        $alert_message = '✅ Rendez-vous réservé avec succès !';
        $alert_type    = 'success';
    } else {
        $alert_message = '❌ ' . implode(' ', $errors);
        $alert_type    = 'error';
    }
}

// ── ÉVALUATION ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'evaluer') {
    $rdv_id      = intval($_POST['rdv_id']      ?? 0);
    $note        = intval($_POST['note']         ?? 0);
    $commentaire = trim($_POST['commentaire']    ?? '');
    $errors      = [];
    if ($rdv_id <= 0)           $errors[] = 'RDV invalide.';
    if ($note < 1 || $note > 5) $errors[] = 'Note invalide (1 à 5).';
    if (empty($errors)) {
        $rdv = $rendezvousC->getRendezvousById($rdv_id);
        if ($rdv && $rdv['patient_id'] == $patient_id) {
            if (strtotime($rdv['date_rdv']) < mktime(0,0,0)) {
                if (!$evaluationC->evalExistsByRdv($rdv_id)) {
                    $eval = new Evaluation(null, $patient_id, $rdv['medecin_id'], $rdv_id, $note, $commentaire ?: null);
                    $evaluationC->addEvaluation($eval);
                    $alert_message = '⭐ Merci pour votre évaluation !';
                    $alert_type    = 'success';
                } else {
                    $alert_message = '❌ Vous avez déjà évalué ce rendez-vous.';
                    $alert_type    = 'error';
                }
            } else {
                $alert_message = '❌ Vous ne pouvez évaluer qu\'un RDV passé.';
                $alert_type    = 'error';
            }
        } else {
            $alert_message = '❌ Accès refusé.';
            $alert_type    = 'error';
        }
    } else {
        $alert_message = '❌ ' . implode(' ', $errors);
        $alert_type    = 'error';
    }
}

// ══════════════════════════════════════════
// DONNÉES POUR L'AFFICHAGE
// ══════════════════════════════════════════
$medecins = $rendezvousC->listMedecins();
$mes_rdvs = $rendezvousC->getRendezvousByPatientId($patient_id);

// ── Évaluations ──
// Stats par médecin (note moyenne)
$eval_stats = []; // [medecin_id => ['moyenne'=>X,'total'=>Y]]
foreach ($medecins as $doc) {
    $eval_stats[$doc['id']] = $evaluationC->getStatsMedecin($doc['id']);
}
// RDV déjà évalués
$rdvs_evalues = []; // [rdv_id => true]
foreach ($mes_rdvs as $r) {
    if ($evaluationC->evalExistsByRdv($r['id'])) {
        $rdvs_evalues[$r['id']] = true;
    }
}

// ── Recherche & tri RDV ──
$rdv_search_medecin = trim($_GET['rdv_medecin'] ?? '');
$rdv_search_date    = trim($_GET['rdv_date']    ?? '');
$rdv_tri            = in_array($_GET['rdv_tri'] ?? '', ['asc','desc']) ? $_GET['rdv_tri'] : 'desc';

$selected_medecin_id = intval($_GET['medecin_id'] ?? 0);
$selected_date       = htmlspecialchars($_GET['date'] ?? '');

// RDV en cours de modification
$edit_rdv_id = intval($_GET['edit'] ?? 0);
$edit_rdv    = null;
if ($edit_rdv_id > 0) {
    $tmp = $rendezvousC->getRendezvousById($edit_rdv_id);
    if ($tmp && $tmp['patient_id'] == $patient_id) {
        $edit_rdv = $tmp;
        if (!$selected_medecin_id) $selected_medecin_id = $edit_rdv['medecin_id'];
        if (!$selected_date)       $selected_date       = $edit_rdv['date_rdv'];
    }
}

// Créneaux horaires
$slots = [];
if ($selected_medecin_id > 0 && $selected_date) {
    $slots = $rendezvousC->getMedecinAvailability($selected_medecin_id, $selected_date);
}

// ── Helpers ──
function formatDateFR(string $d): string {
    [$y,$m,$j] = explode('-', $d);
    $jours = ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'];
    $mois  = ['jan','fév','mar','avr','mai','juin','juil','août','sep','oct','nov','déc'];
    $dow   = (int) date('w', mktime(0,0,0,(int)$m,(int)$j,(int)$y));
    return $jours[$dow].' '.(int)$j.' '.$mois[(int)$m-1].'. '.$y;
}

$today   = date('Y-m-d');
$maxDate = date('Y-m-d', strtotime('+30 days'));

$AVATAR_COLORS = [
    ['bg'=>'#eff4ff','color'=>'#1a56db'],
    ['bg'=>'#ecfdf5','color'=>'#0da271'],
    ['bg'=>'#fff7ed','color'=>'#e05a2b'],
];

// ── Charger les médecins avec tous leurs champs depuis la BD ──
$AVATAR_COLORS_RECH = [
    ['bg'=>'#eff4ff','color'=>'#1a56db'],
    ['bg'=>'#ecfdf5','color'=>'#0da271'],
    ['bg'=>'#fff7ed','color'=>'#e05a2b'],
    ['bg'=>'#f5f3ff','color'=>'#7c3aed'],
    ['bg'=>'#fef9c3','color'=>'#ca8a04'],
];

// Requête étendue avec ville, adresse, latitude, longitude
$medecins_complets = config::getConnexion()
    ->query('SELECT id, nom, specialite, ville, adresse, latitude, longitude FROM medecins ORDER BY nom')
    ->fetchAll();

$MEDECINS_STATIQUES = [];
foreach ($medecins_complets as $idx_m => $doc_m) {
    $c_m = $AVATAR_COLORS_RECH[$idx_m % count($AVATAR_COLORS_RECH)];
    $nom_clean = trim(str_replace(['Dr.','Dr '], '', $doc_m['nom']));
    $MEDECINS_STATIQUES[] = [
        'id'       => $doc_m['id'],
        'nom'      => $doc_m['nom'],
        'spec'     => $doc_m['specialite'],
        'ville'    => $doc_m['ville']    ?? 'Tunis',
        'adresse'  => $doc_m['adresse']  ?? '',
        'lat'      => floatval($doc_m['latitude']  ?? 36.8065),
        'lng'      => floatval($doc_m['longitude'] ?? 10.1815),
        'exp'      => '—',
        'initials' => strtoupper(substr($nom_clean, 0, 2)),
        'bg'       => $c_m['bg'],
        'color'    => $c_m['color'],
    ];
}
$filtre_spec  = trim($_GET['spec']  ?? '');
$filtre_ville = trim($_GET['ville'] ?? '');
$filtre_tri   = in_array($_GET['tri_note'] ?? '', ['asc','desc']) ? $_GET['tri_note'] : '';
$specialites  = array_unique(array_column($MEDECINS_STATIQUES, 'spec'));

// Filtrage
$med_filtres = array_filter($MEDECINS_STATIQUES, function($m) use ($filtre_spec,$filtre_ville) {
    return (!$filtre_spec  || stripos($m['spec'],  $filtre_spec)  !== false)
        && (!$filtre_ville || stripos($m['ville'], $filtre_ville) !== false);
});

// Tri par note d'évaluation
if ($filtre_tri !== '') {
    usort($med_filtres, function($a, $b) use ($eval_stats, $filtre_tri) {
        $moy_a = floatval($eval_stats[$a['id']]['moyenne'] ?? 0);
        $moy_b = floatval($eval_stats[$b['id']]['moyenne'] ?? 0);
        return $filtre_tri === 'asc'
            ? $moy_a <=> $moy_b
            : $moy_b <=> $moy_a;
    });
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediLink — Mes Rendez-vous</title>
    <!-- Leaflet.js carte -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --blue:#2563eb; --blue-dark:#1d4ed8; --blue-light:#eff6ff; --blue-mid:#60a5fa;
    --green:#10b981; --green-light:#ecfdf5;
    --navy:#0f172a; --navy2:#1e293b;
    --red:#ef4444; --red-light:#fef2f2;
    --orange-light:#fffbeb; --orange:#f59e0b;
    --gray-50:#f8fafc; --gray-100:#f1f5f9; --gray-200:#e2e8f0;
    --gray-400:#94a3b8; --gray-600:#475569; --gray-900:#0f172a;
    --radius:16px; --radius-lg:24px; --radius-xl:32px;
}
body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--gray-50); color:var(--gray-900); font-size:14px; line-height:1.6; overflow-x: hidden; }

/* NAVBAR */
.navbar-medilink { background:rgba(255, 255, 255, 0.85); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); border-bottom:1px solid rgba(0,0,0,0.05); padding:0 40px; height:72px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:1000; box-shadow:0 4px 30px rgba(0,0,0,0.03); }
.nav-logo { display:flex; align-items:center; text-decoration:none; font-size:24px; font-weight:800; letter-spacing:-0.5px; }
.nav-logo span { background:linear-gradient(135deg,var(--blue),#8b5cf6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
.nav-user { display:flex; align-items:center; gap:16px; }
.user-info { display:flex; align-items:center; gap:12px; background:rgba(255,255,255,0.5); padding:6px 16px 6px 6px; border-radius:999px; border:1px solid var(--gray-200); box-shadow:0 2px 10px rgba(0,0,0,0.02); }
.user-avatar { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,var(--blue),#8b5cf6); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; box-shadow:0 4px 10px rgba(37,99,235,0.3); }
.user-text { display:flex; flex-direction:column; justify-content:center; }
.user-name { font-size:13px; font-weight:700; color:var(--gray-900); line-height:1.2; }
.user-role { font-size:11px; color:var(--gray-400); font-weight:500; }
.btn-logout { display:flex; align-items:center; gap:8px; padding:8px 18px; background:var(--red-light); color:var(--red); border:1px solid rgba(239,68,68,.1); border-radius:12px; font-size:13px; font-weight:600; text-decoration:none; transition:all .3s ease; }
.btn-logout:hover { background:var(--red); color:white; box-shadow:0 4px 15px rgba(239,68,68,0.2); transform:translateY(-1px); }

/* HERO */
.hero { background:linear-gradient(135deg,var(--navy) 0%,#1e293b 100%); padding:100px 40px 120px; position:relative; overflow:hidden; }
.hero::before { content:''; position:absolute; top:-50%; right:-10%; width:60%; height:200%; background:radial-gradient(circle,rgba(37,99,235,0.3) 0%,rgba(15,23,42,0) 60%); transform:rotate(-15deg); pointer-events:none; }
.hero::after  { content:''; position:absolute; bottom:-20%; left:-10%; width:50%; height:150%; background:radial-gradient(circle,rgba(16,185,129,0.2) 0%,rgba(15,23,42,0) 60%); transform:rotate(15deg); pointer-events:none; }
.hero-inner { max-width:920px; margin:0 auto; position:relative; z-index:1; }
.hero h1 { font-size:48px; font-weight:800; color:#fff; line-height:1.15; margin-bottom:16px; letter-spacing:-1px; }
.hero p { color:rgba(255,255,255,.75); font-size:18px; max-width:500px; line-height:1.6; font-weight:400; }

/* MAIN */
.main-content { max-width:960px; margin:-40px auto 80px; padding:0 40px; position:relative; z-index:10; }
.section-heading { font-size:18px; font-weight:800; color:var(--gray-900); margin-bottom:24px; display:flex; align-items:center; gap:12px; letter-spacing:-0.5px; }
.section-heading::before { content:''; display:inline-block; width:4px; height:20px; background:linear-gradient(180deg,var(--blue),#8b5cf6); border-radius:4px; }

/* ALERT */
.page-alert { padding:16px 20px; margin-bottom:24px; border-radius:16px; font-size:14px; font-weight:600; box-shadow:0 4px 15px rgba(0,0,0,0.03); display:flex; align-items:center; gap:10px; }
.page-alert.success { background:var(--green-light); color:#047857; border:1px solid rgba(16,185,129,.2); }
.page-alert.error   { background:var(--red-light);   color:#b91c1c; border:1px solid rgba(239,68,68,.2); }

/* SEARCH */
.rech-card { background:rgba(255,255,255,0.8); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.9); border-radius:var(--radius-xl); padding:32px; margin-bottom:48px; box-shadow:0 10px 40px rgba(0,0,0,0.04); }
.rech-filters { display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:16px; margin-bottom:24px; align-items:end; flex-wrap:wrap; }
.rech-form-group { display:flex; flex-direction:column; gap:8px; }
.rech-label { font-size:12px; font-weight:700; color:var(--gray-600); text-transform:uppercase; letter-spacing:.05em; }
.rech-input { padding:12px 16px; border:1.5px solid var(--gray-200); border-radius:12px; font-size:14px; font-family:'Plus Jakarta Sans',sans-serif; color:var(--gray-900); background:#fff; outline:none; transition:all .3s ease; height:44px; box-shadow:inset 0 2px 4px rgba(0,0,0,0.02); }
.rech-input:focus { border-color:var(--blue); box-shadow:0 0 0 4px rgba(37,99,235,.15); background:#fff; }
.btn-rech-reset { height:44px; padding:0 20px; background:linear-gradient(135deg,var(--navy),#1e293b); color:#fff; border:none; border-radius:12px; font-size:14px; font-weight:600; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; white-space:nowrap; transition:all .3s ease; box-shadow:0 4px 15px rgba(15,23,42,0.2); }
.btn-rech-reset:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(15,23,42,0.3); }
.rech-tags { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:24px; }
.rech-tag { padding:6px 16px; border-radius:999px; font-size:13px; font-weight:600; border:1px solid var(--gray-200); background:#fff; color:var(--gray-600); text-decoration:none; transition:all .3s ease; box-shadow:0 2px 5px rgba(0,0,0,0.02); }
.rech-tag:hover,.rech-tag.active { background:var(--blue); border-color:var(--blue); color:#fff; box-shadow:0 4px 12px rgba(37,99,235,0.2); transform:translateY(-1px); }
.rech-info { font-size:13px; color:var(--gray-400); margin-bottom:16px; font-weight:500; }
.rech-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:20px; }
.rech-doc-card { background:#fff; border:1px solid rgba(0,0,0,0.05); border-radius:var(--radius-lg); padding:28px 24px; text-align:center; transition:all .4s cubic-bezier(0.4, 0, 0.2, 1); box-shadow:0 4px 20px rgba(0,0,0,0.02); position:relative; overflow:hidden; }
.rech-doc-card::before { content:''; position:absolute; top:0; left:0; width:100%; height:4px; background:linear-gradient(90deg,var(--blue),#8b5cf6); opacity:0; transition:opacity .3s ease; }
.rech-doc-card:hover { transform:translateY(-6px); box-shadow:0 20px 40px rgba(0,0,0,0.08); }
.rech-doc-card:hover::before { opacity:1; }
.rech-doc-card.selected-card { border-color:var(--blue); background:var(--blue-light); box-shadow:0 0 0 4px rgba(37,99,235,.15); }
.rech-doc-rating { display:flex; align-items:center; justify-content:center; gap:5px; margin:6px 0 10px; flex-wrap:wrap; }
.rech-stars { display:inline-flex; gap:1px; }
.rstar { font-size:13px; color:#e2e8f0; }
.rstar.on { color:#f59e0b; }
.rech-note-val { font-size:13px; font-weight:700; color:var(--gray-900); }
.rech-note-count { font-size:11px; color:var(--gray-400); }
.rech-note-empty { font-size:11px; color:var(--gray-400); font-style:italic; }
.btn-rech-rdv.selected { background:var(--blue); color:#fff; border-color:var(--blue); }

/* ── MESSAGE SÉLECTION MÉDECIN ── */
.select-banner {
    display:none; align-items:center; gap:16px;
    background:rgba(239, 246, 255, 0.9); backdrop-filter:blur(10px); border:1px solid rgba(37,99,235,.15);
    border-radius:var(--radius-lg); padding:16px 24px; margin-bottom:24px;
    animation: slideIn .4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow:0 8px 30px rgba(37,99,235,0.08);
}
.select-banner.show { display:flex; }
@keyframes slideIn { from{opacity:0;transform:translateY(-10px) scale(0.98)} to{opacity:1;transform:translateY(0) scale(1)} }
.select-banner-icon { font-size:28px; flex-shrink:0; background:linear-gradient(135deg,var(--blue),#8b5cf6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
.select-banner-text { flex:1; }
.select-banner-name { font-size:15px; font-weight:800; color:var(--gray-900); letter-spacing:-0.3px; }
.select-banner-sub  { font-size:13px; color:var(--gray-600); margin-top:2px; font-weight:500; }
.select-banner-ok { padding:10px 20px; background:linear-gradient(135deg,var(--blue),#8b5cf6); color:#fff; border:none;
                    border-radius:12px; font-size:14px; font-weight:700; cursor:pointer;
                    font-family:"Plus Jakarta Sans",sans-serif; text-decoration:none;
                    display:inline-flex; align-items:center; gap:8px; transition:all .3s ease;
                    box-shadow:0 4px 15px rgba(37,99,235,0.3); }
.select-banner-ok:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(37,99,235,0.4); }

/* ── CARTE GÉOLOCALISATION ── */
.map-section { background:rgba(255,255,255,0.8); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.9); border-radius:var(--radius-xl); overflow:hidden; margin-bottom:24px; box-shadow:0 10px 40px rgba(0,0,0,0.04); }
.map-header { display:flex; align-items:center; justify-content:space-between; padding:20px 24px; border-bottom:1px solid rgba(0,0,0,0.05); flex-wrap:wrap; gap:12px; }
.map-title  { font-size:15px; font-weight:700; color:var(--gray-900); display:flex; align-items:center; gap:10px; }
.btn-locate { display:inline-flex; align-items:center; gap:8px; padding:8px 16px;
              background:var(--blue-light); color:var(--blue); border:none; border-radius:10px;
              font-size:13px; font-weight:700; cursor:pointer;
              font-family:"Plus Jakarta Sans",sans-serif; transition:all .3s ease; }
.btn-locate:hover { background:var(--blue); color:#fff; transform:translateY(-1px); box-shadow:0 4px 12px rgba(37,99,235,0.2); }
.btn-locate:disabled { opacity:.6; cursor:not-allowed; transform:none; box-shadow:none; }
#map { height:380px; width:100%; z-index:1; }
.map-legend { display:flex; gap:20px; padding:16px 24px; flex-wrap:wrap; background:rgba(248,250,252,0.5); }
.legend-item { display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; color:var(--gray-600); }
.legend-dot  { width:12px; height:12px; border-radius:50%; flex-shrink:0; box-shadow:0 2px 5px rgba(0,0,0,0.1); }
.dist-badge  { display:inline-block; background:var(--green-light); color:var(--green);
               border-radius:6px; padding:2px 8px; font-size:11px; font-weight:700; margin-left:6px; }
.rech-avatar { width:72px; height:72px; border-radius:50%; margin:0 auto 16px; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:700; box-shadow:0 8px 20px rgba(0,0,0,0.08); transition:transform .3s ease; }
.rech-doc-card:hover .rech-avatar { transform:scale(1.05); }
.rech-doc-name { font-size:16px; font-weight:800; color:var(--gray-900); margin-bottom:4px; letter-spacing:-0.3px; }
.rech-doc-spec { font-size:13px; color:var(--blue); font-weight:600; margin-bottom:8px; }
.rech-doc-city { font-size:13px; color:var(--gray-500); margin-bottom:16px; display:flex; align-items:center; justify-content:center; gap:4px; }
.rech-doc-meta { display:flex; justify-content:center; gap:24px; margin-bottom:20px; padding:12px 0; border-top:1px dashed var(--gray-200); border-bottom:1px dashed var(--gray-200); }
.rech-meta-item { font-size:12px; color:var(--gray-500); text-align:center; font-weight:500; }
.rech-meta-item strong { display:block; font-size:15px; font-weight:800; color:var(--gray-900); margin-bottom:2px; }
.btn-rech-rdv { display:inline-block; width:100%; background:var(--blue-light); color:var(--blue); border:none; border-radius:12px; padding:12px 16px; font-size:14px; font-weight:700; text-decoration:none; text-align:center; transition:all .3s ease; }
.btn-rech-rdv:hover { background:var(--blue); color:#fff; box-shadow:0 8px 20px rgba(37,99,235,0.25); transform:translateY(-2px); }

/* DOCTORS */

/* FORM */
.form-card { background:rgba(255,255,255,0.8); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.9); border-radius:var(--radius-xl); padding:36px; margin-bottom:48px; box-shadow:0 10px 40px rgba(0,0,0,0.04); }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px; }
.form-group { display:flex; flex-direction:column; gap:8px; }
.form-group label { font-size:12px; font-weight:700; color:var(--gray-600); text-transform:uppercase; letter-spacing:.05em; }
.form-group input,.form-group select { padding:12px 16px; border:1.5px solid var(--gray-200); border-radius:12px; font-size:14px; font-family:'Plus Jakarta Sans',sans-serif; color:var(--gray-900); background:#fff; transition:all .3s ease; outline:none; box-shadow:inset 0 2px 4px rgba(0,0,0,0.02); }
.form-group input:focus,.form-group select:focus { border-color:var(--blue); box-shadow:0 0 0 4px rgba(37,99,235,.15); background:#fff; }

/* TIME PICKER */
.time-picker-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; padding:16px 0; }
.slot { padding:14px 10px; border:2px solid var(--gray-200); border-radius:14px; background:#fff; font-size:14px; font-weight:600; text-align:center; line-height:1.4; min-height:68px; display:flex; align-items:center; justify-content:center; flex-direction:column; transition:all .2s ease; }
.slot.available { border-color:var(--green); color:var(--green); cursor:pointer; }
.slot.available:hover { background:var(--green-light); transform:translateY(-2px); box-shadow:0 4px 12px rgba(16,185,129,0.1); }
.slot.available.sel { background:linear-gradient(135deg,var(--blue),#8b5cf6)!important; color:#fff!important; border-color:transparent!important; font-weight:800!important; box-shadow:0 8px 20px rgba(37,99,235,.3)!important; transform:translateY(-2px); }
.slot.occupied { border-color:var(--gray-200); color:var(--gray-400); background:var(--gray-50); font-weight:600; cursor:not-allowed; opacity:0.7; }
.slot.occupied small { color:var(--gray-400); font-size:11px; font-weight:500; }
.empty-slots { grid-column:1/-1; padding:40px 20px; text-align:center; color:var(--gray-500); font-size:15px; font-weight:500; background:rgba(248,250,252,0.8); border-radius:16px; border:2px dashed var(--gray-200); }

.btn-confirm { width:100%; background:linear-gradient(135deg,var(--blue),#8b5cf6); color:#fff; border:none; border-radius:14px; padding:16px 24px; font-size:15px; font-weight:700; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:all .3s ease; box-shadow:0 8px 25px rgba(37,99,235,0.3); }
.btn-confirm:hover { transform:translateY(-2px); box-shadow:0 12px 35px rgba(37,99,235,0.4); }
.btn-cancel { display:block; margin-top:12px; text-align:center; width:100%; background:var(--gray-100); color:var(--gray-600); border:none; border-radius:14px; padding:14px 24px; font-size:14px; font-weight:600; text-decoration:none; transition:all .3s ease; }
.btn-cancel:hover { background:var(--gray-200); color:var(--gray-900); }

/* RDV LIST */
#rdvList { display:flex; flex-direction:column; gap:16px; }
.rdv-item { background:rgba(255,255,255,0.8); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.9); border-radius:var(--radius-lg); padding:24px; display:flex; align-items:center; gap:20px; transition:all .3s ease; flex-wrap:wrap; box-shadow:0 4px 15px rgba(0,0,0,0.02); }
.rdv-item:hover { transform:translateY(-2px); box-shadow:0 12px 30px rgba(0,0,0,0.06); }
.rdv-icon { width:56px; height:56px; border-radius:16px; background:linear-gradient(135deg,var(--blue-light),#e0e7ff); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:24px; color:var(--blue); box-shadow:0 4px 12px rgba(37,99,235,0.1); }
.rdv-info { flex:1; min-width:200px; }
.rdv-doc  { font-size:16px; font-weight:800; color:var(--gray-900); margin-bottom:2px; }
.rdv-spec { font-size:13px; color:var(--blue); font-weight:600; }
.rdv-time { font-size:13px; color:var(--gray-500); margin-top:6px; display:flex; align-items:center; gap:6px; font-weight:500; }
.rdv-status { display:inline-flex; align-items:center; gap:6px; background:var(--green-light); color:#047857; border-radius:999px; padding:6px 14px; font-size:12px; font-weight:700; white-space:nowrap; box-shadow:0 2px 5px rgba(16,185,129,0.1); }
.rdv-status-dot { width:6px; height:6px; border-radius:50%; background:var(--green); flex-shrink:0; box-shadow:0 0 5px var(--green); }
.rdv-actions { display:flex; gap:10px; }
.btn-action { padding:10px 16px; border-radius:12px; font-size:13px; font-weight:700; cursor:pointer; transition:all .3s ease; text-decoration:none; display:inline-flex; align-items:center; gap:8px; font-family:'Plus Jakarta Sans',sans-serif; border:none; }
.btn-edit   { background:var(--blue-light); color:var(--blue); box-shadow:0 2px 8px rgba(37,99,235,0.1); }
.btn-edit:hover   { background:var(--blue); color:#fff; transform:translateY(-1px); box-shadow:0 6px 15px rgba(37,99,235,0.2); }
.btn-delete { background:var(--red-light); color:var(--red); box-shadow:0 2px 8px rgba(239,68,68,0.1); }
.btn-delete:hover { background:var(--red); color:#fff; transform:translateY(-1px); box-shadow:0 6px 15px rgba(239,68,68,0.2); }
.empty-state { text-align:center; padding:80px 20px; color:var(--gray-500); background:rgba(255,255,255,0.5); border-radius:var(--radius-lg); border:1px dashed var(--gray-300); }
.empty-state i { font-size:48px; color:var(--gray-300); margin-bottom:16px; }
.empty-state strong { display:block; font-size:18px; font-weight:800; color:var(--gray-900); margin:0 0 8px; }

/* ── RDV SEARCH BAR ── */
.rdv-search-bar { display:flex; gap:16px; align-items:flex-end; flex-wrap:wrap; background:rgba(255,255,255,0.8); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.9); border-radius:var(--radius-lg); padding:20px 24px; margin-bottom:20px; box-shadow:0 4px 15px rgba(0,0,0,0.02); }
.rdv-sg { display:flex; flex-direction:column; gap:8px; flex:1; min-width:180px; }
.rdv-sg label { font-size:12px; font-weight:700; color:var(--gray-600); text-transform:uppercase; letter-spacing:.05em; }
.rdv-sg input, .rdv-sg select { padding:10px 14px; border:1.5px solid var(--gray-200); border-radius:12px; font-size:14px; font-family:'Plus Jakarta Sans',sans-serif; color:var(--gray-900); background:#fff; outline:none; transition:all .3s ease; height:44px; box-shadow:inset 0 2px 4px rgba(0,0,0,0.02); }
.rdv-sg input:focus, .rdv-sg select:focus { border-color:var(--blue); box-shadow:0 0 0 4px rgba(37,99,235,.15); }
.btn-rdv-search { height:44px; padding:0 24px; background:linear-gradient(135deg,var(--blue),#8b5cf6); color:#fff; border:none; border-radius:12px; font-size:14px; font-weight:700; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; white-space:nowrap; transition:all .3s ease; box-shadow:0 4px 15px rgba(37,99,235,0.2); }
.btn-rdv-search:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(37,99,235,0.3); }
.btn-rdv-reset { height:44px; padding:0 16px; background:var(--gray-100); color:var(--gray-600); border:none; border-radius:12px; font-size:14px; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; font-weight:600; white-space:nowrap; text-decoration:none; display:inline-flex; align-items:center; transition:all .3s ease; }
.btn-rdv-reset:hover { background:var(--gray-200); color:var(--gray-900); }
.rdv-result-info { font-size:13px; color:var(--gray-500); margin-bottom:12px; font-weight:500; }
.rdv-sort-link { color:var(--gray-600); text-decoration:none; font-weight:700; font-size:13px; display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:8px; transition:all .3s ease; }
.rdv-sort-link:hover, .rdv-sort-link.active { background:var(--blue-light); color:var(--blue); }

/* ── ÉTOILES ── */
.stars { display:inline-flex; gap:2px; }
.star { font-size:15px; color:#e2e8f0; }
.star.on { color:var(--orange); text-shadow:0 0 5px rgba(245,158,11,0.3); }
.doc-rating { font-size:13px; color:var(--gray-600); margin:6px 0 16px; display:flex; align-items:center; justify-content:center; gap:8px; }
.doc-rating strong { font-weight:800; color:var(--gray-900); font-size:14px; }

/* ── BOUTON ÉVALUER ── */
.btn-eval { padding:8px 16px; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer;
            background:var(--orange-light); color:var(--orange); border:none;
            font-family:'Plus Jakarta Sans',sans-serif; transition:all .3s ease; }
.btn-eval:hover { background:var(--orange); color:#fff; transform:translateY(-1px); box-shadow:0 4px 12px rgba(245,158,11,0.2); }
.btn-evaluated { padding:8px 16px; border-radius:10px; font-size:13px; font-weight:700;
                 background:var(--green-light); color:var(--green);
                 border:none; cursor:default; }

/* ── MODAL ÉVALUATION ── */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(8px);
                 z-index:2000; align-items:center; justify-content:center; padding:20px; }
.modal-overlay.open { display:flex; animation: fadeIn .3s ease; }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
.modal-box { background:#fff; border-radius:var(--radius-xl); padding:40px;
             max-width:500px; width:100%; box-shadow:0 24px 60px rgba(0,0,0,.2); position:relative;
             animation: slideUp .4s cubic-bezier(0.4, 0, 0.2, 1); }
@keyframes slideUp { from{opacity:0; transform:translateY(20px) scale(0.95)} to{opacity:1; transform:translateY(0) scale(1)} }
.modal-close { position:absolute; top:24px; right:24px; background:var(--gray-100); border:none; border-radius:50%; width:36px; height:36px;
               font-size:16px; cursor:pointer; color:var(--gray-600); display:flex; align-items:center; justify-content:center; transition:all .2s ease; }
.modal-close:hover { background:var(--gray-200); color:var(--gray-900); transform:rotate(90deg); }
.modal-title { font-size:20px; font-weight:800; color:var(--gray-900); margin-bottom:8px; letter-spacing:-0.5px; }
.modal-sub   { font-size:14px; color:var(--gray-600); margin-bottom:28px; }
.star-picker { display:flex; gap:10px; justify-content:center; margin-bottom:24px; }
.star-picker span { font-size:42px; cursor:pointer; color:#e2e8f0; transition:all .2s cubic-bezier(0.4, 0, 0.2, 1); user-select:none; }
.star-picker span.hover,.star-picker span.sel { color:var(--orange); transform:scale(1.15); filter:drop-shadow(0 4px 10px rgba(245,158,11,0.3)); }
.modal-textarea { width:100%; padding:14px 16px; border:1.5px solid var(--gray-200);
                  border-radius:12px; font-size:14px; font-family:'Plus Jakarta Sans',sans-serif;
                  resize:vertical; min-height:100px; outline:none; transition:all .3s ease; box-shadow:inset 0 2px 4px rgba(0,0,0,0.02); }
.modal-textarea:focus { border-color:var(--blue); box-shadow:0 0 0 4px rgba(37,99,235,.15); }
.modal-note-label { font-size:14px; font-weight:700; color:var(--orange); text-align:center;
                    margin-bottom:20px; min-height:20px; }
.btn-submit-eval { width:100%; padding:16px; background:linear-gradient(135deg,var(--blue),#8b5cf6); color:#fff; border:none;
                   border-radius:14px; font-size:15px; font-weight:700; cursor:pointer;
                   font-family:'Plus Jakarta Sans',sans-serif; margin-top:20px; transition:all .3s ease; box-shadow:0 8px 25px rgba(37,99,235,0.3); }
.btn-submit-eval:hover { transform:translateY(-2px); box-shadow:0 12px 35px rgba(37,99,235,0.4); }

@media (max-width:768px) {
    .navbar-medilink { padding:0 20px; }
    .user-info { display:none; }
    .hero { padding:60px 20px 80px; }
    .hero h1 { font-size:36px; }
    .main-content { padding:20px; }
    .form-row { grid-template-columns:1fr; }
    .rech-filters { grid-template-columns:1fr; }
    .rdv-item { flex-direction:column; align-items:flex-start; }
    .rdv-actions { width:100%; justify-content:space-between; }
    .time-picker-grid { grid-template-columns:repeat(2,1fr); }
}

/* ══ CHATBOT MÉDICAL ══ */
.chat-fab{position:fixed;bottom:32px;right:32px;z-index:4000;width:68px;height:68px;border-radius:50%;background:linear-gradient(135deg,var(--blue),#8b5cf6);color:#fff;border:none;cursor:pointer;box-shadow:0 10px 30px rgba(37,99,235,.4);font-size:28px;display:flex;align-items:center;justify-content:center;transition:all .3s cubic-bezier(0.4, 0, 0.2, 1);font-family:inherit;}
.chat-fab:hover{transform:scale(1.08) translateY(-4px); box-shadow:0 15px 40px rgba(37,99,235,.5);}
.chat-fab .notif{position:absolute;top:-4px;right:-4px;width:22px;height:22px;border-radius:50%;background:var(--red);border:2px solid #fff;font-size:11px;font-weight:800;color:#fff;display:flex;align-items:center;justify-content:center;animation:notifPulse 2s infinite;}
@keyframes notifPulse{0%,100%{transform:scale(1);box-shadow:0 0 0 0 rgba(239,68,68,0.7)}70%{transform:scale(1.1);box-shadow:0 0 0 10px rgba(239,68,68,0)}}
.chat-win{position:fixed;bottom:120px;right:32px;z-index:3999;width:420px;height:640px;background:rgba(255,255,255,0.95);backdrop-filter:blur(20px);border-radius:28px;box-shadow:0 30px 80px rgba(0,0,0,.15);display:none;flex-direction:column;overflow:hidden;border:1px solid rgba(255,255,255,0.8);}
.chat-win.open{display:flex;animation:chatSlide .4s cubic-bezier(.34,1.56,.64,1);}
@keyframes chatSlide{from{opacity:0;transform:translateY(40px) scale(.95);transform-origin:bottom right;}to{opacity:1;transform:none;transform-origin:bottom right;}}
.chat-head{background:linear-gradient(135deg,var(--navy) 0%,#1e293b 100%);padding:24px;display:flex;align-items:center;gap:16px;flex-shrink:0; position:relative; overflow:hidden;}
.chat-head::before { content:''; position:absolute; top:-50%; right:-20%; width:100%; height:200%; background:radial-gradient(circle,rgba(37,99,235,0.3) 0%,rgba(15,23,42,0) 60%); pointer-events:none; }
.chat-head-icon{width:48px;height:48px;border-radius:16px;background:rgba(255,255,255,.1);backdrop-filter:blur(5px);display:flex;align-items:center;justify-content:center;font-size:24px;border:1px solid rgba(255,255,255,.2); position:relative; z-index:2;}
.chat-head-text{flex:1; position:relative; z-index:2;}
.chat-head-name{font-size:18px;font-weight:800;color:#fff;letter-spacing:-.5px;margin-bottom:2px;}
.chat-head-status{font-size:12px;font-weight:600;color:rgba(255,255,255,.7);display:flex;align-items:center;gap:6px;}
.chat-status-dot{width:8px;height:8px;border-radius:50%;background:#34d399;flex-shrink:0;box-shadow:0 0 8px #34d399;}
.chat-close-btn{background:rgba(255,255,255,.1);border:none;color:#fff;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;transition:all .2s ease; position:relative; z-index:2;}
.chat-close-btn:hover{background:rgba(255,255,255,.25); transform:rotate(90deg);}
.chat-body{flex:1;overflow-y:auto;padding:24px;display:flex;flex-direction:column;gap:16px;background:transparent;}
.chat-body::-webkit-scrollbar{width:6px;}
.chat-body::-webkit-scrollbar-thumb{background:var(--gray-300);border-radius:3px;}
.chat-msg{display:flex;gap:12px;max-width:85%;}
.chat-msg.user{align-self:flex-end;flex-direction:row-reverse;}
.chat-msg.bot{align-self:flex-start;}
.chat-av{width:36px;height:36px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center; box-shadow:0 4px 10px rgba(0,0,0,0.05);}
.chat-msg.bot .chat-av{background:linear-gradient(135deg,var(--blue-light),#e0e7ff); color:var(--blue); font-size:18px;}
.chat-msg.user .chat-av{background:linear-gradient(135deg,var(--blue),#8b5cf6);color:#fff;font-size:12px;font-weight:800;}
.chat-bubble{padding:14px 18px;border-radius:20px;font-size:14px;line-height:1.6;max-width:100%;}
.chat-msg.bot .chat-bubble{background:#fff;color:var(--gray-900);border-bottom-left-radius:6px;border:1px solid rgba(0,0,0,0.05);box-shadow:0 4px 15px rgba(0,0,0,.03);}
.chat-msg.user .chat-bubble{background:linear-gradient(135deg,var(--blue),#8b5cf6);color:#fff;border-bottom-right-radius:6px;box-shadow:0 4px 15px rgba(37,99,235,.2);}
.doc-card-chat{background:var(--blue-light);border:1px solid rgba(26,86,219,.2);border-radius:16px;padding:16px;margin-top:12px;}
.doc-card-chat-name{font-size:15px;font-weight:800;color:var(--navy);margin-bottom:4px;}
.doc-card-chat-spec{font-size:13px;color:var(--blue);font-weight:600;margin-bottom:8px;}
.doc-card-chat-stars{display:flex;align-items:center;gap:4px;font-size:13px;margin-bottom:12px;color:var(--orange);}
.btn-rdv-chat{display:flex;align-items:center;justify-content:center;gap:8px;background:var(--blue);color:#fff;border:none;border-radius:10px;padding:12px 16px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .3s ease;font-family:inherit;width:100%; box-shadow:0 4px 12px rgba(37,99,235,0.3);}
.btn-rdv-chat:hover{background:var(--blue-dark); transform:translateY(-1px);}
.typing-indicator{display:none;align-self:flex-start;background:#fff;border-radius:20px;border-bottom-left-radius:6px;padding:16px 20px;gap:6px;border:1px solid rgba(0,0,0,0.05);box-shadow:0 4px 15px rgba(0,0,0,.03);}
.typing-indicator.show{display:flex;}
.t-dot{width:8px;height:8px;border-radius:50%;background:var(--blue);animation:tdot 1.4s infinite;}
.t-dot:nth-child(2){animation-delay:.2s}
.t-dot:nth-child(3){animation-delay:.4s}
@keyframes tdot{0%,60%,100%{transform:translateY(0);opacity:.3}30%{transform:translateY(-8px);opacity:1}}
.chat-suggestions{display:flex;gap:8px;flex-wrap:wrap;padding:12px 24px 16px;background:rgba(255,255,255,0.5);border-top:1px solid rgba(0,0,0,0.05);}
.chat-sug{padding:8px 16px;border-radius:999px;border:1px solid var(--gray-200);background:#fff;color:var(--gray-600);font-size:13px;font-weight:600;cursor:pointer;transition:all .2s ease;white-space:nowrap;font-family:inherit;}
.chat-sug:hover{background:var(--blue-light);color:var(--blue);border-color:var(--blue); transform:translateY(-1px);}
.chat-footer{display:flex;align-items:flex-end;gap:12px;padding:16px 24px 24px;border-top:1px solid rgba(0,0,0,0.05);background:#fff;flex-shrink:0;}
.chat-textarea{flex:1;padding:12px 16px;border:1.5px solid var(--gray-200);border-radius:24px;font-size:14px;font-family:inherit;outline:none;resize:none;max-height:100px;line-height:1.5;transition:all .3s ease;background:var(--gray-50);}
.chat-textarea:focus{border-color:var(--blue);background:#fff;box-shadow:0 0 0 4px rgba(37,99,235,.1);}
.chat-send-btn{width:48px;height:48px;border-radius:50%;flex-shrink:0;background:linear-gradient(135deg,var(--blue),#8b5cf6);color:#fff;border:none;cursor:pointer;font-size:20px;display:flex;align-items:center;justify-content:center;transition:all .3s ease;box-shadow:0 4px 15px rgba(37,99,235,.3);}
.chat-send-btn:hover{transform:scale(1.05) rotate(-10deg); box-shadow:0 8px 20px rgba(37,99,235,.4);}
.chat-send-btn:disabled{background:var(--gray-300); color:var(--gray-500); cursor:not-allowed;transform:none;box-shadow:none;}
@media(max-width:500px){.chat-win{width:calc(100vw - 32px);right:16px;bottom:100px;height:75vh;}.chat-fab{bottom:24px;right:24px;width:60px;height:60px;}}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar-medilink">
    <a href="homePatient.php" class="nav-logo"><span>MediLink</span></a>
    <div class="nav-user">
        <div class="user-info">
            <div class="user-avatar"><?php echo strtoupper(substr($patient_prenom,0,1).substr($patient_nom,0,1)); ?></div>
            <div class="user-text">
                <div class="user-name"><?php echo htmlspecialchars($patient_prenom.' '.$patient_nom); ?></div>
                <div class="user-role">Patient</div>
            </div>
        </div>
        <a href="/medilink_medicament/MediLink/views/rdv/front/logoutPatient.php" class="btn-logout">Déconnexion</a>
    </div>
</nav>

<!-- HERO -->
<div class="hero">
    <div class="hero-inner">
        <h1>Bienvenue, <?php echo htmlspecialchars($patient_prenom); ?> 👋</h1>
        <p>Consultez vos rendez-vous médicaux et réservez de nouvelles consultations.</p>
    </div>
</div>

<div class="main-content">

<?php if ($alert_message): ?>
    <div class="page-alert <?php echo $alert_type; ?>"><?php echo $alert_message; ?></div>
<?php endif; ?>

    <!-- ══ RECHERCHE MÉDECIN ══ -->
    <div class="section-heading">Rechercher un médecin</div>

    <!-- ══ CARTE GÉOLOCALISATION ══ -->
    <?php
    // Construire JSON des médecins pour Leaflet (coords depuis BD)
    // Construire JSON pour Leaflet depuis les vraies coords BD
    $medecins_json = [];
    foreach ($MEDECINS_STATIQUES as $m) {
        $st_j  = $eval_stats[$m['id']] ?? [];
        $moy_j = floatval($st_j['moyenne'] ?? 0);
        $tot_j = intval($st_j['total']   ?? 0);
        $medecins_json[] = [
            'id'       => $m['id'],
            'nom'      => $m['nom'],
            'spec'     => $m['spec'],
            'ville'    => $m['ville'],
            'adresse'  => $m['adresse'],
            'lat'      => $m['lat'],
            'lng'      => $m['lng'],
            'note'     => $moy_j,
            'avis'     => $tot_j,
            'color'    => $m['color'],
            'initials' => $m['initials'],
        ];
    }
    ?>
    <div class="map-section">
        <div class="map-header">
            <div class="map-title">🗺️ Localisation des médecins</div>
            <button class="btn-locate" id="btnLocate" onclick="localiserPatient()">📍 Ma position</button>
        </div>
        <div id="map"></div>
        <div class="map-legend">
            <div class="legend-item"><div class="legend-dot" style="background:#1a56db"></div> Médecin disponible</div>
            <div class="legend-item"><div class="legend-dot" style="background:#22c55e"></div> Votre position</div>
        </div>
    </div>

    <div class="rech-card">
        <form method="GET" action="homePatient.php">
            <div class="rech-filters">
                <div class="rech-form-group">
                    <label class="rech-label">Spécialité</label>
                    <select name="spec" class="rech-input" onchange="this.form.submit()">
                        <option value="">Toutes les spécialités</option>
                        <?php foreach ($specialites as $s): ?>
                            <option value="<?php echo htmlspecialchars($s); ?>" <?php echo $filtre_spec===$s?'selected':''; ?>>
                                <?php echo htmlspecialchars($s); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="rech-form-group">
                    <label class="rech-label">Ville</label>
                    <input type="text" name="ville" class="rech-input" placeholder="Ex: Tunis, Sfax…"
                           value="<?php echo htmlspecialchars($filtre_ville); ?>">
                </div>
                <div class="rech-form-group" style="flex:0 0 auto">
                    <label class="rech-label">🔃 Tri par note</label>
                    <select name="tri_note" class="rech-input" style="height:40px">
                        <option value=""   <?php echo $filtre_tri===''    ?'selected':''; ?>>— Par défaut</option>
                        <option value="desc" <?php echo $filtre_tri==='desc'?'selected':''; ?>>↓ Meilleures notes</option>
                        <option value="asc"  <?php echo $filtre_tri==='asc' ?'selected':''; ?>>↑ Notes croissantes</option>
                    </select>
                </div>
                <button type="submit" class="btn-rech-reset">🔍 Chercher</button>
            </div>
        </form>
        <div class="rech-tags">
            <a href="homePatient.php" class="rech-tag <?php echo !$filtre_spec?'active':''; ?>">Tous</a>
            <?php foreach ($specialites as $s): ?>
                <a href="homePatient.php?spec=<?php echo urlencode($s); ?>"
                   class="rech-tag <?php echo $filtre_spec===$s?'active':''; ?>">
                    <?php echo htmlspecialchars($s); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="rech-info">
            <strong><?php echo count($med_filtres); ?></strong>
            médecin<?php echo count($med_filtres)>1?'s':''; ?> trouvé<?php echo count($med_filtres)>1?'s':''; ?>
        </div>
        <div class="rech-grid">
            <?php if (empty($med_filtres)): ?>
                <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--gray-400);">
                    <div style="font-size:32px;margin-bottom:10px">🔍</div>
                    <strong>Aucun résultat</strong>
                </div>
            <?php else: ?>
                <?php foreach ($med_filtres as $m):
                    $st_m  = $eval_stats[$m['id']] ?? [];
                    $moy_m = floatval($st_m['moyenne'] ?? 0);
                    $tot_m = intval($st_m['total']   ?? 0);
                    $isSel_m = ($selected_medecin_id == $m['id']);
                ?>
                    <div class="rech-doc-card <?php echo $isSel_m ? 'selected-card' : ''; ?>">
                        <div class="rech-avatar" style="background:<?php echo $m['bg']; ?>;color:<?php echo $m['color']; ?>">
                            <?php echo $m['initials']; ?>
                        </div>
                        <div class="rech-doc-name"><?php echo htmlspecialchars($m['nom']); ?></div>
                        <div class="rech-doc-spec"><?php echo htmlspecialchars($m['spec']); ?></div>
                        <div class="rech-doc-city">📍 <?php echo htmlspecialchars($m['ville']); ?></div>
                        <!-- Note évaluations réelles -->
                        <div class="rech-doc-rating">
                            <span class="rech-stars">
                                <?php for ($s=1;$s<=5;$s++): ?>
                                    <span class="rstar <?php echo $s<=$moy_m?'on':''; ?>">★</span>
                                <?php endfor; ?>
                            </span>
                            <?php if ($tot_m > 0): ?>
                                <span class="rech-note-val"><?php echo number_format($moy_m,1); ?></span>
                                <span class="rech-note-count">(<?php echo $tot_m; ?> avis)</span>
                            <?php else: ?>
                                <span class="rech-note-empty">Pas encore d'avis</span>
                            <?php endif; ?>
                        </div>
                        <div class="rech-doc-meta">
                            <div class="rech-meta-item"><strong><?php echo $m['exp']; ?></strong>Exp.</div>
                        </div>
                        <a href="homePatient.php?medecin_id=<?php echo $m['id']; ?>&date=<?php echo urlencode($selected_date); ?>#formRDV"
                           class="btn-rech-rdv <?php echo $isSel_m?'selected':''; ?>">
                            <?php echo $isSel_m ? '✓ Sélectionné' : 'Sélectionner'; ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>


    <!-- ══ FORMULAIRE RDV ══ -->
    <!-- Banner confirmation sélection -->
    <?php if ($selected_medecin_id > 0):
        $doc_sel = null;
        foreach ($medecins as $d) { if ($d['id'] == $selected_medecin_id) { $doc_sel = $d; break; } }
    ?>
    <?php if ($doc_sel): ?>
    <div class="select-banner show" id="selectBanner">
        <div class="select-banner-icon">👨‍⚕️</div>
        <div class="select-banner-text">
            <div class="select-banner-name">✓ <?php echo htmlspecialchars($doc_sel['nom']); ?> sélectionné</div>
            <div class="select-banner-sub"><?php echo htmlspecialchars($doc_sel['specialite']); ?> · Choisissez maintenant une date et un créneau</div>
        </div>
        <a href="#formRDV" class="select-banner-ok">Réserver ↓</a>
    </div>
    <?php endif; ?>
    <?php endif; ?>
    <div class="section-heading" id="formRDV">
        <?php echo $edit_rdv ? '✏️ Modifier le rendez-vous' : 'Réserver un nouveau rendez-vous'; ?>
    </div>
    <div class="form-card">
        <form method="POST" action="homePatient.php#formRDV">
            <input type="hidden" name="action" value="<?php echo $edit_rdv?'update':'add'; ?>">
            <?php if ($edit_rdv): ?>
                <input type="hidden" name="rdv_id" value="<?php echo $edit_rdv['id']; ?>">
            <?php endif; ?>

            <div class="form-row">
                <!-- Médecin -->
                <div class="form-group">
                    <label>Médecin *</label>
                    <select name="medecin_id" id="selMedecin"
                        onchange="location.href='homePatient.php?medecin_id='+this.value+'&date='+document.getElementById('selDate').value+'<?php echo $edit_rdv?'&edit='.$edit_rdv['id']:''; ?>#formRDV'">
                        <option value="">-- Sélectionner un médecin --</option>
                        <?php foreach ($medecins as $doc): ?>
                            <option value="<?php echo $doc['id']; ?>" <?php echo $selected_medecin_id==$doc['id']?'selected':''; ?>>
                                <?php echo htmlspecialchars($doc['nom'].' — '.$doc['specialite']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Date -->
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="date_rdv" id="selDate"
                           min="<?php echo $today; ?>" max="<?php echo $maxDate; ?>"
                           value="<?php echo htmlspecialchars($selected_date); ?>"
                           onchange="changerDate(this.value, document.getElementById('selMedecin').value, '<?php echo $edit_rdv ? $edit_rdv['id'] : ''; ?>')">
                </div>
            </div>

            <!-- Grille des créneaux -->
            <div class="form-group" style="margin-bottom:18px">
                <label>Créneau horaire *</label>
                <div class="time-picker-grid">
                <?php if (!$selected_medecin_id || !$selected_date): ?>
                    <div class="empty-slots">📅 Sélectionnez un médecin et une date pour voir les créneaux</div>
                <?php elseif (empty($slots)): ?>
                    <div class="empty-slots">⚠️ Aucun créneau disponible pour cette date</div>
                <?php else:
                    $heure_sel = $edit_rdv ? substr($edit_rdv['heure_rdv'],0,5) : '';
                    foreach ($slots as $slot):
                        if ($slot['occupied']):
                            $sub = $slot['type']==='lunch' ? 'Pause' : 'Réservé';
                ?>
                        <div class="slot occupied">
                            <?php echo $slot['slot']; ?>
                            <small><?php echo $sub; ?></small>
                        </div>
                <?php   else:
                            $isSel = ($heure_sel === $slot['slot']);
                ?>
                        <label class="slot available <?php echo $isSel?'sel':''; ?>" style="cursor:pointer">
                            <input type="radio" name="heure_rdv" value="<?php echo $slot['slot']; ?>"
                                   <?php echo $isSel?'checked':''; ?> style="display:none"
                                   onclick="document.querySelectorAll('.slot.available').forEach(s=>s.classList.remove('sel'));this.closest('label').classList.add('sel')">
                            <?php echo $slot['slot']; ?>
                        </label>
                <?php   endif;
                    endforeach;
                endif; ?>
                </div>
            </div>

            <button type="submit" class="btn-confirm">
                <?php echo $edit_rdv?'Modifier le rendez-vous':'✓ Confirmer le rendez-vous'; ?>
            </button>
            <?php if ($edit_rdv): ?>
                <a href="homePatient.php" class="btn-cancel">❌ Annuler la modification</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- ══ MES RENDEZ-VOUS ══ -->
    <div class="section-heading" id="mesRDV">📋 Mes rendez-vous</div>

    <!-- Barre de recherche & tri -->
    <form method="GET" action="homePatient.php#mesRDV">
        <div class="rdv-search-bar">
            <div class="rdv-sg">
                <label>👨‍⚕️ Nom du médecin</label>
                <input type="text" name="rdv_medecin"
                       placeholder="Ex: Ahmed, Sara…"
                       value="<?php echo htmlspecialchars($rdv_search_medecin); ?>">
            </div>
            <div class="rdv-sg">
                <label>📅 Date du RDV</label>
                <input type="date" name="rdv_date"
                       value="<?php echo htmlspecialchars($rdv_search_date); ?>">
            </div>
            <div class="rdv-sg" style="flex:0 0 auto">
                <label>🔃 Tri par date</label>
                <select name="rdv_tri">
                    <option value="desc" <?php echo $rdv_tri==='desc'?'selected':''; ?>>↓ Plus récent</option>
                    <option value="asc"  <?php echo $rdv_tri==='asc' ?'selected':''; ?>>↑ Plus ancien</option>
                </select>
            </div>
            <button type="submit" class="btn-rdv-search">Rechercher</button>
            <a href="homePatient.php#mesRDV" class="btn-rdv-reset">✕ Réinitialiser</a>
        </div>
    </form>

    <?php
    // ── Filtrage ──
    $rdvs_affiches = $mes_rdvs;

    if ($rdv_search_medecin !== '') {
        $rdvs_affiches = array_filter($rdvs_affiches, function($r) use ($rdv_search_medecin) {
            return stripos($r['medecin_nom'], $rdv_search_medecin) !== false;
        });
    }
    if ($rdv_search_date !== '') {
        $rdvs_affiches = array_filter($rdvs_affiches, function($r) use ($rdv_search_date) {
            return $r['date_rdv'] === $rdv_search_date;
        });
    }

    // ── Tri par date + heure ──
    usort($rdvs_affiches, function($a, $b) use ($rdv_tri) {
        $da = $a['date_rdv'] . ' ' . $a['heure_rdv'];
        $db = $b['date_rdv'] . ' ' . $b['heure_rdv'];
        return $rdv_tri === 'asc' ? strcmp($da, $db) : strcmp($db, $da);
    });

    $total_rdvs = count($rdvs_affiches);
    ?>

    <div class="rdv-result-info">
        <strong><?php echo $total_rdvs; ?></strong>
        rendez-vous<?php if ($rdv_search_medecin || $rdv_search_date): ?> — filtre actif<?php endif; ?>
        &nbsp;·&nbsp;
        <?php
        $next = $rdv_tri === 'asc' ? 'desc' : 'asc';
        $icon = $rdv_tri === 'asc' ? '↑ Plus ancien' : '↓ Plus récent';
        $q = http_build_query([
            'rdv_medecin' => $rdv_search_medecin,
            'rdv_date'    => $rdv_search_date,
            'rdv_tri'     => $next,
        ]);
        ?>
        <a href="homePatient.php?<?php echo $q; ?>#mesRDV"
           class="rdv-sort-link active">
            Trier <?php echo $rdv_tri === 'asc' ? '↓ Plus récent' : '↑ Plus ancien'; ?>
        </a>
    </div>

    <div id="rdvList">
        <?php if (empty($rdvs_affiches)): ?>
            <div class="empty-state">
                <div style="font-size:28px;margin-bottom:10px"><?php echo ($rdv_search_medecin || $rdv_search_date) ? '🔍' : '📭'; ?></div>
                <strong><?php echo ($rdv_search_medecin || $rdv_search_date) ? 'Aucun résultat' : 'Aucun rendez-vous'; ?></strong>
                <p style="color:var(--gray-400);margin-top:6px">
                    <?php echo ($rdv_search_medecin || $rdv_search_date)
                        ? 'Aucun rendez-vous ne correspond à votre recherche.'
                        : 'Réservez votre premier rendez-vous ci-dessus !'; ?>
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($rdvs_affiches as $rdv): ?>
                <div class="rdv-item">
                    <div class="rdv-icon">📅</div>
                    <div class="rdv-info">
                        <div class="rdv-doc"><?php echo htmlspecialchars($rdv['medecin_nom']); ?></div>
                        <div class="rdv-spec"><?php echo htmlspecialchars($rdv['specialite']); ?></div>
                        <div class="rdv-time">
                            <?php echo formatDateFR($rdv['date_rdv']); ?> &nbsp;·&nbsp; <?php echo htmlspecialchars(substr($rdv['heure_rdv'],0,5)); ?>
                        </div>
                    </div>
                    <div class="rdv-status">
                        <span class="rdv-status-dot"></span>
                        <?php echo ucfirst(htmlspecialchars($rdv['statut'])); ?>
                    </div>
                    <div class="rdv-actions">
                        <?php
                        $est_passe = strtotime($rdv['date_rdv']) < mktime(0,0,0);
                        $deja_evalue = isset($rdvs_evalues[$rdv['id']]);
                        ?>
                        <?php if ($est_passe && !$deja_evalue): ?>
                            <button class="btn-eval"
                                    onclick="ouvrirModal(<?php echo $rdv['id']; ?>, '<?php echo addslashes($rdv['medecin_nom']); ?>', '<?php echo htmlspecialchars(substr($rdv['heure_rdv'],0,5)); ?>', '<?php echo $rdv['date_rdv']; ?>')">
                                ⭐ Évaluer
                            </button>
                        <?php elseif ($est_passe && $deja_evalue): ?>
                            <span class="btn-evaluated">✅ Évalué</span>
                        <?php else: ?>
                            <a href="homePatient.php?edit=<?php echo $rdv['id']; ?>#formRDV"
                               class="btn-action btn-edit">✏️ Modifier</a>
                            <form method="POST" action="homePatient.php" style="display:inline"
                                  onsubmit="return confirm('Supprimer ce rendez-vous ?')">
                                <input type="hidden" name="action"  value="delete">
                                <input type="hidden" name="rdv_id" value="<?php echo $rdv['id']; ?>">
                                <button type="submit" class="btn-action btn-delete">🗑️ Supprimer</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div><!-- .main-content -->

<!-- ══ SCRIPT LEAFLET CARTE ══ -->
<script>
const MEDECINS_MAP = <?php echo json_encode(array_values($medecins_json)); ?>;

let map, playerMarker, routeLayer = null;
let patientLat = null, patientLng = null;
const medecinMarkers = {}; // id → marker

// ── Initialiser la carte ──
function initMap() {
    map = L.map("map", { zoomControl:true }).setView([36.8, 10.18], 7);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap contributors",
        maxZoom: 18
    }).addTo(map);

    MEDECINS_MAP.forEach(function(m) {
        const stars    = "★".repeat(Math.round(m.note)) + "☆".repeat(5 - Math.round(m.note));
        const noteText = m.avis > 0
            ? `<span style="color:#f59e0b">${stars}</span> <strong>${m.note.toFixed(1)}</strong> (${m.avis} avis)`
            : `<span style="color:#94a3b8;font-size:11px">Pas encore d'avis</span>`;

        const icon = L.divIcon({
            className: "",
            html: `<div style="
                width:44px;height:44px;border-radius:50%;
                background:${m.color};color:#fff;
                display:flex;align-items:center;justify-content:center;
                font-weight:700;font-size:13px;
                border:3px solid #fff;
                box-shadow:0 2px 10px rgba(0,0,0,.3);
                font-family:'Plus Jakarta Sans',sans-serif;
                cursor:pointer;
            ">${m.initials}</div>`,
            iconSize: [44,44], iconAnchor: [22,22], popupAnchor: [0,-26],
        });

        const marker = L.marker([m.lat, m.lng], { icon }).addTo(map);
        medecinMarkers[m.id] = marker;

        // Bouton "Itinéraire" dans la popup — visible seulement si position connue
        marker.bindPopup(buildPopup(m, null), { maxWidth:230 });

        // Au clic sur le marqueur : reconstruire popup avec distance + bouton itinéraire
        marker.on("click", function() {
            const dist = (patientLat !== null)
                ? haversine(patientLat, patientLng, m.lat, m.lng)
                : null;
            marker.setPopupContent(buildPopup(m, dist));
        });
    });
}

// ── Construire le contenu HTML de la popup ──
function buildPopup(m, dist) {
    const stars    = "★".repeat(Math.round(m.note)) + "☆".repeat(5 - Math.round(m.note));
    const noteText = m.avis > 0
        ? `<span style="color:#f59e0b">${stars}</span> <strong>${m.note.toFixed(1)}</strong> (${m.avis} avis)`
        : `<span style="color:#94a3b8;font-size:11px">Pas encore d'avis</span>`;

    const distHtml = dist !== null
        ? `<div style="font-size:12px;color:#0da271;font-weight:600;margin-bottom:8px">📏 ${dist} km de vous</div>`
        : "";

    const routeBtn = (patientLat !== null)
        ? `<button onclick="afficherItineraire(${m.id}, ${m.lat}, ${m.lng})"
               style="display:block;width:100%;text-align:center;
                      background:#1a56db;color:#fff;
                      padding:7px 12px;border-radius:7px;font-size:12px;
                      font-weight:600;border:none;cursor:pointer;margin-top:6px;">
               🗺️ Itinéraire
           </button>`
        : `<div style="font-size:11px;color:#94a3b8;text-align:center;margin-top:6px;font-style:italic">
               Activez votre position pour l'itinéraire
           </div>`;

    return `
        <div style="font-family:'Plus Jakarta Sans',sans-serif;min-width:190px;padding:4px">
            <div style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:2px">${m.nom}</div>
            <div style="font-size:12px;color:#475569;margin-bottom:4px">${m.spec}</div>
            <div style="font-size:12px;color:#475569;margin-bottom:4px">📍 ${m.ville}</div>
            ${m.adresse ? `<div style="font-size:11px;color:#94a3b8;margin-bottom:8px;font-style:italic">🏥 ${m.adresse}</div>` : ""}
            <div style="font-size:12px;margin-bottom:8px">${noteText}</div>
            ${distHtml}
            <a href="homePatient.php?medecin_id=${m.id}#formRDV"
               style="display:block;text-align:center;background:#ecfdf5;color:#0da271;
                      border:1px solid rgba(13,162,113,.3);
                      padding:6px 12px;border-radius:7px;font-size:12px;
                      font-weight:600;text-decoration:none;margin-bottom:6px;">
                ✓ Sélectionner
            </a>
            ${routeBtn}
        </div>`;
}

// ── Géolocalisation du patient ──
function localiserPatient() {
    const btn = document.getElementById("btnLocate");
    if (!navigator.geolocation) {
        alert("La géolocalisation n'est pas supportée par votre navigateur.");
        return;
    }
    btn.disabled = true;
    btn.innerHTML = "⏳ Localisation...";

    navigator.geolocation.getCurrentPosition(
        function(pos) {
            patientLat = pos.coords.latitude;
            patientLng = pos.coords.longitude;

            if (playerMarker) map.removeLayer(playerMarker);

            const patientIcon = L.divIcon({
                className: "",
                html: `<div style="
                    width:20px;height:20px;border-radius:50%;
                    background:#22c55e;border:3px solid #fff;
                    box-shadow:0 0 0 4px rgba(34,197,94,.35);
                "></div>`,
                iconSize: [20,20], iconAnchor: [10,10],
            });

            playerMarker = L.marker([patientLat, patientLng], { icon: patientIcon })
                .addTo(map)
                .bindPopup("<strong>📍 Votre position</strong>")
                .openPopup();

            // Recalculer distances dans tous les marqueurs
            MEDECINS_MAP.forEach(function(m) {
                m.distance = haversine(patientLat, patientLng, m.lat, m.lng);
            });

            // Zoom pour tout voir
            const allPoints = MEDECINS_MAP.map(m => [m.lat, m.lng]);
            allPoints.push([patientLat, patientLng]);
            map.fitBounds(allPoints, { padding:[40,40] });

            btn.disabled  = false;
            btn.innerHTML = "✅ Positionné";

            // Afficher automatiquement l'itinéraire vers le médecin sélectionné (si déjà choisi)
            const selId = <?php echo $selected_medecin_id ?: 0; ?>;
            if (selId > 0) {
                const sel = MEDECINS_MAP.find(m => m.id == selId);
                if (sel) afficherItineraire(sel.id, sel.lat, sel.lng);
            }
        },
        function(err) {
            alert("Impossible d'obtenir votre position : " + err.message);
            btn.disabled  = false;
            btn.innerHTML = "📍 Ma position";
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

// ── Afficher l'itinéraire via OSRM (API gratuite) ──
async function afficherItineraire(medecinId, destLat, destLng) {
    if (patientLat === null) {
        alert("Veuillez d'abord activer votre position avec le bouton « 📍 Ma position ».");
        return;
    }

    // Fermer la popup pour bien voir la carte
    map.closePopup();

    // Supprimer l'ancien itinéraire
    if (routeLayer) { map.removeLayer(routeLayer); routeLayer = null; }

    // Afficher indicateur de chargement sur la carte
    const loadDiv = document.createElement("div");
    loadDiv.id = "routeLoader";
    loadDiv.style.cssText = "position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(255,255,255,.9);padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;z-index:1000;box-shadow:0 4px 16px rgba(0,0,0,.15)";
    loadDiv.textContent = "🗺️ Calcul de l'itinéraire...";
    document.getElementById("map").appendChild(loadDiv);

    try {
        // API OSRM — gratuite, open source, pas de clé requise
        const url = `https://router.project-osrm.org/route/v1/driving/${patientLng},${patientLat};${destLng},${destLat}?overview=full&geometries=geojson`;
        const res  = await fetch(url);
        const data = await res.json();

        document.getElementById("map").removeChild(loadDiv);

        if (data.code !== "Ok" || !data.routes || !data.routes.length) {
            alert("Impossible de calculer l'itinéraire. Vérifiez votre connexion.");
            return;
        }

        const route    = data.routes[0];
        const distKm   = (route.distance / 1000).toFixed(1);
        const durMin   = Math.round(route.duration / 60);
        const geojson  = route.geometry;

        // Dessiner le tracé sur la carte
        routeLayer = L.geoJSON(geojson, {
            style: {
                color      : "#1a56db",
                weight     : 5,
                opacity    : 0.85,
                dashArray  : null,
                lineCap    : "round",
                lineJoin   : "round"
            }
        }).addTo(map);

        // Zoom sur l'itinéraire complet
        map.fitBounds(routeLayer.getBounds(), { padding:[40,40] });

        // Trouver le nom du médecin
        const med = MEDECINS_MAP.find(m => m.id == medecinId);

        // Afficher une popup d'info sur l'itinéraire
        const midPoint = geojson.coordinates[Math.floor(geojson.coordinates.length / 2)];
        L.popup({ closeButton: true, className:"route-popup" })
            .setLatLng([midPoint[1], midPoint[0]])
            .setContent(`
                <div style="font-family:'Plus Jakarta Sans',sans-serif;padding:4px;min-width:180px">
                    <div style="font-weight:700;font-size:13px;color:#0f172a;margin-bottom:6px">
                        🗺️ Itinéraire calculé
                    </div>
                    <div style="font-size:12px;color:#475569;margin-bottom:4px">
                        📍 Vers : <strong>${med ? med.nom : "Médecin"}</strong>
                    </div>
                    <div style="display:flex;gap:12px;margin-top:8px">
                        <div style="text-align:center;background:#eff4ff;border-radius:8px;padding:8px 12px;flex:1">
                            <div style="font-size:18px;font-weight:700;color:#1a56db">${distKm}</div>
                            <div style="font-size:10px;color:#6b7280">km</div>
                        </div>
                        <div style="text-align:center;background:#ecfdf5;border-radius:8px;padding:8px 12px;flex:1">
                            <div style="font-size:18px;font-weight:700;color:#0da271">${durMin}</div>
                            <div style="font-size:10px;color:#6b7280">min</div>
                        </div>
                    </div>
                    <button onclick="effacerItineraire()"
                        style="width:100%;margin-top:10px;padding:6px;background:#fee2e2;
                               color:#dc2626;border:1px solid #fecaca;border-radius:6px;
                               font-size:12px;font-weight:600;cursor:pointer;font-family:inherit">
                        ✕ Effacer l'itinéraire
                    </button>
                </div>
            `)
            .openOn(map);

    } catch(err) {
        if (document.getElementById("routeLoader")) {
            document.getElementById("map").removeChild(document.getElementById("routeLoader"));
        }
        console.error("OSRM error:", err);
        alert("Erreur lors du calcul de l'itinéraire.");
    }
}

// ── Effacer l'itinéraire ──
function effacerItineraire() {
    if (routeLayer) { map.removeLayer(routeLayer); routeLayer = null; }
    map.closePopup();
}

// ── Formule Haversine ──
function haversine(lat1, lng1, lat2, lng2) {
    const R    = 6371;
    const dLat = (lat2-lat1) * Math.PI/180;
    const dLng = (lng2-lng1) * Math.PI/180;
    const a    = Math.sin(dLat/2)*Math.sin(dLat/2) +
                 Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*
                 Math.sin(dLng/2)*Math.sin(dLng/2);
    return Math.round(R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)) * 10) / 10;
}

document.addEventListener("DOMContentLoaded", initMap);
</script>

<!-- ══ MODAL ÉVALUATION ══ -->
<div class="modal-overlay" id="evalModal">
    <div class="modal-box">
        <button class="modal-close" onclick="fermerModal()">✕</button>
        <div class="modal-title">⭐ Évaluer votre consultation</div>
        <div class="modal-sub" id="modal-sub">Dr. — · —</div>

        <div class="star-picker" id="starPicker">
            <span data-v="1">★</span>
            <span data-v="2">★</span>
            <span data-v="3">★</span>
            <span data-v="4">★</span>
            <span data-v="5">★</span>
        </div>
        <div class="modal-note-label" id="noteLabel">Choisissez une note</div>

        <form method="POST" action="homePatient.php#mesRDV" id="evalForm">
            <input type="hidden" name="action"  value="evaluer">
            <input type="hidden" name="rdv_id"  id="modal-rdv-id">
            <input type="hidden" name="note"    id="modal-note" value="0">
            <textarea name="commentaire" class="modal-textarea"
                      placeholder="Commentaire facultatif (expérience, attente, qualité de la consultation…)"></textarea>
            <button type="submit" class="btn-submit-eval" id="btnSubmitEval" disabled>
                Envoyer mon évaluation
            </button>
        </form>
    </div>
</div>

<script>
/* ── Validation date côté client : lundi–samedi uniquement ── */
function changerDate(dateVal, medecinId, editId) {
    if (!dateVal) return;

    // Vérifier dimanche (getDay() = 0)
    const [y, m, d] = dateVal.split('-').map(Number);
    const date = new Date(y, m - 1, d);
    const jour = date.getDay(); // 0=dim, 1=lun, ..., 6=sam

    if (jour === 0) {
        alert('❌ Le cabinet est fermé le dimanche.\nVeuillez choisir du lundi au samedi.');
        // Remettre la valeur précédente ou vider le champ
        document.getElementById('selDate').value = '';
        return;
    }

    // Date passée
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    if (date < today) {
        alert('❌ Impossible de choisir une date passée.');
        document.getElementById('selDate').value = '';
        return;
    }

    // Date valide → rediriger pour charger les créneaux
    let url = 'homePatient.php?medecin_id=' + medecinId + '&date=' + dateVal;
    if (editId) url += '&edit=' + editId;
    url += '#formRDV';
    location.href = url;
}

/* ── Modal évaluation ── */
const LABELS = ["","😞 Très mauvais","😐 Insuffisant","🙂 Passable","😊 Bien","🤩 Excellent !"];
let selectedNote = 0;

function ouvrirModal(rdvId, medecin, heure, date) {
    selectedNote = 0;
    document.getElementById("modal-rdv-id").value = rdvId;
    document.getElementById("modal-note").value   = 0;
    document.getElementById("modal-sub").textContent = medecin + " · " + heure + " · " + formatDateModal(date);
    document.getElementById("noteLabel").textContent = "Choisissez une note";
    document.getElementById("btnSubmitEval").disabled = true;
    document.querySelectorAll("#starPicker span").forEach(s => s.classList.remove("sel","hover"));
    document.querySelector(".modal-textarea").value = "";
    document.getElementById("evalModal").classList.add("open");
}

function fermerModal() {
    document.getElementById("evalModal").classList.remove("open");
}

// Fermer en cliquant sur l'overlay
document.getElementById("evalModal").addEventListener("click", function(e) {
    if (e.target === this) fermerModal();
});

// Étoiles interactives
document.querySelectorAll("#starPicker span").forEach(function(star) {
    star.addEventListener("mouseover", function() {
        const v = parseInt(this.dataset.v);
        document.querySelectorAll("#starPicker span").forEach(s => {
            s.classList.toggle("hover", parseInt(s.dataset.v) <= v);
        });
    });
    star.addEventListener("mouseout", function() {
        document.querySelectorAll("#starPicker span").forEach(s => s.classList.remove("hover"));
    });
    star.addEventListener("click", function() {
        selectedNote = parseInt(this.dataset.v);
        document.getElementById("modal-note").value = selectedNote;
        document.getElementById("noteLabel").textContent = LABELS[selectedNote];
        document.getElementById("btnSubmitEval").disabled = false;
        document.querySelectorAll("#starPicker span").forEach(s => {
            s.classList.toggle("sel", parseInt(s.dataset.v) <= selectedNote);
        });
    });
});

function formatDateModal(dateStr) {
    const [y,m,d] = dateStr.split("-");
    const jours = ["Dim","Lun","Mar","Mer","Jeu","Ven","Sam"];
    const mois  = ["jan","fév","mar","avr","mai","juin","juil","août","sep","oct","nov","déc"];
    const dt    = new Date(+y, +m-1, +d);
    return jours[dt.getDay()] + " " + +d + " " + mois[+m-1] + ". " + y;
}
</script>

<!-- ══ CHATBOT MÉDICAL ══ -->






<button class="chat-fab" id="chatFab" onclick="toggleChat()" title="Parlez à notre assistant médical">
    🩺
    <span class="notif" id="chatNotif">1</span>
</button>

<div class="chat-win" id="chatWin">
    <div class="chat-head">
        <div class="chat-head-icon">🤖</div>
        <div class="chat-head-text">
            <div class="chat-head-name">Assistant MediLink</div>
            <div class="chat-head-status">
                <span class="chat-status-dot"></span>
                Prêt à vous orienter
            </div>
        </div>
        <button class="chat-close-btn" onclick="toggleChat()">✕</button>
    </div>

    <div class="chat-body" id="chatBody"></div>

    <div class="typing-indicator" id="typingInd">
        <div class="t-dot"></div>
        <div class="t-dot"></div>
        <div class="t-dot"></div>
    </div>

    <div class="chat-suggestions" id="chatSugs"></div>

    <div class="chat-footer">
        <textarea class="chat-textarea" id="chatTA"
            placeholder="Décrivez vos symptômes..."
            rows="1"
            onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMsg();}"
            oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"></textarea>
        <button class="chat-send-btn" id="chatSendBtn" onclick="sendMsg()">➤</button>
    </div>
</div>

<script>
// ── Données PHP → JS ──
const PAT_NOM = <?php echo json_encode(trim($patient_prenom . ' ' . $patient_nom)); ?>;
const PAT_INITIALS = PAT_NOM.split(' ').map(w => w[0] || '').join('').toUpperCase().slice(0, 2);

const MEDECINS_IA = <?php
    $m_ia = [];
    foreach ($medecins as $d) {
        $st = $eval_stats[$d['id']] ?? [];
        $m_ia[] = [
            'id'      => $d['id'],
            'nom'     => $d['nom'],
            'spec'    => $d['specialite'],
            'ville'   => $d['ville']   ?? 'Tunis',
            'note'    => round(floatval($st['moyenne'] ?? 0), 1),
            'avis'    => intval($st['total'] ?? 0),
        ];
    }
    echo json_encode($m_ia);
?>;

// ── État ──
let hist = [];
let opened = false;
let ready  = false;

// ── Prompt système ──
function sysPrompt() {
    const liste = MEDECINS_IA.map(m => {
        const n = m.note > 0 ? ` | Note: ${m.note}/5 (${m.avis} avis)` : ' | Pas encore évalué';
        return `• ${m.nom} — ${m.spec} — ${m.ville}${n} [ID=${m.id}]`;
    }).join('\n');

    // Extraire les spécialités disponibles pour le prompt
    const specsDisponibles = [...new Set(MEDECINS_IA.map(m => m.spec))].join(', ');

    return `Tu es l'assistant médical intelligent de la plateforme MediLink en Tunisie.
Tu aides le patient "${PAT_NOM}" à trouver le médecin approprié selon ses symptômes.

MÉDECINS DISPONIBLES SUR MEDILINK (LISTE COMPLÈTE ET DÉFINITIVE):
${liste}

SPÉCIALITÉS DISPONIBLES: ${specsDisponibles}

INSTRUCTIONS STRICTES — RESPECTER ABSOLUMENT:
1. Accueille chaleureusement le patient dès le premier message.
2. Écoute attentivement ses symptômes.
3. Pose 1 ou 2 questions courtes pour mieux comprendre si nécessaire.
4. Détermine quelle spécialité médicale est nécessaire pour les symptômes du patient.
5. RÈGLE FONDAMENTALE — NE JAMAIS VIOLER:
   - Si la spécialité nécessaire EST dans la liste → recommande le médecin correspondant avec le format ##RDV##.
   - Si la spécialité nécessaire N'EST PAS dans la liste → dis EXACTEMENT:
     "Pour vos symptômes, vous avez besoin d'un(e) [SPECIALITE NÉCESSAIRE]. Malheureusement, MediLink ne dispose pas encore de ce spécialiste. Nous vous conseillons de consulter un(e) [SPECIALITE NÉCESSAIRE] dans un autre établissement."
   - Dans ce cas : NE PROPOSE AUCUN autre médecin de la liste. JAMAIS.
   - N'essaie JAMAIS de remplacer une spécialité manquante par une autre qui "pourrait convenir".
6. Quand tu recommandes un médecin disponible, utilise OBLIGATOIREMENT ce format exact:
   ##RDV##ID##NOM##SPECIALITE##
   Exemple: ##RDV##1##Dr. Ahmed##Cardiologue##
7. Ne pose JAMAIS de diagnostic médical — tu orientes seulement.
8. En cas d'urgence grave (douleur thoracique intense, difficulté à respirer), dis d'appeler le 15 (SAMU Tunisie) immédiatement.
9. Réponds toujours en français, de façon empathique et concise (2-3 phrases max).`;
}

function toggleChat() {
    opened = !opened;
    document.getElementById('chatWin').classList.toggle('open', opened);
    document.getElementById('chatNotif').style.display = 'none';

    if (opened && !ready) {
        ready = true;
        welcome();
    }
    if (opened) setTimeout(() => document.getElementById('chatTA').focus(), 150);
}

function welcome() {
    addMsg('bot',
        `Bonjour **${PAT_NOM}** ! 👋\n\n` +
        `Je suis votre assistant médical. Décrivez-moi vos symptômes et je vous orienterai vers le médecin le plus adapté parmi nos spécialistes.`
    );
    showSugs([
        "J'ai mal à la tête",
        "Douleurs dans la poitrine",
        "Problème de peau",
        "Mal aux dents",
        "Douleurs abdominales"
    ]);
}

function addMsg(role, text) {
    const body = document.getElementById('chatBody');
    const div  = document.createElement('div');
    div.className = 'chat-msg ' + role;

    const av = role === 'bot'
        ? `<div class="chat-av">🤖</div>`
        : `<div class="chat-av">${PAT_INITIALS}</div>`;

    let html = text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\n/g, '<br>');

    html = html.replace(/##RDV##(\d+)##([^#]+)##([^#]+)##/g, (_, id, nom, spec) => {
        const med = MEDECINS_IA.find(m => m.id == id);
        const stars = med && med.note > 0
            ? '★'.repeat(Math.round(med.note)) + '☆'.repeat(5 - Math.round(med.note))
            : '☆☆☆☆☆';
        const noteText = med && med.note > 0
            ? `${stars} ${med.note}/5 (${med.avis} avis)`
            : 'Pas encore évalué';
        const ville = med ? med.ville : '';
        return `
            <div class="doc-card-chat">
                <div class="doc-card-chat-name">👨‍⚕️ ${nom}</div>
                <div class="doc-card-chat-spec">${spec} · 📍 ${ville}</div>
                <div class="doc-card-chat-stars">${noteText}</div>
                <a href="homePatient.php?medecin_id=${id}#formRDV" class="btn-rdv-chat">
                    📅 Prendre rendez-vous
                </a>
            </div>`;
    });

    div.innerHTML = `${av}<div class="chat-bubble">${html}</div>`;
    body.appendChild(div);
    body.scrollTop = body.scrollHeight;

    if (!opened && role === 'bot') {
        document.getElementById('chatNotif').style.display = 'flex';
    }
}

function showSugs(list) {
    document.getElementById('chatSugs').innerHTML = list
        .map(s => `<button class="chat-sug" onclick="pickSug('${s.replace(/'/g, "\\'")}')">${s}</button>`)
        .join('');
}
function pickSug(t) {
    document.getElementById('chatTA').value = t;
    document.getElementById('chatSugs').innerHTML = '';
    sendMsg();
}


async function sendMsg() {
    const ta   = document.getElementById('chatTA');
    const text = ta.value.trim();
    if (!text) return;

    addMsg('user', text);
    ta.value = '';
    ta.style.height = 'auto';
    document.getElementById('chatSendBtn').disabled = true;
    document.getElementById('chatSugs').innerHTML   = '';

    hist.push({ role: 'user', content: text });

    const typing = document.getElementById('typingInd');
    typing.classList.add('show');
    document.getElementById('chatBody').scrollTop = 99999;

    try {
        const res  = await fetch('chatbot.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ system: sysPrompt(), messages: hist })
        });
        const data = await res.json();
        typing.classList.remove('show');

        if (data.content && data.content[0] && data.content[0].text) {
            const reply = data.content[0].text;
            hist.push({ role: 'assistant', content: reply });
            addMsg('bot', reply);

            // Sugg après 1 réponse
            if (hist.length === 2) {
                showSugs(["Plus de détails", "Autre symptôme", "C'est urgent"]);
            }
        } else {
            const err = data.error
                ? (typeof data.error === 'object' ? (data.error.message || 'Erreur API') : data.error)
                : 'Réponse inattendue du serveur';
            addMsg('bot', '❌ ' + err);
        }
    } catch(e) {
        typing.classList.remove('show');
        addMsg('bot', '❌ Impossible de contacter le serveur. Vérifiez que chatbot.php est configuré.');
    } finally {
        document.getElementById('chatSendBtn').disabled = false;
        document.getElementById('chatTA').focus();
    }
}
</script>
</body>
</html>