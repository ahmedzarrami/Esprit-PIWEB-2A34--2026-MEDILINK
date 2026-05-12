<?php

declare(strict_types=1);

require_once __DIR__ . '/FrontController.php';
require_once __DIR__ . '/../../config/Database.php';

class FrontAssistantController extends FrontController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function index(): void
    {
        $stmt = $this->db->query(
            'SELECT id, nom, description, dosage, forme, fabricant, prix FROM medicaments ORDER BY nom ASC'
        );
        $medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('assistant/index', [
            'medicaments' => $medicaments,
        ]);
    }
}

