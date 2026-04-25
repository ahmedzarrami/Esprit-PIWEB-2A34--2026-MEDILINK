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
<<<<<<< HEAD
        $forums = Forum::readAll();
=======
        $pdo = Database::getConnection();
        
        $search = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'date_desc';

        $query = "SELECT f.*, (SELECT COUNT(*) FROM post p WHERE p.id_forum = f.id_forum) AS nb_posts FROM forum f";
        $params = [];

        if ($search !== '') {
            $query .= " WHERE f.titre LIKE :search1 OR f.description LIKE :search2";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }

        switch ($sort) {
            case 'posts_desc':
                $query .= " ORDER BY nb_posts DESC, f.created_at DESC";
                break;
            case 'date_desc':
            default:
                $query .= " ORDER BY f.created_at DESC";
                break;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $forums = $stmt->fetchAll();
        
        // Stats for hero
        $totalForums = count($forums);
        $totalPosts = array_sum(array_column($forums, 'nb_posts'));

>>>>>>> master
        require __DIR__ . '/../View/front_office/forum/list.php';
    }

    /**
     * Afficher un forum avec ses posts (Front Office)
     * @param int $id
     */
    public function show($id): void {
<<<<<<< HEAD
        $forum = Forum::read((int)$id);
        if (!$forum) {
            header('Location: index.php?controller=forum&action=list');
            exit;
        }
        $posts = Post::getByForum((int)$id);
=======
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

        $search = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'date_desc';

        $query = "
            SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role,
                   (SELECT COUNT(*) FROM commentaire c WHERE c.id_post = p.id_post) AS nb_commentaires
            FROM post p
            JOIN utilisateur u ON p.id_auteur = u.id
            WHERE p.id_forum = :id_forum
        ";
        
        $params = [':id_forum' => $id];

        if ($search !== '') {
            $query .= " AND (p.contenu LIKE :search1 OR u.nom LIKE :search2 OR u.prenom LIKE :search3)";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
            $params[':search3'] = '%' . $search . '%';
        }

        switch ($sort) {
            case 'comments_desc':
                $query .= " ORDER BY nb_commentaires DESC, p.date_publication DESC";
                break;
            case 'date_desc':
            default:
                $query .= " ORDER BY p.date_publication DESC";
                break;
        }

        $stmtPosts = $pdo->prepare($query);
        $stmtPosts->execute($params);
        $posts = $stmtPosts->fetchAll();

>>>>>>> master
        require __DIR__ . '/../View/front_office/forum/show.php';
    }

    // ===== BACK OFFICE =====

    /**
     * Afficher la liste des forums (Back Office Admin)
     */
    public function adminList(): void {
<<<<<<< HEAD
        $forums = Forum::readAll();
=======
        $pdo = Database::getConnection();
        
        // --- 1. Filtres & Tri (Partie Métier) ---
        $search = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'date_desc';

        $query = "SELECT f.*, (SELECT COUNT(*) FROM post p WHERE p.id_forum = f.id_forum) AS nb_posts FROM forum f";
        $params = [];

        if ($search !== '') {
            $query .= " WHERE f.titre LIKE :search1 OR f.description LIKE :search2";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }

        switch ($sort) {
            case 'date_asc':
                $query .= " ORDER BY f.created_at ASC";
                break;
            case 'posts_desc':
                $query .= " ORDER BY nb_posts DESC";
                break;
            case 'date_desc':
            default:
                $query .= " ORDER BY f.created_at DESC";
                break;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $forums = $stmt->fetchAll();

        // --- 2. Statistiques (Partie Métier) ---
        $stmtStatsTotal = $pdo->query("SELECT COUNT(*) as total FROM forum");
        $totalForums = $stmtStatsTotal->fetch()['total'];

        $stmtStatsPosts = $pdo->query("SELECT COUNT(*) as total FROM post");
        $totalPosts = $stmtStatsPosts->fetch()['total'];

        $stmtTopForum = $pdo->query("SELECT f.id_forum, f.titre, COUNT(p.id_post) as nb
                                     FROM forum f
                                     LEFT JOIN post p ON f.id_forum = p.id_forum
                                     GROUP BY f.id_forum
                                     ORDER BY nb DESC LIMIT 1");
        $topForum = $stmtTopForum->fetch();

>>>>>>> master
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
<<<<<<< HEAD
                $forum = new Forum(null, $titre, $description);
                if ($forum->create()) {
=======
                $pdo = Database::getConnection();
                $stmt = $pdo->prepare("INSERT INTO forum (titre, description) VALUES (:titre, :description)");
                $result = $stmt->execute([
                    ':titre'       => $titre,
                    ':description' => $description
                ]);

                if ($result) {
>>>>>>> master
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
<<<<<<< HEAD
        $forum = Forum::read((int)$id);
        if (!$forum) {
=======
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM forum WHERE id_forum = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
>>>>>>> master
            header('Location: index.php?controller=forum&action=adminList');
            exit;
        }

<<<<<<< HEAD
=======
        $forum = new Forum(
            $data['id_forum'],
            $data['titre'],
            $data['description'],
            $data['created_at']
        );

>>>>>>> master
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
<<<<<<< HEAD
                if ($forum->update()) {
=======

                $updateStmt = $pdo->prepare("UPDATE forum SET titre = :titre, description = :description WHERE id_forum = :id");
                $result = $updateStmt->execute([
                    ':titre'       => $forum->getTitre(),
                    ':description' => $forum->getDescription(),
                    ':id'          => $forum->getIdForum()
                ]);

                if ($result) {
>>>>>>> master
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
<<<<<<< HEAD
        Forum::delete((int)$id);
=======
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM forum WHERE id_forum = :id");
        $stmt->execute([':id' => $id]);
        
>>>>>>> master
        header('Location: index.php?controller=forum&action=adminList');
        exit;
    }
}
