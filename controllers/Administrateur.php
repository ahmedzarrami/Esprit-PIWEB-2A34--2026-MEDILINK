<?php
require_once __DIR__ . '/Utilisateur.php';
require_once __DIR__ . '/../models/Administrateur.php';

/**
 * AdministrateurModelController (controllers/Administrateur.php)
 * Implémentation des méthodes de la classe Administrateur
 * Contient toute la logique d'accès à la base de données pour la table administrateur
 */
class AdministrateurModelController
{
    /**
     * Inscription administrateur — insère dans utilisateur + administrateur
     */
    public static function sInscrire(array $admin): int
    {
        // Insérer dans la table utilisateur (parent)
        $id = UtilisateurController::sInscrire($admin);

        // Insérer dans la table administrateur
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO administrateur (id) VALUES (:id)");
        $stmt->execute([':id' => $id]);

        return $id;
    }

    /**
     * Gérer les utilisateurs — Récupérer la liste complète avec données jointes
     */
    public static function gererUtilisateurs(): array
    {
        $pdo = Database::getInstance();
        $sql = "SELECT u.*,
                       p.date_naissance, p.sexe, p.adresse, p.groupe_sanguin,
                       ps.specialite, ps.numero_ordre, ps.biographie
                FROM utilisateur u
                LEFT JOIN patient p ON u.id = p.id
                LEFT JOIN professionnel_sante ps ON u.id = ps.id
                ORDER BY u.date_creation DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Récupérer un utilisateur complet par ID (avec données spécifiques au rôle)
     */
    public static function getUtilisateurComplet(int $id): ?array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            "SELECT u.*,
                    p.date_naissance, p.sexe, p.adresse, p.groupe_sanguin,
                    ps.specialite, ps.numero_ordre, ps.biographie
             FROM utilisateur u
             LEFT JOIN patient p ON u.id = p.id
             LEFT JOIN professionnel_sante ps ON u.id = ps.id
             WHERE u.id = :id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Modérer le forum (placeholder — Module 4)
     */
    public static function modererForum(): void
    {
        // À implémenter dans le module Forum
    }

    /**
     * Gérer les produits (placeholder — Module 5)
     */
    public static function gererProduits(): void
    {
        // À implémenter dans le module Parapharmacie
    }
}
