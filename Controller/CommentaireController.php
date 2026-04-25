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

<<<<<<< HEAD
        $commentaire = new Commentaire(null, $contenu, null, $idPost, (int)$idAuteur);
        if ($commentaire->create()) {
=======
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO commentaire (contenu, id_post, id_auteur) VALUES (:contenu, :id_post, :id_auteur)");
        $result = $stmt->execute([
            ':contenu'   => $contenu,
            ':id_post'   => $idPost,
            ':id_auteur' => $idAuteur
        ]);

        if ($result) {
>>>>>>> master
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
<<<<<<< HEAD
        $commentaires = Commentaire::readAll();
=======
        $pdo = Database::getConnection();
        
        // --- 1. Filtres & Tri (Partie Métier) ---
        $search = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'date_desc';

        $query = "
            SELECT c.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role,
                   p.contenu AS post_contenu, f.titre AS forum_titre
            FROM commentaire c
            JOIN utilisateur u ON c.id_auteur = u.id
            JOIN post p ON c.id_post = p.id_post
            JOIN forum f ON p.id_forum = f.id_forum
        ";
        
        $params = [];

        if ($search !== '') {
            $query .= " WHERE c.contenu LIKE :search1 OR u.nom LIKE :search2 OR u.prenom LIKE :search3 OR p.contenu LIKE :search4 OR f.titre LIKE :search5";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
            $params[':search3'] = '%' . $search . '%';
            $params[':search4'] = '%' . $search . '%';
            $params[':search5'] = '%' . $search . '%';
        }

        switch ($sort) {
            case 'date_asc':
                $query .= " ORDER BY c.date_commentaire ASC";
                break;
            case 'date_desc':
            default:
                $query .= " ORDER BY c.date_commentaire DESC";
                break;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $commentaires = $stmt->fetchAll();

        // --- 2. Statistiques (Partie Métier) ---
        $stmtStatsTotal = $pdo->query("SELECT COUNT(*) as total FROM commentaire");
        $totalComments = $stmtStatsTotal->fetch()['total'];

        $stmtTopUser = $pdo->query("SELECT u.id, u.nom, u.prenom, COUNT(c.id_commentaire) as nb
                                    FROM utilisateur u
                                    JOIN commentaire c ON u.id = c.id_auteur
                                    GROUP BY u.id
                                    ORDER BY nb DESC LIMIT 1");
        $topUser = $stmtTopUser->fetch();

>>>>>>> master
        require __DIR__ . '/../View/back_office/commentaire/list.php';
    }

    /**
     * Supprimer un commentaire (Back Office Admin)
     * @param int $id
     */
    public function delete($id): void {
<<<<<<< HEAD
        $commentaire = Commentaire::read((int)$id);
        Commentaire::delete((int)$id);
=======
        $pdo = Database::getConnection();

        // Récupérer le commentaire pour la redirection
        $stmtRead = $pdo->prepare("SELECT * FROM commentaire WHERE id_commentaire = :id");
        $stmtRead->execute([':id' => $id]);
        $commentaire = $stmtRead->fetch();

        // Supprimer
        $stmtDel = $pdo->prepare("DELETE FROM commentaire WHERE id_commentaire = :id");
        $stmtDel->execute([':id' => $id]);
>>>>>>> master

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
