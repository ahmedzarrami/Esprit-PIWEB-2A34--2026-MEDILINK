<?php
/**
 * Classe Forum — Modèle OOP
 * Gestion des forums avec PDO (prepared statements)
 */
class Forum {
    // Propriétés privées (encapsulation)
    private ?int $idForum;
    private string $titre;
    private ?string $description;
    private ?string $createdAt;

    /**
     * Constructeur
     */
    public function __construct(
        ?int $idForum = null,
        string $titre = '',
        ?string $description = null,
        ?string $createdAt = null
    ) {
        $this->idForum = $idForum;
        $this->titre = $titre;
        $this->description = $description;
        $this->createdAt = $createdAt;
    }

    // ===== GETTERS =====
    public function getIdForum(): ?int { return $this->idForum; }
    public function getTitre(): string { return $this->titre; }
    public function getDescription(): ?string { return $this->description; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    // ===== SETTERS =====
    public function setIdForum(int $idForum): void { $this->idForum = $idForum; }
    public function setTitre(string $titre): void { $this->titre = $titre; }
    public function setDescription(?string $description): void { $this->description = $description; }
}
