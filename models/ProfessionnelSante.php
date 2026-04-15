<?php
require_once __DIR__ . '/Utilisateur.php';

/**
 * Classe ProfessionnelSante — Hérite de Utilisateur
 */
class ProfessionnelSante extends Utilisateur
{
    // ── Propriétés spécifiques ──
    private string  $specialite  = '';
    private string  $numeroOrdre = '';
    private ?string $biographie  = null;

    // ── Constructeur ──
    public function __construct(
        string $nom = '',
        string $prenom = '',
        string $email = '',
        string $motDePasse = '',
        string $telephone = '',
        string $statutCompte = 'Actif',
        string $specialite = '',
        string $numeroOrdre = '',
        ?string $biographie = null
    ) {
        parent::__construct($nom, $prenom, $email, $motDePasse, $telephone, $statutCompte, 'Professionnel');
        $this->specialite  = $specialite;
        $this->numeroOrdre = $numeroOrdre;
        $this->biographie  = $biographie;
    }

    // ── Getters ──
    public function getSpecialite(): string   { return $this->specialite; }
    public function getNumeroOrdre(): string  { return $this->numeroOrdre; }
    public function getBiographie(): ?string  { return $this->biographie; }

    // ── Setters ──
    public function setSpecialite(string $s): void   { $this->specialite = $s; }
    public function setNumeroOrdre(string $n): void  { $this->numeroOrdre = $n; }
    public function setBiographie(?string $b): void  { $this->biographie = $b; }

    /**
     * Inscription professionnel — insère dans utilisateur + professionnel_sante
     */
    public function sInscrire(): int
    {
        $id = parent::sInscrire();

        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO professionnel_sante (id, specialite, numero_ordre, biographie)
             VALUES (:id, :spec, :ordre, :bio)"
        );
        $stmt->execute([
            ':id'    => $id,
            ':spec'  => $this->specialite,
            ':ordre' => $this->numeroOrdre,
            ':bio'   => $this->biographie,
        ]);

        return $id;
    }

    /**
     * Modifier le profil professionnel
     */
    public function modifierProfil(): bool
    {
        parent::modifierProfil();

        $pdo = Database::getInstance();

        $check = $pdo->prepare("SELECT COUNT(*) FROM professionnel_sante WHERE id = :id");
        $check->execute([':id' => $this->getId()]);

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
            ':id'    => $this->getId(),
            ':spec'  => $this->specialite,
            ':ordre' => $this->numeroOrdre,
            ':bio'   => $this->biographie,
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
