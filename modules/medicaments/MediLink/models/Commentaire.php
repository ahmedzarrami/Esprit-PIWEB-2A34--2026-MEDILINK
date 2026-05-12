<?php
/**
 * Classe Commentaire — Modèle OOP
 * Gestion des commentaires (Entité)
 */
class Commentaire {
    // Propriétés privées (encapsulation)
    private ?int $idCommentaire;
    private string $contenu;
    private ?string $dateCommentaire;
    private int $idPost;
    private int $idAuteur;

    /**
     * Constructeur
     */
    public function __construct(
        ?int $idCommentaire = null,
        string $contenu = '',
        ?string $dateCommentaire = null,
        int $idPost = 0,
        int $idAuteur = 0
    ) {
        $this->idCommentaire = $idCommentaire;
        $this->contenu = $contenu;
        $this->dateCommentaire = $dateCommentaire;
        $this->idPost = $idPost;
        $this->idAuteur = $idAuteur;
    }

    // ===== GETTERS =====
    public function getIdCommentaire(): ?int { return $this->idCommentaire; }
    public function getContenu(): string { return $this->contenu; }
    public function getDateCommentaire(): ?string { return $this->dateCommentaire; }
    public function getIdPost(): int { return $this->idPost; }
    public function getIdAuteur(): int { return $this->idAuteur; }

    // ===== SETTERS =====
    public function setIdCommentaire(int $id): void { $this->idCommentaire = $id; }
    public function setContenu(string $contenu): void { $this->contenu = $contenu; }
    public function setIdPost(int $idPost): void { $this->idPost = $idPost; }
    public function setIdAuteur(int $idAuteur): void { $this->idAuteur = $idAuteur; }
}
