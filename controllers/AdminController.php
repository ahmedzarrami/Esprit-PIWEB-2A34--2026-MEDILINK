<?php
// Chargement de tous les contrôleurs bas-niveau nécessaires au CRUD complet
require_once __DIR__ . '/Utilisateur.php';
require_once __DIR__ . '/Patient.php';
require_once __DIR__ . '/ProfessionnelSante.php';
require_once __DIR__ . '/Administrateur.php';
// Chargement des modèles abstraits correspondants
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/ProfessionnelSante.php';
require_once __DIR__ . '/../models/Administrateur.php';

/**
 * AdminController
 * Contrôleur de HAUT NIVEAU pour le backoffice administrateur.
 * Appelé par admin.php via des requêtes AJAX (retourne du JSON).
 * Gère le CRUD complet sur tous les types d'utilisateurs.
 * Méthodes non statiques : instancié dans admin.php avec $adminCtrl = new AdminController()
 */
class AdminController
{
    /**
     * Retourne la liste complète de tous les utilisateurs.
     * Inclut les données spécifiques à chaque rôle via les jointures SQL.
     */
    public function list(): array
    {
        // Délègue à AdministrateurModelController qui fait le double LEFT JOIN
        return AdministrateurModelController::gererUtilisateurs();
    }

    /**
     * Retourne les données complètes d'un utilisateur par son ID.
     * Utilisé pour pré-remplir la modale d'édition dans le backoffice.
     */
    public function getUser(int $id): ?array
    {
        return AdministrateurModelController::getUtilisateurComplet($id);
    }

    /**
     * Retourne les 4 statistiques affichées dans les cartes du dashboard admin.
     * Retourne un tableau associatif avec les compteurs.
     */
    public function getStats(): array
    {
        return [
            'total'    => UtilisateurController::countByRole(),           // Tous les utilisateurs
            'patients' => UtilisateurController::countByRole('Patient'),  // Patients uniquement
            'pros'     => UtilisateurController::countByRole('Professionnel'), // Professionnels
            'actifs'   => UtilisateurController::countActifs(),           // Comptes actifs (tous rôles)
        ];
    }

    /**
     * Valide les données du formulaire de création/modification d'utilisateur.
     * Méthode PRIVÉE : utilisée uniquement par create() et update().
     * Le paramètre $editingId vaut null en création, l'ID de l'utilisateur en modification.
     * Retourne un tableau d'erreurs (vide = valide).
     */
    private function valider(array $data, ?int $editingId = null): array
    {
        $errors = [];

        // --- Validation du prénom ---
        $prenom = trim($data['prenom'] ?? '');
        if (empty($prenom)) {
            $errors['prenom'] = 'Prénom obligatoire.';
        } elseif (strlen($prenom) < 2) {
            $errors['prenom'] = 'Minimum 2 caractères.';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s.\-\']+$/', $prenom)) {
            $errors['prenom'] = 'Le prénom ne doit contenir que des lettres.';
        }

