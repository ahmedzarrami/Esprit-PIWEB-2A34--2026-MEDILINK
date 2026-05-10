<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/FaceAuth.php';
require_once __DIR__ . '/Utilisateur.php';

/**
 * FaceAuthController — Implémente la reconnaissance faciale (OOP / MVC).
 * Hérite du contrat défini dans le modèle abstrait FaceAuth.
 *
 * Utilise les descripteurs faciaux produits par face-api.js (côté client).
 * Un descripteur est un vecteur de 128 flottants représentant un visage.
 * La similarité se mesure par distance euclidienne (< 0.6 = même personne).
 */
class FaceAuthController extends FaceAuth
{
    // Seuil de distance euclidienne : en-dessous = même personne reconnue
    private const DISTANCE_THRESHOLD = 0.55;

    // ──────────────────────────────────────────────────────────
    // Implémentation des méthodes abstraites (contrat du modèle)
    // ──────────────────────────────────────────────────────────

    /**
     * Enregistre ou remplace le descripteur facial d'un utilisateur.
     * Le descripteur est stocké en JSON dans la colonne face_descriptor.
     */
    public static function sauvegarderDescripteur(int $userId, string $descriptorJson): bool
    {
        // Valider que c'est un JSON contenant exactement 128 nombres
        $decoded = json_decode($descriptorJson, true);
        if (!is_array($decoded) || count($decoded) !== 128) {
            return false;
        }

        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE utilisateur SET face_descriptor = :desc WHERE id = :id");
        return $stmt->execute([':desc' => $descriptorJson, ':id' => $userId]);
    }

    /**
     * Compare le descripteur fourni à tous les descripteurs stockés en base.
     * Retourne l'utilisateur correspondant si la distance est sous le seuil, null sinon.
     */
    public static function trouverCorrespondance(array $descripteur): ?array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->query("SELECT id, nom, prenom, email, role, statut_compte, face_descriptor
                             FROM utilisateur
                             WHERE face_descriptor IS NOT NULL");
        $users = $stmt->fetchAll();

        $bestUser     = null;
        $bestDistance = PHP_FLOAT_MAX;

        foreach ($users as $user) {
            $stored = json_decode($user['face_descriptor'], true);
            if (!is_array($stored) || count($stored) !== 128) {
                continue;
            }

            $distance = self::distanceEuclidienne($descripteur, $stored);

            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $bestUser     = $user;
            }
        }

        if ($bestUser !== null && $bestDistance < self::DISTANCE_THRESHOLD) {
            return $bestUser;
        }

        return null;
    }

    /**
     * Supprime le descripteur facial d'un utilisateur (désactive la reconnaisance).
     */
    public static function supprimerDescripteur(int $userId): bool
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE utilisateur SET face_descriptor = NULL WHERE id = :id");
        return $stmt->execute([':id' => $userId]);
    }

    // ──────────────────────────────────────────────────────────
    // Méthodes métier publiques (appelées depuis index.php via AJAX)
    // ──────────────────────────────────────────────────────────

    /**
     * Traite la connexion par visage :
     * Valide le JSON reçu, cherche la correspondance, ouvre la session si trouvée.
     */
    public function seConnecterParVisage(string $descriptorJson): array
    {
        $decoded = json_decode($descriptorJson, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded) || count($decoded) !== 128) {
            return ['success' => false, 'error' => 'Descripteur facial invalide.'];
        }

        $user = self::trouverCorrespondance($decoded);

        if (!$user) {
            return ['success' => false, 'error' => 'Visage non reconnu. Veuillez utiliser votre email et mot de passe.'];
        }

        // Vérifier que le compte est actif
        if ($user['statut_compte'] !== 'Actif') {
            return ['success' => false, 'error' => 'Votre compte est ' . strtolower($user['statut_compte']) . '. Contactez l\'administrateur.'];
        }

        // Ouvrir la session
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_role']  = $user['role'];
        $_SESSION['user_nom']   = $user['prenom'] . ' ' . $user['nom'];
        $_SESSION['user_email'] = $user['email'];

        return [
            'success' => true,
            'role'    => $user['role'],
            'nom'     => $user['prenom'] . ' ' . $user['nom'],
        ];
    }

    /**
     * Traite l'enregistrement du visage depuis la page profil.
     */
    public function enregistrerVisage(int $userId, string $descriptorJson): array
    {
        if (!self::sauvegarderDescripteur($userId, $descriptorJson)) {
            return ['success' => false, 'error' => 'Descripteur facial invalide (128 valeurs requises).'];
        }
        return ['success' => true, 'message' => 'Reconnaissance faciale activée avec succès.'];
    }

    /**
     * Vérifie si un utilisateur a un descripteur facial enregistré.
     */
    public static function aDescripteur(int $userId): bool
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("SELECT face_descriptor FROM utilisateur WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch();
        return !empty($row['face_descriptor']);
    }

    // ──────────────────────────────────────────────────────────
    // Calcul mathématique interne
    // ──────────────────────────────────────────────────────────

    /**
     * Calcule la distance euclidienne entre deux vecteurs de 128 dimensions.
     * Formule : sqrt( sum( (a_i - b_i)^2 ) )
     * Résultat < 0.55 → même personne avec haute probabilité.
     */
    private static function distanceEuclidienne(array $a, array $b): float
    {
        $sum = 0.0;
        for ($i = 0; $i < 128; $i++) {
            $diff  = (float)$a[$i] - (float)$b[$i];
            $sum  += $diff * $diff;
        }
        return sqrt($sum);
    }
}
