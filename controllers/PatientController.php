<?php
require_once __DIR__ . '/../models/Patient.php';

/**
 * PatientController — Gère le profil patient (front-office)
 */
class PatientController
{
    /**
     * Récupérer les données complètes du patient connecté
     */
    public function getProfil(int $userId): ?array
    {
        return Patient::getPatientById($userId);
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(int $userId, array $data): array
    {
        $errors = [];

        // Validation serveur
        $prenom = trim($data['prenom'] ?? '');
        $nom    = trim($data['nom'] ?? '');
        $email  = trim($data['email'] ?? '');
        $tel    = trim($data['telephone'] ?? '');

        if (empty($prenom) || strlen($prenom) < 2) {
            $errors['prenom'] = 'Prénom invalide (min. 2 caractères).';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s.\-\']+$/', $prenom)) {
            $errors['prenom'] = 'Le prénom ne doit contenir que des lettres.';
        }
        if (empty($nom) || strlen($nom) < 2) {
            $errors['nom'] = 'Nom invalide (min. 2 caractères).';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s.\-\']+$/', $nom)) {
            $errors['nom'] = 'Le nom ne doit contenir que des lettres.';
        }
        if (empty($email) || !preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
            $errors['email'] = 'Email invalide.';
        } elseif (Utilisateur::emailExiste($email, $userId)) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }
        $telDigits = preg_replace('/\D/', '', $tel);
        if (empty($tel) || strlen($telDigits) !== 8) {
            $errors['telephone'] = 'Le téléphone doit contenir exactement 8 chiffres.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $patient = new Patient();
            $patient->setId($userId);
            $patient->setNom($nom);
            $patient->setPrenom($prenom);
            $patient->setEmail($email);
            $patient->setTelephone($tel);
            $patient->setStatutCompte($data['statut_compte'] ?? 'Actif');
            $patient->setDateNaissance($data['date_naissance'] ?? null);
            $patient->setSexe($data['sexe'] ?? null);
            $patient->setAdresse($data['adresse'] ?? null);
            $patient->setGroupeSanguin($data['groupe_sanguin'] ?? null);

            $patient->modifierProfil();

            // Mettre à jour la session
            $_SESSION['user_nom']   = $prenom . ' ' . $nom;
            $_SESSION['user_email'] = $email;

            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['global' => 'Erreur : ' . $e->getMessage()]];
        }
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(int $userId, string $oldPw, string $newPw, string $confirmPw): array
    {
        $errors = [];

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

        // Vérifier l'ancien mot de passe
        $userData = Utilisateur::getById($userId);
        if (!$userData || !password_verify($oldPw, $userData['mot_de_passe'])) {
            return ['success' => false, 'errors' => ['old_password' => 'Mot de passe actuel incorrect.']];
        }

        try {
            $patient = new Patient();
            $patient->setId($userId);
            $patient->changerMotDePasse($newPw);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['global' => 'Erreur : ' . $e->getMessage()]];
        }
    }
}
