<?php
class Rendezvous {
    private ?int $id;
    private ?int $medecin_id;
    private ?string $date_rdv;
    private ?string $heure_rdv;
    private ?string $statut;

    public function __construct(?int $id = null, ?int $medecin_id = null, ?string $date_rdv = null, ?string $heure_rdv = null, ?string $statut = 'confirmé') {
        $this->id = $id;
        $this->medecin_id = $medecin_id;
        $this->date_rdv = $date_rdv;
        $this->heure_rdv = $heure_rdv;
        $this->statut = $statut;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getMedecinId(): ?int { return $this->medecin_id; }
    public function getDateRdv(): ?string { return $this->date_rdv; }
    public function getHeureRdv(): ?string { return $this->heure_rdv; }
    public function getStatut(): ?string { return $this->statut; }

    // Setters
    public function setMedecinId(int $medecin_id): self { $this->medecin_id = $medecin_id; return $this; }
    public function setDateRdv(string $date_rdv): self { $this->date_rdv = $date_rdv; return $this; }
    public function setHeureRdv(string $heure_rdv): self { $this->heure_rdv = $heure_rdv; return $this; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
}
?>