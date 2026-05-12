<?php
// Dépendance vers le contrôleur utilisateur de base
require_once __DIR__ . '/Utilisateur.php';
// Dépendance vers le modèle abstrait Administrateur
require_once __DIR__ . '/../models/Administrateur.php';

/**
 * AdministrateurModelController
 * Gère les opérations sur la table "administrateur" et les requêtes globales
 * qui concernent tous les types d'utilisateurs (vue unifiée pour le backoffice).
 * Toutes les méthodes sont statiques.
 */
class AdministrateurModelController
{
    /**
     * Crée un compte administrateur dans les deux tables :
     * 1. Insère les données dans "utilisateur"
     * 2. Insère une ligne dans "administrateur" (table simple, juste l'ID)
     * L'admin n'a pas de données spécifiques supplémentaires — la table "administrateur"
     * existe uniquement pour respecter la structure d'héritage.
     */
    public static function sInscrire(array $admin): int
    {
        // Création dans la table parente avec hash du mot de passe
        $id = UtilisateurController::sInscrire($admin);

        // La table "administrateur" ne contient que l'ID (clé étrangère vers utilisateur)
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO administrateur (id) VALUES (:id)");
        $stmt->execute([':id' => $id]);

        return $id;
    }

    /**
     * Récupère la liste COMPLÈTE de tous les utilisateurs avec leurs données spécifiques.
     * Utilise un double LEFT JOIN pour récupérer les colonnes des trois tables en une seule requête.
     * Les colonnes qui ne s'appliquent pas à un rôle auront la valeur NULL
     * (ex: "specialite" sera NULL pour un patient).
     */
    public static function gererUtilisateurs(): array
    {
        $pdo = Database::getInstance();

        // u.* = toutes les colonnes de "utilisateur"
        // Les colonnes patient et professionnel sont ajoutées à droite
        // LEFT JOIN garde tous les utilisateurs même sans ligne dans patient ou professionnel_sante
        $sql = "SELECT u.*,
                       p.date_naissance, p.sexe, p.adresse, p.groupe_sanguin,
                       ps.specialite, ps.numero_ordre, ps.biographie
                FROM utilisateur u
                LEFT JOIN patient p ON u.id = p.id
                LEFT JOIN professionnel_sante ps ON u.id = ps.id
                ORDER BY u.date_creation DESC";

        // query() car pas de paramètres variables (pas de risque d'injection SQL ici)
        $stmt = $pdo->query($sql);
        // fetchAll() retourne tous les résultats dans un seul tableau PHP
        return $stmt->fetchAll();
    }

    /**
     * Récupère les données COMPLÈTES d'un seul utilisateur par son ID.
     * Même jointure que gererUtilisateurs() mais filtrée sur un ID précis.
     * Utilisée par l'admin pour afficher le détail d'un utilisateur dans une modale.
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

        // Retourne null si aucun utilisateur trouvé avec cet ID
        return $row ?: null;
    }

    /**
     * Placeholder pour la modération du forum (Module 4).
     * La méthode est déclarée ici car le modèle abstrait Administrateur l'exige,
     * mais elle sera implémentée lors du développement du module Forum.
     */
    public static function modererForum(): void
    {
        // À implémenter dans le module Forum
    }

    /**
     * Placeholder pour la gestion des produits (Module 5).
     * Même raison : contrat du modèle abstrait respecté,
     * implémentation reportée au module Parapharmacie.
     */
    public static function gererProduits(): void
    {
        // À implémenter dans le module Parapharmacie
    }
}
