<?php

/**
 * Classe abstraite Utilisateur (Modèle)
 * Contient les attributs et les signatures de méthodes.
 * L'implémentation des méthodes se trouve dans controllers/Utilisateur.php
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

    

    /**
     * Inscription — Insère dans la table utilisateur
     * Retourne l'ID inséré
     */
    abstract public function sInscrire(): int;

    /**
     * Connexion — Vérifie email et mot de passe
     * Retourne les données utilisateur ou false
     */
    abstract public static function seConnecter(string $email, string $motDePasse);

    /**
     * Modifier le profil (table utilisateur)
     */
    abstract public function modifierProfil(): bool;

    /**
     * Modifier le mot de passe
     */
    abstract public function changerMotDePasse(string $nouveauMdp): bool;

    /**
     * Récupérer un utilisateur par ID
     */
    abstract public static function getById(int $id): ?array;

    /**
     * Récupérer un utilisateur par email
     */
    abstract public static function getByEmail(string $email): ?array;

    /**
     * Lister tous les utilisateurs
     */
    abstract public static function getAll(): array;

    /**
     * Supprimer un utilisateur par ID
     */
    abstract public static function supprimer(int $id): bool;

    /**
     * Compter le nombre d'utilisateurs par rôle
     */
    abstract public static function countByRole(string $role = ''): int;

    /**
     * Compter les comptes actifs
     */
    abstract public static function countActifs(): int;

    /**
     * Vérifier si un email existe déjà
     */
    abstract public static function emailExiste(string $email, ?int $excludeId = null): bool;
}
