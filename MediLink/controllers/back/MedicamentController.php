<?php

declare(strict_types=1);

require_once __DIR__ . '/BackController.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Medicament.php';

class MedicamentController extends BackController
{
    private PDO       $db;
    private Medicament $medicament;
    private int       $perPage = 5;

    private array $allowedSorts = [
        'id'        => 'id',
        'nom'       => 'nom',
        'forme'     => 'forme',
        'fabricant' => 'fabricant',
        'prix'      => 'prix',
    ];

    public function __construct()
    {
        $this->db         = Database::getConnection();
        $this->medicament = new Medicament();
    }

    /* â”€â”€ Actions â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */

    public function index(): void
    {
        $keyword       = trim((string) ($_GET['search']   ?? ''));
        $sortBy        = $this->sanitizeSortBy((string)   ($_GET['sort_by']  ?? 'id'));
        $sortDirection = $this->sanitizeSortDirection((string) ($_GET['sort_dir'] ?? 'desc'));
        $page          = max(1, (int) ($_GET['page'] ?? 1));

        $totalItems = $this->countFiltered($keyword);
        $totalPages = max(1, (int) ceil($totalItems / $this->perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $this->perPage;

        $medicaments = $this->getPaginated($keyword, $sortBy, $sortDirection, $this->perPage, $offset);

        $this->render('medicament/index', [
            'medicaments'   => $medicaments,
            'search'        => $keyword,
            'sortBy'        => $sortBy,
            'sortDirection' => $sortDirection,
            'page'          => $page,
            'perPage'       => $this->perPage,
            'totalItems'    => $totalItems,
            'totalPages'    => $totalPages,
            'sortOptions'   => $this->getSortOptions(),
            'stats'         => [
                'total'      => $this->countAll(),
                'formes'     => $this->countDistinctFormes(),
                'fabricants' => $this->countDistinctFabricants(),
            ],
            'pageTitle'     => 'Gestion des mÃ©dicaments',
        ]);
    }

    public function show(): void
    {
        $id         = $this->getIdFromQuery();
        $medicament = $this->getById($id);
        if ($medicament === null) {
            $this->abort('MÃ©dicament introuvable.');
        }

        $this->render('medicament/show', [
            'medicament' => $medicament,
            'pageTitle'  => 'DÃ©tail du mÃ©dicament',
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

            if ($errors !== []) {
                $formMessage = 'Veuillez corriger les erreurs du formulaire.';
            } else {
                try {
                    $this->hydrate($this->medicament, $data);
                    if ($this->insert($this->buildPayload($this->medicament))) {
                        $this->setFlash('success', 'Le mÃ©dicament a Ã©tÃ© ajoutÃ© avec succÃ¨s.');
                        $this->redirect('index.php?action=index');
                    }
                    $formMessage = 'Une erreur est survenue lors de l\'ajout.';
                } catch (Throwable) {
                    $formMessage = 'Impossible d\'ajouter le mÃ©dicament pour le moment.';
                }
            }
        }

        $this->render('medicament/create', [
            'data'        => $data,
            'errors'      => $errors,
            'formMessage' => $formMessage,
            'pageTitle'   => 'Ajouter un mÃ©dicament',
        ]);
    }

    public function edit(): void
    {
        $id         = $this->getIdFromQuery();
        $medicament = $this->getById($id);
        if ($medicament === null) {
            $this->abort('MÃ©dicament introuvable.');
        }

        $errors      = [];
        $data        = $medicament;
        $formMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $this->sanitize($_POST);
            $errors = $this->validate($data);

            if ($errors !== []) {
                $formMessage = 'Veuillez corriger les erreurs du formulaire.';
            } else {
                try {
                    $this->hydrate($this->medicament, $data);
                    if ($this->update($id, $this->buildPayload($this->medicament))) {
                        $this->setFlash('success', 'Le mÃ©dicament a Ã©tÃ© modifiÃ© avec succÃ¨s.');
                        $this->redirect('index.php?action=index');
                    }
                    $formMessage = 'Une erreur est survenue lors de la modification.';
                } catch (Throwable) {
                    $formMessage = 'Impossible de modifier le mÃ©dicament pour le moment.';
                }
            }
        }

        $this->render('medicament/edit', [
            'medicament'  => $medicament,
            'data'        => $data,
            'errors'      => $errors,
            'formMessage' => $formMessage,
            'pageTitle'   => 'Modifier un mÃ©dicament',
        ]);
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=index');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->setFlash('error', 'Identifiant invalide.');
            $this->redirect('index.php?action=index');
        }

        try {
            if ($this->deleteById($id)) {
                $this->setFlash('success', 'Le mÃ©dicament a Ã©tÃ© supprimÃ© avec succÃ¨s.');
            } else {
                $this->setFlash('error', 'Le mÃ©dicament n\'existe plus ou n\'a pas pu Ãªtre supprimÃ©.');
            }
        } catch (Throwable) {
            $this->setFlash('error', 'Impossible de supprimer le mÃ©dicament pour le moment.');
        }

        $this->redirect('index.php?action=index');
    }

    /* â”€â”€ RequÃªtes DB â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */

    private function getPaginated(string $keyword, string $sortBy, string $sortDirection, int $limit, int $offset): array
    {
        $sql = 'SELECT id, nom, description, dosage, forme, fabricant, prix, created_at FROM medicaments';

        if ($keyword !== '') {
            $sql .= ' WHERE nom LIKE :k1 OR description LIKE :k2 OR dosage LIKE :k3 OR forme LIKE :k4 OR fabricant LIKE :k5';
        }

        $col       = $this->allowedSorts[$sortBy] ?? 'id';
        $direction = strtolower($sortDirection) === 'asc' ? 'ASC' : 'DESC';
        $sql      .= " ORDER BY {$col} {$direction} LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        if ($keyword !== '') {
            $val = '%' . $keyword . '%';
            $stmt->bindValue(':k1', $val, PDO::PARAM_STR);
            $stmt->bindValue(':k2', $val, PDO::PARAM_STR);
            $stmt->bindValue(':k3', $val, PDO::PARAM_STR);
            $stmt->bindValue(':k4', $val, PDO::PARAM_STR);
            $stmt->bindValue(':k5', $val, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function countFiltered(string $keyword): int
    {
        $sql    = 'SELECT COUNT(*) FROM medicaments';
        $params = [];

        if ($keyword !== '') {
            $sql .= ' WHERE nom LIKE :k1 OR description LIKE :k2 OR dosage LIKE :k3 OR forme LIKE :k4 OR fabricant LIKE :k5';
            $val = '%' . $keyword . '%';
            $params = [':k1' => $val, ':k2' => $val, ':k3' => $val, ':k4' => $val, ':k5' => $val];
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
        $stmt = $this->db->prepare(
            'INSERT INTO medicaments (nom, description, dosage, forme, fabricant, prix)
             VALUES (:nom, :description, :dosage, :forme, :fabricant, :prix)'
        );
        return $stmt->execute($data);
    }

    private function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE medicaments
             SET nom=:nom, description=:description, dosage=:dosage,
                 forme=:forme, fabricant=:fabricant, prix=:prix
             WHERE id=:id'
        );
        return $stmt->execute(array_merge($data, ['id' => $id]));
    }

    private function deleteById(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM medicaments WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /* â”€â”€ Helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */

    private function hydrate(Medicament $medicament, array $data): void
    {
        $medicament->setNom($data['nom']);
        $medicament->setDescription($data['description']);
        $medicament->setDosage($data['dosage']);
        $medicament->setForme($data['forme']);
        $medicament->setFabricant($data['fabricant']);
        $medicament->setPrix((float) $data['prix']);
    }

    private function buildPayload(Medicament $medicament): array
    {
        return [
            'nom'         => $medicament->getNom(),
            'description' => $medicament->getDescription() ?? '',
            'dosage'      => $medicament->getDosage() ?? '',
            'forme'       => $medicament->getForme() ?? '',
            'fabricant'   => $medicament->getFabricant() ?? '',
            'prix'        => number_format($medicament->getPrix(), 2, '.', ''),
        ];
    }

    private function sanitize(array $post): array
    {
        $normalize = fn(string $v): string => preg_replace('/\s+/', ' ', trim($v)) ?? '';
        return [
            'nom'         => $normalize((string) ($post['nom']         ?? '')),
            'description' => $normalize((string) ($post['description'] ?? '')),
            'dosage'      => $normalize((string) ($post['dosage']      ?? '')),
            'forme'       => $normalize((string) ($post['forme']       ?? '')),
            'fabricant'   => $normalize((string) ($post['fabricant']   ?? '')),
            'prix'        => str_replace(',', '.', trim((string) ($post['prix'] ?? ''))),
        ];
    }

    /** @return array<string,string> */
    private function validate(array $data): array
    {
        $errors = [];

        if ($data['nom'] === '') {
            $errors['nom'] = 'Le nom du mÃ©dicament est obligatoire.';
        } elseif (mb_strlen($data['nom']) < 3) {
            $errors['nom'] = 'Le nom doit contenir au moins 3 caractÃ¨res.';
        } elseif (mb_strlen($data['nom']) > 100) {
            $errors['nom'] = 'Le nom ne doit pas dÃ©passer 100 caractÃ¨res.';
        } elseif (!preg_match("/^[\p{L}0-9 .,'()\-\/]+$/u", $data['nom'])) {
            $errors['nom'] = 'Le nom contient des caractÃ¨res non autorisÃ©s.';
        }

        if ($data['description'] === '') {
            $errors['description'] = 'La description est obligatoire.';
        } elseif (mb_strlen($data['description']) < 15) {
            $errors['description'] = 'La description doit contenir au moins 15 caractÃ¨res.';
        } elseif (mb_strlen($data['description']) > 500) {
            $errors['description'] = 'La description ne doit pas dÃ©passer 500 caractÃ¨res.';
        }

        if ($data['dosage'] === '') {
            $errors['dosage'] = 'Le dosage est obligatoire.';
        } elseif (mb_strlen($data['dosage']) < 2) {
            $errors['dosage'] = 'Le dosage doit contenir au moins 2 caractÃ¨res.';
        } elseif (mb_strlen($data['dosage']) > 50) {
            $errors['dosage'] = 'Le dosage ne doit pas dÃ©passer 50 caractÃ¨res.';
        } elseif (!preg_match('/\d/', $data['dosage'])) {
            $errors['dosage'] = 'Le dosage doit contenir au moins un chiffre.';
        }

        if ($data['forme'] === '') {
            $errors['forme'] = 'La forme est obligatoire.';
        } elseif (mb_strlen($data['forme']) < 2) {
            $errors['forme'] = 'La forme doit contenir au moins 2 caractÃ¨res.';
        } elseif (mb_strlen($data['forme']) > 50) {
            $errors['forme'] = 'La forme ne doit pas dÃ©passer 50 caractÃ¨res.';
        }

        if ($data['fabricant'] === '') {
            $errors['fabricant'] = 'Le fabricant est obligatoire.';
        } elseif (mb_strlen($data['fabricant']) < 2) {
            $errors['fabricant'] = 'Le fabricant doit contenir au moins 2 caractÃ¨res.';
        } elseif (mb_strlen($data['fabricant']) > 100) {
            $errors['fabricant'] = 'Le fabricant ne doit pas dÃ©passer 100 caractÃ¨res.';
        }

        if ($data['prix'] === '') {
            $errors['prix'] = 'Le prix est obligatoire.';
        } elseif (!preg_match('/^\d+(\.\d{1,2})?$/', $data['prix'])) {
            $errors['prix'] = 'Le prix doit contenir au maximum 2 dÃ©cimales.';
        } elseif ((float) $data['prix'] <= 0) {
            $errors['prix'] = 'Le prix doit Ãªtre un nombre positif.';
        }

        return $errors;
    }

    private function emptyForm(): array
    {
        return ['nom' => '', 'description' => '', 'dosage' => '', 'forme' => '', 'fabricant' => '', 'prix' => ''];
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
        return ['id' => 'ID', 'nom' => 'Nom', 'forme' => 'Forme', 'fabricant' => 'Fabricant', 'prix' => 'Prix'];
    }

    private function sanitizeSortBy(string $v): string
    {
        return array_key_exists($v, $this->allowedSorts) ? $v : 'id';
    }

    private function sanitizeSortDirection(string $v): string
    {
        return strtolower($v) === 'asc' ? 'asc' : 'desc';
    }
}

