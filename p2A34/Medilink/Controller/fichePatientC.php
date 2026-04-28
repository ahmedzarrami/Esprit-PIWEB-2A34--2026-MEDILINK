<?php
$basePath = dirname(__DIR__);
require_once $basePath . '/config.php';
require_once $basePath . '/Model/fichePatient.php';

class FichePatientC {

    public function addFichePatient(FichePatient $fiche) {
        $sql = "INSERT INTO fiche_patient (rendezvous_id, groupsanguin, allergies, antecedents, notesGenerales, date_creation)
                VALUES (:rendezvous_id, :groupsanguin, :allergies, :antecedents, :notesGenerales, :date_creation)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'rendezvous_id'   => $fiche->getRendezvousId(),
                'groupsanguin'    => $fiche->getGroupsanguin(),
                'allergies'       => $fiche->getAllergies(),
                'antecedents'     => $fiche->getAntecedents(),
                'notesGenerales'  => $fiche->getNotesGenerales(),
                'date_creation'   => $fiche->getDateCreation()
            ]);
            if ($result) {
                return $db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    public function listFichePatient() {
        $sql = "SELECT fp.*, r.date_rdv, r.heure_rdv, m.nom as medecin_nom, m.specialite,
                        p.nom as patient_nom, p.prenom as patient_prenom
                FROM fiche_patient fp
                JOIN rendezvous r ON fp.rendezvous_id = r.id
                JOIN medecins m ON r.medecin_id = m.id
                LEFT JOIN patients p ON r.patient_id = p.id
                ORDER BY fp.date_creation DESC";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            return $query->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    public function listFichePatientByMedecinId($medecin_id) {
        $sql = "SELECT fp.*, r.date_rdv, r.heure_rdv, m.nom as medecin_nom, m.specialite
                FROM fiche_patient fp
                JOIN rendezvous r ON fp.rendezvous_id = r.id
                JOIN medecins m ON r.medecin_id = m.id
                WHERE m.id = :medecin_id
                ORDER BY fp.date_creation DESC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['medecin_id' => $medecin_id]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    public function getFichePatientById($id) {
        $sql = "SELECT fp.*, r.date_rdv, r.heure_rdv, m.nom as medecin_nom, m.specialite
                FROM fiche_patient fp
                JOIN rendezvous r ON fp.rendezvous_id = r.id
                JOIN medecins m ON r.medecin_id = m.id
                WHERE fp.idfiche = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    public function getFichePatientByRendezvousId($rendezvous_id) {
        $sql = "SELECT * FROM fiche_patient WHERE rendezvous_id = :rendezvous_id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['rendezvous_id' => $rendezvous_id]);
            return $query->fetch();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    public function updateFichePatient(FichePatient $fiche) {
        $sql = "UPDATE fiche_patient SET
                rendezvous_id   = :rendezvous_id,
                groupsanguin    = :groupsanguin,
                allergies       = :allergies,
                antecedents     = :antecedents,
                notesGenerales  = :notesGenerales
                WHERE idfiche   = :idfiche";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'idfiche'         => $fiche->getIdfiche(),
                'rendezvous_id'   => $fiche->getRendezvousId(),
                'groupsanguin'    => $fiche->getGroupsanguin(),
                'allergies'       => $fiche->getAllergies(),
                'antecedents'     => $fiche->getAntecedents(),
                'notesGenerales'  => $fiche->getNotesGenerales()
            ]);
            return $query->rowCount();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    public function deleteFichePatient($id) {
        $sql = "DELETE FROM fiche_patient WHERE idfiche = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }
}
?>