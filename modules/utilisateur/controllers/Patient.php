<?php
// Dépendance vers le contrôleur utilisateur (pour les opérations sur la table "utilisateur")
require_once __DIR__ . '/Utilisateur.php';
// Dépendance vers le modèle abstrait Patient (définit les méthodes obligatoires)
require_once __DIR__ . '/../models/Patient.php';

/**
 * PatientModelController
 * Gère les opérations sur la table "patient" qui ÉTEND la table "utilisateur".
 * La relation est 1-1 : chaque patient a un ID identique dans les deux tables.
 * Toutes les méthodes sont statiques.
 */
class PatientModelController
{
    /**
     * Crée un nouveau patient dans les deux tables :
     * 1. Insère les données communes dans "utilisateur"
     * 2. Insère les données médicales dans "patient"
     * Retourne l'ID du patient créé.
     */
    public static function sInscrire(array $patient): int
    {
        // Étape 1 : insertion dans la table parente "utilisateur"
        // Cette méthode retourne l'ID auto-incrémenté de la nouvelle ligne
        $id = UtilisateurController::sInscrire($patient);

        // Étape 2 : insertion dans la table fille "patient" avec le MÊME ID
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO patient (id, date_naissance, sexe, adresse, groupe_sanguin)
             VALUES (:id, :dob, :sexe, :adresse, :gs)"
        );

        $stmt->execute([
            ':id'      => $id,
            // ?? null : si la clé n'existe pas dans $patient, on insère NULL
            ':dob'     => $patient['date_naissance'] ?? null,
            ':sexe'    => $patient['sexe'] ?? null,
            ':adresse' => $patient['adresse'] ?? null,
            ':gs'      => $patient['groupe_sanguin'] ?? null,
        ]);

        return $id;
    }

    /**
     * Met à jour le profil d'un patient dans les deux tables.
     * Utilise un "upsert manuel" pour la table "patient" :
     * vérifie si la ligne existe avant de choisir UPDATE ou INSERT.
     */
    public static function modifierProfil(array $patient): bool
    {
        // Étape 1 : mise à jour des champs communs dans "utilisateur"
        UtilisateurController::modifierProfil($patient);

        $pdo = Database::getInstance();

        // Vérifie si une ligne existe déjà dans la table "patient" pour cet ID
        // Cas possible : un admin a créé le patient sans données médicales
        $check = $pdo->prepare("SELECT COUNT(*) FROM patient WHERE id = :id");
        $check->execute([':id' => $patient['id']]);

        if ((int) $check->fetchColumn() > 0) {
            // La ligne existe : on fait une mise à jour
            $stmt = $pdo->prepare(
                "UPDATE patient
                 SET date_naissance = :dob, sexe = :sexe, adresse = :adresse, groupe_sanguin = :gs
                 WHERE id = :id"
            );
        } else {
            // La ligne n'existe pas encore : on l'insère (première complétion du profil)
            $stmt = $pdo->prepare(
                "INSERT INTO patient (id, date_naissance, sexe, adresse, groupe_sanguin)
                 VALUES (:id, :dob, :sexe, :adresse, :gs)"
            );
        }

        // Exécution de la requête choisie (UPDATE ou INSERT) avec les mêmes paramètres
        return $stmt->execute([
            ':id'      => $patient['id'],
            ':dob'     => $patient['date_naissance'] ?? null,
            ':sexe'    => $patient['sexe'] ?? null,
            ':adresse' => $patient['adresse'] ?? null,
            ':gs'      => $patient['groupe_sanguin'] ?? null,
        ]);
    }

    /**
     * Récupère toutes les données d'un patient par son ID.
     * Fait une jointure entre "utilisateur" et "patient" pour tout avoir en un seul tableau.
     * Retourne null si l'utilisateur n'existe pas ou n'est pas un Patient.
     */
    public static function getPatientById(int $id): ?array
    {
        $pdo  = Database::getInstance();

        // LEFT JOIN : retourne les colonnes des deux tables dans un seul résultat
        // u.* = toutes les colonnes de "utilisateur" (nom, email, role, etc.)
        // p.date_naissance, p.sexe... = colonnes spécifiques à "patient"
        $stmt = $pdo->prepare(
            "SELECT u.*, p.date_naissance, p.sexe, p.adresse, p.groupe_sanguin
             FROM utilisateur u
             LEFT JOIN patient p ON u.id = p.id
             WHERE u.id = :id AND u.role = 'Patient'"
            // Le filtre role = 'Patient' évite de charger un professionnel par erreur
        );

        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        // fetch() retourne false si aucun résultat — on le convertit en null
        return $row ?: null;
    }
}
