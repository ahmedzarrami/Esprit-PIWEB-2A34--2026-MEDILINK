<?php

declare(strict_types=1);

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Medicament.php';

class HomeController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function index(): void
    {
        $stmt = $this->db->prepare('SELECT * FROM medicaments ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue(':limit', 3, PDO::PARAM_INT);
        $stmt->execute();

        $featuredMedicaments = array_map(function (array $row): array {
            $m = new Medicament();
            $m->setId((int) $row['id']);
            $m->setNom($row['nom']);
            $m->setDescription($row['description'] ?: null);
            $m->setDosage($row['dosage'] ?: null);
            $m->setForme($row['forme'] ?: null);
            $m->setFabricant($row['fabricant'] ?: null);
            $m->setPrix((float) $row['prix']);
            $m->setStock((int) $row['stock']);
            $m->setDateExpiration($row['date_expiration'] ?: null);
            $m->setCreatedAt($row['created_at']);

            return [
                'id'              => $m->getId(),
                'nom'             => $m->getNom(),
                'description'     => $m->getDescription(),
                'dosage'          => $m->getDosage(),
                'forme'           => $m->getForme(),
                'fabricant'       => $m->getFabricant(),
                'prix'            => $m->getPrix(),
                'stock'           => $m->getStock(),
                'date_expiration' => $m->getDateExpiration(),
                'created_at'      => $m->getCreatedAt(),
            ];
        }, $stmt->fetchAll());

        $this->render('home/index', [
            'featuredMedicaments' => $featuredMedicaments,
        ]);
    }
}
