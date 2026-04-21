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
        $post = Post::read((int)$id);
        if (!$post) {
            header('Location: index.php?controller=forum&action=list');
            exit;
        }
        $commentaires = Commentaire::getByPost((int)$id);
        $forum = Forum::read($post['id_forum']);
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

        $forum = Forum::read((int)$idForum);
        if (!$forum) {
            header('Location: index.php?controller=forum&action=list');
            exit;
        }

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
                $post = new Post(null, $contenu, null, (int)$idForum, (int)$idAuteur);
                if ($post->create()) {
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
        $posts = Post::readAll();
        require __DIR__ . '/../View/back_office/post/list.php';
    }

    /**
     * Modifier un post (Back Office Admin)
     * @param int $id
     */
    public function edit($id): void {
        $post = Post::read((int)$id);
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
                $postObj = new Post((int)$id, $contenu);
                if ($postObj->update()) {
                    $success = "Post modifié avec succès !";
                    // Rafraîchir les données
                    $post = Post::read((int)$id);
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
        Post::delete((int)$id);
        header('Location: index.php?controller=post&action=adminList');
        exit;
    }
}
