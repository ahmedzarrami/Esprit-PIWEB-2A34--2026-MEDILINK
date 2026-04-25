<?php

declare(strict_types=1);

class Medicament
{
    private ?int $id = null;
    private string $nom = '';
    private string $description = '';
    private string $dosage = '';
    private string $forme = '';
    private string $fabricant = '';
    private float $prix = 0.0;
    private ?string $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDosage(): string
    {
        return $this->dosage;
    }

    public function setDosage(string $dosage): void
    {
        $this->dosage = $dosage;
    }

    public function getForme(): string
    {
        return $this->forme;
    }

    public function setForme(string $forme): void
    {
        $this->forme = $forme;
    }

    public function getFabricant(): string
    {
        return $this->fabricant;
    }

    public function setFabricant(string $fabricant): void
    {
        $this->fabricant = $fabricant;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): void
    {
        $this->prix = $prix;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
