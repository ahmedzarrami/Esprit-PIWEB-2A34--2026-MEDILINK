<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Produit.php';

class ParapharmacieController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Liste les produits pour le front-office
     */
    public function listProduits(string $categorie = null): array
    {
        $sql = "SELECT * FROM produits";
        $params = [];

        if ($categorie) {
            $sql .= " WHERE categorie = :categorie";
            $params['categorie'] = $categorie;
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un produit par son ID
     */
    public function getProduit(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM produits WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Récupère les catégories uniques
     */
    public function getCategories(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT categorie FROM produits ORDER BY categorie ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
