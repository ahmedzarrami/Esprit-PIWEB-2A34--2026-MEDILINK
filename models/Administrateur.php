<?php
require_once __DIR__ . '/Utilisateur.php';

/**
 * Classe Administrateur — Hérite de Utilisateur
 */
class Administrateur extends Utilisateur
{
    // ── Constructeur ──
    public function __construct(
        string $nom = '',
        string $prenom = '',
        string $email = '',
        string $motDePasse = '',
        string $telephone = '',
        string $statutCompte = 'Actif'
    ) {
        parent::__construct($nom, $prenom, $email, $motDePasse, $telephone, $statutCompte, 'Administrateur');
    }

    /**
     * Inscription administrateur — insère dans utilisateur + administrateur
     */
    public function sInscrire(): int
    {
        $id = parent::sInscrire();

        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO administrateur (id) VALUES (:id)");
        $stmt->execute([':id' => $id]);

        return $id;
    }

    /**
     * Gérer les utilisateurs — Récupérer la liste complète avec données jointes
     */
    public function gererUtilisateurs(): array
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
    public function getUtilisateurComplet(int $id): ?array
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
    public function modererForum(): void
    {
        // À implémenter dans le module Forum
    }

    /**
     * Gérer les produits (placeholder — Module 5)
     */
    public function gererProduits(): void
    {
        // À implémenter dans le module Parapharmacie
    }
}
