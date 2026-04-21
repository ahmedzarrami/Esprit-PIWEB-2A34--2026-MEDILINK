<?php
/**
 * Classe Post — Modèle OOP
 * Gestion des posts de forum avec PDO (prepared statements)
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

    // ===== MÉTHODES CRUD =====

    /**
     * Créer un nouveau post
     * @return bool
     */
    public function create(): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO post (contenu, id_forum, id_auteur) VALUES (:contenu, :id_forum, :id_auteur)");
        $result = $stmt->execute([
            ':contenu'   => $this->contenu,
            ':id_forum'  => $this->idForum,
            ':id_auteur' => $this->idAuteur
        ]);
        if ($result) {
            $this->idPost = (int)$pdo->lastInsertId();
        }
        return $result;
    }

    /**
     * Lire un post par son ID (avec info auteur)
     * @param int $id
     * @return array|null
     */
    public static function read(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role
            FROM post p
            JOIN utilisateur u ON p.id_auteur = u.id
            WHERE p.id_post = :id
        ");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        return $data ?: null;
    }

    /**
     * Récupérer tous les posts d'un forum
     * @param int $idForum
     * @return array
     */
    public static function getByForum(int $idForum): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role,
                   (SELECT COUNT(*) FROM commentaire c WHERE c.id_post = p.id_post) AS nb_commentaires
            FROM post p
            JOIN utilisateur u ON p.id_auteur = u.id
            WHERE p.id_forum = :id_forum
            ORDER BY p.date_publication DESC
        ");
        $stmt->execute([':id_forum' => $idForum]);
        return $stmt->fetchAll();
    }

    /**
     * Récupérer tous les posts (pour l'admin)
     * @return array
     */
    public static function readAll(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("
            SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role,
                   f.titre AS forum_titre,
                   (SELECT COUNT(*) FROM commentaire c WHERE c.id_post = p.id_post) AS nb_commentaires
            FROM post p
            JOIN utilisateur u ON p.id_auteur = u.id
            JOIN forum f ON p.id_forum = f.id_forum
            ORDER BY p.date_publication DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Mettre à jour un post
     * @return bool
     */
    public function update(): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE post SET contenu = :contenu WHERE id_post = :id");
        return $stmt->execute([
            ':contenu' => $this->contenu,
            ':id'      => $this->idPost
        ]);
    }

    /**
     * Supprimer un post par son ID
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM post WHERE id_post = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Récupérer les posts d'un auteur
     * @param int $idAuteur
     * @return array
     */
    public static function getByAuteur(int $idAuteur): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, f.titre AS forum_titre
            FROM post p
            JOIN forum f ON p.id_forum = f.id_forum
            WHERE p.id_auteur = :id_auteur
            ORDER BY p.date_publication DESC
        ");
        $stmt->execute([':id_auteur' => $idAuteur]);
        return $stmt->fetchAll();
    }
}
