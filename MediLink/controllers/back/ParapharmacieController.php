<?php

declare(strict_types=1);

require_once __DIR__ . '/BackController.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Produit.php';

/**
 * ParapharmacieController — Back-office CRUD pour les produits parapharmacie
 * Suit le même pattern que MedicamentController
 */
class ParapharmacieController extends BackController
{
    private PDO    $db;
    private Produit $produit;
    private int    $perPage = 6;

    private const UPLOAD_DIR   = __DIR__ . '/../../public/img/parapharmacie/';
    private const UPLOAD_URL   = '/medilink_medicament/MediLink/public/img/parapharmacie/';
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_SIZE      = 2 * 1024 * 1024; // 2 MB

    private array $allowedSorts = [
        'id'        => 'id',
        'nom'       => 'nom',
        'reference' => 'reference',
        'categorie' => 'categorie',
        'prix'      => 'prix',
        'stock'     => 'stock',
    ];

    private const CATEGORIES = [
        'Soins visage',
        'Soins corps',
        'Hygiène',
        'Compléments alimentaires',
        'Bébé & Maman',
        'Capillaire',
        'Solaire',
        'Minceur',
        'Orthopédie',
        'Autre',
    ];

    public function __construct()
    {
        $this->db      = Database::getConnection();
        $this->produit = new Produit();
    }

    /* ── Actions ─────────────────────────────── */

    public function index(): void
    {
        $keyword       = trim((string) ($_GET['search']   ?? ''));
        $sortBy        = $this->sanitizeSortBy((string)   ($_GET['sort_by']  ?? 'id'));
        $sortDirection = $this->sanitizeSortDirection((string) ($_GET['sort_dir'] ?? 'desc'));
        $filterCat     = trim((string) ($_GET['categorie'] ?? ''));
        $page          = max(1, (int) ($_GET['page'] ?? 1));

        $totalItems = $this->countFiltered($keyword, $filterCat);
        $totalPages = max(1, (int) ceil($totalItems / $this->perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $this->perPage;

        $produits = $this->getPaginated($keyword, $filterCat, $sortBy, $sortDirection, $this->perPage, $offset);

        $this->render('parapharmacie/index', [
            'produits'      => $produits,
            'search'        => $keyword,
            'sortBy'        => $sortBy,
            'sortDirection' => $sortDirection,
            'filterCat'     => $filterCat,
            'page'          => $page,
            'perPage'       => $this->perPage,
            'totalItems'    => $totalItems,
            'totalPages'    => $totalPages,
            'sortOptions'   => $this->getSortOptions(),
            'categories'    => self::CATEGORIES,
            'stats'         => [
                'total'      => $this->countAll(),
                'categories' => $this->countDistinctCategories(),
                'disponible' => $this->countInStock(),
                'rupture'    => $this->countOutOfStock(),
            ],
            'pageTitle'     => 'Gestion Parapharmacie',
        ]);
    }

    public function show(): void
    {
        $id      = $this->getIdFromQuery();
        $produit = $this->getById($id);
        if ($produit === null) {
            $this->abort('Produit introuvable.');
        }

        $this->render('parapharmacie/show', [
            'produit'   => $produit,
            'pageTitle' => 'Détail du produit',
        ]);
    }

    public function create(): void
    {
        $errors      = [];
        $data        = $this->emptyForm();
        $formMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $this->sanitize($_POST);
            $errors = $this->validate($data);
            $imageError = $this->validateImage($_FILES['image'] ?? null);
            if ($imageError) $errors['image'] = $imageError;

            if ($errors !== []) {
                $formMessage = 'Veuillez corriger les erreurs du formulaire.';
            } else {
                try {
                    $imagePath = $this->uploadImage($_FILES['image'] ?? null);
                    $this->hydrate($this->produit, $data);
                    $this->produit->setImagePath($imagePath);
                    if ($this->insertProduit($this->buildPayload($this->produit))) {
                        $this->setFlash('success', 'Le produit a été ajouté avec succès.');
                        $this->redirect('parapharmacie.php?action=index');
                    }
                    $formMessage = 'Une erreur est survenue lors de l\'ajout.';
                } catch (\Throwable $e) {
                    if (str_contains($e->getMessage(), 'Duplicate entry')) {
                        $errors['reference'] = 'Cette référence existe déjà.';
                        $formMessage = 'Veuillez corriger les erreurs du formulaire.';
                    } else {
                        $formMessage = 'Impossible d\'ajouter le produit pour le moment.';
                    }
                }
            }
        }

        $this->render('parapharmacie/create', [
            'data'        => $data,
            'errors'      => $errors,
            'formMessage' => $formMessage,
            'categories'  => self::CATEGORIES,
            'pageTitle'   => 'Ajouter un produit',
        ]);
    }

    public function edit(): void
    {
        $id      = $this->getIdFromQuery();
        $produit = $this->getById($id);
        if ($produit === null) {
            $this->abort('Produit introuvable.');
        }

        $errors      = [];
        $data        = $produit;
        $formMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $this->sanitize($_POST);
            $errors = $this->validate($data);
            $imageError = $this->validateImage($_FILES['image'] ?? null, required: false);
            if ($imageError) $errors['image'] = $imageError;

            if ($errors !== []) {
                $formMessage = 'Veuillez corriger les erreurs du formulaire.';
            } else {
                try {
                    $newImagePath = $this->uploadImage($_FILES['image'] ?? null);
                    // Conserver l'ancienne image si aucune nouvelle n'est téléversée
                    $finalImagePath = $newImagePath ?? ($produit['image_path'] ?? null);
                    // Supprimer l'ancienne image si une nouvelle est fournie
                    if ($newImagePath && !empty($produit['image_path'])) {
                        $oldFile = self::UPLOAD_DIR . $produit['image_path'];
                        if (is_file($oldFile)) @unlink($oldFile);
                    }
                    $this->hydrate($this->produit, $data);
                    $this->produit->setImagePath($finalImagePath);
                    if ($this->updateProduit($id, $this->buildPayload($this->produit))) {
                        $this->setFlash('success', 'Le produit a été modifié avec succès.');
                        $this->redirect('parapharmacie.php?action=index');
                    }
                    $formMessage = 'Une erreur est survenue lors de la modification.';
                } catch (\Throwable $e) {
                    if (str_contains($e->getMessage(), 'Duplicate entry')) {
                        $errors['reference'] = 'Cette référence existe déjà.';
                        $formMessage = 'Veuillez corriger les erreurs du formulaire.';
                    } else {
                        $formMessage = 'Impossible de modifier le produit pour le moment.';
                    }
                }
            }
        }

        $this->render('parapharmacie/edit', [
            'produit'     => $produit,
            'data'        => $data,
            'errors'      => $errors,
            'formMessage' => $formMessage,
            'categories'  => self::CATEGORIES,
            'pageTitle'   => 'Modifier un produit',
        ]);
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('parapharmacie.php?action=index');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->setFlash('error', 'Identifiant invalide.');
            $this->redirect('parapharmacie.php?action=index');
        }

