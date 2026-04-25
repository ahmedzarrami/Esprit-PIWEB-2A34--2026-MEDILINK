<?php

declare(strict_types=1);

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Ordonnance.php';
require_once __DIR__ . '/../models/Medicament.php';

class FrontOrdonnanceController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function index(): void
    {
        $search  = trim((string) ($_GET['search'] ?? ''));
        $page    = max(1, (int) ($_GET['page']   ?? 1));
        $perPage = 10;

        $where  = $search !== '' ? 'WHERE numero LIKE :search OR patient_nom LIKE :search' : '';
        $params = $search !== '' ? [':search' => '%' . $search . '%'] : [];

        $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM ordonnances $where");
        $stmtCount->execute($params);
        $total = (int) $stmtCount->fetchColumn();

        $totalPages = max(1, (int) ceil($total / $perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT * FROM ordonnances $where ORDER BY date_ordonnance DESC LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();

        $ordonnances = array_map([$this, 'rowToArray'], $stmt->fetchAll());

        $this->render('ordonnance/index', [
            'ordonnances' => $ordonnances,
            'search'      => $search,
            'total'       => $total,
            'totalPages'  => $totalPages,
            'page'        => $page,
            'deleted'     => isset($_GET['success']) && $_GET['success'] === 'deleted',
        ]);
    }

    public function create(): void
    {
        $this->render('ordonnance/create', [
            'medicaments' => $this->getAllMedicaments(),
            'errors'      => [],
            'old'         => [],
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=ordonnances');
        }

        $patientNom     = trim((string) ($_POST['patient_nom']     ?? ''));
        $patientAge     = trim((string) ($_POST['patient_age']     ?? ''));
        $patientSexe    = $_POST['patient_sexe']                   ?? '';
        $dateOrdonnance = trim((string) ($_POST['date_ordonnance'] ?? ''));
        $notes          = trim((string) ($_POST['notes']           ?? ''));
        $lignes         = $_POST['lignes']                         ?? [];

        $errors = [];

        if ($patientNom === '')     $errors[] = 'Le nom du patient est requis.';
        if ($dateOrdonnance === '') $errors[] = 'La date de l\'ordonnance est requise.';
        if (empty($lignes))         $errors[] = 'Veuillez ajouter au moins un médicament.';

        foreach ($lignes as $i => $ligne) {
            $n = $i + 1;
            if (empty($ligne['medicament_id']))         $errors[] = "Ligne $n : sélectionnez un médicament.";
            if (empty(trim($ligne['posologie'] ?? ''))) $errors[] = "Ligne $n : la posologie est requise.";
        }

        if (!empty($errors)) {
            $this->render('ordonnance/create', [
                'medicaments' => $this->getAllMedicaments(),
                'errors'      => $errors,
                'old'         => $_POST,
            ]);
            return;
        }

        $numero = 'ORD-' . strtoupper(uniqid());

        $ordonnance = new Ordonnance();
        $ordonnance->setNumero($numero);
        $ordonnance->setPatientNom($patientNom);
        $ordonnance->setPatientAge($patientAge !== '' ? (int) $patientAge : null);
        $ordonnance->setPatientSexe(in_array($patientSexe, ['M', 'F'], true) ? $patientSexe : null);
        $ordonnance->setDateOrdonnance($dateOrdonnance);
        $ordonnance->setNotes($notes ?: null);

        $stmt = $this->db->prepare(
            'INSERT INTO ordonnances (numero, patient_nom, patient_age, patient_sexe, date_ordonnance, notes)
             VALUES (:numero, :patient_nom, :patient_age, :patient_sexe, :date_ordonnance, :notes)'
        );
        $stmt->execute([
            ':numero'          => $ordonnance->getNumero(),
            ':patient_nom'     => $ordonnance->getPatientNom(),
            ':patient_age'     => $ordonnance->getPatientAge(),
            ':patient_sexe'    => $ordonnance->getPatientSexe(),
            ':date_ordonnance' => $ordonnance->getDateOrdonnance(),
            ':notes'           => $ordonnance->getNotes(),
        ]);

        $id = (int) $this->db->lastInsertId();

        $stmtLigne = $this->db->prepare(
            'INSERT INTO ordonnance_lignes (ordonnance_id, medicament_id, posologie, duree, quantite)
             VALUES (:ordonnance_id, :medicament_id, :posologie, :duree, :quantite)'
        );
        foreach ($lignes as $ligne) {
            $stmtLigne->execute([
                ':ordonnance_id' => $id,
                ':medicament_id' => (int) $ligne['medicament_id'],
                ':posologie'     => trim($ligne['posologie']),
                ':duree'         => trim($ligne['duree'] ?? ''),
                ':quantite'      => (int) ($ligne['quantite'] ?? 1),
            ]);
        }

        $this->redirect('index.php?action=show_ordonnance&id=' . $id . '&success=created');
    }

    public function show(): void
    {
        $id   = (int) ($_GET['id'] ?? 0);
        $stmt = $this->db->prepare('SELECT * FROM ordonnances WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            http_response_code(404);
            $this->render('medicament/not-found', [
                'errorMessage' => 'L\'ordonnance demandée est introuvable.',
            ]);
            return;
        }

        $this->render('ordonnance/show', [
            'ordonnance' => $this->rowToArray($row),
            'lignes'     => $this->findLignes($id),
            'success'    => $_GET['success'] ?? null,
        ]);
    }

    public function printView(): void
    {
        $id   = (int) ($_GET['id'] ?? 0);
        $stmt = $this->db->prepare('SELECT * FROM ordonnances WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            http_response_code(404);
            echo 'Ordonnance introuvable.';
            return;
        }

        $this->render('ordonnance/print', [
            'ordonnance' => $this->rowToArray($row),
            'lignes'     => $this->findLignes($id),
        ]);
    }

    public function edit(): void
    {
        $id   = (int) ($_GET['id'] ?? 0);
        $stmt = $this->db->prepare('SELECT * FROM ordonnances WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row  = $stmt->fetch();

        if ($row === false) {
            http_response_code(404);
            $this->render('medicament/not-found', ['errorMessage' => 'Ordonnance introuvable.']);
            return;
        }

        $this->render('ordonnance/edit', [
            'ordonnance'  => $this->rowToArray($row),
            'lignes'      => $this->findLignes($id),
            'medicaments' => $this->getAllMedicaments(),
            'errors'      => [],
        ]);
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=ordonnances');
        }

        $id             = (int) ($_POST['id'] ?? 0);
        $patientNom     = trim((string) ($_POST['patient_nom']     ?? ''));
        $patientAge     = trim((string) ($_POST['patient_age']     ?? ''));
        $patientSexe    = $_POST['patient_sexe']                   ?? '';
        $dateOrdonnance = trim((string) ($_POST['date_ordonnance'] ?? ''));
        $notes          = trim((string) ($_POST['notes']           ?? ''));
        $lignes         = $_POST['lignes']                         ?? [];

        $errors = [];
        if ($patientNom === '')     $errors[] = 'Le nom du patient est requis.';
        if ($dateOrdonnance === '') $errors[] = 'La date de l\'ordonnance est requise.';
        if (empty($lignes))         $errors[] = 'Veuillez ajouter au moins un médicament.';

        foreach ($lignes as $i => $ligne) {
            $n = $i + 1;
            if (empty($ligne['medicament_id']))         $errors[] = "Ligne $n : sélectionnez un médicament.";
            if (empty(trim($ligne['posologie'] ?? ''))) $errors[] = "Ligne $n : la posologie est requise.";
        }

        if (!empty($errors)) {
            $stmt = $this->db->prepare('SELECT * FROM ordonnances WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            $this->render('ordonnance/edit', [
                'ordonnance'  => $row ? $this->rowToArray($row) : [],
                'lignes'      => $this->findLignes($id),
                'medicaments' => $this->getAllMedicaments(),
                'errors'      => $errors,
            ]);
            return;
        }

        $this->db->prepare(
            'UPDATE ordonnances SET patient_nom=:patient_nom, patient_age=:patient_age,
             patient_sexe=:patient_sexe, date_ordonnance=:date_ordonnance, notes=:notes
             WHERE id=:id'
        )->execute([
            ':patient_nom'     => $patientNom,
            ':patient_age'     => $patientAge !== '' ? (int) $patientAge : null,
            ':patient_sexe'    => in_array($patientSexe, ['M', 'F'], true) ? $patientSexe : null,
            ':date_ordonnance' => $dateOrdonnance,
            ':notes'           => $notes ?: null,
            ':id'              => $id,
        ]);

        $this->db->prepare('DELETE FROM ordonnance_lignes WHERE ordonnance_id = :id')
                 ->execute([':id' => $id]);

        $stmtLigne = $this->db->prepare(
            'INSERT INTO ordonnance_lignes (ordonnance_id, medicament_id, posologie, duree, quantite)
             VALUES (:ordonnance_id, :medicament_id, :posologie, :duree, :quantite)'
        );
        foreach ($lignes as $ligne) {
            $stmtLigne->execute([
                ':ordonnance_id' => $id,
                ':medicament_id' => (int) $ligne['medicament_id'],
                ':posologie'     => trim($ligne['posologie']),
                ':duree'         => trim($ligne['duree'] ?? ''),
                ':quantite'      => (int) ($ligne['quantite'] ?? 1),
            ]);
        }

        $this->redirect('index.php?action=show_ordonnance&id=' . $id . '&success=updated');
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=ordonnances');
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $this->db->prepare('DELETE FROM ordonnance_lignes WHERE ordonnance_id = :id')->execute([':id' => $id]);
            $this->db->prepare('DELETE FROM ordonnances WHERE id = :id')->execute([':id' => $id]);
        }

        $this->redirect('index.php?action=ordonnances&success=deleted');
    }

    private function findLignes(int $ordonnanceId): array
    {
        $stmt = $this->db->prepare(
            'SELECT ol.*, m.nom AS medicament_nom, m.dosage, m.forme
             FROM ordonnance_lignes ol
             JOIN medicaments m ON m.id = ol.medicament_id
             WHERE ol.ordonnance_id = :id'
        );
        $stmt->execute([':id' => $ordonnanceId]);
        return $stmt->fetchAll();
    }

    private function getAllMedicaments(): array
    {
        $stmt = $this->db->query('SELECT * FROM medicaments ORDER BY nom ASC');
        return array_map(function (array $row): array {
            $m = new Medicament();
            $m->setId((int) $row['id']);
            $m->setNom($row['nom']);
            $m->setPrix((float) $row['prix']);
            $m->setDosage($row['dosage'] ?: null);
            $m->setDescription($row['description'] ?: null);
            $m->setForme($row['forme'] ?: null);
            return [
                'id'          => $m->getId(),
                'nom'         => $m->getNom(),
                'prix'        => $m->getPrix(),
                'dosage'      => $m->getDosage() ?? '',
                'description' => $m->getDescription() ?? '',
                'forme'       => $m->getForme() ?? '',
            ];
        }, $stmt->fetchAll());
    }

    private function rowToArray(array $row): array
    {
        $o = new Ordonnance();
        $o->setId((int) $row['id']);
        $o->setNumero($row['numero']);
        $o->setPatientNom($row['patient_nom']);
        $o->setPatientAge(isset($row['patient_age']) ? (int) $row['patient_age'] : null);
        $o->setPatientSexe($row['patient_sexe'] ?: null);
        $o->setDateOrdonnance($row['date_ordonnance']);
        $o->setNotes($row['notes'] ?: null);
        $o->setCreatedAt($row['created_at']);

        return [
            'id'               => $o->getId(),
            'numero'           => $o->getNumero(),
            'patient_nom'      => $o->getPatientNom(),
            'patient_age'      => $o->getPatientAge(),
            'patient_sexe'     => $o->getPatientSexe(),
            'date_ordonnance'  => $o->getDateOrdonnance(),
            'notes'            => $o->getNotes(),
            'created_at'       => $o->getCreatedAt(),
            'updated_at'       => $row['updated_at'] ?? null,
        ];
    }
}
