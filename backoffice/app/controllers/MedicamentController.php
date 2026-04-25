<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Medicament.php';

class MedicamentController
{
    private PDO $db;
    private Medicament $medicament;
    private int $perPage = 5;

    private array $allowedSorts = [
        'id' => 'id',
        'nom' => 'nom',
        'forme' => 'forme',
        'fabricant' => 'fabricant',
        'prix' => 'prix',
    ];

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->medicament = new Medicament();
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    public function index(): void
    {
        $keyword = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
        $sortBy = $this->sanitizeSortBy((string) ($_GET['sort_by'] ?? 'id'));
        $sortDirection = $this->sanitizeSortDirection((string) ($_GET['sort_dir'] ?? 'desc'));
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $totalItems = $this->countFiltered($keyword);
        $totalPages = max(1, (int) ceil($totalItems / $this->perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $this->perPage;

        $medicaments = $this->getPaginated($keyword, $sortBy, $sortDirection, $this->perPage, $offset);

        $this->render('medicament/index', [
            'medicaments' => $medicaments,
            'search' => $keyword,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
            'page' => $page,
            'perPage' => $this->perPage,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'sortOptions' => $this->getSortOptions(),
            'stats' => [
                'total' => $this->countAll(),
                'formes' => $this->countDistinctFormes(),
                'fabricants' => $this->countDistinctFabricants(),
            ],
            'pageTitle' => 'Gestion des médicaments',
        ]);
    }

    public function show(): void
    {
        $id = $this->getIdFromQuery();
        $medicament = $this->getById($id);
        if ($medicament === null) {
            $this->abort('Médicament introuvable.');
        }

        $this->render('medicament/show', [
            'medicament' => $medicament,
            'pageTitle' => 'Détail du médicament',
        ]);
    }

    public function create(): void
    {
        $errors = [];
        $data = $this->getEmptyForm();
        $formMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitize($_POST);
            $errors = $this->validate($data);

            if ($errors !== []) {
                $formMessage = 'Veuillez corriger les erreurs du formulaire.';
            } else {
                try {
                    $this->hydrateMedicamentFromForm($this->medicament, $data);
                    $payload = $this->buildPayloadFromMedicament($this->medicament);

                    if ($this->insert($payload)) {
                        $this->setFlash('success', 'Le médicament a été ajouté avec succès.');
                        $this->redirect('index.php?action=index');
                    }
                    $formMessage = 'Une erreur est survenue lors de l\'ajout du médicament.';
                } catch (Throwable) {
                    $formMessage = 'Impossible d\'ajouter le médicament pour le moment.';
                }
            }
        }

        $this->render('medicament/create', [
            'data' => $data,
            'errors' => $errors,
            'formMessage' => $formMessage,
            'pageTitle' => 'Ajouter un médicament',
        ]);
    }

    public function edit(): void
    {
        $id = $this->getIdFromQuery();
        $medicament = $this->getById($id);
        if ($medicament === null) {
            $this->abort('Médicament introuvable.');
        }

        $errors = [];
        $data = $medicament;
        $formMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitize($_POST);
            $errors = $this->validate($data);

            if ($errors !== []) {
                $formMessage = 'Veuillez corriger les erreurs du formulaire.';
            } else {
                try {
                    $this->hydrateMedicamentFromForm($this->medicament, $data);
                    $payload = $this->buildPayloadFromMedicament($this->medicament);

                    if ($this->update($id, $payload)) {
                        $this->setFlash('success', 'Le médicament a été modifié avec succès.');
                        $this->redirect('index.php?action=index');
                    }
                    $formMessage = 'Une erreur est survenue lors de la modification du médicament.';
                } catch (Throwable) {
                    $formMessage = 'Impossible de modifier le médicament pour le moment.';
                }
            }
        }

        $this->render('medicament/edit', [
            'medicament' => $medicament,
            'data' => $data,
            'errors' => $errors,
            'formMessage' => $formMessage,
            'pageTitle' => 'Modifier un médicament',
        ]);
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=index');
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('error', 'Identifiant de médicament invalide.');
            $this->redirect('index.php?action=index');
        }

