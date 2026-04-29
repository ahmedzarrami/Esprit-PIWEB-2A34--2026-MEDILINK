<?php
// Chargement de la configuration de la base de données (singleton PDO)
require_once __DIR__ . '/../config/Database.php';
// Chargement du modèle abstrait Utilisateur (définit le contrat des méthodes)
require_once __DIR__ . '/../models/Utilisateur.php';

/**
 * UtilisateurController
 * Implémente toutes les opérations CRUD sur la table "utilisateur".
 * Toutes les méthodes sont statiques : on les appelle sans instancier la classe.
 * Ex: UtilisateurController::getById(5)
 */
class UtilisateurController
{
    /**
     * Insère un nouvel utilisateur dans la table "utilisateur".
     * Le tableau $user est passé PAR RÉFÉRENCE (&) pour pouvoir
     * y ajouter l'ID généré après l'insertion.
     * Retourne l'ID de la ligne insérée.
     */
    public static function sInscrire(array &$user): int
    {
        // Récupération de l'instance PDO unique (pattern Singleton)
        $pdo = Database::getInstance();

        // Requête préparée : les :placeholders empêchent les injections SQL
        $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, telephone, statut_compte, role)
                VALUES (:nom, :prenom, :email, :mdp, :tel, :statut, :role)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':nom'    => $user['nom'],
            ':prenom' => $user['prenom'],
            ':email'  => $user['email'],
            // Le mot de passe est hashé avec bcrypt avant d'être stocké
            // PASSWORD_DEFAULT utilise l'algorithme le plus sécurisé disponible
            ':mdp'    => password_hash($user['mot_de_passe'], PASSWORD_DEFAULT),
            ':tel'    => $user['telephone'],
            // Si le statut n'est pas fourni, "Actif" par défaut
            ':statut' => $user['statut_compte'] ?? 'Actif',
            ':role'   => $user['role'],
        ]);

        // Récupère l'ID auto-incrémenté de la ligne qui vient d'être insérée
        $id = (int) $pdo->lastInsertId();

        // Ajoute l'ID dans le tableau original (accessible depuis l'appelant grâce au &)
        $user['id'] = $id;

        return $id;
    }

    /**
     * Vérifie les identifiants de connexion.
     * Retourne le tableau complet de l'utilisateur si succès, false sinon.
     */
    public static function seConnecter(string $email, string $motDePasse)
    {
        $pdo = Database::getInstance();

        // Recherche l'utilisateur par email uniquement (pas par mot de passe en clair)
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $stmt->execute([':email' => $email]);

        // fetch() retourne un tableau associatif ou false si aucun résultat
        $user = $stmt->fetch();

        // password_verify() compare le mot de passe saisi avec le hash stocké en base
        // C'est la seule méthode sécurisée pour vérifier un mot de passe hashé
        if ($user && password_verify($motDePasse, $user['mot_de_passe'])) {
            return $user; // Connexion réussie : retourne toutes les données utilisateur
        }

        return false; // Email inexistant ou mot de passe incorrect
    }

    /**
     * Met à jour les informations de base dans la table "utilisateur".
     * Ne touche PAS aux tables filles (patient, professionnel_sante).
     * Retourne true si la mise à jour a réussi.
     */
    public static function modifierProfil(array $user): bool
    {
        $pdo = Database::getInstance();

        $sql = "UPDATE utilisateur
                SET nom = :nom, prenom = :prenom, email = :email,
                    telephone = :tel, statut_compte = :statut
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            ':nom'    => $user['nom'],
            ':prenom' => $user['prenom'],
            ':email'  => $user['email'],
            ':tel'    => $user['telephone'],
            ':statut' => $user['statut_compte'] ?? 'Actif',
            ':id'     => $user['id'],
        ]);
    }

    /**
     * Remplace le mot de passe d'un utilisateur par un nouveau hash.
     * Le nouveau mot de passe en clair est hashé avant stockage.
     */
    public static function changerMotDePasse(int $id, string $nouveauMdp): bool
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE utilisateur SET mot_de_passe = :mdp WHERE id = :id");

        return $stmt->execute([
            // Re-hashage du nouveau mot de passe
            ':mdp' => password_hash($nouveauMdp, PASSWORD_DEFAULT),
            ':id'  => $id,
        ]);
    }

    /**
     * Retourne un utilisateur complet par son ID.
     * Retourne null si aucun utilisateur trouvé (grâce à ?: null).
     */
    public static function getById(int $id): ?array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch();
        // L'opérateur ?: retourne $row si truthy, null sinon (fetch() retourne false si vide)
        return $row ?: null;
    }

    /**
     * Retourne un utilisateur par son adresse email.
     * Utile pour vérifier si un email existe avant inscription.
     */
    public static function getByEmail(string $email): ?array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $stmt->execute([':email' => $email]);

        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Retourne la liste de tous les utilisateurs, triés du plus récent au plus ancien.
     * Utilise query() car pas de paramètre (pas de risque d'injection SQL ici).
     */
    public static function getAll(): array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM utilisateur ORDER BY date_creation DESC");
        // fetchAll() retourne un tableau de tableaux (tous les résultats d'un coup)
        return $stmt->fetchAll();
    }

    /**
     * Supprime un utilisateur par ID.
     * Grâce aux clés étrangères ON DELETE CASCADE en base,
     * les lignes dans patient/professionnel_sante sont supprimées automatiquement.
     */
    public static function supprimer(int $id): bool
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Compte le nombre d'utilisateurs, optionnellement filtré par rôle.
     * Si $role est vide, compte tous les utilisateurs sans distinction.
     */
    public static function countByRole(string $role = ''): int
    {
        $pdo = Database::getInstance();

        if ($role) {
            // Filtre par rôle spécifique (ex: 'Patient', 'Professionnel')
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE role = :role");
            $stmt->execute([':role' => $role]);
        } else {
            // Compte tous les utilisateurs sans filtre
            $stmt = $pdo->query("SELECT COUNT(*) FROM utilisateur");
        }

        // fetchColumn() retourne la valeur de la première colonne du premier résultat
        return (int) $stmt->fetchColumn();
    }

    /**
     * Compte uniquement les comptes ayant le statut "Actif".
     */
    public static function countActifs(): int
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->query("SELECT COUNT(*) FROM utilisateur WHERE statut_compte = 'Actif'");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Vérifie si un email est déjà utilisé dans la base.
     * $excludeId permet d'exclure un utilisateur du check (utile lors d'une modification :
     * on ne veut pas que l'utilisateur soit bloqué par son propre email actuel).
     */
    public static function emailExiste(string $email, ?int $excludeId = null): bool
    {
        $pdo = Database::getInstance();

        if ($excludeId) {
            // En mode modification : exclut l'utilisateur qu'on est en train de modifier
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = :email AND id != :id");
            $stmt->execute([':email' => $email, ':id' => $excludeId]);
        } else {
            // En mode création : vérifie simplement si l'email existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = :email");
            $stmt->execute([':email' => $email]);
        }

        // Retourne true si le COUNT est supérieur à 0 (email déjà pris)
        return (int) $stmt->fetchColumn() > 0;
    }
}
