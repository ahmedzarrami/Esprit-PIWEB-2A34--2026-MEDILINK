<?php
require_once __DIR__ . '/../../../config/session.php';
require_login();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Controller/rendezvousC.php';

$action = $_GET['action'] ?? '';
$controller = new RendezvousC();

try {
    switch ($action) {

        /* ── LIST ALL RDV ── */
        case 'list':
            $rdvs = $controller->listRendezvous();
            echo json_encode(['success' => true, 'data' => $rdvs]);
            break;

        /* ── LIST RDV BY MEDECIN ── */
        case 'listByMedecin':
            $medecin_id = isset($_GET['medecin_id']) ? intval($_GET['medecin_id']) : 0;
            if (!$medecin_id) {
                echo json_encode(['success' => false, 'message' => 'medecin_id requis']);
                break;
            }
            $rdvs = $controller->getRendezvousByMedecinId($medecin_id);
            echo json_encode(['success' => true, 'data' => $rdvs]);
            break;

        /* ── LIST RDV BY PATIENT ── */
        case 'listByPatient':
            $patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;
            if (!$patient_id) {
                echo json_encode(['success' => false, 'message' => 'patient_id requis']);
                break;
            }
            $rdvs = $controller->getRendezvousByPatientId($patient_id);
            echo json_encode(['success' => true, 'data' => $rdvs]);
            break;

        /* ── GET SINGLE RDV ── */
        case 'get':
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'id requis']);
                break;
            }
            $rdv = $controller->getRendezvousById($id);
            if ($rdv) {
                echo json_encode(['success' => true, 'data' => $rdv]);
            } else {
                echo json_encode(['success' => false, 'message' => 'RDV introuvable']);
            }
            break;

        /* ── ADD RDV ── */
        case 'add':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }

            $medecin_id = intval($input['medecin_id'] ?? 0);
            $patient_id = intval($input['patient_id'] ?? 0);
            $date_rdv   = $input['date_rdv'] ?? '';
            $heure_rdv  = $input['heure_rdv'] ?? '';
            $statut     = $input['statut'] ?? 'confirmé';

            if (!$medecin_id || !$date_rdv || !$heure_rdv) {
                echo json_encode(['success' => false, 'message' => 'Champs requis: medecin_id, date_rdv, heure_rdv']);
                break;
            }

            // Vérifier que le médecin existe
            if (!$controller->medecinExists($medecin_id)) {
                echo json_encode(['success' => false, 'message' => 'Médecin introuvable']);
                break;
            }

            $rdv = new Rendezvous(null, $medecin_id, $patient_id ?: null, $date_rdv, $heure_rdv, $statut);
            $newId = $controller->addRendezvous($rdv);
            if ($newId) {
                echo json_encode(['success' => true, 'message' => 'RDV ajouté', 'id' => $newId]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
            }
            break;

        /* ── UPDATE RDV ── */
        case 'update':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }

            $id         = intval($input['id'] ?? 0);
            $medecin_id = intval($input['medecin_id'] ?? 0);
            $date_rdv   = $input['date_rdv'] ?? '';
            $heure_rdv  = $input['heure_rdv'] ?? '';
            $statut     = $input['statut'] ?? 'confirmé';

            if (!$id || !$medecin_id || !$date_rdv || !$heure_rdv) {
                echo json_encode(['success' => false, 'message' => 'Champs requis: id, medecin_id, date_rdv, heure_rdv']);
                break;
            }

            $rdv = new Rendezvous($id, $medecin_id, null, $date_rdv, $heure_rdv, $statut);
            $rows = $controller->updateRendezvous($rdv);
            echo json_encode(['success' => true, 'message' => 'RDV modifié', 'rows' => $rows]);
            break;

        /* ── DELETE RDV ── */
        case 'delete':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }
            $id = intval($input['id'] ?? ($_GET['id'] ?? 0));
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'id requis']);
                break;
            }
            $rows = $controller->deleteRendezvous($id);
            if ($rows > 0) {
                echo json_encode(['success' => true, 'message' => 'RDV supprimé']);
            } else {
                echo json_encode(['success' => false, 'message' => 'RDV introuvable ou déjà supprimé']);
            }
            break;

        /* ── LIST MEDECINS ── */
        case 'medecins':
            $medecins = $controller->listMedecins();
            echo json_encode(['success' => true, 'data' => $medecins]);
            break;

        /* ── GET MEDECIN ── */
        case 'getMedecin':
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'id requis']);
                break;
            }
            $medecin = $controller->getMedecinById($id);
            if ($medecin) {
                echo json_encode(['success' => true, 'data' => $medecin]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Médecin introuvable']);
            }
            break;

        /* ── AVAILABILITY ── */
        case 'availability':
            $medecin_id = isset($_GET['medecin_id']) ? intval($_GET['medecin_id']) : 0;
            $date       = $_GET['date'] ?? '';
            if (!$medecin_id || !$date) {
                echo json_encode(['success' => false, 'message' => 'medecin_id et date requis']);
                break;
            }
            $slots = $controller->getMedecinAvailability($medecin_id, $date);
            echo json_encode(['success' => true, 'data' => $slots]);
            break;

        /* ── UNKNOWN ACTION ── */
        default:
            echo json_encode(['success' => false, 'message' => 'Action inconnue: ' . $action]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>
