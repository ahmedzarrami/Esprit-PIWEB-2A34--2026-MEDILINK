<?php
session_start();

// ── Vérifier que le patient est connecté ──
if (!isset($_SESSION['patient_id']) || empty($_SESSION['patient_id'])) {
    header('Location: loginPatient.php');
    exit;
}

$basePath = dirname(__DIR__) . '/..';
require_once $basePath . '/config.php';
require_once $basePath . '/Controller/rendezvousC.php';
require_once $basePath . '/Model/rendezvous.php';
require_once $basePath . '/Controller/evaluationC.php';
require_once $basePath . '/Model/evaluation.php';

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

// ── Construire la liste des médecins depuis la BD (avec couleurs et initiales) ──
$AVATAR_COLORS_RECH = [
    ['bg'=>'#eff4ff','color'=>'#1a56db'],
    ['bg'=>'#ecfdf5','color'=>'#0da271'],
    ['bg'=>'#fff7ed','color'=>'#e05a2b'],
    ['bg'=>'#f5f3ff','color'=>'#7c3aed'],
    ['bg'=>'#fef9c3','color'=>'#ca8a04'],
];
$MEDECINS_STATIQUES = [];
foreach ($medecins as $idx_m => $doc_m) {
    $c_m = $AVATAR_COLORS_RECH[$idx_m % count($AVATAR_COLORS_RECH)];
    $MEDECINS_STATIQUES[] = [
        'id'       => $doc_m['id'],
        'nom'      => $doc_m['nom'],
        'spec'     => $doc_m['specialite'],
        'ville'    => $doc_m['ville']    ?? 'Tunis',
        'exp'      => ($doc_m['experience'] ?? '') ?: '—',
        'initials' => strtoupper(substr(str_replace('Dr. ','',str_replace('Dr.','', $doc_m['nom'])),0,2)),
        'bg'       => $c_m['bg'],
        'color'    => $c_m['color'],
    ];
}
$filtre_spec  = trim($_GET['spec']  ?? '');
$filtre_ville = trim($_GET['ville'] ?? '');
$specialites  = array_unique(array_column($MEDECINS_STATIQUES, 'spec'));
$med_filtres  = array_filter($MEDECINS_STATIQUES, function($m) use ($filtre_spec,$filtre_ville) {
    return (!$filtre_spec  || stripos($m['spec'],  $filtre_spec)  !== false)
        && (!$filtre_ville || stripos($m['ville'], $filtre_ville) !== false);
});
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediLink — Mes Rendez-vous</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --blue:#1a56db; --blue-dark:#1a46c4; --blue-light:#eff4ff; --blue-mid:#6694f8;
    --green:#0da271; --green-light:#ecfdf5;
    --navy:#0f1b2d; --navy2:#1e2f45;
    --red:#dc2626; --red-light:#fee2e2;
    --orange-light:#fef3c7;
    --gray-50:#f8fafc; --gray-100:#f1f5f9; --gray-200:#e2e8f0;
    --gray-400:#94a3b8; --gray-600:#475569; --gray-900:#0f172a;
    --radius:12px; --radius-lg:18px; --radius-xl:24px;
}
body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--gray-50); color:var(--gray-900); font-size:14px; line-height:1.6; }

