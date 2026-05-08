<?php
if (!class_exists('EvaluationC')) {
$_evalBasePath = dirname(__DIR__);
require_once $_evalBasePath . '/config.php';
require_once $_evalBasePath . '/Model/evaluation.php';


class EvaluationC {

    // ── Ajouter une évaluation ──
    public function addEvaluation(Evaluation $eval): bool|int {
        $sql = "INSERT INTO evaluations
                    (patient_id, medecin_id, rendezvous_id, note, commentaire, date_eval)
                VALUES
                    (:patient_id, :medecin_id, :rendezvous_id, :note, :commentaire, :date_eval)";
        $db = config::getConnexion();
        try {
            $q = $db->prepare($sql);
            $q->execute([
                'patient_id'    => $eval->getPatientId(),
                'medecin_id'    => $eval->getMedecinId(),
                'rendezvous_id' => $eval->getRendezvousId(),
                'note'          => $eval->getNote(),
                'commentaire'   => $eval->getCommentaire(),
                'date_eval'     => $eval->getDateEval(),
            ]);
            return $db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Erreur BD: ' . $e->getMessage());
        }
    }

    // ── Vérifier si un RDV a déjà été évalué ──
    public function evalExistsByRdv(int $rendezvous_id): bool {
        $sql = "SELECT id FROM evaluations WHERE rendezvous_id = :rdv_id LIMIT 1";
        $db  = config::getConnexion();
        try {
            $q = $db->prepare($sql);
            $q->execute(['rdv_id' => $rendezvous_id]);
            return $q->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

    // ── Récupérer l'évaluation d'un RDV ──
    public function getEvalByRdv(int $rendezvous_id): array|false {
        $sql = "SELECT e.*, p.nom as patient_nom, p.prenom as patient_prenom
                FROM evaluations e
                LEFT JOIN patients p ON e.patient_id = p.id
                WHERE e.rendezvous_id = :rdv_id";
        $db  = config::getConnexion();
        try {
            $q = $db->prepare($sql);
            $q->execute(['rdv_id' => $rendezvous_id]);
            return $q->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ── Statistiques d'un médecin (note moyenne + nombre) ──
    public function getStatsMedecin(int $medecin_id): array {
        $sql = "SELECT
                    COUNT(*)        AS total,
                    ROUND(AVG(note), 1) AS moyenne,
                    SUM(note = 5)   AS nb5,
                    SUM(note = 4)   AS nb4,
                    SUM(note = 3)   AS nb3,
                    SUM(note = 2)   AS nb2,
                    SUM(note = 1)   AS nb1
                FROM evaluations
                WHERE medecin_id = :medecin_id";
        $db  = config::getConnexion();
        try {
            $q = $db->prepare($sql);
            $q->execute(['medecin_id' => $medecin_id]);
            $row = $q->fetch();
            return $row ?: ['total'=>0,'moyenne'=>0,'nb5'=>0,'nb4'=>0,'nb3'=>0,'nb2'=>0,'nb1'=>0];
        } catch (PDOException $e) {
            return ['total'=>0,'moyenne'=>0,'nb5'=>0,'nb4'=>0,'nb3'=>0,'nb2'=>0,'nb1'=>0];
        }
    }

    // ── Toutes les évaluations d'un médecin (avec nom patient) ──
    public function getEvalsByMedecin(int $medecin_id): array {
        $sql = "SELECT e.*, p.nom as patient_nom, p.prenom as patient_prenom,
                        r.date_rdv, r.heure_rdv
                FROM evaluations e
                LEFT JOIN patients p ON e.patient_id = p.id
                LEFT JOIN rendezvous r ON e.rendezvous_id = r.id
                WHERE e.medecin_id = :medecin_id
                ORDER BY e.date_eval DESC";
        $db  = config::getConnexion();
        try {
            $q = $db->prepare($sql);
            $q->execute(['medecin_id' => $medecin_id]);
            return $q->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // ── Moyenne de tous les médecins (pour admin) ──
    public function getMoyenneParMedecin(): array {
        $sql = "SELECT m.id, m.nom, m.specialite,
                        COUNT(e.id)          AS total_evals,
                        ROUND(AVG(e.note),1) AS moyenne
                FROM medecins m
                LEFT JOIN evaluations e ON m.id = e.medecin_id
                GROUP BY m.id, m.nom, m.specialite
                ORDER BY moyenne DESC";
        $db  = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
}
?>
