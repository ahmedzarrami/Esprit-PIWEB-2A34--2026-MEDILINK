<?php
if (!class_exists('Evaluation')) {
class Evaluation {
    private ?int    $id;
    private int     $patient_id;
    private int     $medecin_id;
    private int     $rendezvous_id;
    private int     $note;          // 1 à 5
    private ?string $commentaire;
    private ?string $date_eval;

    public function __construct(
        ?int    $id            = null,
        int     $patient_id    = 0,
        int     $medecin_id    = 0,
        int     $rendezvous_id = 0,
        int     $note          = 5,
        ?string $commentaire   = null,
        ?string $date_eval     = null
    ) {
        $this->id            = $id;
        $this->patient_id    = $patient_id;
        $this->medecin_id    = $medecin_id;
        $this->rendezvous_id = $rendezvous_id;
        $this->note          = max(1, min(5, $note)); // forcer entre 1 et 5
        $this->commentaire   = $commentaire;
        $this->date_eval     = $date_eval ?? date('Y-m-d H:i:s');
    }

    // ── Getters ──
    public function getId():           ?int    { return $this->id; }
    public function getPatientId():    int     { return $this->patient_id; }
    public function getMedecinId():    int     { return $this->medecin_id; }
    public function getRendezvousId(): int     { return $this->rendezvous_id; }
    public function getNote():         int     { return $this->note; }
    public function getCommentaire():  ?string { return $this->commentaire; }
    public function getDateEval():     ?string { return $this->date_eval; }

    // ── Setters ──
    public function setNote(int $note): self {
        $this->note = max(1, min(5, $note));
        return $this;
    }
    public function setCommentaire(?string $c): self {
        $this->commentaire = $c;
        return $this;
    }
}
}