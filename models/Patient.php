<?php
require_once __DIR__ . '/Utilisateur.php';

/**
 * Classe Patient — Hérite de Utilisateur
 */
class Patient extends Utilisateur
{
    // ── Propriétés spécifiques au patient ──
    private ?string $dateNaissance  = null;
    private ?string $sexe           = null;
    private ?string $adresse        = null;
    private ?string $groupeSanguin  = null;

    // ── Constructeur ──
    public function __construct(
        string $nom = '',
        string $prenom = '',
        string $email = '',
        string $motDePasse = '',
        string $telephone = '',
        string $statutCompte = 'Actif',
        ?string $dateNaissance = null,
        ?string $sexe = null,
        ?string $adresse = null,
        ?string $groupeSanguin = null
    ) {
        parent::__construct($nom, $prenom, $email, $motDePasse, $telephone, $statutCompte, 'Patient');
        $this->dateNaissance = $dateNaissance;
        $this->sexe          = $sexe;
        $this->adresse       = $adresse;
        $this->groupeSanguin = $groupeSanguin;
    }

    // ── Getters ──
    public function getDateNaissance(): ?string  { return $this->dateNaissance; }
    public function getSexe(): ?string           { return $this->sexe; }
    public function getAdresse(): ?string        { return $this->adresse; }
    public function getGroupeSanguin(): ?string  { return $this->groupeSanguin; }

    // ── Setters ──
    public function setDateNaissance(?string $d): void { $this->dateNaissance = $d; }
    public function setSexe(?string $s): void          { $this->sexe = $s; }
    public function setAdresse(?string $a): void       { $this->adresse = $a; }
    public function setGroupeSanguin(?string $g): void { $this->groupeSanguin = $g; }

    /**
     * Inscription patient — insère dans utilisateur + patient
     */
    public function sInscrire(): int
    {
        // Insérer dans la table utilisateur (parent)
        $id = parent::sInscrire();

        // Insérer dans la table patient
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO patient (id, date_naissance, sexe, adresse, groupe_sanguin)
             VALUES (:id, :dob, :sexe, :adresse, :gs)"
        );
        $stmt->execute([
            ':id'      => $id,
            ':dob'     => $this->dateNaissance,
            ':sexe'    => $this->sexe,
            ':adresse' => $this->adresse,
            ':gs'      => $this->groupeSanguin,
        ]);

        return $id;
    }

    /**
     * Modifier le profil patient
     */
    public function modifierProfil(): bool
    {
        // Mettre à jour la table utilisateur
        parent::modifierProfil();

        // Mettre à jour la table patient
        $pdo  = Database::getInstance();

        // Vérifier si la ligne patient existe
        $check = $pdo->prepare("SELECT COUNT(*) FROM patient WHERE id = :id");
        $check->execute([':id' => $this->getId()]);

        if ((int) $check->fetchColumn() > 0) {
            $stmt = $pdo->prepare(
                "UPDATE patient
                 SET date_naissance = :dob, sexe = :sexe, adresse = :adresse, groupe_sanguin = :gs
                 WHERE id = :id"
            );
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO patient (id, date_naissance, sexe, adresse, groupe_sanguin)
                 VALUES (:id, :dob, :sexe, :adresse, :gs)"
            );
        }

        return $stmt->execute([
            ':id'      => $this->getId(),
            ':dob'     => $this->dateNaissance,
            ':sexe'    => $this->sexe,
            ':adresse' => $this->adresse,
            ':gs'      => $this->groupeSanguin,
        ]);
    }

    /**
     * Récupérer les données complètes d'un patient par ID
     */
    public static function getPatientById(int $id): ?array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            "SELECT u.*, p.date_naissance, p.sexe, p.adresse, p.groupe_sanguin
             FROM utilisateur u
             LEFT JOIN patient p ON u.id = p.id
             WHERE u.id = :id AND u.role = 'Patient'"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
