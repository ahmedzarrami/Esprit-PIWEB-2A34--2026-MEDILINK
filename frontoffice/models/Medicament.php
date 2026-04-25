<?php

declare(strict_types=1);

class Medicament
{
    private int     $id             = 0;
    private string  $nom            = '';
    private ?string $description    = null;
    private ?string $dosage         = null;
    private ?string $forme          = null;
    private ?string $fabricant      = null;
    private float   $prix           = 0.0;
    private int     $stock          = 0;
    private ?string $dateExpiration = null;
    private string  $createdAt      = '';

    /* ── Getters ──────────────────────────────────── */

    public function getId(): int              { return $this->id; }
    public function getNom(): string          { return $this->nom; }
    public function getDescription(): ?string { return $this->description; }
    public function getDosage(): ?string      { return $this->dosage; }
    public function getForme(): ?string       { return $this->forme; }
    public function getFabricant(): ?string   { return $this->fabricant; }
    public function getPrix(): float          { return $this->prix; }
    public function getStock(): int           { return $this->stock; }
    public function getDateExpiration(): ?string { return $this->dateExpiration; }
    public function getCreatedAt(): string    { return $this->createdAt; }

    /* ── Setters ──────────────────────────────────── */

    public function setId(int $id): void                      { $this->id = $id; }
    public function setNom(string $nom): void                 { $this->nom = $nom; }
    public function setDescription(?string $v): void          { $this->description = $v; }
    public function setDosage(?string $v): void               { $this->dosage = $v; }
    public function setForme(?string $v): void                { $this->forme = $v; }
    public function setFabricant(?string $v): void            { $this->fabricant = $v; }
    public function setPrix(float $v): void                   { $this->prix = $v; }
    public function setStock(int $v): void                    { $this->stock = $v; }
    public function setDateExpiration(?string $v): void       { $this->dateExpiration = $v; }
    public function setCreatedAt(string $v): void             { $this->createdAt = $v; }

}
