<?php
// Chargement de tous les contrôleurs nécessaires à l'authentification
require_once __DIR__ . '/Utilisateur.php';
require_once __DIR__ . '/Patient.php';
require_once __DIR__ . '/ProfessionnelSante.php';
require_once __DIR__ . '/Administrateur.php';
// Chargement des modèles abstraits correspondants
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/ProfessionnelSante.php';
require_once __DIR__ . '/../models/Administrateur.php';

/**
 * AuthController
 * Gère les trois opérations d'authentification :
 * - Inscription (register) : uniquement pour les patients
 * - Connexion (login) : pour tous les rôles
 * - Déconnexion (logout) : pour tous les rôles
 */
class AuthController
{
    /**
     * Valide les données du formulaire d'inscription côté serveur.
     * Méthode PRIVÉE : appelée uniquement depuis register(), pas depuis l'extérieur.
     * Retourne un tableau d'erreurs (vide = tout est valide).
     */
    private function validerInscription(array $data): array
    {
        $errors = [];

        // --- Validation du prénom ---
        $prenom = trim($data['prenom'] ?? ''); // trim() supprime les espaces en début/fin
        if (empty($prenom)) {
            $errors['prenom'] = 'Le prénom est obligatoire.';
        } elseif (strlen($prenom) < 2) {
            $errors['prenom'] = 'Le prénom doit contenir au moins 2 caractères.';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s.\-\']+$/', $prenom)) {
            // Regex : autorise lettres (y compris accentuées), espaces, point, tiret, apostrophe
            $errors['prenom'] = 'Le prénom ne doit contenir que des lettres.';
        }

