<?php
// Dépendances nécessaires pour la gestion du profil professionnel
require_once __DIR__ . '/Utilisateur.php';              // Pour emailExiste(), getById(), changerMotDePasse()
require_once __DIR__ . '/ProfessionnelSante.php';       // Pour getProById(), modifierProfil()
require_once __DIR__ . '/../models/ProfessionnelSante.php'; // Modèle abstrait ProfessionnelSante

/**
 * ProfessionnelController
 * Contrôleur de HAUT NIVEAU pour le front-office professionnel de santé.
 * Symétrique à PatientController mais pour le rôle Professionnel.
 * C'est ce contrôleur qu'utilise index.php pour la page "professionnel".
 * Méthodes non statiques : instancié dans index.php avec $proCtrl = new ProfessionnelController()
 */
class ProfessionnelController
{
    /**
     * Récupère toutes les données du professionnel connecté.
     * Fait la jointure SQL entre "utilisateur" et "professionnel_sante".
     * Retourne null si non trouvé.
     */
    public function getProfil(int $userId): ?array
    {
        // Délègue au contrôleur bas-niveau qui fait le LEFT JOIN
        return ProfessionnelSanteModelController::getProById($userId);
    }

    /**
     * Valide et enregistre les modifications du profil professionnel.
     * Gère les données communes (nom, email) ET les données métier (spécialité, numéro d'ordre).
     * Retourne ['success' => true] ou ['success' => false, 'errors' => [...]]
     */
    public function updateProfile(int $userId, array $data): array
    {
        $errors = [];

        // Nettoyage des espaces pour tous les champs texte
        $prenom = trim($data['prenom'] ?? '');
        $nom    = trim($data['nom'] ?? '');
        $email  = trim($data['email'] ?? '');
        $tel    = trim($data['telephone'] ?? '');
        $spec   = trim($data['specialite'] ?? '');
        $ordre  = trim($data['numero_ordre'] ?? '');

        // --- Validation du prénom ---
        if (empty($prenom) || strlen($prenom) < 2) {
            $errors['prenom'] = 'Prénom invalide (min. 2 caractères).';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s.\-\']+$/', $prenom)) {
            $errors['prenom'] = 'Le prénom ne doit contenir que des lettres.';
        }

        // --- Validation du nom ---
        if (empty($nom) || strlen($nom) < 2) {
            $errors['nom'] = 'Nom invalide (min. 2 caractères).';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s.\-\']+$/', $nom)) {
            $errors['nom'] = 'Le nom ne doit contenir que des lettres.';
        }

        // --- Validation de l'email ---
        if (empty($email) || !preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
            $errors['email'] = 'Email invalide.';
        } elseif (UtilisateurController::emailExiste($email, $userId)) {
            // $userId exclut le professionnel lui-même du check doublon
            $errors['email'] = 'Cet email est déjà utilisé.';
        }

        // --- Validation du téléphone ---
        $telDigits = preg_replace('/\D/', '', $tel); // Supprime les non-chiffres
        if (empty($tel) || strlen($telDigits) !== 8) {
            $errors['telephone'] = 'Le téléphone doit contenir exactement 8 chiffres.';
        }

        // --- Validation de la spécialité (champ métier obligatoire) ---
        if (empty($spec)) {
            $errors['specialite'] = 'La spécialité est obligatoire.';
        }

        // --- Validation du numéro d'ordre (champ métier obligatoire) ---
        if (empty($ordre)) {
            $errors['numero_ordre'] = 'Le numéro d\'ordre est obligatoire.';
        }

        // Arrêt si des erreurs de validation existent
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            // Construction du tableau avec TOUTES les données (utilisateur + professionnel_sante)
            $pro = [
                'id'            => $userId,
                'nom'           => $nom,
                'prenom'        => $prenom,
                'email'         => $email,
                'telephone'     => $tel,
                'statut_compte' => 'Actif', // Le professionnel ne peut pas changer son propre statut
                'specialite'    => $spec,
                'numero_ordre'  => $ordre,
                // La biographie est optionnelle : chaîne vide si non renseignée
                'biographie'    => trim($data['biographie'] ?? ''),
            ];

            // Mise à jour des deux tables : "utilisateur" et "professionnel_sante"
            ProfessionnelSanteModelController::modifierProfil($pro);

            // Mise à jour de la session pour affichage immédiat dans la navbar
            $_SESSION['user_nom']   = $prenom . ' ' . $nom;
            $_SESSION['user_email'] = $email;

            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['global' => 'Erreur : ' . $e->getMessage()]];
        }
    }

    /**
     * Valide et change le mot de passe du professionnel connecté.
     * Logique identique à PatientController::changePassword() car le changement
     * de mot de passe passe par UtilisateurController, commun à tous les rôles.
     */
    public function changePassword(int $userId, string $oldPw, string $newPw, string $confirmPw): array
    {
        $errors = [];

        // --- Validations de base ---
        if (empty($oldPw)) {
            $errors['old_password'] = 'Le mot de passe actuel est requis.';
        }
        if (empty($newPw) || strlen($newPw) < 8) {
            $errors['new_password'] = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
        }
        if ($newPw !== $confirmPw) {
            $errors['confirm_password'] = 'Les mots de passe ne correspondent pas.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Rechargement depuis la base pour obtenir le hash bcrypt actuel
        // On ne stocke JAMAIS le mot de passe en clair dans la session
        $userData = UtilisateurController::getById($userId);

        if (!$userData || !password_verify($oldPw, $userData['mot_de_passe'])) {
            // Vérification que l'ancien mot de passe saisi correspond au hash en base
            return ['success' => false, 'errors' => ['old_password' => 'Mot de passe actuel incorrect.']];
        }

        try {
            // Délègue à UtilisateurController qui re-hash et met à jour en base
            UtilisateurController::changerMotDePasse($userId, $newPw);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['global' => 'Erreur : ' . $e->getMessage()]];
        }
    }
}
