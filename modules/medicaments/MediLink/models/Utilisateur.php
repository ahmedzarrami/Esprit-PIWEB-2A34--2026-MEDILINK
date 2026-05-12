<?php
/**
 * Classe abstraite Utilisateur
 * Représente un utilisateur générique du système MediLink
 * Héritage : Patient, ProfessionnelSante, Administrateur
 */
abstract class Utilisateur {
    // Propriétés privées (encapsulation OOP)
    private ?int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $motDePasse;
    private ?string $telephone;
    private string $statutCompte;
    private string $role;

    /**
     * Constructeur
     */
    public function __construct(
        ?int $id = null,
        string $nom = '',
        string $prenom = '',
        string $email = '',
        string $motDePasse = '',
        ?string $telephone = null,
        string $statutCompte = 'actif',
        string $role = 'patient'
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->telephone = $telephone;
        $this->statutCompte = $statutCompte;
        $this->role = $role;
    }

    // ===== GETTERS =====
    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getEmail(): string { return $this->email; }
    public function getMotDePasse(): string { return $this->motDePasse; }
    public function getTelephone(): ?string { return $this->telephone; }
    public function getStatutCompte(): string { return $this->statutCompte; }
    public function getRole(): string { return $this->role; }
    public function getNomComplet(): string { return $this->prenom . ' ' . $this->nom; }

    // ===== SETTERS =====
    public function setId(int $id): void { $this->id = $id; }
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setMotDePasse(string $motDePasse): void { $this->motDePasse = $motDePasse; }
    public function setTelephone(?string $telephone): void { $this->telephone = $telephone; }
    public function setStatutCompte(string $statutCompte): void { $this->statutCompte = $statutCompte; }
    public function setRole(string $role): void { $this->role = $role; }

    // ===== MÉTHODES ABSTRAITES =====
    /**
     * Inscription de l'utilisateur
     */
    abstract public function sInscrire(): bool;

    /**
     * Connexion de l'utilisateur
     */
    abstract public function seConnecter(string $email, string $motDePasse): bool;

    /**
     * Modification du profil
     */
    public function modifierProfil(): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone WHERE id = :id");
        return $stmt->execute([
            ':nom'       => $this->nom,
            ':prenom'    => $this->prenom,
            ':email'     => $this->email,
            ':telephone' => $this->telephone,
            ':id'        => $this->id
        ]);
    }

    /**
     * Récupérer un utilisateur par son ID
     */
    public static function getById(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Récupérer tous les utilisateurs
     */
    public static function getAll(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM utilisateur ORDER BY nom, prenom");
        return $stmt->fetchAll();
    }
}

/**
 * Classe Patient — hérite de Utilisateur
 */
class Patient extends Utilisateur {
    private ?string $dateNaissance;
    private ?string $sexe;
    private ?string $adresse;

    public function __construct(
        ?int $id = null, string $nom = '', string $prenom = '', string $email = '',
        string $motDePasse = '', ?string $telephone = null, string $statutCompte = 'actif',
        ?string $dateNaissance = null, ?string $sexe = null, ?string $adresse = null
    ) {
        parent::__construct($id, $nom, $prenom, $email, $motDePasse, $telephone, $statutCompte, 'patient');
        $this->dateNaissance = $dateNaissance;
        $this->sexe = $sexe;
        $this->adresse = $adresse;
    }

    public function getDateNaissance(): ?string { return $this->dateNaissance; }
    public function getSexe(): ?string { return $this->sexe; }
    public function getAdresse(): ?string { return $this->adresse; }

    public function sInscrire(): bool { return true; }
    public function seConnecter(string $email, string $motDePasse): bool { return true; }
}

/**
 * Classe ProfessionnelSante — hérite de Utilisateur
 */
class ProfessionnelSante extends Utilisateur {
    private ?string $specialite;
    private ?string $numeroOrdre;
    private ?string $biographie;

    public function __construct(
        ?int $id = null, string $nom = '', string $prenom = '', string $email = '',
        string $motDePasse = '', ?string $telephone = null, string $statutCompte = 'actif',
        ?string $specialite = null, ?string $numeroOrdre = null, ?string $biographie = null
    ) {
        parent::__construct($id, $nom, $prenom, $email, $motDePasse, $telephone, $statutCompte, 'professionnel');
        $this->specialite = $specialite;
        $this->numeroOrdre = $numeroOrdre;
        $this->biographie = $biographie;
    }

    public function getSpecialite(): ?string { return $this->specialite; }
    public function getNumeroOrdre(): ?string { return $this->numeroOrdre; }
    public function getBiographie(): ?string { return $this->biographie; }

    public function sInscrire(): bool { return true; }
    public function seConnecter(string $email, string $motDePasse): bool { return true; }
}

/**
 * Classe Administrateur — hérite de Utilisateur
 */
class Administrateur extends Utilisateur {
    public function __construct(
        ?int $id = null, string $nom = '', string $prenom = '', string $email = '',
        string $motDePasse = '', ?string $telephone = null, string $statutCompte = 'actif'
    ) {
        parent::__construct($id, $nom, $prenom, $email, $motDePasse, $telephone, $statutCompte, 'administrateur');
    }

    public function sInscrire(): bool { return true; }
    public function seConnecter(string $email, string $motDePasse): bool { return true; }
}
