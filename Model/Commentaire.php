<?php
/**
 * Classe Commentaire — Modèle OOP
<<<<<<< HEAD
 * Gestion des commentaires avec PDO (prepared statements)
=======
 * Gestion des commentaires (Entité)
>>>>>>> master
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
<<<<<<< HEAD

    // ===== MÉTHODES CRUD =====

    /**
     * Ajouter un nouveau commentaire
     * @return bool
     */
    public function create(): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO commentaire (contenu, id_post, id_auteur) VALUES (:contenu, :id_post, :id_auteur)");
        $result = $stmt->execute([
            ':contenu'   => $this->contenu,
            ':id_post'   => $this->idPost,
            ':id_auteur' => $this->idAuteur
        ]);
        if ($result) {
            $this->idCommentaire = (int)$pdo->lastInsertId();
        }
        return $result;
    }

    /**
     * Lire un commentaire par son ID
     * @param int $id
     * @return array|null
     */
    public static function read(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT c.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role
            FROM commentaire c
            JOIN utilisateur u ON c.id_auteur = u.id
            WHERE c.id_commentaire = :id
        ");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        return $data ?: null;
    }

    /**
     * Récupérer tous les commentaires d'un post
     * @param int $idPost
     * @return array
     */
    public static function getByPost(int $idPost): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT c.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role
            FROM commentaire c
            JOIN utilisateur u ON c.id_auteur = u.id
            WHERE c.id_post = :id_post
            ORDER BY c.date_commentaire ASC
        ");
        $stmt->execute([':id_post' => $idPost]);
        return $stmt->fetchAll();
    }

    /**
     * Récupérer tous les commentaires (pour modération admin)
     * @return array
     */
    public static function readAll(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("
            SELECT c.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role,
                   p.contenu AS post_contenu, f.titre AS forum_titre
            FROM commentaire c
            JOIN utilisateur u ON c.id_auteur = u.id
            JOIN post p ON c.id_post = p.id_post
            JOIN forum f ON p.id_forum = f.id_forum
            ORDER BY c.date_commentaire DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Supprimer un commentaire par son ID
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM commentaire WHERE id_commentaire = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Compter les commentaires d'un post
     * @param int $idPost
     * @return int
     */
    public static function countByPost(int $idPost): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM commentaire WHERE id_post = :id");
        $stmt->execute([':id' => $idPost]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
=======
>>>>>>> master
}
