<?php
/**
 * api.php — REST API for Rendezvous operations
 * Handles: add, update, delete, list rendez-vous
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

$action = $_GET['action'] ?? $_POST['action'] ?? null;
$rendezvousController = new RendezvousC();
$response = [];

try {
    switch ($action) {
        
        /* ──── ADD RENDEZVOUS ──── */
        case 'add':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['medecin_id']) || !isset($data['date_rdv']) || !isset($data['heure_rdv'])) {
                http_response_code(400);
                $response = ['success' => false, 'message' => 'Missing required fields', 'data_received' => $data];
                break;
            }

            $rdv = new Rendezvous(
                null,
                $data['medecin_id'],
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
