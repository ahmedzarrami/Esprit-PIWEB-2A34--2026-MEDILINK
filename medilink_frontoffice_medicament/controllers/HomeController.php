<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Medicament.php';

class HomeController
{
    private Medicament $medicamentModel;

    public function __construct()
    {
        $db = (new Database())->getConnection();
        $this->medicamentModel = new Medicament($db);
    }

    public function index(): void
    {
        $featuredMedicaments = $this->medicamentModel->getFeatured(3);
        require __DIR__ . '/../views/home/index.php';
    }
}
