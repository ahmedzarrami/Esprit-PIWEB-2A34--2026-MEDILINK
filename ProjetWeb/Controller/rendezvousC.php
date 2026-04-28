<?php
// Utiliser des chemins absolus basés sur la location de ce fichier
$basePath = dirname(__DIR__);
require_once $basePath . '/config.php';
require_once $basePath . '/Model/rendezvous.php';

class RendezvousC {

    // Ajouter un rendez-vous
    public function addRendezvous(Rendezvous $rdv) {
        $sql = "INSERT INTO rendezvous (medecin_id, date_rdv, heure_rdv, statut)
                VALUES (:medecin_id, :date_rdv, :heure_rdv, :statut)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'medecin_id' => $rdv->getMedecinId(),
                'date_rdv'   => $rdv->getDateRdv(),
                'heure_rdv'  => $rdv->getHeureRdv(),
                'statut'     => $rdv->getStatut()
            ]);
            if ($result) {
                return $db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    // Lister tous les rendez-vous (avec jointure médecin)
    public function listRendezvous() {
        $sql = "SELECT r.*, m.nom as medecin_nom, m.specialite 
                FROM rendezvous r
                JOIN medecins m ON r.medecin_id = m.id
                ORDER BY r.date_rdv DESC, r.heure_rdv ASC";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            return $query->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    // Récupérer un rendez-vous par ID
    public function getRendezvousById($id) {
        $sql = "SELECT * FROM rendezvous WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    // Modifier un rendez-vous
    public function updateRendezvous(Rendezvous $rdv) {
        $sql = "UPDATE rendezvous SET
                medecin_id = :medecin_id,
                date_rdv   = :date_rdv,
                heure_rdv  = :heure_rdv,
                statut     = :statut
                WHERE id   = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id'         => $rdv->getId(),
                'medecin_id' => $rdv->getMedecinId(),
                'date_rdv'   => $rdv->getDateRdv(),
                'heure_rdv'  => $rdv->getHeureRdv(),
                'statut'     => $rdv->getStatut()
            ]);
            return $query->rowCount();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    // Supprimer un rendez-vous
    public function deleteRendezvous($id) {
        $sql = "DELETE FROM rendezvous WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    // Récupérer la liste des médecins (pour les formulaires)
    public function listMedecins() {
        $sql = "SELECT id, nom, specialite FROM medecins ORDER BY nom";
        $db = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }
}
?>