<?php
require_once __DIR__ . '/Utilisateur.php';
require_once __DIR__ . '/../models/ProfessionnelSante.php';

/**
 * ProfessionnelSanteController (controllers/ProfessionnelSante.php)
 * Implémentation des méthodes de la classe ProfessionnelSante
 * Contient toute la logique d'accès à la base de données pour la table professionnel_sante
 */
class ProfessionnelSanteModelController
{
    /**
     * Inscription professionnel — insère dans utilisateur + professionnel_sante
     */
    public static function sInscrire(array $pro): int
    {
        // Insérer dans la table utilisateur (parent)
        $id = UtilisateurController::sInscrire($pro);

        // Insérer dans la table professionnel_sante
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO professionnel_sante (id, specialite, numero_ordre, biographie)
             VALUES (:id, :spec, :ordre, :bio)"
        );
        $stmt->execute([
            ':id'    => $id,
            ':spec'  => $pro['specialite'],
            ':ordre' => $pro['numero_ordre'],
            ':bio'   => $pro['biographie'] ?? null,
        ]);

        return $id;
    }

    /**
     * Modifier le profil professionnel
     */
    public static function modifierProfil(array $pro): bool
    {
        // Mettre à jour la table utilisateur
        UtilisateurController::modifierProfil($pro);

        // Mettre à jour la table professionnel_sante
        $pdo = Database::getInstance();

        $check = $pdo->prepare("SELECT COUNT(*) FROM professionnel_sante WHERE id = :id");
        $check->execute([':id' => $pro['id']]);

        if ((int) $check->fetchColumn() > 0) {
            $stmt = $pdo->prepare(
                "UPDATE professionnel_sante
                 SET specialite = :spec, numero_ordre = :ordre, biographie = :bio
                 WHERE id = :id"
            );
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO professionnel_sante (id, specialite, numero_ordre, biographie)
                 VALUES (:id, :spec, :ordre, :bio)"
            );
        }

        return $stmt->execute([
            ':id'    => $pro['id'],
            ':spec'  => $pro['specialite'],
            ':ordre' => $pro['numero_ordre'],
            ':bio'   => $pro['biographie'] ?? null,
        ]);
    }

    /**
     * Récupérer les données complètes d'un professionnel par ID
     */
    public static function getProById(int $id): ?array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            "SELECT u.*, ps.specialite, ps.numero_ordre, ps.biographie
             FROM utilisateur u
             LEFT JOIN professionnel_sante ps ON u.id = ps.id
             WHERE u.id = :id AND u.role = 'Professionnel'"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
