<?php

declare(strict_types=1);

/**
 * Modèle Produit (Parapharmacie)
 * Getters / Setters + validation
 */
class Produit
{
    private int    $id          = 0;
    private string $reference   = '';
    private string $nom         = '';
    private string $description = '';
    private float  $prix        = 0.0;
    private int    $stock       = 0;
    private string $categorie   = 'Autre';
    private ?string $imagePath  = null;

    public function __construct(
        int    $id          = 0,
        string $reference   = '',
        string $nom         = '',
        string $description = '',
        float  $prix        = 0.0,
        int    $stock       = 0,
        string $categorie   = 'Autre',
        ?string $imagePath  = null
    ) {
        $this->id          = $id;
        $this->reference   = $reference;
        $this->nom         = $nom;
        $this->description = $description;
        $this->prix        = $prix;
        $this->stock       = $stock;
        $this->categorie   = $categorie;
        $this->imagePath   = $imagePath;
    }

    // ── Getters ──────────────────────────────

    public function getId(): int            { return $this->id; }
    public function getReference(): string  { return $this->reference; }
    public function getNom(): string        { return $this->nom; }
    public function getDescription(): string { return $this->description; }
    public function getPrix(): float        { return $this->prix; }
    public function getStock(): int         { return $this->stock; }
    public function getCategorie(): string  { return $this->categorie; }
    public function getImagePath(): ?string { return $this->imagePath; }

    // ── Setters ──────────────────────────────

    public function setId(int $id): void                { $this->id = $id; }
    public function setReference(string $ref): void      { $this->reference = $ref; }
    public function setNom(string $nom): void            { $this->nom = $nom; }
    public function setDescription(string $desc): void   { $this->description = $desc; }
    public function setPrix(float $prix): void           { $this->prix = $prix; }
    public function setStock(int $stock): void           { $this->stock = $stock; }
    public function setCategorie(string $cat): void      { $this->categorie = $cat; }
    public function setImagePath(?string $path): void    { $this->imagePath = $path; }

    // ── Statut stock ─────────────────────────

    public function statutStock(): string
    {
        if ($this->stock === 0)  return 'rupture';
        if ($this->stock <= 5)   return 'faible';
        return 'disponible';
    }

    // ── Validation ───────────────────────────

    public function valider(): array
    {
        $erreurs = [];
        if (empty(trim($this->reference))) {
            $erreurs['reference'] = 'La référence est obligatoire.';
        }
        if (empty(trim($this->nom))) {
            $erreurs['nom'] = 'Le nom est obligatoire.';
        } elseif (mb_strlen($this->nom) < 3) {
            $erreurs['nom'] = 'Le nom doit contenir au moins 3 caractères.';
        }
        if ($this->prix < 0) {
            $erreurs['prix'] = 'Le prix ne peut pas être négatif.';
        }
        if ($this->stock < 0) {
            $erreurs['stock'] = 'Le stock ne peut pas être négatif.';
        }
        if (empty(trim($this->categorie))) {
            $this->categorie = 'Autre';
        }
        return $erreurs;
    }
}
