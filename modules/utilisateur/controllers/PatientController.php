<?php
// Dépendances nécessaires pour la gestion du profil patient
require_once __DIR__ . '/Utilisateur.php';       // Pour emailExiste(), getById(), changerMotDePasse()
require_once __DIR__ . '/Patient.php';            // Pour getPatientById(), modifierProfil()
require_once __DIR__ . '/../models/Patient.php';  // Modèle abstrait Patient

/**
 * PatientController
 * Contrôleur de HAUT NIVEAU pour le front-office patient.
 * C'est ce contrôleur qu'utilise index.php directement.
 * Il orchestre les appels aux contrôleurs bas-niveau (PatientModelController, UtilisateurController).
 * Méthodes non statiques : instancié dans index.php avec $patientCtrl = new PatientController()
 */
class PatientController
{
    /**
     * Récupère toutes les données du patient connecté.
     * Simple délégation à PatientModelController qui fait la jointure SQL.
     * Retourne null si l'utilisateur n'existe pas.
     */
    public function getProfil(int $userId): ?array
    {
        // Délègue au contrôleur bas-niveau qui fait le LEFT JOIN utilisateur + patient
        return PatientModelController::getPatientById($userId);
    }

    /**
     * Valide et enregistre les modifications du profil patient.
     * Gère à la fois les données communes (nom, email) et médicales (groupe sanguin, etc.).
     * Retourne ['success' => true] ou ['success' => false, 'errors' => [...]]
     */
    public function updateProfile(int $userId, array $data): array
    {
        $errors = [];

        // Nettoyage des espaces en début/fin de chaque champ texte
        $prenom = trim($data['prenom'] ?? '');
        $nom    = trim($data['nom'] ?? '');
        $email  = trim($data['email'] ?? '');
        $tel    = trim($data['telephone'] ?? '');

        // --- Validation du prénom ---
        if (empty($prenom) || strlen($prenom) < 2) {
            $errors['prenom'] = 'Prénom invalide (min. 2 caractères).';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s.\-\']+$/', $prenom)) {
            // Regex : lettres latines et accentuées, espaces, point, tiret, apostrophe
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
            // $userId en 2ème argument : exclut le patient lui-même du check doublon
            // Sans ce paramètre, son propre email serait détecté comme "déjà utilisé"
            $errors['email'] = 'Cet email est déjà utilisé.';
        }

        // --- Validation du téléphone ---
        // Supprime tous les non-chiffres pour compter uniquement les chiffres réels
        $telDigits = preg_replace('/\D/', '', $tel);
        if (empty($tel) || strlen($telDigits) !== 8) {
            $errors['telephone'] = 'Le téléphone doit contenir exactement 8 chiffres.';
        }

        // Si des erreurs existent, on retourne sans toucher à la base
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            // Construction du tableau complet patient (données utilisateur + données médicales)
            $patient = [
                'id'             => $userId,
                'nom'            => $nom,
                'prenom'         => $prenom,
                'email'          => $email,
                'telephone'      => $tel,
                // On ne permet pas au patient de changer son statut lui-même
                'statut_compte'  => $data['statut_compte'] ?? 'Actif',
                // Données médicales optionnelles : null si non renseignées
                'date_naissance' => $data['date_naissance'] ?? null,
                'sexe'           => $data['sexe'] ?? null,
                'adresse'        => $data['adresse'] ?? null,
                'groupe_sanguin' => $data['groupe_sanguin'] ?? null
            ];

            // Appel au contrôleur bas-niveau qui met à jour les deux tables (utilisateur + patient)
            PatientModelController::modifierProfil($patient);

            // Mise à jour immédiate de la session pour que la navbar affiche les nouvelles valeurs
            // sans nécessiter une nouvelle connexion
            $_SESSION['user_nom']   = $prenom . ' ' . $nom;
            $_SESSION['user_email'] = $email;

            return ['success' => true];

        } catch (Exception $e) {
            // Capture les exceptions PDO ou autres erreurs imprévues
            return ['success' => false, 'errors' => ['global' => 'Erreur : ' . $e->getMessage()]];
        }
    }

    /**
     * Valide et change le mot de passe du patient connecté.
     * Vérifie l'ancien mot de passe DEPUIS LA BASE (pas depuis la session).
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
            // Comparaison stricte (===) pour éviter les faux positifs
            $errors['confirm_password'] = 'Les mots de passe ne correspondent pas.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Rechargement depuis la base pour obtenir le hash actuel du mot de passe
        // On ne fait jamais confiance aux données de session pour des opérations sensibles
        $userData = UtilisateurController::getById($userId);

        if (!$userData || !password_verify($oldPw, $userData['mot_de_passe'])) {
            // password_verify() compare le texte saisi avec le bcrypt stocké en base
            return ['success' => false, 'errors' => ['old_password' => 'Mot de passe actuel incorrect.']];
        }

        try {
            // Délègue le hashage et la mise à jour à UtilisateurController
            UtilisateurController::changerMotDePasse($userId, $newPw);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['global' => 'Erreur : ' . $e->getMessage()]];
        }
    }
}
