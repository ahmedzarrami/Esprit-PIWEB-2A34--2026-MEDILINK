<?php
require_once __DIR__ . '/Utilisateur.php';

/**
 * Classe Patient — Hérite de Utilisateur (Modèle)
 * Contient les attributs et les signatures de méthodes.
 * L'implémentation des méthodes se trouve dans controllers/Patient.php
 */
abstract class Patient extends Utilisateur
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
    abstract public function sInscrire(): int;

    /**
     * Modifier le profil patient
     */
    abstract public function modifierProfil(): bool;

    /**
     * Récupérer les données complètes d'un patient par ID
     */
    abstract public static function getPatientById(int $id): ?array;
}
