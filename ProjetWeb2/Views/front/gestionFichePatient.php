<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION['medecin_id']) || empty($_SESSION['medecin_id'])) {
    // Redirectionner vers la page de login
    header('Location: loginMedecin.php');
    exit;
}

// Récupérer les infos du médecin connecté
$medecin_id = $_SESSION['medecin_id'];
$medecin_nom = $_SESSION['medecin_nom'] ?? 'Médecin';

$basePath = dirname(__DIR__) . '/..';
require_once $basePath . '/config.php';
require_once $basePath . '/Controller/fichePatientC.php';
require_once $basePath . '/Controller/evaluationC.php';
require_once $basePath . '/Model/evaluation.php';
require_once $basePath . '/Controller/rendezvousC.php';
require_once $basePath . '/Model/fichePatient.php';

$fichePatientC = new FichePatientC();
$evaluationC   = new EvaluationC();
$eval_stats    = $evaluationC->getStatsMedecin($medecin_id);
$mes_evals     = $evaluationC->getEvalsByMedecin($medecin_id);
$rendezvousC = new RendezvousC();

// Récupérer TOUS les rendez-vous de ce médecin
$rendezvous_list = $rendezvousC->getRendezvousByMedecinId($medecin_id);

$action = $_GET['action'] ?? 'list';
$edit_id = $_GET['edit_id'] ?? null;
$edit_fiche = null;

// ── Recherche & tri ──
$search_patient = trim($_GET['search_patient'] ?? '');
$search_date    = trim($_GET['search_date']    ?? '');
$tri            = in_array($_GET['tri'] ?? '', ['asc','desc']) ? $_GET['tri'] : 'desc';


if ($action === 'edit' && $edit_id) {
    $edit_fiche = $fichePatientC->getFichePatientById($edit_id);
    // Vérifier que cette fiche appartient bien à ce médecin
    if ($edit_fiche && $edit_fiche['medecin_nom'] !== $_SESSION['medecin_nom']) {
        // L'utilisateur essaie d'accéder a une fiche qui n'est pas sienne
        header('Location: gestionFichePatient.php');
        exit;
    }
}

