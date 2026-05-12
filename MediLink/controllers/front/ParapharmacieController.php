<?php

declare(strict_types=1);

require_once __DIR__ . '/FrontController.php';
require_once __DIR__ . '/../../config/Database.php';

class FrontParapharmacieController extends FrontController
{
    private PDO $db;
    private int $perPage = 9;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function show(): void
    {
        $id   = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(404);
            $this->render('parapharmacie/not-found', ['errorMessage' => 'Produit introuvable.']);
            return;
        }

        $stmt = $this->db->prepare('SELECT * FROM produits WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $produit = $stmt->fetch();

        if ($produit === false) {
            http_response_code(404);
            $this->render('parapharmacie/not-found', ['errorMessage' => 'Le produit demandé est introuvable.']);
            return;
        }

        $this->render('parapharmacie/show', ['produit' => $produit]);
    }

    public function index(): void
    {
        $search    = trim((string) ($_GET['search']    ?? ''));
        $categorie = trim((string) ($_GET['categorie'] ?? ''));
        $sort      = trim((string) ($_GET['sort']      ?? 'nom_asc'));
        $page      = max(1, (int) ($_GET['page']       ?? 1));

        $sortMap = [
            'nom_asc'   => 'nom ASC',
            'nom_desc'  => 'nom DESC',
            'prix_asc'  => 'prix ASC',
            'prix_desc' => 'prix DESC',
        ];
        $orderBy = $sortMap[$sort] ?? 'nom ASC';

        [$where, $params] = $this->buildWhere($search, $categorie);

        $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM produits $where");
        $stmtCount->execute($params);
        $total = (int) $stmtCount->fetchColumn();

        $totalPages = max(1, (int) ceil($total / $this->perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $this->perPage;

        $stmt = $this->db->prepare(
            "SELECT * FROM produits $where ORDER BY $orderBy LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit',  $this->perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,        PDO::PARAM_INT);
        $stmt->execute();
        $produits = $stmt->fetchAll();

        $cats = $this->db->query(
            "SELECT DISTINCT categorie FROM produits ORDER BY categorie ASC"
        )->fetchAll(\PDO::FETCH_COLUMN);

        $this->render('parapharmacie/index', [
            'produits'    => $produits,
            'categories'  => $cats,
            'search'      => $search,
            'categorie'   => $categorie,
            'sort'        => $sort,
            'total'       => $total,
            'totalPages'  => $totalPages,
            'page'        => $page,
        ]);
    }

    private function buildWhere(string $search, string $categorie): array
    {
        $clauses = [];
        $params  = [];

        if ($search !== '') {
            $clauses[]    = '(nom LIKE :s1 OR description LIKE :s2 OR categorie LIKE :s3)';
            $val          = '%' . $search . '%';
            $params[':s1'] = $val;
            $params[':s2'] = $val;
            $params[':s3'] = $val;
        }

        if ($categorie !== '') {
            $clauses[]         = 'categorie = :cat';
            $params[':cat']    = $categorie;
        }

        $where = $clauses !== [] ? 'WHERE ' . implode(' AND ', $clauses) : '';
        return [$where, $params];
    }
}
