<?php
/**
 * Classe Post — Modèle OOP
 * Gestion des posts de forum (Entité)
 */
class Post {
    // Propriétés privées (encapsulation)
    private ?int $idPost;
    private string $contenu;
    private ?string $datePublication;
    private int $idForum;
    private int $idAuteur;

    /**
     * Constructeur
     */
    public function __construct(
        ?int $idPost = null,
        string $contenu = '',
        ?string $datePublication = null,
        int $idForum = 0,
        int $idAuteur = 0
    ) {
        $this->idPost = $idPost;
        $this->contenu = $contenu;
        $this->datePublication = $datePublication;
        $this->idForum = $idForum;
        $this->idAuteur = $idAuteur;
    }

    // ===== GETTERS =====
    public function getIdPost(): ?int { return $this->idPost; }
    public function getContenu(): string { return $this->contenu; }
    public function getDatePublication(): ?string { return $this->datePublication; }
    public function getIdForum(): int { return $this->idForum; }
    public function getIdAuteur(): int { return $this->idAuteur; }

    // ===== SETTERS =====
    public function setIdPost(int $idPost): void { $this->idPost = $idPost; }
    public function setContenu(string $contenu): void { $this->contenu = $contenu; }
    public function setIdForum(int $idForum): void { $this->idForum = $idForum; }
    public function setIdAuteur(int $idAuteur): void { $this->idAuteur = $idAuteur; }
}
