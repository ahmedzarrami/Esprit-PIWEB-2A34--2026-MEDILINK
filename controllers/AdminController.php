<?php
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/ProfessionnelSante.php';
require_once __DIR__ . '/../models/Administrateur.php';

/**
 * AdminController — CRUD utilisateurs pour le backoffice
 */
class AdminController
{
    private Administrateur $admin;

    public function __construct()
    {
        $this->admin = new Administrateur();
    }

    /**
     * Lister tous les utilisateurs avec données jointes
     */
    public function list(): array
    {
        return $this->admin->gererUtilisateurs();
    }

    /**
     * Récupérer un utilisateur complet par ID
     */
    public function getUser(int $id): ?array
    {
        return $this->admin->getUtilisateurComplet($id);
    }

    /**
     * Statistiques
     */
    public function getStats(): array
    {
        return [
            'total'    => Utilisateur::countByRole(),
            'patients' => Utilisateur::countByRole('Patient'),
            'pros'     => Utilisateur::countByRole('Professionnel'),
            'actifs'   => Utilisateur::countActifs(),
        ];
    }

    /**
     * Validation côté serveur pour création/modification
     */
    private function valider(array $data, ?int $editingId = null): array
    {
        $errors = [];

        // Prénom
        $prenom = trim($data['prenom'] ?? '');
        if (empty($prenom)) {
            $errors['prenom'] = 'Prénom obligatoire.';
        } elseif (strlen($prenom) < 2) {
            $errors['prenom'] = 'Minimum 2 caractères.';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s.\-\']+$/', $prenom)) {
            $errors['prenom'] = 'Le prénom ne doit contenir que des lettres.';
        }

