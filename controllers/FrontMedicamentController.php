<?php

declare(strict_types=1);

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Medicament.php';

class FrontMedicamentController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function index(): void
    {
        $search  = trim((string) ($_GET['search'] ?? ''));
        $sort    = trim((string) ($_GET['sort']   ?? 'nom_asc'));
        $page    = max(1, (int) ($_GET['page']    ?? 1));
        $perPage = 6;

        $sortMap = [
            'nom_asc'   => 'nom ASC',
            'nom_desc'  => 'nom DESC',
            'prix_asc'  => 'prix ASC',
            'prix_desc' => 'prix DESC',
        ];
        $orderBy = $sortMap[$sort] ?? 'nom ASC';

        $where  = $search !== '' ? 'WHERE nom LIKE :search' : '';
        $params = $search !== '' ? [':search' => '%' . $search . '%'] : [];

        $total = (int) $this->db->prepare("SELECT COUNT(*) FROM medicaments $where")->execute($params)
            ? $this->db->query("SELECT COUNT(*) FROM medicaments $where" . ($search !== '' ? '' : ''))->fetchColumn()
            : 0;

        $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM medicaments $where");
        $stmtCount->execute($params);
        $total = (int) $stmtCount->fetchColumn();

        $totalPages = max(1, (int) ceil($total / $perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT * FROM medicaments $where ORDER BY $orderBy LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();

        $medicaments = array_map([$this, 'rowToArray'], $stmt->fetchAll());

        $this->render('medicament/index', [
            'medicaments' => $medicaments,
            'search'      => $search,
            'sort'        => $sort,
            'total'       => $total,
            'totalPages'  => $totalPages,
            'page'        => $page,
        ]);
    }

    public function show(): void
    {
        $id   = (int) ($_GET['id'] ?? 0);
        $stmt = $this->db->prepare('SELECT * FROM medicaments WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            http_response_code(404);
            $this->render('medicament/not-found', [
                'errorMessage' => 'Le médicament demandé est introuvable.',
            ]);
            return;
        }

        $this->render('medicament/show', [
            'medicament' => $this->rowToArray($row),
        ]);
    }

    private function rowToArray(array $row): array
    {
        $m = new Medicament();
        $m->setId((int) $row['id']);
        $m->setNom($row['nom']);
        $m->setDescription($row['description'] ?: null);
        $m->setDosage($row['dosage'] ?: null);
        $m->setForme($row['forme'] ?: null);
        $m->setFabricant($row['fabricant'] ?: null);
        $m->setPrix((float) $row['prix']);
        $m->setStock((int) $row['stock']);
        $m->setDateExpiration($row['date_expiration'] ?: null);
        $m->setCreatedAt($row['created_at']);

        return [
            'id'              => $m->getId(),
            'nom'             => $m->getNom(),
            'description'     => $m->getDescription(),
            'dosage'          => $m->getDosage(),
            'forme'           => $m->getForme(),
            'fabricant'       => $m->getFabricant(),
            'prix'            => $m->getPrix(),
            'stock'           => $m->getStock(),
            'date_expiration' => $m->getDateExpiration(),
            'created_at'      => $m->getCreatedAt(),
        ];
    }
}