        try {
            if ($this->deleteById($id)) {
                $this->setFlash('success', 'Le médicament a été supprimé avec succès.');
            } else {
                $this->setFlash('error', 'Le médicament n\'existe plus ou n\'a pas pu être supprimé.');
            }
        } catch (Throwable) {
            $this->setFlash('error', 'Impossible de supprimer le médicament pour le moment.');
        }

        $this->redirect('index.php?action=index');
    }

    // -------------------------------------------------------------------------
    // Requêtes base de données
    // -------------------------------------------------------------------------

    private function getPaginated(string $keyword, string $sortBy, string $sortDirection, int $limit, int $offset): array
    {
        $sql = 'SELECT id, nom, description, dosage, forme, fabricant, prix, created_at FROM medicaments';

        if ($keyword !== '') {
            $sql .= ' WHERE nom LIKE :keyword OR description LIKE :keyword OR dosage LIKE :keyword OR forme LIKE :keyword OR fabricant LIKE :keyword';
        }

        $sortColumn = $this->allowedSorts[$sortBy] ?? 'id';
        $direction = strtolower($sortDirection) === 'asc' ? 'ASC' : 'DESC';

        $sql .= " ORDER BY {$sortColumn} {$direction} LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        if ($keyword !== '') {
            $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function countFiltered(string $keyword): int
    {
        $sql = 'SELECT COUNT(*) FROM medicaments';
        $params = [];

        if ($keyword !== '') {
            $sql .= ' WHERE nom LIKE :keyword OR description LIKE :keyword OR dosage LIKE :keyword OR forme LIKE :keyword OR fabricant LIKE :keyword';
            $params['keyword'] = '%' . $keyword . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    private function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM medicaments')->fetchColumn();
    }

    private function countDistinctFormes(): int
    {
        return (int) $this->db->query('SELECT COUNT(DISTINCT forme) FROM medicaments')->fetchColumn();
    }

    private function countDistinctFabricants(): int
    {
        return (int) $this->db->query('SELECT COUNT(DISTINCT fabricant) FROM medicaments')->fetchColumn();
    }

    private function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM medicaments WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    private function insert(array $data): bool
    {
        $sql = 'INSERT INTO medicaments (nom, description, dosage, forme, fabricant, prix)
                VALUES (:nom, :description, :dosage, :forme, :fabricant, :prix)';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'nom' => $data['nom'],
            'description' => $data['description'],
            'dosage' => $data['dosage'],
            'forme' => $data['forme'],
            'fabricant' => $data['fabricant'],
            'prix' => $data['prix'],
        ]);
    }

    private function update(int $id, array $data): bool
    {
        $sql = 'UPDATE medicaments
                SET nom = :nom,
                    description = :description,
                    dosage = :dosage,
                    forme = :forme,
                    fabricant = :fabricant,
                    prix = :prix
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'nom' => $data['nom'],
            'description' => $data['description'],
            'dosage' => $data['dosage'],
            'forme' => $data['forme'],
            'fabricant' => $data['fabricant'],
            'prix' => $data['prix'],
        ]);
    }

    private function deleteById(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM medicaments WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function hydrateMedicamentFromForm(Medicament $medicament, array $data): void
    {
        $medicament->setNom($data['nom']);
        $medicament->setDescription($data['description']);
        $medicament->setDosage($data['dosage']);
        $medicament->setForme($data['forme']);
        $medicament->setFabricant($data['fabricant']);
        $medicament->setPrix((float) $data['prix']);
    }

    private function buildPayloadFromMedicament(Medicament $medicament): array
    {
        return [
            'nom' => $medicament->getNom(),
            'description' => $medicament->getDescription(),
            'dosage' => $medicament->getDosage(),
            'forme' => $medicament->getForme(),
            'fabricant' => $medicament->getFabricant(),
            'prix' => number_format($medicament->getPrix(), 2, '.', ''),
        ];
    }

    private function sanitize(array $postData): array
    {
        $clean = [];
        $clean['nom'] = $this->normalizeText((string) ($postData['nom'] ?? ''));
        $clean['description'] = $this->normalizeText((string) ($postData['description'] ?? ''));
        $clean['dosage'] = $this->normalizeText((string) ($postData['dosage'] ?? ''));
        $clean['forme'] = $this->normalizeText((string) ($postData['forme'] ?? ''));
        $clean['fabricant'] = $this->normalizeText((string) ($postData['fabricant'] ?? ''));
        $clean['prix'] = str_replace(',', '.', trim((string) ($postData['prix'] ?? '')));

        return $clean;
    }

    /** @return array<string,string> */
    private function validate(array $data): array
    {
        $errors = [];

        if ($data['nom'] === '') {
            $errors['nom'] = 'Le nom du médicament est obligatoire.';
        } elseif (mb_strlen($data['nom']) < 3) {
            $errors['nom'] = 'Le nom doit contenir au moins 3 caractères.';
        } elseif (mb_strlen($data['nom']) > 100) {
            $errors['nom'] = 'Le nom ne doit pas dépasser 100 caractères.';
        } elseif (!preg_match("/^[\p{L}0-9 .,'()\-\/]+$/u", $data['nom'])) {
            $errors['nom'] = 'Le nom contient des caractères non autorisés.';
        }

        if ($data['description'] === '') {
            $errors['description'] = 'La description du médicament est obligatoire.';
        } elseif (mb_strlen($data['description']) < 15) {
            $errors['description'] = 'La description doit contenir au moins 15 caractères.';
        } elseif (mb_strlen($data['description']) > 500) {
            $errors['description'] = 'La description ne doit pas dépasser 500 caractères.';
        }

        if ($data['dosage'] === '') {
            $errors['dosage'] = 'Le dosage est obligatoire.';
        } elseif (mb_strlen($data['dosage']) < 2) {
            $errors['dosage'] = 'Le dosage doit contenir au moins 2 caractères.';
        } elseif (mb_strlen($data['dosage']) > 50) {
            $errors['dosage'] = 'Le dosage ne doit pas dépasser 50 caractères.';
        } elseif (!preg_match('/\d/', $data['dosage'])) {
            $errors['dosage'] = 'Le dosage doit contenir au moins un chiffre.';
        }

        if ($data['forme'] === '') {
            $errors['forme'] = 'La forme est obligatoire.';
        } elseif (mb_strlen($data['forme']) < 2) {
            $errors['forme'] = 'La forme doit contenir au moins 2 caractères.';
        } elseif (mb_strlen($data['forme']) > 50) {
            $errors['forme'] = 'La forme ne doit pas dépasser 50 caractères.';
        }

        if ($data['fabricant'] === '') {
            $errors['fabricant'] = 'Le fabricant est obligatoire.';
        } elseif (mb_strlen($data['fabricant']) < 2) {
            $errors['fabricant'] = 'Le fabricant doit contenir au moins 2 caractères.';
        } elseif (mb_strlen($data['fabricant']) > 100) {
            $errors['fabricant'] = 'Le fabricant ne doit pas dépasser 100 caractères.';
        }

        if ($data['prix'] === '') {
            $errors['prix'] = 'Le prix est obligatoire.';
        } elseif (!preg_match('/^\d+(\.\d{1,2})?$/', $data['prix'])) {
            $errors['prix'] = 'Le prix doit contenir au maximum 2 décimales.';
        } elseif ((float) $data['prix'] <= 0) {
            $errors['prix'] = 'Le prix doit être un nombre positif.';
        }

        return $errors;
    }

    private function normalizeText(string $value): string
    {
        return preg_replace('/\s+/', ' ', trim($value)) ?? '';
    }

    private function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/' . $view . '.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    private function getIdFromQuery(): int
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            $this->abort('Identifiant invalide.');
        }
        return $id;
    }

    private function abort(string $message): void
    {
        http_response_code(404);
        echo '<h2>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</h2>';
        exit;
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    private function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    /** @return array<string,string> */
    private function getSortOptions(): array
    {
        return [
            'id' => 'ID',
            'nom' => 'Nom',
            'forme' => 'Forme',
            'fabricant' => 'Fabricant',
            'prix' => 'Prix',
        ];
    }

    private function sanitizeSortBy(string $sortBy): string
    {
        $allowed = array_keys($this->getSortOptions());
        return in_array($sortBy, $allowed, true) ? $sortBy : 'id';
    }

    private function sanitizeSortDirection(string $sortDirection): string
    {
        return strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';
    }

    /** @return array<string,string> */
    private function getEmptyForm(): array
    {
        return [
            'nom' => '',
            'description' => '',
            'dosage' => '',
            'forme' => '',
            'fabricant' => '',
            'prix' => '',
        ];
    }
}
