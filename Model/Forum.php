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

    // ===== MÉTHODES CRUD =====

    /**
     * Créer un nouveau forum en base de données
     * @return bool
     */
    public function create(): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO forum (titre, description) VALUES (:titre, :description)");
        $result = $stmt->execute([
            ':titre'       => $this->titre,
            ':description' => $this->description
        ]);
        if ($result) {
            $this->idForum = (int)$pdo->lastInsertId();
        }
        return $result;
    }

    /**
     * Lire un forum par son ID
     * @param int $id
     * @return Forum|null
     */
    public static function read(int $id): ?self {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM forum WHERE id_forum = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new self(
                $data['id_forum'],
                $data['titre'],
                $data['description'],
                $data['created_at']
            );
        }
        return null;
    }

    /**
     * Lire tous les forums
     * @return array of Forum
     */
    public static function readAll(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT f.*, (SELECT COUNT(*) FROM post p WHERE p.id_forum = f.id_forum) AS nb_posts FROM forum f ORDER BY f.created_at DESC");
        return $stmt->fetchAll();
    }

    /**
     * Mettre à jour un forum
     * @return bool
     */
    public function update(): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE forum SET titre = :titre, description = :description WHERE id_forum = :id");
        return $stmt->execute([
            ':titre'       => $this->titre,
            ':description' => $this->description,
            ':id'          => $this->idForum
        ]);
    }

    /**
     * Supprimer un forum par son ID
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM forum WHERE id_forum = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Compter le nombre de posts dans un forum
     * @param int $idForum
     * @return int
     */
    public static function countPosts(int $idForum): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM post WHERE id_forum = :id");
        $stmt->execute([':id' => $idForum]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
}
