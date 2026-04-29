<?php
// Dépendance vers le contrôleur utilisateur de base
require_once __DIR__ . '/Utilisateur.php';
// Dépendance vers le modèle abstrait ProfessionnelSante
require_once __DIR__ . '/../models/ProfessionnelSante.php';

/**
 * ProfessionnelSanteModelController
 * Gère les opérations sur la table "professionnel_sante" qui ÉTEND "utilisateur".
 * Même principe que PatientModelController : relation 1-1 par ID commun.
 * Toutes les méthodes sont statiques.
 */
class ProfessionnelSanteModelController
{
    /**
     * Crée un professionnel de santé dans les deux tables :
     * 1. Insère les données communes dans "utilisateur"
     * 2. Insère les données professionnelles dans "professionnel_sante"
     * Retourne l'ID créé.
     */
    public static function sInscrire(array $pro): int
    {
        // Étape 1 : création de la ligne dans la table parente "utilisateur"
        // UtilisateurController::sInscrire() hashe le mot de passe et retourne l'ID
        $id = UtilisateurController::sInscrire($pro);

        // Étape 2 : insertion des données métier dans "professionnel_sante"
        // Le même ID est utilisé pour lier les deux tables
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO professionnel_sante (id, specialite, numero_ordre, biographie)
             VALUES (:id, :spec, :ordre, :bio)"
        );

        $stmt->execute([
            ':id'    => $id,
            ':spec'  => $pro['specialite'],       // ex: "Cardiologie"
            ':ordre' => $pro['numero_ordre'],      // ex: "TN-MED-12345"
            // La biographie est optionnelle : null si non fournie
            ':bio'   => $pro['biographie'] ?? null,
        ]);

        return $id;
    }

    /**
     * Met à jour le profil d'un professionnel dans les deux tables.
     * Même logique d'upsert manuel que pour le patient :
     * UPDATE si la ligne existe dans professionnel_sante, INSERT sinon.
     */
    public static function modifierProfil(array $pro): bool
    {
        // Étape 1 : mise à jour des colonnes communes dans "utilisateur"
        UtilisateurController::modifierProfil($pro);

        $pdo = Database::getInstance();

        // Vérifie si une ligne existe déjà dans "professionnel_sante" pour cet ID
        $check = $pdo->prepare("SELECT COUNT(*) FROM professionnel_sante WHERE id = :id");
        $check->execute([':id' => $pro['id']]);

        if ((int) $check->fetchColumn() > 0) {
            // Ligne trouvée : mise à jour des données professionnelles
            $stmt = $pdo->prepare(
                "UPDATE professionnel_sante
                 SET specialite = :spec, numero_ordre = :ordre, biographie = :bio
                 WHERE id = :id"
            );
        } else {
            // Ligne absente : première insertion des données professionnelles
            $stmt = $pdo->prepare(
                "INSERT INTO professionnel_sante (id, specialite, numero_ordre, biographie)
                 VALUES (:id, :spec, :ordre, :bio)"
            );
        }

        // Les deux requêtes (UPDATE et INSERT) utilisent les mêmes paramètres nommés
        return $stmt->execute([
            ':id'    => $pro['id'],
            ':spec'  => $pro['specialite'],
            ':ordre' => $pro['numero_ordre'],
            ':bio'   => $pro['biographie'] ?? null,
        ]);
    }

    /**
     * Récupère toutes les données d'un professionnel par son ID.
     * Jointure entre "utilisateur" et "professionnel_sante".
     * Retourne null si non trouvé ou si le rôle n'est pas "Professionnel".
     */
    public static function getProById(int $id): ?array
    {
        $pdo  = Database::getInstance();

        // LEFT JOIN : fusionne les deux tables en un seul tableau de résultats
        // u.* = nom, email, telephone, role, statut_compte...
        // ps.specialite, ps.numero_ordre, ps.biographie = données métier
        $stmt = $pdo->prepare(
            "SELECT u.*, ps.specialite, ps.numero_ordre, ps.biographie
             FROM utilisateur u
             LEFT JOIN professionnel_sante ps ON u.id = ps.id
             WHERE u.id = :id AND u.role = 'Professionnel'"
            // Filtre sur le rôle : sécurité supplémentaire pour ne pas charger
            // un patient ou un admin avec cette méthode
        );

        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        // Retourne null si fetch() renvoie false (aucun résultat)
        return $row ?: null;
    }
}
