<?php
/**
 * ForumController — Contrôleur MVC pour la gestion des forums
 * Gère les actions Front Office et Back Office
 */
class ForumController {

    // ===== FRONT OFFICE =====

    /**
     * Afficher la liste des forums (Front Office)
     */
    public function list(): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT f.*, (SELECT COUNT(*) FROM post p WHERE p.id_forum = f.id_forum) AS nb_posts FROM forum f ORDER BY f.created_at DESC");
        $forums = $stmt->fetchAll();
        require __DIR__ . '/../View/front_office/forum/list.php';
    }

    /**
     * Afficher un forum avec ses posts (Front Office)
     * @param int $id
     */
    public function show($id): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM forum WHERE id_forum = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            header('Location: index.php?controller=forum&action=list');
            exit;
        }

        $forum = new Forum(
            $data['id_forum'],
            $data['titre'],
            $data['description'],
            $data['created_at']
        );

        $stmtPosts = $pdo->prepare("
            SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role,
                   (SELECT COUNT(*) FROM commentaire c WHERE c.id_post = p.id_post) AS nb_commentaires
            FROM post p
            JOIN utilisateur u ON p.id_auteur = u.id
            WHERE p.id_forum = :id_forum
            ORDER BY p.date_publication DESC
        ");
        $stmtPosts->execute([':id_forum' => $id]);
        $posts = $stmtPosts->fetchAll();

        require __DIR__ . '/../View/front_office/forum/show.php';
    }

    // ===== BACK OFFICE =====

    /**
     * Afficher la liste des forums (Back Office Admin)
     */
    public function adminList(): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT f.*, (SELECT COUNT(*) FROM post p WHERE p.id_forum = f.id_forum) AS nb_posts FROM forum f ORDER BY f.created_at DESC");
        $forums = $stmt->fetchAll();
        require __DIR__ . '/../View/back_office/forum/list.php';
    }

    /**
     * Afficher le formulaire de création d'un forum (Back Office)
     */
    public function create(): void {
        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Validation côté serveur
            if (empty($titre)) {
                $errors[] = "Le titre est obligatoire.";
            }
            if (strlen($titre) < 3) {
                $errors[] = "Le titre doit contenir au moins 3 caractères.";
            }
            if (strlen($titre) > 200) {
                $errors[] = "Le titre ne doit pas dépasser 200 caractères.";
            }

            if (empty($errors)) {
                $pdo = Database::getConnection();
                $stmt = $pdo->prepare("INSERT INTO forum (titre, description) VALUES (:titre, :description)");
                $result = $stmt->execute([
                    ':titre'       => $titre,
                    ':description' => $description
                ]);

                if ($result) {
                    $success = "Forum créé avec succès !";
                } else {
                    $errors[] = "Erreur lors de la création du forum.";
                }
            }
        }

        require __DIR__ . '/../View/back_office/forum/create.php';
    }

    /**
     * Afficher le formulaire d'édition d'un forum (Back Office)
     * @param int $id
     */
    public function edit($id): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM forum WHERE id_forum = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            header('Location: index.php?controller=forum&action=adminList');
            exit;
        }

        $forum = new Forum(
            $data['id_forum'],
            $data['titre'],
            $data['description'],
            $data['created_at']
        );

        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Validation côté serveur
            if (empty($titre)) {
                $errors[] = "Le titre est obligatoire.";
            }
            if (strlen($titre) < 3) {
                $errors[] = "Le titre doit contenir au moins 3 caractères.";
            }

            if (empty($errors)) {
                $forum->setTitre($titre);
                $forum->setDescription($description);

                $updateStmt = $pdo->prepare("UPDATE forum SET titre = :titre, description = :description WHERE id_forum = :id");
                $result = $updateStmt->execute([
                    ':titre'       => $forum->getTitre(),
                    ':description' => $forum->getDescription(),
                    ':id'          => $forum->getIdForum()
                ]);

                if ($result) {
                    $success = "Forum modifié avec succès !";
                } else {
                    $errors[] = "Erreur lors de la modification du forum.";
                }
            }
        }

        require __DIR__ . '/../View/back_office/forum/edit.php';
    }

    /**
     * Supprimer un forum (Back Office)
     * @param int $id
     */
    public function delete($id): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM forum WHERE id_forum = :id");
        $stmt->execute([':id' => $id]);
        
        header('Location: index.php?controller=forum&action=adminList');
        exit;
    }
}