// Traiter le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idfiche = $_POST['idfiche'] ?? null;
    $rendezvous_id = $_POST['rendezvous_id'] ?? null;
    $groupsanguin = $_POST['groupsanguin'] ?? '';
    $allergies = $_POST['allergies'] ?? '';
    $antecedents = $_POST['antecedents'] ?? '';
    $notesGenerales = $_POST['notesGenerales'] ?? '';

    // Vérification de sécurité: le rendez-vous appartient-il au médecin connecté?
    $rdv = $rendezvousC->getRendezvousById($rendezvous_id);
    if (!$rdv || $rdv['medecin_id'] != $medecin_id) {
        // Tentative de manipulation : le rendez-vous n'existe pas ou n'appartient pas au médecin
        header('Location: gestionFichePatient.php');
        exit;
    }

    if ($idfiche) {
        // Modification
        $fiche = new FichePatient($idfiche, $rendezvous_id, $groupsanguin, $allergies, $antecedents, $notesGenerales);
        $fichePatientC->updateFichePatient($fiche);
        $message = "✅ Fiche patient modifiée avec succès";
        $action = 'list';
        header('Location: gestionFichePatient.php?success=1');
        exit;
    } else {
        // Ajout
        $fiche = new FichePatient(null, $rendezvous_id, $groupsanguin, $allergies, $antecedents, $notesGenerales);
        $new_id = $fichePatientC->addFichePatient($fiche);
        header('Location: gestionFichePatient.php?success=1');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediLink — Gestion des Fiches Patients</title>
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
    --orange-light:#fef3c7;
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
.navbar-medilink {
    background: #fff;
    border-bottom: 1px solid var(--gray-200);
    padding: 0 40px;
    height: 68px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 1000;
}
.nav-logo { display: flex; align-items: center; text-decoration: none; font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
.nav-logo span { background: linear-gradient(90deg, #1a56db 0%, #0da271 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

.nav-links { display: flex; gap: 4px; }
.nav-links a {
    padding: 6px 16px;
    border-radius: 8px;
    color: var(--gray-600);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: .15s;
}
.nav-links a:hover, .nav-links a.active {
    background: var(--blue-light);
    color: var(--blue);
}

.btn-admin {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    background: var(--navy);
    color: #fff;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: .15s;
    white-space: nowrap;
}
.btn-admin:hover { background: var(--navy2); color: #fff; }

/* ── MAIN CONTENT ── */
.main-content { max-width: 920px; margin: 0 auto; padding: 44px 40px 60px; }

.section-heading {
    font-size: 15px; font-weight: 600; color: var(--gray-900);
    margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
}
.section-heading::before {
    content: ''; display: inline-block; width: 3px; height: 16px;
    background: var(--blue); border-radius: 2px;
}

/* ── ALERT ── */
.alert {
    padding: 14px 16px; margin-bottom: 22px; border-radius: var(--radius-lg);
    background: var(--green-light); color: var(--green); border: 1px solid rgba(13, 146, 113, .2);
}

/* ── FORM CARD ── */
.form-card { background: #fff; border: 1px solid var(--gray-200); border-radius: var(--radius-xl); padding: 28px; margin-bottom: 30px; }
.form-card h2 { font-size: 16px; font-weight: 600; color: var(--gray-900); margin-bottom: 24px; }

.form-group { margin-bottom: 18px; }
.form-label { font-size: 11px; font-weight: 600; color: var(--gray-600); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px; display: block; }
.form-input, .form-select, .form-textarea {
    width: 100%; padding: 10px 13px; border: 1px solid var(--gray-200);
    border-radius: var(--radius); font-size: 14px; font-family: 'Plus Jakarta Sans', sans-serif;
    color: var(--gray-900); background: #fff; outline: none; transition: .15s;
}
.form-input:focus, .form-select:focus, .form-textarea:focus {
    border-color: var(--blue); box-shadow: 0 0 0 3px rgba(26, 86, 219, .1);
}
.form-textarea { min-height: 100px; resize: vertical; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-row.full { grid-template-columns: 1fr; }

.button-group { display: flex; gap: 12px; margin-top: 24px; }
.btn { padding: 10px 22px; border: none; border-radius: var(--radius); font-size: 13px; font-weight: 500; cursor: pointer; transition: .15s; font-family: 'Plus Jakarta Sans', sans-serif; }
.btn-primary { background: var(--blue); color: #fff; }
.btn-primary:hover { background: var(--blue-dark); }
.btn-secondary { background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-200); }
.btn-secondary:hover { background: var(--gray-200); }

/* ── TABS ── */
.tabs { display: flex; gap: 4px; border-bottom: 1px solid var(--gray-200); margin-bottom: 28px; }
.tab-btn { padding: 12px 20px; background: none; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-weight: 500; color: var(--gray-600); transition: .15s; font-size: 14px; font-family: 'Plus Jakarta Sans', sans-serif; }
.tab-btn:hover { color: var(--gray-900); }
.tab-btn.active { color: var(--blue); border-bottom-color: var(--blue); }

/* ── TABLE ── */
.list-card { background: #fff; border: 1px solid var(--gray-200); border-radius: var(--radius-xl); overflow: hidden; }
.list-header { padding: 20px 28px; border-bottom: 1px solid var(--gray-200); background: var(--gray-50); }
.list-header h2 { font-size: 15px; font-weight: 600; color: var(--gray-900); }
table { width: 100%; }
th { padding: 12px 28px; background: var(--gray-50); font-size: 11px; font-weight: 600; color: var(--gray-600); text-transform: uppercase; letter-spacing: .04em; text-align: left; border-bottom: 1px solid var(--gray-200); }
td { padding: 14px 28px; border-bottom: 1px solid var(--gray-200); font-size: 13px; color: var(--gray-900); }
tr:hover { background: var(--gray-50); }
tr:last-child td { border-bottom: none; }

.btn-small { padding: 6px 12px; font-size: 12px; border-radius: 6px; }
.btn-edit { background: var(--green); color: #fff; text-decoration: none; display: inline-flex; align-items: center; }
.btn-edit:hover { background: #059669; }
.btn-delete { background: var(--red); color: #fff; }
.btn-delete:hover { background: #b91c1c; }

.empty-state { text-align: center; padding: 60px 28px; }
.empty-icon { font-size: 48px; margin-bottom: 16px; }
.empty-text { color: var(--gray-400); font-size: 14px; }

/* ── MODAL ── */
h2 { font-size: 18px; font-weight: 600; margin-bottom: 4px; color: var(--gray-900); }
.subtitle { font-size: 13px; color: var(--gray-600); margin-bottom: 20px; }

/* ── ÉTOILES & ÉVALS ── */
.stars-display { display:inline-flex; gap:2px; }
.star-d { font-size:16px; color:#e2e8f0; }
.star-d.on { color:#f59e0b; }
.eval-summary { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-xl);
                padding:24px 28px; margin-bottom:24px; display:flex; align-items:center; gap:32px; flex-wrap:wrap; }
.eval-big-note { font-size:52px; font-weight:700; color:var(--gray-900); line-height:1; }
.eval-big-sub  { font-size:13px; color:var(--gray-600); margin-top:4px; }
.eval-bars { flex:1; min-width:200px; display:flex; flex-direction:column; gap:6px; }
.eval-bar-row { display:flex; align-items:center; gap:8px; font-size:12px; }
.eval-bar-track { flex:1; height:8px; background:var(--gray-100); border-radius:100px; overflow:hidden; }
.eval-bar-fill  { height:100%; background:#f59e0b; border-radius:100px; }
.eval-card { background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius-lg);
             padding:16px 20px; margin-bottom:12px; }
.eval-card-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; flex-wrap:wrap; gap:8px; }
.eval-patient { font-size:13px; font-weight:600; color:var(--gray-900); }
.eval-date    { font-size:11px; color:var(--gray-400); }
.eval-comment { font-size:13px; color:var(--gray-600); font-style:italic; margin-top:8px; padding-top:8px;
                border-top:1px solid var(--gray-100); line-height:1.6; }
.no-evals { text-align:center; padding:48px 20px; color:var(--gray-400); }
.no-evals strong { display:block; font-size:15px; color:var(--gray-600); margin-top:10px; }

/* ── SEARCH & SORT BAR ── */
.search-bar {
    display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;
    background: #fff; border: 1px solid var(--gray-200); border-radius: var(--radius-lg);
    padding: 16px 20px; margin-bottom: 16px;
}
.search-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 160px; }
.search-group label { font-size: 11px; font-weight: 600; color: var(--gray-600); text-transform: uppercase; letter-spacing: .05em; }
.search-group input, .search-group select {
    padding: 9px 12px; border: 1px solid var(--gray-200); border-radius: 8px;
    font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif;
    color: var(--gray-900); background: #fff; outline: none; transition: .15s; height: 38px;
}
.search-group input:focus, .search-group select:focus {
    border-color: var(--blue); box-shadow: 0 0 0 3px rgba(26,86,219,.1);
}
.btn-search { height: 38px; padding: 0 18px; background: var(--blue); color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; white-space: nowrap; transition: .15s; }
.btn-search:hover { background: var(--blue-dark); }
.btn-reset-search { height: 38px; padding: 0 14px; background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-200); border-radius: 8px; font-size: 13px; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; white-space: nowrap; transition: .15s; text-decoration: none; display: inline-flex; align-items: center; }
.btn-reset-search:hover { background: var(--gray-200); }
.result-info { font-size: 12px; color: var(--gray-400); padding: 0 4px 12px; }
.sort-link { display: inline-flex; align-items: center; gap: 4px; color: var(--gray-600); text-decoration: none; font-weight: 600; font-size: 11px; transition: .15s; }
.sort-link:hover { color: var(--blue); }
.sort-link.active { color: var(--blue); }
</style>
</head>
</head>
<body>
    <!-- ── NAVBAR ── -->
    <nav class="navbar-medilink">
        <a href="home.php" class="nav-logo">
            <span>MediLink</span>
        </a>
        <div class="nav-links">
            <a href="home.php">Accueil</a>
            <a href="gestionFichePatient.php" class="active">Fiches Patients</a>
            <a href="gestionFichePatient.php?action=evals">⭐ Mes évaluations</a>
            <a href="../admin/rapportFichesPatient.php">📊 Rapport</a>
        </div>
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="display: flex; flex-direction: column; font-size: 12px; text-align: right;">
                <strong style="color: var(--blue);">Dr. <?php echo htmlspecialchars($medecin_nom); ?></strong>
                <span style="color: var(--gray-400); font-size: 11px;">ID: #<?php echo htmlspecialchars($medecin_id); ?></span>
            </div>
            <a href="loginMedecin.php?logout=1" class="btn-admin" style="background: var(--red); padding: 8px 14px;">
                🚪 Déconnexion
            </a>
        </div>
    </nav>

    <!-- ── MAIN CONTENT ── -->
    <div class="main-content">
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert">✅ Opération effectuée avec succès</div>
        <?php endif; ?>
        <?php if (isset($message)): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- ── TABS ── -->
        <div class="tabs">
            <button class="tab-btn <?php echo ($action === 'list' || !isset($_GET['action'])) ? 'active' : ''; ?>" onclick="location.href='gestionFichePatient.php'">
                📋 Liste des fiches
            </button>
            <button class="tab-btn <?php echo $action === 'add' ? 'active' : ''; ?>" onclick="location.href='?action=add'">
                ➕ Nouvelle fiche
            </button>
        </div>

        <?php if ($action === 'add' || ($action === 'edit' && $edit_fiche)): ?>
            <!-- ── FORM VIEW ── -->
            <div class="form-card">
                <h2><?php echo $edit_fiche ? '✏️ Modifier une fiche' : '➕ Créer une fiche'; ?></h2>
                <p class="subtitle"><?php echo $edit_fiche ? 'Mettez à jour les informations du patient' : 'Remplissez les informations médicales du patient après consultation'; ?></p>

                <form method="POST">
                    <?php if ($edit_fiche): ?>
                        <input type="hidden" name="idfiche" value="<?php echo $edit_fiche['idfiche']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label" for="rendezvous_id">Rendez-vous *</label>
                        <select name="rendezvous_id" id="rendezvous_id" class="form-select" required>
                            <option value="">-- Sélectionner un rendez-vous --</option>
                            <?php foreach ($rendezvous_list as $rdv): ?>
                                <option value="<?php echo $rdv['id']; ?>" 
                                    <?php echo ($edit_fiche && $edit_fiche['rendezvous_id'] === $rdv['id']) ? 'selected' : ''; ?>>
                                    Dr. <?php echo htmlspecialchars($rdv['medecin_nom']); ?> - <?php echo htmlspecialchars($rdv['date_rdv']); ?> à <?php echo htmlspecialchars($rdv['heure_rdv']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="groupsanguin">Groupe Sanguin *</label>
                            <select name="groupsanguin" id="groupsanguin" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="O+" <?php echo ($edit_fiche && $edit_fiche['groupsanguin'] === 'O+') ? 'selected' : ''; ?>>O+</option>
                                <option value="O-" <?php echo ($edit_fiche && $edit_fiche['groupsanguin'] === 'O-') ? 'selected' : ''; ?>>O-</option>
                                <option value="A+" <?php echo ($edit_fiche && $edit_fiche['groupsanguin'] === 'A+') ? 'selected' : ''; ?>>A+</option>
                                <option value="A-" <?php echo ($edit_fiche && $edit_fiche['groupsanguin'] === 'A-') ? 'selected' : ''; ?>>A-</option>
                                <option value="B+" <?php echo ($edit_fiche && $edit_fiche['groupsanguin'] === 'B+') ? 'selected' : ''; ?>>B+</option>
                                <option value="B-" <?php echo ($edit_fiche && $edit_fiche['groupsanguin'] === 'B-') ? 'selected' : ''; ?>>B-</option>
                                <option value="AB+" <?php echo ($edit_fiche && $edit_fiche['groupsanguin'] === 'AB+') ? 'selected' : ''; ?>>AB+</option>
                                <option value="AB-" <?php echo ($edit_fiche && $edit_fiche['groupsanguin'] === 'AB-') ? 'selected' : ''; ?>>AB-</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="allergies">🚨 Allergies</label>
                        <textarea name="allergies" id="allergies" class="form-textarea" placeholder="Ex: Pénicilline, Pollen, Arachides..."><?php echo $edit_fiche ? htmlspecialchars($edit_fiche['allergies']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="antecedents">📚 Antécédents Médicaux</label>
                        <textarea name="antecedents" id="antecedents" class="form-textarea" placeholder="Ex: Hypertension, Diabète, Asthme..."><?php echo $edit_fiche ? htmlspecialchars($edit_fiche['antecedents']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notesGenerales">📝 Notes Générales</label>
                        <textarea name="notesGenerales" id="notesGenerales" class="form-textarea" placeholder="Notes de diagnostic et observations..."><?php echo $edit_fiche ? htmlspecialchars($edit_fiche['notesGenerales']) : ''; ?></textarea>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $edit_fiche ? '💾 Modifier' : '✅ Créer'; ?>
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="location.href='gestionFichePatient.php'">
                            ❌ Annuler
                        </button>
                    </div>
                </form>
            </div>

        <?php elseif ($action === 'list' || $action === '' || $action === null): ?>
            <!-- ── LIST VIEW ── -->

            <!-- Barre de recherche & tri -->
            <form method="GET" action="gestionFichePatient.php">
                <div class="search-bar">
                    <div class="search-group">
                        <label>👤 Nom du patient</label>
                        <input type="text" name="search_patient"
                               placeholder="Ex: Ben Ali, Trabelsi…"
                               value="<?php echo htmlspecialchars($search_patient); ?>">
                    </div>
                    <div class="search-group">
                        <label>📅 Date du RDV</label>
                        <input type="date" name="search_date"
                               value="<?php echo htmlspecialchars($search_date); ?>">
                    </div>
                    <div class="search-group" style="flex:0 0 auto">
                        <label>🔃 Tri par date</label>
                        <select name="tri">
                            <option value="desc" <?php echo $tri==='desc'?'selected':''; ?>>↓ Plus récent</option>
                            <option value="asc"  <?php echo $tri==='asc' ?'selected':''; ?>>↑ Plus ancien</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-search">Appliquer</button>
                    <a href="gestionFichePatient.php" class="btn-reset-search">✕ Réinitialiser</a>
                </div>
            </form>

            <div class="list-card">
                <div class="list-header">
                    <h2>📋 Fiches Patients Enregistrées</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>👤 Patient</th>
                            <th>🩸 Groupe</th>
                            <th>
                                <?php
                                $next_tri = ($tri === 'asc') ? 'desc' : 'asc';
                                $icon_tri = ($tri === 'asc')  ? '↑' : '↓';
                                $params = http_build_query([
                                    'search_patient' => $search_patient,
                                    'search_date'    => $search_date,
                                    'tri'            => $next_tri,
                                ]);
                                ?>
                                <a href="gestionFichePatient.php?<?php echo $params; ?>"
                                   class="sort-link <?php echo 'active'; ?>"
                                   title="Inverser le tri">
                                    📅 Date RDV <?php echo $icon_tri; ?>
                                </a>
                            </th>
                            <th>🕐 Heure</th>
                            <th>⚕️ Spécialité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Charger toutes les fiches du médecin
                            $fiches = $fichePatientC->listFichePatientByMedecinId($medecin_id);

                            // ── FILTRAGE ──
                            if ($search_patient !== '') {
                                $fiches = array_filter($fiches, function($f) use ($search_patient) {
                                    $full = trim(($f['patient_prenom'] ?? '') . ' ' . ($f['patient_nom'] ?? ''));
                                    return stripos($full, $search_patient) !== false
                                        || stripos($f['patient_nom']    ?? '', $search_patient) !== false
                                        || stripos($f['patient_prenom'] ?? '', $search_patient) !== false;
                                });
                            }
                            if ($search_date !== '') {
                                $fiches = array_filter($fiches, function($f) use ($search_date) {
                                    return $f['date_rdv'] === $search_date;
                                });
                            }

                            // ── TRI par date + heure ──
                            usort($fiches, function($a, $b) use ($tri) {
                                // Combiner date et heure pour un tri précis
                                $da = $a['date_rdv'] . ' ' . $a['heure_rdv'];
                                $db = $b['date_rdv'] . ' ' . $b['heure_rdv'];
                                return $tri === 'asc'
                                    ? strcmp($da, $db)
                                    : strcmp($db, $da);
                            });

                            $total = count($fiches);
                        ?>
                        <tr>
                            <td colspan="7" style="padding:8px 28px 0; border:none">
                                <span class="result-info">
                                    <strong><?php echo $total; ?></strong>
                                    fiche<?php echo $total > 1 ? 's' : ''; ?> trouvée<?php echo $total > 1 ? 's' : ''; ?>
                                    <?php if ($search_patient || $search_date): ?>
                                        — filtre actif
                                    <?php endif; ?>
                                </span>
                            </td>
                        </tr>
                        <?php
                            if (empty($fiches)):
                        ?>
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <div class="empty-icon">🔍</div>
                                    <div class="empty-text">Aucune fiche ne correspond à votre recherche</div>
                                </td>
                            </tr>
                        <?php else:
                            foreach ($fiches as $fiche):
                        ?>
                            <tr>
                                <td><strong>#<?php echo htmlspecialchars($fiche['idfiche']); ?></strong></td>
                                <td><?php
                                    $pnom = trim(($fiche['patient_prenom'] ?? '') . ' ' . ($fiche['patient_nom'] ?? ''));
                                    echo $pnom ? htmlspecialchars($pnom) : '<em style="color:var(--gray-400)">—</em>';
                                ?></td>
                                <td><?php echo htmlspecialchars($fiche['groupsanguin'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($fiche['date_rdv']); ?></td>
                                <td><?php echo htmlspecialchars(substr($fiche['heure_rdv'],0,5)); ?></td>
                                <td><?php echo htmlspecialchars($fiche['specialite']); ?></td>
                                <td>
                                    <a href="?action=edit&edit_id=<?php echo $fiche['idfiche']; ?>">
                                        <button class="btn btn-small btn-edit">✏️ Modifier</button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif;
                        } catch (Exception $e) {
                            echo '<tr><td colspan="7" class="empty-state"><div class="empty-text">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div></td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($action === 'evals'): ?>
            <div class="eval-summary">
                <div style="text-align:center">
                    <div class="eval-big-note"><?php echo $eval_stats['total']>0?number_format($eval_stats['moyenne'],1):'—'; ?></div>
                    <div class="stars-display" style="margin:6px 0">
                        <?php for($s=1;$s<=5;$s++): ?>
                            <span class="star-d <?php echo $s<=round($eval_stats['moyenne'])?'on':''; ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <div class="eval-big-sub"><?php echo $eval_stats['total']; ?> avis reçus</div>
                </div>
                <div class="eval-bars">
                    <?php
                    $total_e = max(1, $eval_stats['total']);
                    foreach ([5,4,3,2,1] as $niv):
                        $nb  = intval($eval_stats['nb'.$niv] ?? 0);
                        $pct = round($nb / $total_e * 100);
                    ?>
                    <div class="eval-bar-row">
                        <span style="min-width:16px;font-size:12px;color:var(--gray-600)"><?php echo $niv; ?>★</span>
                        <div class="eval-bar-track"><div class="eval-bar-fill" style="width:<?php echo $pct; ?>%"></div></div>
                        <span style="min-width:24px;font-size:11px;color:var(--gray-400);text-align:right"><?php echo $nb; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="list-card">
                <div class="list-header"><h2>⭐ Avis de mes patients</h2></div>
                <div style="padding:8px 20px 20px">
                <?php if (empty($mes_evals)): ?>
                    <div class="no-evals">
                        <div style="font-size:32px">⭐</div>
                        <strong>Aucune évaluation pour l'instant</strong>
                        <p style="margin-top:6px;font-size:13px;color:var(--gray-400)">Les patients peuvent vous évaluer après chaque consultation.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($mes_evals as $ev): ?>
                        <div class="eval-card">
                            <div class="eval-card-header">
                                <div>
                                    <div class="eval-patient">👤 <?php echo htmlspecialchars(trim(($ev['patient_prenom']??'').' '.($ev['patient_nom']??'')) ?: 'Patient'); ?></div>
                                    <div style="font-size:11px;color:var(--gray-400);margin-top:2px">RDV du <?php echo $ev['date_rdv']; ?> à <?php echo substr($ev['heure_rdv'],0,5); ?></div>
                                </div>
                                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px">
                                    <div class="stars-display">
                                        <?php for($s=1;$s<=5;$s++): ?>
                                            <span class="star-d <?php echo $s<=$ev['note']?'on':''; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="eval-date"><?php echo date('d/m/Y', strtotime($ev['date_eval'])); ?></span>
                                </div>
                            </div>
                            <?php if (!empty($ev['commentaire'])): ?>
                                <div class="eval-comment">"<?php echo htmlspecialchars($ev['commentaire']); ?>"</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>