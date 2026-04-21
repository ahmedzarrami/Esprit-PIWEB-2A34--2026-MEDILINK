<?php
/**
 * CommentaireController — Contrôleur MVC pour la gestion des commentaires
 * Gère les actions Front Office et Back Office
 */
class CommentaireController {

    // ===== FRONT OFFICE =====

    /**
     * Ajouter un commentaire à un post (Front Office)
     */
    public function add(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=forum&action=list');
            exit;
        }

        $contenu  = trim($_POST['contenu'] ?? '');
        $idPost   = (int)($_POST['id_post'] ?? 0);
        $idAuteur = $_SESSION['user']['id'] ?? 0;

        $errors = [];

        // Validation côté serveur
        if (empty($contenu)) {
            $errors[] = "Le commentaire ne peut pas être vide.";
        }
        if (strlen($contenu) < 3) {
            $errors[] = "Le commentaire doit contenir au moins 3 caractères.";
        }
        if (strlen($contenu) > 2000) {
            $errors[] = "Le commentaire ne doit pas dépasser 2000 caractères.";
        }
        if ($idPost <= 0) {
            $errors[] = "Post invalide.";
        }

        if (!empty($errors)) {
            // Stocker les erreurs en session pour les afficher après redirection
            $_SESSION['comment_errors'] = $errors;
            $_SESSION['comment_contenu'] = $contenu;
            header('Location: index.php?controller=post&action=show&id=' . $idPost);
            exit;
        }

        $commentaire = new Commentaire(null, $contenu, null, $idPost, (int)$idAuteur);
        if ($commentaire->create()) {
            $_SESSION['comment_success'] = "Commentaire ajouté avec succès !";
        } else {
            $_SESSION['comment_errors'] = ["Erreur lors de l'ajout du commentaire."];
        }

        header('Location: index.php?controller=post&action=show&id=' . $idPost);
        exit;
    }

    // ===== BACK OFFICE =====

    /**
     * Afficher la liste de tous les commentaires (Back Office Admin — modération)
     */
    public function adminList(): void {
        $commentaires = Commentaire::readAll();
        require __DIR__ . '/../View/back_office/commentaire/list.php';
    }

    /**
     * Supprimer un commentaire (Back Office Admin)
     * @param int $id
     */
    public function delete($id): void {
        $commentaire = Commentaire::read((int)$id);
        Commentaire::delete((int)$id);

        // Rediriger vers la bonne page selon le contexte
        $from = $_GET['from'] ?? 'admin';
        if ($from === 'post' && $commentaire) {
            header('Location: index.php?controller=post&action=show&id=' . $commentaire['id_post']);
        } else {
            header('Location: index.php?controller=commentaire&action=adminList');
        }
        exit;
    }
}
