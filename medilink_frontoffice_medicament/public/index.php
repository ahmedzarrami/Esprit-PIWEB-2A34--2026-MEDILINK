<?php

declare(strict_types=1);

require_once __DIR__ . '/../controllers/HomeController.php';
require_once __DIR__ . '/../controllers/FrontMedicamentController.php';

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

    default:
        http_response_code(404);
        echo 'Page introuvable';
        break;
}