        // --- Validation du nom ---
        $nom = trim($data['nom'] ?? '');
        if (empty($nom)) {
            $errors['nom'] = 'Le nom est obligatoire.';
        } elseif (strlen($nom) < 2) {
            $errors['nom'] = 'Le nom doit contenir au moins 2 caractères.';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s.\-\']+$/', $nom)) {
            $errors['nom'] = 'Le nom ne doit contenir que des lettres.';
        }

        // --- Validation de l'email ---
        $email = trim($data['email'] ?? '');
        if (empty($email)) {
            $errors['email'] = 'L\'email est obligatoire.';
        } elseif (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
            // Regex simple : quelquechose @ quelquechose . quelquechose
            $errors['email'] = 'Format d\'email invalide.';
        } elseif (UtilisateurController::emailExiste($email)) {
            // Vérification en base : l'email ne doit pas déjà être utilisé
            $errors['email'] = 'Cet email est déjà utilisé.';
        }

        // --- Validation du téléphone (8 chiffres tunisiens) ---
        $tel = trim($data['telephone'] ?? '');
        // preg_replace supprime tout ce qui n'est pas un chiffre pour compter précisément
        $telDigits = preg_replace('/\D/', '', $tel);
        if (empty($tel)) {
            $errors['telephone'] = 'Le téléphone est obligatoire.';
        } elseif (strlen($telDigits) !== 8) {
            // Doit contenir exactement 8 chiffres (format tunisien)
            $errors['telephone'] = 'Le téléphone doit contenir exactement 8 chiffres.';
        }

        // --- Validation du mot de passe ---
        $mdp = $data['mot_de_passe'] ?? '';
        if (empty($mdp)) {
            $errors['mot_de_passe'] = 'Le mot de passe est obligatoire.';
        } elseif (strlen($mdp) < 8) {
            $errors['mot_de_passe'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        } else {
            // Calcul d'un score de complexité (0 à 3)
            $score = 0;
            if (preg_match('/[A-Z]/', $mdp)) $score++; // +1 si contient une majuscule
            if (preg_match('/[0-9]/', $mdp)) $score++; // +1 si contient un chiffre
            if (preg_match('/[^A-Za-z0-9]/', $mdp)) $score++; // +1 si contient un symbole
            if ($score < 1) {
                $errors['mot_de_passe'] = 'Mot de passe trop faible.';
            }
        }

        // --- Validation de la confirmation du mot de passe ---
        if ($mdp !== ($data['confirm_mdp'] ?? '')) {
            $errors['confirm_mdp'] = 'Les mots de passe ne correspondent pas.';
        }

        // --- Validation du rôle : seul Patient peut s'inscrire ---
        // Les professionnels et admins sont créés UNIQUEMENT par l'administrateur
        if (empty($data['role']) || $data['role'] !== 'Patient') {
            $errors['role'] = 'Seuls les patients peuvent s\'inscrire. Les professionnels sont ajoutés par l\'administrateur.';
        }

        return $errors; // Tableau vide = formulaire valide
    }

    /**
     * Traite l'inscription d'un nouveau patient.
     * Valide les données, crée le compte, ouvre la session.
     * Retourne un tableau avec 'success' => true/false et les éventuelles erreurs.
     */
    public function register(array $data): array
    {
        // Validation côté serveur (indépendante de la validation JS côté client)
        $errors = $this->validerInscription($data);
        if (!empty($errors)) {
            // S'il y a des erreurs, on arrête et on les retourne pour les afficher
            return ['success' => false, 'errors' => $errors];
        }

        try {
            // Construction du tableau patient avec toutes les données nécessaires
            $user = [
                'nom'            => trim($data['nom']),
                'prenom'         => trim($data['prenom']),
                'email'          => trim($data['email']),
                'mot_de_passe'   => $data['mot_de_passe'],
                'telephone'      => trim($data['telephone']),
                'statut_compte'  => 'Actif', // Nouveau compte actif par défaut
                'role'           => 'Patient', // Forcé à Patient, jamais changeable via ce formulaire
                'date_naissance' => !empty($data['date_naissance']) ? $data['date_naissance'] : null,
                'sexe'           => $data['sexe'] ?? null,
                'adresse'        => $data['adresse'] ?? null
            ];

            // Insertion dans utilisateur + patient (via PatientModelController)
            $id = PatientModelController::sInscrire($user);

            // Ouverture de la session après inscription réussie
            // Ces variables de session sont utilisées partout dans l'application
            $_SESSION['user_id']   = $id;
            $_SESSION['user_role'] = $data['role'];
            $_SESSION['user_nom']  = trim($data['prenom']) . ' ' . trim($data['nom']);

            return ['success' => true, 'id' => $id];

        } catch (Exception $e) {
            // En cas d'erreur base de données (ex: contrainte unique violée)
            return ['success' => false, 'errors' => ['global' => 'Erreur lors de l\'inscription : ' . $e->getMessage()]];
        }
    }

    /**
     * Traite la connexion d'un utilisateur (tous rôles confondus).
     * Valide les inputs, vérifie les identifiants, ouvre la session.
     * Retourne un tableau avec 'success' => true/false.
     */
    public function login(string $email, string $motDePasse): array
    {
        // --- Validations minimales des champs ---
        if (empty(trim($email))) {
            return ['success' => false, 'errors' => ['email' => 'L\'email est obligatoire.']];
        }
        if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', trim($email))) {
            return ['success' => false, 'errors' => ['email' => 'Format d\'email invalide.']];
        }
        if (empty($motDePasse)) {
            return ['success' => false, 'errors' => ['password' => 'Le mot de passe est obligatoire.']];
        }

        // Vérification des identifiants en base (email + password_verify)
        $user = UtilisateurController::seConnecter(trim($email), $motDePasse);

        if (!$user) {
            // Message volontairement vague : on ne précise pas si c'est l'email ou le mot de passe
            // (sécurité : empêche l'énumération des emails existants)
            return ['success' => false, 'errors' => ['global' => 'Email ou mot de passe incorrect.']];
        }

        // Connexion réussie : alimentation de la session PHP
        // Ces 4 variables sont disponibles sur toutes les pages via $_SESSION
        $_SESSION['user_id']   = $user['id'];    // Clé primaire pour les requêtes SQL
        $_SESSION['user_role'] = $user['role'];  // Détermine quelle interface afficher
        $_SESSION['user_nom']  = $user['prenom'] . ' ' . $user['nom']; // Affiché dans la navbar
        $_SESSION['user_email']= $user['email']; // Affiché dans le profil

        return ['success' => true, 'user' => $user];
    }

    /**
     * Déconnecte l'utilisateur en détruisant complètement sa session.
     * Redirige vers la page d'accueil.
     * Retourne void car la redirection coupe l'exécution.
     */
    public function logout(): void
    {
        // Supprime toutes les données de session côté serveur
        session_destroy();

        // header() envoie un en-tête HTTP de redirection au navigateur
        header('Location: index.php?page=home');

        // exit stoppe immédiatement l'exécution PHP pour éviter que du code
        // s'exécute après la redirection (bonne pratique de sécurité)
        exit;
    }
}
