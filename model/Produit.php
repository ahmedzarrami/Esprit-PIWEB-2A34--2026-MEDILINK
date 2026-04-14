<?php
// ════════════════════════════════════════════
//  model/Produit.php  –  Modèle Produit
// ════════════════════════════════════════════

class Produit {
    public int    $id;
    public string $reference;
    public string $nom;
    public string $description;
    public float  $prix;
    public int    $stock;
    public string $categorie;

    public function __construct(
        int    $id          = 0,
        string $reference   = '',
        string $nom         = '',
        string $description = '',
        float  $prix        = 0.0,
        int    $stock       = 0,
        string $categorie   = ''
    ) {
        $this->id          = $id;
        $this->reference   = $reference;
        $this->nom         = $nom;
        $this->description = $description;
        $this->prix        = $prix;
        $this->stock       = $stock;
        $this->categorie   = $categorie;
    }

    // Retourne le statut du stock
    public function statutStock(): string {
        if ($this->stock === 0)    return 'rupture';
        if ($this->stock <= 5)     return 'faible';
        return 'disponible';
    }

    // Validation de base
    public function valider(): array {
        $erreurs = [];
        if (empty(trim($this->reference)))   $erreurs[] = 'La référence est obligatoire.';
        if (empty(trim($this->nom)))         $erreurs[] = 'Le nom est obligatoire.';
        if ($this->prix < 0)                 $erreurs[] = 'Le prix ne peut pas être négatif.';
        if ($this->stock < 0)                $erreurs[] = 'Le stock ne peut pas être négatif.';
        if (empty(trim($this->categorie)))   $this->categorie = 'Autre'; // Défaut si vide
        return $erreurs;
    }
}
