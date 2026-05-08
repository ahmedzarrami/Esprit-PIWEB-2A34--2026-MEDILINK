<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = Database::getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS reaction (
        id_reaction INT AUTO_INCREMENT PRIMARY KEY,
        type ENUM('like','dislike') NOT NULL,
        id_post INT DEFAULT NULL,
        id_commentaire INT DEFAULT NULL,
        id_utilisateur INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_post) REFERENCES post(id_post) ON DELETE CASCADE,
        FOREIGN KEY (id_commentaire) REFERENCES commentaire(id_commentaire) ON DELETE CASCADE,
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id) ON DELETE CASCADE,
        UNIQUE KEY unique_reaction_post (id_post, id_utilisateur),
        UNIQUE KEY unique_reaction_commentaire (id_commentaire, id_utilisateur)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $pdo->exec($sql);
    echo "Table 'reaction' créée avec succès.";
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
