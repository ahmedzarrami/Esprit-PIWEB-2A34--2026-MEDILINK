<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Medicament.php';

class FrontMedicamentController
{
    private Medicament $medicamentModel;

    public function __construct()
    {
        $db = (new Database())->getConnection();
        $this->medicamentModel = new Medicament($db);
    }

    public function index(): void
    {
        $search = trim((string) ($_GET['search'] ?? ''));
        $sort = trim((string) ($_GET['sort'] ?? 'nom_asc'));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 6;

        $total = $this->medicamentModel->countAll($search);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $medicaments = $this->medicamentModel->getPaginated($perPage, $offset, $search, $sort);

        require __DIR__ . '/../views/medicament/index.php';
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $medicament = $this->medicamentModel->getById($id);

        if ($medicament === null) {
            http_response_code(404);
            $errorMessage = 'Le médicament demandé est introuvable.';
            require __DIR__ . '/../views/medicament/not-found.php';
            return;
        }

        require __DIR__ . '/../views/medicament/show.php';
    }
}
