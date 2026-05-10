<?php

declare(strict_types=1);

require_once __DIR__ . '/../../controllers/front/HomeController.php';
require_once __DIR__ . '/../../controllers/front/MedicamentController.php';
require_once __DIR__ . '/../../controllers/front/OrdonnanceController.php';
require_once __DIR__ . '/../../controllers/front/AssistantController.php';

$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'home':
        (new HomeController())->index();
        break;

    case 'medicaments':
        (new FrontMedicamentController())->index();
        break;

    case 'show_medicament':
        (new FrontMedicamentController())->show();
        break;

    case 'ordonnances':
        (new FrontOrdonnanceController())->index();
        break;

    case 'create_ordonnance':
        (new FrontOrdonnanceController())->create();
        break;

    case 'store_ordonnance':
        (new FrontOrdonnanceController())->store();
        break;

    case 'show_ordonnance':
        (new FrontOrdonnanceController())->show();
        break;

    case 'print_ordonnance':
        (new FrontOrdonnanceController())->printView();
        break;

    case 'edit_ordonnance':
        (new FrontOrdonnanceController())->edit();
        break;

    case 'update_ordonnance':
        (new FrontOrdonnanceController())->update();
        break;

    case 'delete_ordonnance':
        (new FrontOrdonnanceController())->delete();
        break;

    case 'assistant':
        (new FrontAssistantController())->index();
        break;

    default:
        http_response_code(404);
        echo 'Page introuvable';
        break;
}
