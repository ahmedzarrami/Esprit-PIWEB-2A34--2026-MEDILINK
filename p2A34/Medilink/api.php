<?php
/**
 * api.php — REST API for Rendezvous and FichePatient operations
 * Handles: add, update, delete, list rendez-vous et fiches patients
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Chemins absolus pour éviter les problèmes de chemins relatifs
$basePath = __DIR__;
require_once $basePath . '/config.php';
require_once $basePath . '/Model/rendezvous.php';
require_once $basePath . '/Controller/rendezvousC.php';
require_once $basePath . '/Model/fichePatient.php';
require_once $basePath . '/Controller/fichePatientC.php';

$action = $_GET['action'] ?? $_POST['action'] ?? null;
$rendezvousController = new RendezvousC();
$fichePatientController = new FichePatientC();
$response = [];

try {
    switch ($action) {
        
        /* ──── ADD RENDEZVOUS ──── */
        case 'add':
        case 'add_rendezvous':
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Fallback pour traiter les données URL-encoded
            if (!$data || !is_array($data)) {
                parse_str(file_get_contents('php://input'), $data);
            }
            
            if (!isset($data['medecin_id']) || !isset($data['date_rdv']) || !isset($data['heure_rdv'])) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'Missing required fields'];
                break;
            }

            $rdv = new Rendezvous(
                null,
                $data['medecin_id'],
                $data['patient_id'] ?? null,
                $data['date_rdv'],
                $data['heure_rdv'],
                $data['statut'] ?? 'confirmé'
            );

            $id = $rendezvousController->addRendezvous($rdv);
            if ($id && $id !== false) {
                http_response_code(201);
                $response = ['success' => true, 'message' => 'Rendez-vous ajouté avec succès', 'id' => $id];
            } else {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'Erreur lors de l\'insertion en base de données'];
            }
            break;

        /* ──── UPDATE RENDEZVOUS ──── */
        case 'update':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id']) || !isset($data['date_rdv']) || !isset($data['heure_rdv'])) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'Missing required fields'];
                break;
            }

            $rdv = new Rendezvous(
                $data['id'],
                $data['medecin_id'] ?? null,
                $data['date_rdv'],
                $data['heure_rdv'],
                $data['statut'] ?? 'confirmé'
            );

            $rowCount = $rendezvousController->updateRendezvous($rdv);
            if ($rowCount > 0) {
                http_response_code(200);
                $response = ['success' => true, 'message' => 'Rendez-vous modifié'];
            } else {
                http_response_code(404);
                $response = ['success' => false, 'message' => 'Rendez-vous non trouvé'];
            }
            break;

        /* ──── DELETE RENDEZVOUS ──── */
        case 'delete':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'ID required'];
                break;
            }

            $rowCount = $rendezvousController->deleteRendezvous($id);
            if ($rowCount > 0) {
                http_response_code(200);
                $response = ['success' => true, 'message' => 'Rendez-vous supprimé'];
            } else {
                http_response_code(404);
                $response = ['success' => false, 'message' => 'Rendez-vous non trouvé'];
            }
            break;

        /* ──── LIST RENDEZVOUS ──── */
        case 'list':
            $rdvs = $rendezvousController->listRendezvous();
            http_response_code(200);
            $response = ['success' => true, 'data' => $rdvs];
            break;

        /* ──── GET SINGLE RENDEZVOUS ──── */
        case 'get':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'ID required'];
                break;
            }
            $rdv = $rendezvousController->getRendezvousById($id);
            if ($rdv) {
                http_response_code(200);
                $response = ['success' => true, 'data' => $rdv];
            } else {
                http_response_code(404);
                $response = ['success' => false, 'message' => 'Rendez-vous not found'];
            }
            break;

        /* ──── GET MEDECINS ──── */
        case 'medecins':
            $medecins = $rendezvousController->listMedecins();
            http_response_code(200);
            $response = ['success' => true, 'data' => $medecins];
            break;

        /* ═══════════════════════════════════════════════════════════
           🏥 FICHE PATIENT ENDPOINTS
           ═══════════════════════════════════════════════════════════ */

        /* ──── ADD FICHE PATIENT ──── */
        case 'add_fiche':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['rendezvous_id']) || !isset($data['groupsanguin'])) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'Missing required fields (rendezvous_id, groupsanguin)'];
                break;
            }

            $fiche = new FichePatient(
                null,
                $data['rendezvous_id'],
                $data['groupsanguin'],
                $data['allergies'] ?? '',
                $data['antecedents'] ?? '',
                $data['notesGenerales'] ?? ''
            );

            $id = $fichePatientController->addFichePatient($fiche);
            if ($id && $id !== false) {
                http_response_code(201);
                $response = ['success' => true, 'message' => 'Fiche patient ajoutée avec succès', 'id' => $id];
            } else {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'Erreur lors de l\'insertion en base de données'];
            }
            break;

        /* ──── UPDATE FICHE PATIENT ──── */
        case 'update_fiche':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['idfiche'])) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'idfiche required'];
                break;
            }

            $fiche = new FichePatient(
                $data['idfiche'],
                $data['rendezvous_id'] ?? null,
                $data['groupsanguin'] ?? null,
                $data['allergies'] ?? null,
                $data['antecedents'] ?? null,
                $data['notesGenerales'] ?? null
            );

            $rowCount = $fichePatientController->updateFichePatient($fiche);
            if ($rowCount > 0) {
                http_response_code(200);
                $response = ['success' => true, 'message' => 'Fiche patient modifiée'];
            } else {
                http_response_code(404);
                $response = ['success' => false, 'message' => 'Fiche patient non trouvée'];
            }
            break;

        /* ──── DELETE FICHE PATIENT ──── */
        case 'delete_fiche':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['idfiche'] ?? null;

            if (!$id) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'idfiche required'];
                break;
            }

            $rowCount = $fichePatientController->deleteFichePatient($id);
            if ($rowCount > 0) {
                http_response_code(200);
                $response = ['success' => true, 'message' => 'Fiche patient supprimée'];
            } else {
                http_response_code(404);
                $response = ['success' => false, 'message' => 'Fiche patient non trouvée'];
            }
            break;

        /* ──── LIST FICHE PATIENTS ──── */
        case 'list_fiches':
            $fiches = $fichePatientController->listFichePatient();
            http_response_code(200);
            $response = ['success' => true, 'data' => $fiches];
            break;

        /* ──── GET SINGLE FICHE PATIENT ──── */
        case 'get_fiche':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'id required'];
                break;
            }
            $fiche = $fichePatientController->getFichePatientById($id);
            if ($fiche) {
                http_response_code(200);
                $response = ['success' => true, 'data' => $fiche];
            } else {
                http_response_code(404);
                $response = ['success' => false, 'message' => 'Fiche patient not found'];
            }
            break;

        /* ──── GET FICHE BY RENDEZVOUS ID ──── */
        case 'get_fiche_rdv':
            $rendezvous_id = $_GET['rendezvous_id'] ?? null;
            if (!$rendezvous_id) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'rendezvous_id required'];
                break;
            }
            $fiche = $fichePatientController->getFichePatientByRendezvousId($rendezvous_id);
            if ($fiche) {
                http_response_code(200);
                $response = ['success' => true, 'data' => $fiche];
            } else {
                http_response_code(404);
                $response = ['success' => false, 'message' => 'Fiche patient not found for this appointment'];
            }
            break;

        /* ═══════════════════════════════════════════════════════════
           👨‍⚕️ PATIENT ENDPOINTS
           ═══════════════════════════════════════════════════════════ */

        /* ──── LIST RENDEZVOUS BY PATIENT ──── */
        case 'list_rendezvous_patient':
            $patient_id = $_GET['patient_id'] ?? null;
            if (!$patient_id) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'patient_id required'];
                break;
            }
            $rdvs = $rendezvousController->getRendezvousByPatientId($patient_id);
            http_response_code(200);
            $response = ['success' => true, 'rendezvous' => $rdvs];
            break;

        case 'get_medecin_availability':
            $medecin_id = $_GET['medecin_id'] ?? null;
            $date = $_GET['date'] ?? null;
            if (!$medecin_id || !$date) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'medecin_id and date required'];
                break;
            }
            $availability = $rendezvousController->getMedecinAvailability($medecin_id, $date);
            http_response_code(200);
            $response = ['success' => true, 'slots' => $availability];
            break;

        case 'update_rendezvous':
            $id = $_POST['id'] ?? null;
            $date_rdv = $_POST['date_rdv'] ?? null;
            $heure_rdv = $_POST['heure_rdv'] ?? null;
            $statut = $_POST['statut'] ?? 'confirmé';
            
            if (!$id || !$date_rdv || !$heure_rdv) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'Missing required fields'];
                break;
            }
            
            // Récupérer le RDV existant pour obtenir medecin_id et patient_id
            $existingRdv = $rendezvousController->getRendezvousById($id);
            if (!$existingRdv) {
                http_response_code(404);
                $response = ['success' => false, 'message' => 'Rendez-vous not found'];
                break;
            }
            
            $rdv = new Rendezvous(
                $id,
                $existingRdv['medecin_id'],
                $existingRdv['patient_id'],
                $date_rdv,
                $heure_rdv,
                $statut
            );
            
            $result = $rendezvousController->updateRendezvous($rdv);
            if ($result !== false) {
                http_response_code(200);
                $response = ['success' => true, 'message' => 'Rendez-vous updated successfully'];
            } else {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'Failed to update rendezvous'];
            }
            break;

        case 'delete_rendezvous':
            $id = $_POST['id'] ?? null;
            if (!$id) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'id required'];
                break;
            }
            
            $result = $rendezvousController->deleteRendezvous($id);
            if ($result > 0) {
                http_response_code(200);
                $response = ['success' => true, 'message' => 'Rendez-vous deleted successfully'];
            } else {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'Failed to delete rendezvous'];
            }
            break;

        default:
            http_response_code(400);
            $response = ['success' => false, 'message' => 'Invalid action'];
    }

} catch (Exception $e) {
    http_response_code(500);
    $response = ['success' => false, 'message' => 'Server error: ' . $e->getMessage()];
}

echo json_encode($response);
exit;
?>