/* NAVBAR */
.navbar-medilink { background:#fff; border-bottom:1px solid var(--gray-200); padding:0 40px; height:68px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:1000; }
.nav-logo { display:flex; align-items:center; text-decoration:none; font-size:22px; font-weight:700; letter-spacing:-0.5px; }
.nav-logo span { background:linear-gradient(90deg,#1a56db 0%,#0da271 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
.nav-user { display:flex; align-items:center; gap:14px; }
.user-info { display:flex; align-items:center; gap:10px; }
.user-avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#1a56db,#6694f8); color:white; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:12px; }
.user-text { display:flex; flex-direction:column; }
.user-name { font-size:13px; font-weight:600; color:var(--gray-900); }
.user-role { font-size:11px; color:var(--gray-400); }
.btn-logout { display:flex; align-items:center; gap:7px; padding:8px 16px; background:var(--red-light); color:var(--red); border:1px solid rgba(220,38,38,.2); border-radius:8px; font-size:13px; font-weight:500; text-decoration:none; transition:.15s; }
.btn-logout:hover { background:var(--red); color:white; border-color:var(--red); }

/* HERO */
.hero { background:linear-gradient(135deg,#1a46c4 0%,#2563eb 55%,#3b7ff7 100%); padding:72px 40px 88px; position:relative; overflow:hidden; }
.hero::before { content:''; position:absolute; top:-80px; right:-80px; width:350px; height:350px; background:rgba(255,255,255,.06); border-radius:50%; }
.hero::after  { content:''; position:absolute; bottom:-100px; left:42%; width:220px; height:220px; background:rgba(255,255,255,.04); border-radius:50%; }
.hero-inner { max-width:920px; margin:0 auto; position:relative; z-index:1; }
.hero h1 { font-size:40px; font-weight:600; color:#fff; line-height:1.2; margin-bottom:14px; }
.hero p { color:rgba(255,255,255,.75); font-size:15px; max-width:460px; line-height:1.75; }

/* MAIN */
.main-content { max-width:920px; margin:0 auto; padding:44px 40px 60px; }
.section-heading { font-size:15px; font-weight:600; color:var(--gray-900); margin-bottom:20px; display:flex; align-items:center; gap:10px; }
.section-heading::before { content:''; display:inline-block; width:3px; height:16px; background:var(--blue); border-radius:2px; }

/* ALERT */
.page-alert { padding:14px 16px; margin-bottom:22px; border-radius:var(--radius-lg); font-size:13px; font-weight:500; }
.page-alert.success { background:var(--green-light); color:#065f46; border:1px solid rgba(13,162,113,.25); }
.page-alert.error   { background:var(--red-light);   color:#991b1b; border:1px solid rgba(220,38,38,.25); }

/* SEARCH */
.rech-card { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-xl); padding:28px; margin-bottom:44px; }
.rech-filters { display:grid; grid-template-columns:1fr 1fr auto; gap:14px; margin-bottom:18px; align-items:end; }
.rech-form-group { display:flex; flex-direction:column; gap:6px; }
.rech-label { font-size:11px; font-weight:500; color:var(--gray-600); text-transform:uppercase; letter-spacing:.05em; }
.rech-input { padding:10px 13px; border:1px solid var(--gray-200); border-radius:8px; font-size:14px; font-family:'Plus Jakarta Sans',sans-serif; color:var(--gray-900); background:#fff; outline:none; transition:.15s; height:40px; }
.rech-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,86,219,.1); }
.btn-rech-reset { height:40px; padding:0 16px; background:var(--gray-100); color:var(--gray-600); border:1px solid var(--gray-200); border-radius:8px; font-size:13px; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; white-space:nowrap; transition:.15s; }
.btn-rech-reset:hover { color:var(--gray-900); border-color:var(--gray-400); }
.rech-tags { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; }
.rech-tag { padding:4px 14px; border-radius:100px; font-size:12px; font-weight:500; border:1px solid var(--gray-200); background:var(--gray-100); color:var(--gray-600); text-decoration:none; transition:.15s; }
.rech-tag:hover,.rech-tag.active { background:var(--blue-light); border-color:var(--blue-mid); color:var(--blue-dark); }
.rech-info { font-size:12px; color:var(--gray-400); margin-bottom:14px; }
.rech-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:16px; }
.rech-doc-card { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); padding:24px 20px; text-align:center; transition:.2s ease; }
.rech-doc-card:hover { border-color:var(--blue-mid); transform:translateY(-2px); box-shadow:0 8px 24px rgba(26,86,219,.1); }
.rech-doc-card.selected-card { border-color:var(--blue); background:var(--blue-light); box-shadow:0 0 0 3px rgba(26,86,219,.15); }
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
    display:none; align-items:center; gap:12px;
    background:var(--blue-light); border:1px solid rgba(26,86,219,.25);
    border-radius:var(--radius-lg); padding:14px 18px; margin-bottom:20px;
    animation: slideIn .3s ease;
}
.select-banner.show { display:flex; }
@keyframes slideIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
.select-banner-icon { font-size:24px; flex-shrink:0; }
.select-banner-text { flex:1; }
.select-banner-name { font-size:14px; font-weight:700; color:var(--blue-dark); }
.select-banner-sub  { font-size:12px; color:var(--blue); margin-top:2px; }
.select-banner-ok { padding:7px 16px; background:var(--blue); color:#fff; border:none;
                    border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;
                    font-family:"Plus Jakarta Sans",sans-serif; text-decoration:none;
                    display:inline-flex; align-items:center; gap:5px; transition:.15s; }
.select-banner-ok:hover { background:var(--blue-dark); }
.rech-avatar { width:64px; height:64px; border-radius:50%; margin:0 auto 14px; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:600; }
.rech-doc-name { font-size:15px; font-weight:700; color:var(--gray-900); margin-bottom:4px; }
.rech-doc-spec { font-size:12px; color:var(--gray-400); margin-bottom:6px; }
.rech-doc-city { font-size:12px; color:var(--gray-600); margin-bottom:12px; }
.rech-doc-meta { display:flex; justify-content:center; gap:20px; margin-bottom:14px; }
.rech-meta-item { font-size:11px; color:var(--gray-600); text-align:center; }
.rech-meta-item strong { display:block; font-size:13px; font-weight:600; color:var(--gray-900); margin-bottom:2px; }
.btn-rech-rdv { display:inline-block; width:100%; background:var(--blue-light); color:var(--blue); border:1px solid rgba(26,86,219,.2); border-radius:8px; padding:10px 12px; font-size:13px; font-weight:600; text-decoration:none; text-align:center; transition:.15s; }
.btn-rech-rdv:hover { background:var(--blue); color:#fff; border-color:var(--blue); }

/* DOCTORS */

/* FORM */
.form-card { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-xl); padding:30px 28px; margin-bottom:44px; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:18px; }
.form-group { display:flex; flex-direction:column; gap:6px; }
.form-group label { font-size:11px; font-weight:500; color:var(--gray-600); text-transform:uppercase; letter-spacing:.05em; }
.form-group input,.form-group select { padding:10px 14px; border:1px solid var(--gray-200); border-radius:8px; font-size:14px; font-family:'Plus Jakarta Sans',sans-serif; color:var(--gray-900); background:#fff; transition:.15s; outline:none; }
.form-group input:focus,.form-group select:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,86,219,.1); }

/* TIME PICKER */
.time-picker-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; padding:15px 0; }
.slot { padding:12px 8px; border:2px solid var(--gray-200); border-radius:8px; background:#fff; font-size:13px; font-weight:500; text-align:center; line-height:1.3; min-height:60px; display:flex; align-items:center; justify-content:center; flex-direction:column; }
.slot.available { border-color:var(--green); color:var(--green); cursor:pointer; transition:.15s; }
.slot.available:hover { background:rgba(13,162,113,.08); }
.slot.available.sel { background:var(--blue)!important; color:#fff!important; border-color:var(--blue)!important; font-weight:700!important; box-shadow:0 0 0 3px rgba(26,86,219,.2)!important; }
.slot.occupied { border-color:var(--red); color:#fff; background:var(--red); font-weight:600; cursor:not-allowed; }
.slot.occupied small { color:#fff; font-size:10px; }
.empty-slots { grid-column:1/-1; padding:30px 20px; text-align:center; color:var(--gray-400); font-size:14px; background:rgba(249,250,251,.8); border-radius:8px; border:1px dashed var(--gray-200); }

.btn-confirm { width:100%; background:var(--blue); color:#fff; border:none; border-radius:10px; padding:13px 20px; font-size:14px; font-weight:600; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:.15s; }
.btn-confirm:hover { background:var(--blue-dark); transform:translateY(-1px); box-shadow:0 6px 20px rgba(26,86,219,.3); }
.btn-cancel { display:block; margin-top:10px; text-align:center; width:100%; background:var(--gray-100); color:var(--gray-600); border:1px solid var(--gray-200); border-radius:10px; padding:11px 20px; font-size:13px; font-weight:500; text-decoration:none; transition:.15s; }
.btn-cancel:hover { background:var(--gray-200); }

/* RDV LIST */
#rdvList { display:flex; flex-direction:column; gap:12px; }
.rdv-item { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); padding:18px 22px; display:flex; align-items:center; gap:16px; transition:.15s; flex-wrap:wrap; }
.rdv-item:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); }
.rdv-icon { width:46px; height:46px; border-radius:10px; background:var(--blue-light); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:20px; }
.rdv-info { flex:1; min-width:200px; }
.rdv-doc  { font-size:14px; font-weight:600; color:var(--gray-900); }
.rdv-spec { font-size:12px; color:var(--gray-600); }
.rdv-time { font-size:12px; color:var(--gray-400); margin-top:3px; }
.rdv-status { display:inline-flex; align-items:center; gap:5px; background:var(--green-light); color:var(--green); border-radius:100px; padding:4px 12px; font-size:11px; font-weight:500; white-space:nowrap; }
.rdv-status-dot { width:5px; height:5px; border-radius:50%; background:var(--green); flex-shrink:0; }
.rdv-actions { display:flex; gap:8px; }
.btn-action { padding:8px 12px; border-radius:6px; font-size:12px; font-weight:500; cursor:pointer; transition:.2s; text-decoration:none; display:inline-flex; align-items:center; gap:5px; font-family:'Plus Jakarta Sans',sans-serif; }
.btn-edit   { background:var(--blue-light); color:var(--blue); border:1px solid rgba(26,86,219,.2); }
.btn-edit:hover   { background:var(--blue); color:#fff; }
.btn-delete { background:var(--red-light); color:var(--red); border:1px solid rgba(220,38,38,.2); }
.btn-delete:hover { background:var(--red); color:#fff; }
.empty-state { text-align:center; padding:56px 20px; color:var(--gray-400); }
.empty-state strong { display:block; font-size:15px; color:var(--gray-600); margin:12px 0 4px; }

/* ── RDV SEARCH BAR ── */
.rdv-search-bar { display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg); padding:14px 18px; margin-bottom:14px; }
.rdv-sg { display:flex; flex-direction:column; gap:5px; flex:1; min-width:150px; }
.rdv-sg label { font-size:11px; font-weight:600; color:var(--gray-600); text-transform:uppercase; letter-spacing:.05em; }
.rdv-sg input, .rdv-sg select { padding:8px 11px; border:1px solid var(--gray-200); border-radius:8px; font-size:13px; font-family:'Plus Jakarta Sans',sans-serif; color:var(--gray-900); background:#fff; outline:none; transition:.15s; height:36px; }
.rdv-sg input:focus, .rdv-sg select:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,86,219,.1); }
.btn-rdv-search { height:36px; padding:0 16px; background:var(--blue); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; white-space:nowrap; transition:.15s; }
.btn-rdv-search:hover { background:var(--blue-dark); }
.btn-rdv-reset { height:36px; padding:0 12px; background:var(--gray-100); color:var(--gray-600); border:1px solid var(--gray-200); border-radius:8px; font-size:13px; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; white-space:nowrap; text-decoration:none; display:inline-flex; align-items:center; transition:.15s; }
.btn-rdv-reset:hover { background:var(--gray-200); }
.rdv-result-info { font-size:12px; color:var(--gray-400); margin-bottom:10px; }
.rdv-sort-link { color:var(--gray-600); text-decoration:none; font-weight:600; font-size:12px; display:inline-flex; align-items:center; gap:3px; padding:3px 8px; border-radius:6px; transition:.15s; }
.rdv-sort-link:hover { background:var(--blue-light); color:var(--blue); }
.rdv-sort-link.active { background:var(--blue-light); color:var(--blue); }

/* ── ÉTOILES ── */
.stars { display:inline-flex; gap:2px; }
.star { font-size:14px; color:#e2e8f0; }
.star.on { color:#f59e0b; }
.doc-rating { font-size:12px; color:var(--gray-600); margin:4px 0 12px; display:flex; align-items:center; justify-content:center; gap:6px; }
.doc-rating strong { font-weight:700; color:var(--gray-900); font-size:13px; }

/* ── BOUTON ÉVALUER ── */
.btn-eval { padding:7px 12px; border-radius:6px; font-size:12px; font-weight:500; cursor:pointer;
            background:#fef3c7; color:#d97706; border:1px solid #fde68a;
            font-family:'Plus Jakarta Sans',sans-serif; transition:.15s; }
.btn-eval:hover { background:#f59e0b; color:#fff; border-color:#f59e0b; }
.btn-evaluated { padding:7px 12px; border-radius:6px; font-size:12px; font-weight:500;
                 background:var(--green-light); color:var(--green);
                 border:1px solid rgba(13,162,113,.2); cursor:default; }

/* ── MODAL ÉVALUATION ── */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);
                 z-index:2000; align-items:center; justify-content:center; padding:20px; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff; border-radius:var(--radius-xl); padding:32px 28px;
             max-width:460px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,.2); position:relative; }
.modal-close { position:absolute; top:16px; right:16px; background:none; border:none;
               font-size:20px; cursor:pointer; color:var(--gray-400); line-height:1; }
.modal-close:hover { color:var(--gray-900); }
.modal-title { font-size:17px; font-weight:700; color:var(--gray-900); margin-bottom:4px; }
.modal-sub   { font-size:13px; color:var(--gray-600); margin-bottom:22px; }
.star-picker { display:flex; gap:8px; justify-content:center; margin-bottom:18px; }
.star-picker span { font-size:36px; cursor:pointer; color:#e2e8f0; transition:.15s; user-select:none; }
.star-picker span.hover,.star-picker span.sel { color:#f59e0b; transform:scale(1.15); }
.modal-textarea { width:100%; padding:10px 12px; border:1px solid var(--gray-200);
                  border-radius:8px; font-size:13px; font-family:'Plus Jakarta Sans',sans-serif;
                  resize:vertical; min-height:90px; outline:none; transition:.15s; }
.modal-textarea:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(26,86,219,.1); }
.modal-note-label { font-size:12px; font-weight:600; color:var(--gray-600); text-align:center;
                    margin-bottom:16px; min-height:18px; }
.btn-submit-eval { width:100%; padding:12px; background:var(--blue); color:#fff; border:none;
                   border-radius:10px; font-size:14px; font-weight:600; cursor:pointer;
                   font-family:'Plus Jakarta Sans',sans-serif; margin-top:14px; transition:.15s; }
.btn-submit-eval:hover { background:var(--blue-dark); }

@media (max-width:700px) {
    .navbar-medilink { padding:0 16px; gap:8px; }
    .user-info { display:none; }
    .hero { padding:48px 20px 60px; }
    .hero h1 { font-size:28px; }
    .main-content { padding:28px 20px 48px; }
    .form-row { grid-template-columns:1fr; }
    .rech-filters { grid-template-columns:1fr; }
    .rdv-item { flex-wrap:wrap; }
    .rdv-actions { width:100%; }
    .time-picker-grid { grid-template-columns:repeat(3,1fr); }
}
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
        <a href="logoutPatient.php" class="btn-logout">Déconnexion</a>
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
</body>
</html>