        // --- Validation du nom ---
        $nom = trim($data['nom'] ?? '');
        if (empty($nom)) {
            $errors['nom'] = 'Nom obligatoire.';
        } elseif (strlen($nom) < 2) {
            $errors['nom'] = 'Minimum 2 caractères.';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s.\-\']+$/', $nom)) {
            $errors['nom'] = 'Le nom ne doit contenir que des lettres.';
        }

        // --- Validation de l'email ---
        $email = trim($data['email'] ?? '');
        if (empty($email)) {
            $errors['email'] = 'Email obligatoire.';
        } elseif (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
            $errors['email'] = 'Email invalide.';
        } elseif (UtilisateurController::emailExiste($email, $editingId)) {
            // En modification ($editingId non null), exclut l'utilisateur courant du check
            $errors['email'] = 'Cet email est déjà utilisé.';
        }

        // --- Validation du téléphone (exactement 8 chiffres) ---
        $tel = trim($data['telephone'] ?? '');
        $telDigits = preg_replace('/\D/', '', $tel); // Supprime les caractères non numériques
        if (empty($tel)) {
            $errors['telephone'] = 'Téléphone obligatoire.';
        } elseif (strlen($telDigits) !== 8) {
            $errors['telephone'] = 'Le téléphone doit contenir exactement 8 chiffres.';
        }

        // --- Validation du rôle ---
        // Seuls ces trois rôles sont autorisés dans l'application
        if (empty($data['role']) || !in_array($data['role'], ['Patient', 'Professionnel', 'Administrateur'])) {
            $errors['role'] = 'Rôle invalide.';
        }

        // --- Validation du mot de passe ---
        $mdp = $data['mot_de_passe'] ?? '';
        if (!$editingId && empty($mdp)) {
            // En CRÉATION : le mot de passe est obligatoire
            $errors['mot_de_passe'] = 'Mot de passe obligatoire.';
        }
        if (!empty($mdp)) {
            // Si un mot de passe est fourni (création ou changement en modification)
            if (strlen($mdp) < 8) {
                $errors['mot_de_passe'] = 'Minimum 8 caractères.';
            } else {
                // Score de complexité : doit avoir au moins 2 critères sur 3
                $score = 0;
                if (preg_match('/[A-Z]/', $mdp)) $score++; // Majuscule
                if (preg_match('/[0-9]/', $mdp)) $score++; // Chiffre
                if (preg_match('/[^A-Za-z0-9]/', $mdp)) $score++; // Symbole
                if ($score < 2) {
                    // L'admin exige un score >= 2 (plus strict que l'auto-inscription qui exige 1)
                    $errors['mot_de_passe'] = 'Trop faible : ajoutez majuscules, chiffres ou symboles.';
                }
            }
            // Vérification que la confirmation correspond
            if ($mdp !== ($data['confirm_mdp'] ?? '')) {
                $errors['confirm_mdp'] = 'Les mots de passe ne correspondent pas.';
            }
        }

        // --- Validation de la date de naissance (si fournie) ---
        $dob = $data['date_naissance'] ?? '';
        if (!empty($dob)) {
            $d = strtotime($dob); // Convertit la date string en timestamp Unix
            if ($d === false || $d > time()) {
                // La date ne doit pas être dans le futur
                $errors['date_naissance'] = 'Date invalide.';
            }
        }

        // --- Validations supplémentaires si rôle = Professionnel ---
        if (($data['role'] ?? '') === 'Professionnel') {

            // La spécialité est obligatoire pour un professionnel
            if (empty(trim($data['specialite'] ?? ''))) {
                $errors['specialite'] = 'Spécialité obligatoire.';
            }

            // Le numéro d'ordre doit respecter le format TN-MED-12345
            $ordre = trim($data['numero_ordre'] ?? '');
            if (empty($ordre)) {
                $errors['numero_ordre'] = 'N° d\'ordre obligatoire.';
            } elseif (!preg_match('/^TN-[A-Z]{2,5}-\d{4,6}$/', $ordre)) {
                // Regex : TN- + 2 à 5 lettres majuscules + - + 4 à 6 chiffres
                // Valide : TN-MED-12345 | TN-PHARM-123456
                $errors['numero_ordre'] = 'Format: TN-MED-12345';
            }
        }

        return $errors; // Tableau vide = toutes les validations passées
    }

    /**
     * Crée un nouvel utilisateur selon son rôle.
     * Utilise un switch pour appeler le bon contrôleur-modèle selon le rôle choisi.
     * Retourne l'ID du nouvel utilisateur ou les erreurs de validation.
     */
    public function create(array $data): array
    {
        // Validation complète avant toute insertion en base
        $errors = $this->valider($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            // Le switch route vers le bon contrôleur selon le rôle
            switch ($data['role']) {

                case 'Patient':
                    // Tableau avec les colonnes de "utilisateur" + colonnes de "patient"
                    $user = [
                        'nom'            => trim($data['nom']),
                        'prenom'         => trim($data['prenom']),
                        'email'          => trim($data['email']),
                        'mot_de_passe'   => $data['mot_de_passe'],
                        'telephone'      => trim($data['telephone']),
                        'statut_compte'  => $data['statut'] ?? 'Actif',
                        'role'           => 'Patient',
                        'date_naissance' => $data['date_naissance'] ?? null,
                        'sexe'           => $data['sexe'] ?? null,
                        'adresse'        => $data['adresse'] ?? null,
                        'groupe_sanguin' => $data['groupe_sanguin'] ?? null
                    ];
                    // Insère dans utilisateur + patient
                    $id = PatientModelController::sInscrire($user);
                    break;

                case 'Professionnel':
                    // Tableau avec les colonnes de "utilisateur" + colonnes de "professionnel_sante"
                    $user = [
                        'nom'            => trim($data['nom']),
                        'prenom'         => trim($data['prenom']),
                        'email'          => trim($data['email']),
                        'mot_de_passe'   => $data['mot_de_passe'],
                        'telephone'      => trim($data['telephone']),
                        'statut_compte'  => $data['statut'] ?? 'Actif',
                        'role'           => 'Professionnel',
                        'specialite'     => trim($data['specialite']),
                        'numero_ordre'   => trim($data['numero_ordre']),
                        'biographie'     => $data['biographie'] ?? null
                    ];
                    // Insère dans utilisateur + professionnel_sante
                    $id = ProfessionnelSanteModelController::sInscrire($user);
                    break;

                case 'Administrateur':
                    // Tableau avec uniquement les colonnes de "utilisateur"
                    // (la table administrateur ne contient que l'ID)
                    $user = [
                        'nom'            => trim($data['nom']),
                        'prenom'         => trim($data['prenom']),
                        'email'          => trim($data['email']),
                        'mot_de_passe'   => $data['mot_de_passe'],
                        'telephone'      => trim($data['telephone']),
                        'statut_compte'  => $data['statut'] ?? 'Actif',
                        'role'           => 'Administrateur'
                    ];
                    // Insère dans utilisateur + administrateur
                    $id = AdministrateurModelController::sInscrire($user);
                    break;
            }

            return ['success' => true, 'id' => $id];

        } catch (Exception $e) {
            // Capture les erreurs PDO (contrainte unique, connexion perdue, etc.)
            return ['success' => false, 'errors' => ['global' => 'Erreur : ' . $e->getMessage()]];
        }
    }

    /**
     * Modifie un utilisateur existant identifié par $id.
     * Le mot de passe n'est changé que s'il est fourni dans $data (optionnel en modification).
     */
    public function update(int $id, array $data): array
    {
        // Validation avec $id passé en 2ème argument (mode modification)
        $errors = $this->valider($data, $id);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $role = $data['role'];

            if ($role === 'Patient') {
                $user = [
                    'id'             => $id,
                    'nom'            => trim($data['nom']),
                    'prenom'         => trim($data['prenom']),
                    'email'          => trim($data['email']),
                    'telephone'      => trim($data['telephone']),
                    'statut_compte'  => $data['statut'] ?? 'Actif',
                    'date_naissance' => $data['date_naissance'] ?? null,
                    'sexe'           => $data['sexe'] ?? null,
                    'adresse'        => $data['adresse'] ?? null,
                    'groupe_sanguin' => $data['groupe_sanguin'] ?? null
                ];
                // Met à jour utilisateur + patient (avec upsert si la ligne patient manque)
                PatientModelController::modifierProfil($user);

            } elseif ($role === 'Professionnel') {
                $user = [
                    'id'             => $id,
                    'nom'            => trim($data['nom']),
                    'prenom'         => trim($data['prenom']),
                    'email'          => trim($data['email']),
                    'telephone'      => trim($data['telephone']),
                    'statut_compte'  => $data['statut'] ?? 'Actif',
                    'specialite'     => trim($data['specialite']),
                    'numero_ordre'   => trim($data['numero_ordre']),
                    'biographie'     => $data['biographie'] ?? null
                ];
                // Met à jour utilisateur + professionnel_sante
                ProfessionnelSanteModelController::modifierProfil($user);

            } else {
                // Administrateur : mise à jour uniquement dans "utilisateur"
                $user = [
                    'id'             => $id,
                    'nom'            => trim($data['nom']),
                    'prenom'         => trim($data['prenom']),
                    'email'          => trim($data['email']),
                    'telephone'      => trim($data['telephone']),
                    'statut_compte'  => $data['statut'] ?? 'Actif'
                ];
                UtilisateurController::modifierProfil($user);
            }

            // Changement de mot de passe optionnel : seulement si un nouveau est fourni
            $mdp = $data['mot_de_passe'] ?? '';
            if (!empty($mdp)) {
                // Re-hash et mise à jour dans la colonne mot_de_passe
                UtilisateurController::changerMotDePasse($id, $mdp);
            }

            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['global' => 'Erreur : ' . $e->getMessage()]];
        }
    }

    /**
     * Supprime définitivement un utilisateur par son ID.
     * La suppression en cascade (ON DELETE CASCADE) efface aussi les lignes
     * dans patient ou professionnel_sante automatiquement via les clés étrangères.
     */
    public function delete(int $id): array
    {
        try {
            UtilisateurController::supprimer($id);
            return ['success' => true];
        } catch (Exception $e) {
            // Une exception peut survenir si des clés étrangères sans CASCADE bloquent la suppression
            return ['success' => false, 'errors' => ['global' => 'Erreur : ' . $e->getMessage()]];
        }
    }
}
