<?php
require_once __DIR__ . '/Utilisateur.php';

/**
 * Classe ProfessionnelSante — Hérite de Utilisateur (Modèle)
 * Contient les attributs et les signatures de méthodes.
 * L'implémentation des méthodes se trouve dans controllers/ProfessionnelSante.php
 */
abstract class ProfessionnelSante extends Utilisateur
{
    // ── Propriétés spécifiques ──
    private string  $specialite  = '';
    private string  $numeroOrdre = '';
    private ?string $biographie  = null;

    // ── Constructeur ──
    public function __construct(
        string $nom = '',
        string $prenom = '',
        string $email = '',
        string $motDePasse = '',
        string $telephone = '',
        string $statutCompte = 'Actif',
        string $specialite = '',
        string $numeroOrdre = '',
        ?string $biographie = null
    ) {
        parent::__construct($nom, $prenom, $email, $motDePasse, $telephone, $statutCompte, 'Professionnel');
        $this->specialite  = $specialite;
        $this->numeroOrdre = $numeroOrdre;
        $this->biographie  = $biographie;
    }

    // ── Getters ──
    public function getSpecialite(): string   { return $this->specialite; }
    public function getNumeroOrdre(): string  { return $this->numeroOrdre; }
    public function getBiographie(): ?string  { return $this->biographie; }

    // ── Setters ──
    public function setSpecialite(string $s): void   { $this->specialite = $s; }
    public function setNumeroOrdre(string $n): void  { $this->numeroOrdre = $n; }
    public function setBiographie(?string $b): void  { $this->biographie = $b; }


    /**
     * Inscription professionnel — insère dans utilisateur + professionnel_sante
     */
    abstract public function sInscrire(): int;

    /**
     * Modifier le profil professionnel
     */
    abstract public function modifierProfil(): bool;

    /**
     * Récupérer les données complètes d'un professionnel par ID
     */
    abstract public static function getProById(int $id): ?array;
}
