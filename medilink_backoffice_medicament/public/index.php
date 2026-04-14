<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../app/controllers/MedicamentController.php';

$controller = new MedicamentController();
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'show':
        $controller->show();
        break;
    case 'create':
        $controller->create();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'delete':
        $controller->delete();
        break;
    default:
        http_response_code(404);
        echo 'Action non reconnue.';
        break;
}
