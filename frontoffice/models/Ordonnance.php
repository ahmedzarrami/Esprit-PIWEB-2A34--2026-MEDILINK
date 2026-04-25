<?php

declare(strict_types=1);

class Ordonnance
{
    private int     $id           = 0;
    private string  $numero       = '';
    private string  $patientNom   = '';
    private ?int    $patientAge   = null;
    private ?string $patientSexe  = null;
    private string  $dateOrdonnance = '';
    private ?string $notes        = null;
    private string  $createdAt    = '';

    /* ── Getters ──────────────────────────────────── */

    public function getId(): int           { return $this->id; }
    public function getNumero(): string    { return $this->numero; }
    public function getPatientNom(): string { return $this->patientNom; }
    public function getPatientAge(): ?int  { return $this->patientAge; }
    public function getPatientSexe(): ?string { return $this->patientSexe; }
    public function getDateOrdonnance(): string { return $this->dateOrdonnance; }
    public function getNotes(): ?string    { return $this->notes; }
    public function getCreatedAt(): string { return $this->createdAt; }

    /* ── Setters ──────────────────────────────────── */

    public function setId(int $id): void                      { $this->id = $id; }
    public function setNumero(string $numero): void           { $this->numero = $numero; }
    public function setPatientNom(string $nom): void          { $this->patientNom = $nom; }
    public function setPatientAge(?int $age): void            { $this->patientAge = $age; }
    public function setPatientSexe(?string $sexe): void       { $this->patientSexe = $sexe; }
    public function setDateOrdonnance(string $date): void     { $this->dateOrdonnance = $date; }
    public function setNotes(?string $notes): void            { $this->notes = $notes; }
    public function setCreatedAt(string $createdAt): void     { $this->createdAt = $createdAt; }

}
