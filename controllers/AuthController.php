<?php
require_once __DIR__ . '/Utilisateur.php';
require_once __DIR__ . '/Patient.php';
require_once __DIR__ . '/ProfessionnelSante.php';
require_once __DIR__ . '/Administrateur.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/ProfessionnelSante.php';
require_once __DIR__ . '/../models/Administrateur.php';

/**
 * AuthController — Gère l'inscription, la connexion et la déconnexion
 */
class AuthController
{
    /**
     * Validation côté serveur des données d'inscription
     * Retourne un tableau d'erreurs (vide si tout est valide)
     */
    private function validerInscription(array $data): array
    {
        $errors = [];

        // Prénom
        $prenom = trim($data['prenom'] ?? '');
        if (empty($prenom)) {
            $errors['prenom'] = 'Le prénom est obligatoire.';
        } elseif (strlen($prenom) < 2) {
            $errors['prenom'] = 'Le prénom doit contenir au moins 2 caractères.';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s.\-\']+$/', $prenom)) {
            $errors['prenom'] = 'Le prénom ne doit contenir que des lettres.';
        }

        // Nom
        $nom = trim($data['nom'] ?? '');
        if (empty($nom)) {
            $errors['nom'] = 'Le nom est obligatoire.';
        } elseif (strlen($nom) < 2) {
            $errors['nom'] = 'Le nom doit contenir au moins 2 caractères.';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s.\-\']+$/', $nom)) {
            $errors['nom'] = 'Le nom ne doit contenir que des lettres.';
        }

        // Email
        $email = trim($data['email'] ?? '');
        if (empty($email)) {
            $errors['email'] = 'L\'email est obligatoire.';
        } elseif (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
            $errors['email'] = 'Format d\'email invalide.';
        } elseif (UtilisateurController::emailExiste($email)) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }

        // Téléphone — exactement 8 chiffres
        $tel = trim($data['telephone'] ?? '');
        $telDigits = preg_replace('/\D/', '', $tel);
        if (empty($tel)) {
            $errors['telephone'] = 'Le téléphone est obligatoire.';
        } elseif (strlen($telDigits) !== 8) {
            $errors['telephone'] = 'Le téléphone doit contenir exactement 8 chiffres.';
        }

        // Mot de passe
        $mdp = $data['mot_de_passe'] ?? '';
        if (empty($mdp)) {
            $errors['mot_de_passe'] = 'Le mot de passe est obligatoire.';
        } elseif (strlen($mdp) < 8) {
            $errors['mot_de_passe'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        } else {
            $score = 0;
            if (preg_match('/[A-Z]/', $mdp)) $score++;
            if (preg_match('/[0-9]/', $mdp)) $score++;
            if (preg_match('/[^A-Za-z0-9]/', $mdp)) $score++;
            if ($score < 1) {
                $errors['mot_de_passe'] = 'Mot de passe trop faible.';
            }
        }

        // Confirmation
        if ($mdp !== ($data['confirm_mdp'] ?? '')) {
            $errors['confirm_mdp'] = 'Les mots de passe ne correspondent pas.';
        }

        // Rôle — seul Patient est autorisé à s'inscrire
        if (empty($data['role']) || $data['role'] !== 'Patient') {
            $errors['role'] = 'Seuls les patients peuvent s\'inscrire. Les professionnels sont ajoutés par l\'administrateur.';
        }

        return $errors;
    }

    /**
     * Traitement de l'inscription
     */
    public function register(array $data): array
    {
        $errors = $this->validerInscription($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            // Seul Patient est permis via inscription
            $user = [
                'nom'            => trim($data['nom']),
                'prenom'         => trim($data['prenom']),
                'email'          => trim($data['email']),
                'mot_de_passe'   => $data['mot_de_passe'],
                'telephone'      => trim($data['telephone']),
                'statut_compte'  => 'Actif',
                'role'           => 'Patient',
                'date_naissance' => !empty($data['date_naissance']) ? $data['date_naissance'] : null,
                'sexe'           => $data['sexe'] ?? null,
                'adresse'        => $data['adresse'] ?? null
            ];

            $id = PatientModelController::sInscrire($user);

            // Démarrer la session
            $_SESSION['user_id']   = $id;
            $_SESSION['user_role'] = $data['role'];
            $_SESSION['user_nom']  = trim($data['prenom']) . ' ' . trim($data['nom']);

            return ['success' => true, 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['global' => 'Erreur lors de l\'inscription : ' . $e->getMessage()]];
        }
    }

    /**
     * Traitement de la connexion
     */
    public function login(string $email, string $motDePasse): array
    {
        // Validation serveur
        if (empty(trim($email))) {
            return ['success' => false, 'errors' => ['email' => 'L\'email est obligatoire.']];
        }
        if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', trim($email))) {
            return ['success' => false, 'errors' => ['email' => 'Format d\'email invalide.']];
        }
        if (empty($motDePasse)) {
            return ['success' => false, 'errors' => ['password' => 'Le mot de passe est obligatoire.']];
        }

        $user = UtilisateurController::seConnecter(trim($email), $motDePasse);

        if (!$user) {
            return ['success' => false, 'errors' => ['global' => 'Email ou mot de passe incorrect.']];
        }

        // Démarrer la session
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_nom']  = $user['prenom'] . ' ' . $user['nom'];
        $_SESSION['user_email']= $user['email'];

        return ['success' => true, 'user' => $user];
    }

    /**
     * Déconnexion
     */
    public function logout(): void
    {
        session_destroy();
        header('Location: index.php?page=home');
        exit;
    }
}
