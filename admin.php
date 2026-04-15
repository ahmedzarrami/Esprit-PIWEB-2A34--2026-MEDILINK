<?php
/**
 * MediLink — BackOffice Router (admin.php)
 * Point d'entrée pour le panneau d'administration
 */
session_start();

require_once __DIR__ . '/controllers/AdminController.php';

$adminCtrl = new AdminController();

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// ─── Requêtes AJAX (JSON) ───
if (!empty($action) && ($_SERVER['REQUEST_METHOD'] === 'POST' || $action === 'list')) {

    header('Content-Type: application/json; charset=utf-8');

    switch ($action) {
        case 'list':
            $users = $adminCtrl->list();
            $stats = $adminCtrl->getStats();
            echo json_encode(['users' => $users, 'stats' => $stats]);
            exit;

        case 'create':
            $result = $adminCtrl->create($_POST);
            echo json_encode($result);
            exit;

        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                $result = $adminCtrl->update($id, $_POST);
            } else {
                $result = ['success' => false, 'errors' => ['global' => 'ID invalide.']];
            }
            echo json_encode($result);
            exit;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                $result = $adminCtrl->delete($id);
            } else {
                $result = ['success' => false, 'errors' => ['global' => 'ID invalide.']];
            }
            echo json_encode($result);
            exit;

        case 'get':
            $id = (int)($_GET['id'] ?? 0);
            $user = $adminCtrl->getUser($id);
            echo json_encode($user ?: ['error' => 'Utilisateur non trouvé']);
            exit;
    }
}

// ─── Chargement initial de la page (non-AJAX) ───
$allUsers = $adminCtrl->list();
$stats    = $adminCtrl->getStats();
$usersJson = json_encode($allUsers);

$viewFile = __DIR__ . '/views/backoffice/users.php';

// ─── Rendu du layout ───
include __DIR__ . '/views/backoffice/layout.php';
