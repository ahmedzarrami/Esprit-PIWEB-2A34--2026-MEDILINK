<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../../config/session.php';
require_role('Administrateur');

require_once __DIR__ . '/../../controllers/back/MedicamentController.php';

$controller = new MedicamentController();
$action     = $_GET['action'] ?? 'index';

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
