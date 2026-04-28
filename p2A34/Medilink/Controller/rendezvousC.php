<?php
$basePath = dirname(__DIR__);
require_once $basePath . '/config.php';
require_once $basePath . '/Model/rendezvous.php';

class RendezvousC {

    public function addRendezvous(Rendezvous $rdv) {
        $sql = "INSERT INTO rendezvous (medecin_id, patient_id, date_rdv, heure_rdv, statut)
                VALUES (:medecin_id, :patient_id, :date_rdv, :heure_rdv, :statut)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'medecin_id' => $rdv->getMedecinId(),
                'patient_id' => $rdv->getPatientId(),
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

    public function getRendezvousByMedecinId($medecin_id) {
        $sql = "SELECT r.*, m.nom as medecin_nom, m.specialite 
                FROM rendezvous r
                JOIN medecins m ON r.medecin_id = m.id
                WHERE r.medecin_id = :medecin_id
                ORDER BY r.date_rdv DESC, r.heure_rdv ASC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['medecin_id' => $medecin_id]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    public function listMedecins() {
        $sql = "SELECT id, nom, specialite FROM medecins ORDER BY nom";
        $db = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    public function getMedecinById($id) {
        $sql = "SELECT id, nom, specialite, email FROM medecins WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    public function medecinExists($id) {
        $medecin = $this->getMedecinById($id);
        return $medecin !== false;
    }

    public function getRendezvousByPatientId($patient_id) {
        $sql = "SELECT r.*, m.nom as medecin_nom, m.specialite, p.nom as patient_nom, p.prenom as patient_prenom
                FROM rendezvous r
                JOIN medecins m ON r.medecin_id = m.id
                LEFT JOIN patients p ON r.patient_id = p.id
                WHERE r.patient_id = :patient_id
                ORDER BY r.date_rdv DESC, r.heure_rdv ASC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['patient_id' => $patient_id]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    public function getPatientById($id) {
        $sql = "SELECT id, nom, prenom, email, telephone, datedenaissance, sexe, adresse FROM patients WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    public function patientExists($id) {
        $patient = $this->getPatientById($id);
        return $patient !== false;
    }

    public function getMedecinAvailability($medecin_id, $date) {
        $sql = "SELECT heure_rdv FROM rendezvous 
                WHERE medecin_id = :medecin_id 
                AND date_rdv = :date 
                AND statut != 'annulé'
                ORDER BY heure_rdv ASC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['medecin_id' => $medecin_id, 'date' => $date]);
            $bookedResults = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Formater les heures réservées en HH:MM
            $bookedSlots = [];
            foreach ($bookedResults as $row) {
                // Assurer le format HH:MM
                $time = $row['heure_rdv'];
                if (strlen($time) === 5 && strpos($time, ':') !== false) {
                    $bookedSlots[] = $time; // Déjà au bon format
                } else {
                    // Sinon, le formater
                    $bookedSlots[] = substr($time, 0, 5);
                }
            }
            
            // Créneaux de pause déjeuner (12:30 - 14:00)
            $lunchBreak = ['12:30', '13:00', '13:30'];
            
            // Créer la liste des créneaux horaires disponibles (8h-17h, intervalle 30min)
            $allSlots = [];
            for ($h = 8; $h <= 17; $h++) {
                foreach (['00', '30'] as $m) {
                    $slot = sprintf('%02d:%s', $h, $m);
                    
                    // Déterminer le type d'indisponibilité
                    $isLunch = in_array($slot, $lunchBreak);
                    $isBooked = in_array($slot, $bookedSlots);
                    
                    if ($isLunch) {
                        $allSlots[] = [
                            'slot' => $slot,
                            'occupied' => true,
                            'type' => 'lunch'
                        ];
                    } elseif ($isBooked) {
                        $allSlots[] = [
                            'slot' => $slot,
                            'occupied' => true,
                            'type' => 'booked'
                        ];
                    } else {
                        $allSlots[] = [
                            'slot' => $slot,
                            'occupied' => false,
                            'type' => 'available'
                        ];
                    }
                }
            }
            
            return $allSlots;
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }
}
?>