        // Nom
        $nom = trim($data['nom'] ?? '');
        if (empty($nom)) {
            $errors['nom'] = 'Nom obligatoire.';
        } elseif (strlen($nom) < 2) {
            $errors['nom'] = 'Minimum 2 caractères.';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s.\-\']+$/', $nom)) {
            $errors['nom'] = 'Le nom ne doit contenir que des lettres.';
        }

        // Email
        $email = trim($data['email'] ?? '');
        if (empty($email)) {
            $errors['email'] = 'Email obligatoire.';
        } elseif (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
            $errors['email'] = 'Email invalide.';
        } elseif (Utilisateur::emailExiste($email, $editingId)) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }

        // Téléphone — exactement 8 chiffres
        $tel = trim($data['telephone'] ?? '');
        $telDigits = preg_replace('/\D/', '', $tel);
        if (empty($tel)) {
            $errors['telephone'] = 'Téléphone obligatoire.';
        } elseif (strlen($telDigits) !== 8) {
            $errors['telephone'] = 'Le téléphone doit contenir exactement 8 chiffres.';
        }

        // Rôle
        if (empty($data['role']) || !in_array($data['role'], ['Patient', 'Professionnel', 'Administrateur'])) {
            $errors['role'] = 'Rôle invalide.';
        }

        // Mot de passe (obligatoire en création)
        $mdp = $data['mot_de_passe'] ?? '';
        if (!$editingId && empty($mdp)) {
            $errors['mot_de_passe'] = 'Mot de passe obligatoire.';
        }
        if (!empty($mdp)) {
            if (strlen($mdp) < 8) {
                $errors['mot_de_passe'] = 'Minimum 8 caractères.';
            } else {
                $score = 0;
                if (preg_match('/[A-Z]/', $mdp)) $score++;
                if (preg_match('/[0-9]/', $mdp)) $score++;
                if (preg_match('/[^A-Za-z0-9]/', $mdp)) $score++;
                if ($score < 2) {
                    $errors['mot_de_passe'] = 'Trop faible : ajoutez majuscules, chiffres ou symboles.';
                }
            }
            if ($mdp !== ($data['confirm_mdp'] ?? '')) {
                $errors['confirm_mdp'] = 'Les mots de passe ne correspondent pas.';
            }
        }

        // Date de naissance
        $dob = $data['date_naissance'] ?? '';
        if (!empty($dob)) {
            $d = strtotime($dob);
            if ($d === false || $d > time()) {
                $errors['date_naissance'] = 'Date invalide.';
            }
        }

        // Champs Professionnel
        if (($data['role'] ?? '') === 'Professionnel') {
            if (empty(trim($data['specialite'] ?? ''))) {
                $errors['specialite'] = 'Spécialité obligatoire.';
            }
            $ordre = trim($data['numero_ordre'] ?? '');
            if (empty($ordre)) {
                $errors['numero_ordre'] = 'N° d\'ordre obligatoire.';
            } elseif (!preg_match('/^TN-[A-Z]{2,5}-\d{4,6}$/', $ordre)) {
                $errors['numero_ordre'] = 'Format: TN-MED-12345';
            }
        }

        return $errors;
    }

    /**
     * Créer un utilisateur
     */
    public function create(array $data): array
    {
        $errors = $this->valider($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            switch ($data['role']) {
                case 'Patient':
                    $user = new Patient(
                        trim($data['nom']),
                        trim($data['prenom']),
                        trim($data['email']),
                        $data['mot_de_passe'],
                        trim($data['telephone']),
                        $data['statut'] ?? 'Actif',
                        $data['date_naissance'] ?? null,
                        $data['sexe'] ?? null,
                        $data['adresse'] ?? null,
                        $data['groupe_sanguin'] ?? null
                    );
                    break;

                case 'Professionnel':
                    $user = new ProfessionnelSante(
                        trim($data['nom']),
                        trim($data['prenom']),
                        trim($data['email']),
                        $data['mot_de_passe'],
                        trim($data['telephone']),
                        $data['statut'] ?? 'Actif',
                        trim($data['specialite']),
                        trim($data['numero_ordre']),
                        $data['biographie'] ?? null
                    );
                    break;

                case 'Administrateur':
                    $user = new Administrateur(
                        trim($data['nom']),
                        trim($data['prenom']),
                        trim($data['email']),
                        $data['mot_de_passe'],
                        trim($data['telephone']),
                        $data['statut'] ?? 'Actif'
                    );
                    break;
            }

            $id = $user->sInscrire();
            return ['success' => true, 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['global' => 'Erreur : ' . $e->getMessage()]];
        }
    }

    /**
     * Modifier un utilisateur
     */
    public function update(int $id, array $data): array
    {
        $errors = $this->valider($data, $id);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $role = $data['role'];

            if ($role === 'Patient') {
                $user = new Patient();
                $user->setId($id);
                $user->setNom(trim($data['nom']));
                $user->setPrenom(trim($data['prenom']));
                $user->setEmail(trim($data['email']));
                $user->setTelephone(trim($data['telephone']));
                $user->setStatutCompte($data['statut'] ?? 'Actif');
                $user->setDateNaissance($data['date_naissance'] ?? null);
                $user->setSexe($data['sexe'] ?? null);
                $user->setAdresse($data['adresse'] ?? null);
                $user->setGroupeSanguin($data['groupe_sanguin'] ?? null);
                $user->modifierProfil();
            } elseif ($role === 'Professionnel') {
                $user = new ProfessionnelSante();
                $user->setId($id);
                $user->setNom(trim($data['nom']));
                $user->setPrenom(trim($data['prenom']));
                $user->setEmail(trim($data['email']));
                $user->setTelephone(trim($data['telephone']));
                $user->setStatutCompte($data['statut'] ?? 'Actif');
                $user->setSpecialite(trim($data['specialite']));
                $user->setNumeroOrdre(trim($data['numero_ordre']));
                $user->setBiographie($data['biographie'] ?? null);
                $user->modifierProfil();
            } else {
                $user = new Administrateur();
                $user->setId($id);
                $user->setNom(trim($data['nom']));
                $user->setPrenom(trim($data['prenom']));
                $user->setEmail(trim($data['email']));
                $user->setTelephone(trim($data['telephone']));
                $user->setStatutCompte($data['statut'] ?? 'Actif');
                $user->modifierProfil();
            }

            // Mettre à jour le mot de passe si fourni
            $mdp = $data['mot_de_passe'] ?? '';
            if (!empty($mdp)) {
                $user->changerMotDePasse($mdp);
            }

            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['global' => 'Erreur : ' . $e->getMessage()]];
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function delete(int $id): array
    {
        try {
            Utilisateur::supprimer($id);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['global' => 'Erreur : ' . $e->getMessage()]];
        }
    }
}