        try {
            if ($this->deleteById($id)) {
                $this->setFlash('success', 'Le produit a été supprimé avec succès.');
            } else {
                $this->setFlash('error', 'Le produit n\'existe plus ou n\'a pas pu être supprimé.');
            }
        } catch (\Throwable) {
            $this->setFlash('error', 'Impossible de supprimer le produit pour le moment.');
        }

        $this->redirect('parapharmacie.php?action=index');
    }

    /* ── Requêtes DB ──────────────────────────── */

    private function getPaginated(string $keyword, string $cat, string $sortBy, string $sortDirection, int $limit, int $offset): array
    {
        $sql    = 'SELECT id, reference, nom, description, prix, stock, categorie, image_path, created_at FROM produits';
        $where  = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(nom LIKE :k1 OR reference LIKE :k2 OR description LIKE :k3 OR categorie LIKE :k4)';
            $val = '%' . $keyword . '%';
            $params[':k1'] = $val;
            $params[':k2'] = $val;
            $params[':k3'] = $val;
            $params[':k4'] = $val;
        }

        if ($cat !== '') {
            $where[]         = 'categorie = :cat';
            $params[':cat']  = $cat;
        }

        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $col       = $this->allowedSorts[$sortBy] ?? 'id';
        $direction = strtolower($sortDirection) === 'asc' ? 'ASC' : 'DESC';
        $sql      .= " ORDER BY {$col} {$direction} LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function countFiltered(string $keyword, string $cat): int
    {
        $sql    = 'SELECT COUNT(*) FROM produits';
        $where  = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(nom LIKE :k1 OR reference LIKE :k2 OR description LIKE :k3 OR categorie LIKE :k4)';
            $val = '%' . $keyword . '%';
            $params[':k1'] = $val;
            $params[':k2'] = $val;
            $params[':k3'] = $val;
            $params[':k4'] = $val;
        }

        if ($cat !== '') {
            $where[]        = 'categorie = :cat';
            $params[':cat'] = $cat;
        }

        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM produits')->fetchColumn();
    }

    private function countDistinctCategories(): int
    {
        return (int) $this->db->query('SELECT COUNT(DISTINCT categorie) FROM produits')->fetchColumn();
    }

    private function countInStock(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM produits WHERE stock > 0')->fetchColumn();
    }

    private function countOutOfStock(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM produits WHERE stock = 0')->fetchColumn();
    }

    private function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM produits WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    private function insertProduit(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO produits (reference, nom, description, prix, stock, categorie, image_path)
             VALUES (:reference, :nom, :description, :prix, :stock, :categorie, :image_path)'
        );
        return $stmt->execute($data);
    }

    private function updateProduit(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE produits
             SET reference=:reference, nom=:nom, description=:description,
                 prix=:prix, stock=:stock, categorie=:categorie, image_path=:image_path
             WHERE id=:id'
        );
        return $stmt->execute(array_merge($data, ['id' => $id]));
    }

    private function deleteById(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM produits WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /* ── Helpers ──────────────────────────────── */

    private function hydrate(Produit $produit, array $data): void
    {
        $produit->setReference($data['reference']);
        $produit->setNom($data['nom']);
        $produit->setDescription($data['description']);
        $produit->setPrix((float) $data['prix']);
        $produit->setStock((int) $data['stock']);
        $produit->setCategorie($data['categorie']);
    }

    private function buildPayload(Produit $produit): array
    {
        return [
            'reference'   => $produit->getReference(),
            'nom'         => $produit->getNom(),
            'description' => $produit->getDescription() ?? '',
            'prix'        => number_format($produit->getPrix(), 3, '.', ''),
            'stock'       => $produit->getStock(),
            'categorie'   => $produit->getCategorie(),
            'image_path'  => $produit->getImagePath(),
        ];
    }

    private function sanitize(array $post): array
    {
        $normalize = fn(string $v): string => preg_replace('/\s+/', ' ', trim($v)) ?? '';
        return [
            'reference'   => $normalize((string) ($post['reference']   ?? '')),
            'nom'         => $normalize((string) ($post['nom']         ?? '')),
            'description' => $normalize((string) ($post['description'] ?? '')),
            'prix'        => str_replace(',', '.', trim((string) ($post['prix'] ?? ''))),
            'stock'       => trim((string) ($post['stock'] ?? '0')),
            'categorie'   => $normalize((string) ($post['categorie']   ?? 'Autre')),
            'image_path'  => null, // géré séparément via upload
        ];
    }

    /** @return array<string,string> */
    private function validate(array $data): array
    {
        $errors = [];

        if ($data['reference'] === '') {
            $errors['reference'] = 'La référence est obligatoire.';
        } elseif (mb_strlen($data['reference']) < 3) {
            $errors['reference'] = 'La référence doit contenir au moins 3 caractères.';
        } elseif (mb_strlen($data['reference']) > 50) {
            $errors['reference'] = 'La référence ne doit pas dépasser 50 caractères.';
        }

        if ($data['nom'] === '') {
            $errors['nom'] = 'Le nom du produit est obligatoire.';
        } elseif (mb_strlen($data['nom']) < 3) {
            $errors['nom'] = 'Le nom doit contenir au moins 3 caractères.';
        } elseif (mb_strlen($data['nom']) > 100) {
            $errors['nom'] = 'Le nom ne doit pas dépasser 100 caractères.';
        }

        if ($data['description'] !== '' && mb_strlen($data['description']) > 500) {
            $errors['description'] = 'La description ne doit pas dépasser 500 caractères.';
        }

        if ($data['prix'] === '') {
            $errors['prix'] = 'Le prix est obligatoire.';
        } elseif (!preg_match('/^\d+(\.\d{1,3})?$/', $data['prix'])) {
            $errors['prix'] = 'Le prix doit être un nombre avec max 3 décimales.';
        } elseif ((float) $data['prix'] < 0) {
            $errors['prix'] = 'Le prix ne peut pas être négatif.';
        }

        if (!preg_match('/^\d+$/', $data['stock'])) {
            $errors['stock'] = 'Le stock doit être un nombre entier positif.';
        } elseif ((int) $data['stock'] < 0) {
            $errors['stock'] = 'Le stock ne peut pas être négatif.';
        }

        if ($data['categorie'] === '' || !in_array($data['categorie'], self::CATEGORIES, true)) {
            $errors['categorie'] = 'Veuillez choisir une catégorie valide.';
        }

        return $errors;
    }

    private function emptyForm(): array
    {
        return ['reference' => '', 'nom' => '', 'description' => '', 'prix' => '', 'stock' => '0', 'categorie' => '', 'image_path' => null];
    }

    private function validateImage(?array $file, bool $required = false): ?string
    {
        if (empty($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $required ? 'Une image est requise.' : null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Erreur lors du téléversement de l\'image.';
        }
        if ($file['size'] > self::MAX_SIZE) {
            return 'L\'image ne doit pas dépasser 2 Mo.';
        }
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_TYPES, true)) {
            return 'Format accepté : JPG, PNG ou WebP.';
        }
        return null;
    }

    private function uploadImage(?array $file): ?string
    {
        if (empty($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }
        $ext      = match(mime_content_type($file['tmp_name'])) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg',
        };
        $filename = uniqid('prod_', true) . '.' . $ext;
        $dest     = self::UPLOAD_DIR . $filename;
        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new \RuntimeException('Impossible de déplacer l\'image téléversée.');
        }
        return $filename;
    }

    private function getIdFromQuery(): int
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->abort('Identifiant invalide.');
        }
        return $id;
    }

    private function abort(string $message): never
    {
        http_response_code(404);
        echo '<h2>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</h2>';
        exit;
    }

    private function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /** @return array<string,string> */
    private function getSortOptions(): array
    {
        return ['id' => 'ID', 'nom' => 'Nom', 'reference' => 'Référence', 'categorie' => 'Catégorie', 'prix' => 'Prix', 'stock' => 'Stock'];
    }

    private function sanitizeSortBy(string $v): string
    {
        return array_key_exists($v, $this->allowedSorts) ? $v : 'id';
    }

    private function sanitizeSortDirection(string $v): string
    {
        return strtolower($v) === 'asc' ? 'asc' : 'desc';
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
