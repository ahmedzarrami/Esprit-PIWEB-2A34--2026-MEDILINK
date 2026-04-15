<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Classe abstraite Utilisateur
 * Classe parente pour Patient, ProfessionnelSante, Administrateur
 */
abstract class Utilisateur
{
    // ── Propriétés privées ──
    private ?int    $id           = null;
    private string  $nom          = '';
    private string  $prenom       = '';
    private string  $email        = '';
    private string  $motDePasse   = '';
    private string  $telephone    = '';
    private string  $statutCompte = 'Actif';
    private string  $role         = '';
    private ?string $dateCreation = null;

    // ── Constructeur ──
    public function __construct(
        string $nom = '',
        string $prenom = '',
        string $email = '',
        string $motDePasse = '',
        string $telephone = '',
        string $statutCompte = 'Actif',
        string $role = ''
    ) {
        $this->nom          = $nom;
        $this->prenom       = $prenom;
        $this->email        = $email;
        $this->motDePasse   = $motDePasse;
        $this->telephone    = $telephone;
        $this->statutCompte = $statutCompte;
        $this->role         = $role;
    }

    // ══════════════════════════════════════════
    // GETTERS
    // ══════════════════════════════════════════
    public function getId(): ?int            { return $this->id; }
    public function getNom(): string         { return $this->nom; }
    public function getPrenom(): string      { return $this->prenom; }
    public function getEmail(): string       { return $this->email; }
    public function getMotDePasse(): string  { return $this->motDePasse; }
    public function getTelephone(): string   { return $this->telephone; }
    public function getStatutCompte(): string{ return $this->statutCompte; }
    public function getRole(): string        { return $this->role; }
    public function getDateCreation(): ?string { return $this->dateCreation; }

    // ══════════════════════════════════════════
    // SETTERS
    // ══════════════════════════════════════════
    public function setId(?int $id): void            { $this->id = $id; }
    public function setNom(string $nom): void         { $this->nom = $nom; }
    public function setPrenom(string $prenom): void   { $this->prenom = $prenom; }
    public function setEmail(string $email): void     { $this->email = $email; }
    public function setMotDePasse(string $mdp): void  { $this->motDePasse = $mdp; }
    public function setTelephone(string $tel): void   { $this->telephone = $tel; }
    public function setStatutCompte(string $s): void  { $this->statutCompte = $s; }
    public function setRole(string $role): void       { $this->role = $role; }
    public function setDateCreation(?string $d): void { $this->dateCreation = $d; }

    // ══════════════════════════════════════════
    // MÉTHODES CRUD — Utilisateur (table parent)
    // ══════════════════════════════════════════

    /**
     * Inscription — Insère dans la table utilisateur
     * Retourne l'ID inséré
     */
    public function sInscrire(): int
    {
        $pdo = Database::getInstance();
        $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, telephone, statut_compte, role)
                VALUES (:nom, :prenom, :email, :mdp, :tel, :statut, :role)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'    => $this->nom,
            ':prenom' => $this->prenom,
            ':email'  => $this->email,
            ':mdp'    => password_hash($this->motDePasse, PASSWORD_DEFAULT),
            ':tel'    => $this->telephone,
            ':statut' => $this->statutCompte,
            ':role'   => $this->role,
        ]);
        $this->id = (int) $pdo->lastInsertId();
        return $this->id;
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
    public function modifierProfil(): bool
    {
        $pdo = Database::getInstance();
        $sql = "UPDATE utilisateur
                SET nom = :nom, prenom = :prenom, email = :email,
                    telephone = :tel, statut_compte = :statut
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':nom'    => $this->nom,
            ':prenom' => $this->prenom,
            ':email'  => $this->email,
            ':tel'    => $this->telephone,
            ':statut' => $this->statutCompte,
            ':id'     => $this->id,
        ]);
    }

    /**
     * Modifier le mot de passe
     */
    public function changerMotDePasse(string $nouveauMdp): bool
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE utilisateur SET mot_de_passe = :mdp WHERE id = :id");
        return $stmt->execute([
            ':mdp' => password_hash($nouveauMdp, PASSWORD_DEFAULT),
            ':id'  => $this->id,
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
     * Lister tous les utilisateurs (admin)
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
