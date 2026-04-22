<?php
require_once __DIR__ . '/Utilisateur.php';
require_once __DIR__ . '/../models/Patient.php';

/**
 * PatientController (controllers/Patient.php)
 * Implémentation des méthodes de la classe Patient
 * Contient toute la logique d'accès à la base de données pour la table patient
 */
class PatientModelController
{
    /**
     * Inscription patient — insère dans utilisateur + patient
     */
    public static function sInscrire(array $patient): int
    {
        // Insérer dans la table utilisateur (parent)
        $id = UtilisateurController::sInscrire($patient);

        // Insérer dans la table patient
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO patient (id, date_naissance, sexe, adresse, groupe_sanguin)
             VALUES (:id, :dob, :sexe, :adresse, :gs)"
        );
        $stmt->execute([
            ':id'      => $id,
            ':dob'     => $patient['date_naissance'] ?? null,
            ':sexe'    => $patient['sexe'] ?? null,
            ':adresse' => $patient['adresse'] ?? null,
            ':gs'      => $patient['groupe_sanguin'] ?? null,
        ]);

        return $id;
    }

    /**
     * Modifier le profil patient
     */
    public static function modifierProfil(array $patient): bool
    {
        // Mettre à jour la table utilisateur
        UtilisateurController::modifierProfil($patient);

        // Mettre à jour la table patient
        $pdo = Database::getInstance();

        // Vérifier si la ligne patient existe
        $check = $pdo->prepare("SELECT COUNT(*) FROM patient WHERE id = :id");
        $check->execute([':id' => $patient['id']]);

        if ((int) $check->fetchColumn() > 0) {
            $stmt = $pdo->prepare(
                "UPDATE patient
                 SET date_naissance = :dob, sexe = :sexe, adresse = :adresse, groupe_sanguin = :gs
                 WHERE id = :id"
            );
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO patient (id, date_naissance, sexe, adresse, groupe_sanguin)
                 VALUES (:id, :dob, :sexe, :adresse, :gs)"
            );
        }

        return $stmt->execute([
            ':id'      => $patient['id'],
            ':dob'     => $patient['date_naissance'] ?? null,
            ':sexe'    => $patient['sexe'] ?? null,
            ':adresse' => $patient['adresse'] ?? null,
            ':gs'      => $patient['groupe_sanguin'] ?? null,
        ]);
    }

    /**
     * Récupérer les données complètes d'un patient par ID
     */
    public static function getPatientById(int $id): ?array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            "SELECT u.*, p.date_naissance, p.sexe, p.adresse, p.groupe_sanguin
             FROM utilisateur u
             LEFT JOIN patient p ON u.id = p.id
             WHERE u.id = :id AND u.role = 'Patient'"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
