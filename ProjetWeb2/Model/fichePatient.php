<?php
class FichePatient {
    private ?int $idfiche;
    private ?int $rendezvous_id;
    private ?string $groupsanguin;
    private ?string $allergies;
    private ?string $antecedents;
    private ?string $notesGenerales;
    private ?string $date_creation;

    public function __construct(
        ?int $idfiche = null,
        ?int $rendezvous_id = null,
        ?string $groupsanguin = null,
        ?string $allergies = null,
        ?string $antecedents = null,
        ?string $notesGenerales = null,
        ?string $date_creation = null
    ) {
        $this->idfiche = $idfiche;
        $this->rendezvous_id = $rendezvous_id;
        $this->groupsanguin = $groupsanguin;
        $this->allergies = $allergies;
        $this->antecedents = $antecedents;
        $this->notesGenerales = $notesGenerales;
        $this->date_creation = $date_creation ?? date('Y-m-d H:i:s');
    }

    // Getters
    public function getIdfiche(): ?int { return $this->idfiche; }
    public function getRendezvousId(): ?int { return $this->rendezvous_id; }
    public function getGroupsanguin(): ?string { return $this->groupsanguin; }
    public function getAllergies(): ?string { return $this->allergies; }
    public function getAntecedents(): ?string { return $this->antecedents; }
    public function getNotesGenerales(): ?string { return $this->notesGenerales; }
    public function getDateCreation(): ?string { return $this->date_creation; }

    // Setters
    public function setRendezvousId(int $rendezvous_id): self { 
        $this->rendezvous_id = $rendezvous_id; 
        return $this; 
    }
    public function setGroupsanguin(string $groupsanguin): self { 
        $this->groupsanguin = $groupsanguin; 
        return $this; 
    }
    public function setAllergies(string $allergies): self { 
        $this->allergies = $allergies; 
        return $this; 
    }
    public function setAntecedents(string $antecedents): self { 
        $this->antecedents = $antecedents; 
        return $this; 
    }
    public function setNotesGenerales(string $notesGenerales): self { 
        $this->notesGenerales = $notesGenerales; 
        return $this; 
    }
}
?>
