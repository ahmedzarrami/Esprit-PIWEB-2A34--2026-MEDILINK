<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Utilisateur.php';

/**
 * UtilisateurController — Implémentation des méthodes CRUD de la classe Utilisateur
 * Contient toute la logique d'accès à la base de données pour la table utilisateur
 */
class UtilisateurController
{
    /**
     * Inscription — Insère dans la table utilisateur
     * Retourne l'ID inséré
     */
    public static function sInscrire(array &$user): int
    {
        $pdo = Database::getInstance();
        $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, telephone, statut_compte, role)
                VALUES (:nom, :prenom, :email, :mdp, :tel, :statut, :role)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'    => $user['nom'],
            ':prenom' => $user['prenom'],
            ':email'  => $user['email'],
            ':mdp'    => password_hash($user['mot_de_passe'], PASSWORD_DEFAULT),
            ':tel'    => $user['telephone'],
            ':statut' => $user['statut_compte'] ?? 'Actif',
            ':role'   => $user['role'],
        ]);
        $id = (int) $pdo->lastInsertId();
        $user['id'] = $id;
        return $id;
    }

    /**
     * Connexion — Vérifie email et mot de passe
     * Retourne les données utilisateur ou false
     */
    public static function seConnecter(string $email, string $motDePasse)
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($motDePasse, $user['mot_de_passe'])) {
            return $user;
        }
        return false;
    }

    /**
     * Modifier le profil (table utilisateur)
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
     * Modifier le mot de passe
     */
    public static function changerMotDePasse(int $id, string $nouveauMdp): bool
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE utilisateur SET mot_de_passe = :mdp WHERE id = :id");
        return $stmt->execute([
            ':mdp' => password_hash($nouveauMdp, PASSWORD_DEFAULT),
            ':id'  => $id,
        ]);
    }

    /**
     * Récupérer un utilisateur par ID
     */
    public static function getById(int $id): ?array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Récupérer un utilisateur par email
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
     * Lister tous les utilisateurs
     */
    public static function getAll(): array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM utilisateur ORDER BY date_creation DESC");
        return $stmt->fetchAll();
    }

    /**
     * Supprimer un utilisateur par ID
     */
    public static function supprimer(int $id): bool
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Compter le nombre d'utilisateurs par rôle
     */
    public static function countByRole(string $role = ''): int
    {
        $pdo = Database::getInstance();
        if ($role) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE role = :role");
            $stmt->execute([':role' => $role]);
        } else {
            $stmt = $pdo->query("SELECT COUNT(*) FROM utilisateur");
        }
        return (int) $stmt->fetchColumn();
    }

    /**
     * Compter les comptes actifs
     */
    public static function countActifs(): int
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->query("SELECT COUNT(*) FROM utilisateur WHERE statut_compte = 'Actif'");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Vérifier si un email existe déjà (optionnel: exclure un ID)
     */
    public static function emailExiste(string $email, ?int $excludeId = null): bool
    {
        $pdo = Database::getInstance();
        if ($excludeId) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = :email AND id != :id");
            $stmt->execute([':email' => $email, ':id' => $excludeId]);
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = :email");
            $stmt->execute([':email' => $email]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }
}
