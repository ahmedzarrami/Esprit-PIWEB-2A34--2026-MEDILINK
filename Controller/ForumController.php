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
        $forums = Forum::readAll();
        require __DIR__ . '/../View/front_office/forum/list.php';
    }

    /**
     * Afficher un forum avec ses posts (Front Office)
     * @param int $id
     */
    public function show($id): void {
        $forum = Forum::read((int)$id);
        if (!$forum) {
            header('Location: index.php?controller=forum&action=list');
            exit;
        }
        $posts = Post::getByForum((int)$id);
        require __DIR__ . '/../View/front_office/forum/show.php';
    }

    // ===== BACK OFFICE =====

    /**
     * Afficher la liste des forums (Back Office Admin)
     */
    public function adminList(): void {
        $forums = Forum::readAll();
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
                $forum = new Forum(null, $titre, $description);
                if ($forum->create()) {
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
        $forum = Forum::read((int)$id);
        if (!$forum) {
            header('Location: index.php?controller=forum&action=adminList');
            exit;
        }

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
                if ($forum->update()) {
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
        Forum::delete((int)$id);
        header('Location: index.php?controller=forum&action=adminList');
        exit;
    }
}
