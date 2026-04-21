<?php
/**
 * PostController — Contrôleur MVC pour la gestion des posts
 * Gère les actions Front Office et Back Office
 */
class PostController {

    // ===== FRONT OFFICE =====

    /**
     * Afficher un post avec ses commentaires (Front Office)
     * @param int $id
     */
    public function show($id): void {
        $pdo = Database::getConnection();
        
        $stmtPost = $pdo->prepare("
            SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role
            FROM post p
            JOIN utilisateur u ON p.id_auteur = u.id
            WHERE p.id_post = :id
        ");
        $stmtPost->execute([':id' => $id]);
        $post = $stmtPost->fetch();

        if (!$post) {
            header('Location: index.php?controller=forum&action=list');
            exit;
        }

        $stmtCom = $pdo->prepare("
            SELECT c.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role
            FROM commentaire c
            JOIN utilisateur u ON c.id_auteur = u.id
            WHERE c.id_post = :id_post
            ORDER BY c.date_commentaire ASC
        ");
        $stmtCom->execute([':id_post' => $id]);
        $commentaires = $stmtCom->fetchAll();

        $stmtForum = $pdo->prepare("SELECT * FROM forum WHERE id_forum = :id");
        $stmtForum->execute([':id' => $post['id_forum']]);
        $dataForum = $stmtForum->fetch();
        
        // Le view s'attend à un objet avec getTitre()
        $forum = new Forum(
            $dataForum['id_forum'] ?? null,
            $dataForum['titre'] ?? '',
            $dataForum['description'] ?? null,
            $dataForum['created_at'] ?? null
        );

        require __DIR__ . '/../View/front_office/post/show.php';
    }

    /**
     * Créer un nouveau post (Front Office — professionnels uniquement)
     */
    public function create(): void {
        $idForum = $_GET['id_forum'] ?? null;
        if (!$idForum) {
            header('Location: index.php?controller=forum&action=list');
            exit;
        }

        $pdo = Database::getConnection();
        $stmtForum = $pdo->prepare("SELECT * FROM forum WHERE id_forum = :id");
        $stmtForum->execute([':id' => $idForum]);
        $dataForum = $stmtForum->fetch();

        if (!$dataForum) {
            header('Location: index.php?controller=forum&action=list');
            exit;
        }
        
        $forum = new Forum(
            $dataForum['id_forum'],
            $dataForum['titre'],
            $dataForum['description'],
            $dataForum['created_at']
        );

        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenu = trim($_POST['contenu'] ?? '');
            $idAuteur = $_SESSION['user']['id'] ?? 0;

            // Validation côté serveur
            if (empty($contenu)) {
                $errors[] = "Le contenu du post est obligatoire.";
            }
            if (strlen($contenu) < 10) {
                $errors[] = "Le contenu doit contenir au moins 10 caractères.";
            }
            if (strlen($contenu) > 5000) {
                $errors[] = "Le contenu ne doit pas dépasser 5000 caractères.";
            }

            if (empty($errors)) {
                $stmtIns = $pdo->prepare("INSERT INTO post (contenu, id_forum, id_auteur) VALUES (:contenu, :id_forum, :id_auteur)");
                $result = $stmtIns->execute([
                    ':contenu'   => $contenu,
                    ':id_forum'  => $idForum,
                    ':id_auteur' => $idAuteur
                ]);
                
                if ($result) {
                    header('Location: index.php?controller=forum&action=show&id=' . $idForum);
                    exit;
                } else {
                    $errors[] = "Erreur lors de la publication du post.";
                }
            }
        }

        require __DIR__ . '/../View/front_office/post/create.php';
    }

    // ===== BACK OFFICE =====

    /**
     * Afficher la liste de tous les posts (Back Office Admin)
     */
    public function adminList(): void {
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
        $posts = $stmt->fetchAll();
        require __DIR__ . '/../View/back_office/post/list.php';
    }

    /**
     * Modifier un post (Back Office Admin)
     * @param int $id
     */
    public function edit($id): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role
            FROM post p
            JOIN utilisateur u ON p.id_auteur = u.id
            WHERE p.id_post = :id
        ");
        $stmt->execute([':id' => $id]);
        $post = $stmt->fetch();

        if (!$post) {
            header('Location: index.php?controller=post&action=adminList');
            exit;
        }

        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenu = trim($_POST['contenu'] ?? '');

            // Validation côté serveur
            if (empty($contenu)) {
                $errors[] = "Le contenu du post est obligatoire.";
            }
            if (strlen($contenu) < 10) {
                $errors[] = "Le contenu doit contenir au moins 10 caractères.";
            }

            if (empty($errors)) {
                $updateStmt = $pdo->prepare("UPDATE post SET contenu = :contenu WHERE id_post = :id");
                $result = $updateStmt->execute([
                    ':contenu' => $contenu,
                    ':id'      => $id
                ]);
                
                if ($result) {
                    $success = "Post modifié avec succès !";
                    // Rafraîchir les données
                    $stmt->execute([':id' => $id]);
                    $post = $stmt->fetch();
                } else {
                    $errors[] = "Erreur lors de la modification du post.";
                }
            }
        }

        require __DIR__ . '/../View/back_office/post/edit.php';
    }

    /**
     * Supprimer un post (Back Office Admin)
     * @param int $id
     */
    public function delete($id): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM post WHERE id_post = :id");
        $stmt->execute([':id' => $id]);
        
        header('Location: index.php?controller=post&action=adminList');
        exit;
    }
}
