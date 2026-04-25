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
<<<<<<< HEAD
        $post = Post::read((int)$id);
=======
        $pdo = Database::getConnection();
        
        $stmtPost = $pdo->prepare("
            SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role
            FROM post p
            JOIN utilisateur u ON p.id_auteur = u.id
            WHERE p.id_post = :id
        ");
        $stmtPost->execute([':id' => $id]);
        $post = $stmtPost->fetch();

>>>>>>> master
        if (!$post) {
            header('Location: index.php?controller=forum&action=list');
            exit;
        }
<<<<<<< HEAD
        $commentaires = Commentaire::getByPost((int)$id);
        $forum = Forum::read($post['id_forum']);
=======

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

>>>>>>> master
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

<<<<<<< HEAD
        $forum = Forum::read((int)$idForum);
        if (!$forum) {
            header('Location: index.php?controller=forum&action=list');
            exit;
        }
=======
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
>>>>>>> master

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
<<<<<<< HEAD
                $post = new Post(null, $contenu, null, (int)$idForum, (int)$idAuteur);
                if ($post->create()) {
=======
                $stmtIns = $pdo->prepare("INSERT INTO post (contenu, id_forum, id_auteur) VALUES (:contenu, :id_forum, :id_auteur)");
                $result = $stmtIns->execute([
                    ':contenu'   => $contenu,
                    ':id_forum'  => $idForum,
                    ':id_auteur' => $idAuteur
                ]);
                
                if ($result) {
>>>>>>> master
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
<<<<<<< HEAD
        $posts = Post::readAll();
=======
        $pdo = Database::getConnection();
        
        // --- 1. Filtres & Tri (Partie Métier : Recherche et Trie) ---
        $search = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'date_desc';

        $query = "
            SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role,
                   f.titre AS forum_titre,
                   (SELECT COUNT(*) FROM commentaire c WHERE c.id_post = p.id_post) AS nb_commentaires
            FROM post p
            JOIN utilisateur u ON p.id_auteur = u.id
            JOIN forum f ON p.id_forum = f.id_forum
        ";

        $params = [];
        if ($search !== '') {
            $query .= " WHERE p.contenu LIKE :search1 OR u.nom LIKE :search2 OR u.prenom LIKE :search3 OR f.titre LIKE :search4 ";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
            $params[':search3'] = '%' . $search . '%';
            $params[':search4'] = '%' . $search . '%';
        }

        switch ($sort) {
            case 'date_asc':
                $query .= " ORDER BY p.date_publication ASC";
                break;
            case 'comments_desc':
                $query .= " ORDER BY nb_commentaires DESC";
                break;
            case 'date_desc':
            default:
                $query .= " ORDER BY p.date_publication DESC";
                break;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $posts = $stmt->fetchAll();
        
        // --- 2. Statistiques (Partie Métier : Statistique) ---
        $stmtStats1 = $pdo->query("SELECT COUNT(*) as total FROM post");
        $totalPosts = $stmtStats1->fetch()['total'];

        $stmtStats2 = $pdo->query("SELECT COUNT(*) as total FROM commentaire");
        $totalComments = $stmtStats2->fetch()['total'];
        
        $stmtTopPost = $pdo->query("SELECT p.id_post, COUNT(c.id_commentaire) as nb
                                    FROM post p
                                    LEFT JOIN commentaire c ON p.id_post = c.id_post
                                    GROUP BY p.id_post
                                    ORDER BY nb DESC LIMIT 1");
        $topPost = $stmtTopPost->fetch();

>>>>>>> master
        require __DIR__ . '/../View/back_office/post/list.php';
    }

    /**
<<<<<<< HEAD
=======
     * Créer un post (Back Office Admin)
     */
    public function adminCreate(): void {
        $pdo = Database::getConnection();
        
        // Listes pour les sélecteurs
        $stmtForums = $pdo->query("SELECT id_forum, titre FROM forum ORDER BY titre ASC");
        $forums = $stmtForums->fetchAll();
        
        $stmtUsers = $pdo->query("SELECT id, nom, prenom, role FROM utilisateur ORDER BY nom ASC");
        $utilisateurs = $stmtUsers->fetchAll();

        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenu = trim($_POST['contenu'] ?? '');
            $idForum = (int)($_POST['id_forum'] ?? 0);
            $idAuteur = (int)($_POST['id_auteur'] ?? 0);

            // Validation (Côté serveur, côté client géré en JS)
            if (empty($contenu)) {
                $errors[] = "Le contenu du post est obligatoire.";
            }
            if (strlen($contenu) < 10) {
                $errors[] = "Le contenu doit contenir au moins 10 caractères.";
            }
            if ($idForum <= 0) {
                $errors[] = "Veuillez sélectionner un forum valide.";
            }
            if ($idAuteur <= 0) {
                $errors[] = "Veuillez sélectionner un auteur valide.";
            }

            if (empty($errors)) {
                $stmtIns = $pdo->prepare("INSERT INTO post (contenu, id_forum, id_auteur) VALUES (:contenu, :id_forum, :id_auteur)");
                $result = $stmtIns->execute([
                    ':contenu'   => $contenu,
                    ':id_forum'  => $idForum,
                    ':id_auteur' => $idAuteur
                ]);
                
                if ($result) {
                    header('Location: index.php?controller=post&action=adminList');
                    exit;
                } else {
                    $errors[] = "Erreur lors de la création du post.";
                }
            }
        }

        require __DIR__ . '/../View/back_office/post/create.php';
    }

    /**
>>>>>>> master
     * Modifier un post (Back Office Admin)
     * @param int $id
     */
    public function edit($id): void {
<<<<<<< HEAD
        $post = Post::read((int)$id);
=======
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role
            FROM post p
            JOIN utilisateur u ON p.id_auteur = u.id
            WHERE p.id_post = :id
        ");
        $stmt->execute([':id' => $id]);
        $post = $stmt->fetch();

>>>>>>> master
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
<<<<<<< HEAD
                $postObj = new Post((int)$id, $contenu);
                if ($postObj->update()) {
                    $success = "Post modifié avec succès !";
                    // Rafraîchir les données
                    $post = Post::read((int)$id);
=======
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
>>>>>>> master
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
<<<<<<< HEAD
        Post::delete((int)$id);
=======
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM post WHERE id_post = :id");
        $stmt->execute([':id' => $id]);
        
>>>>>>> master
        header('Location: index.php?controller=post&action=adminList');
        exit;
    }
}
