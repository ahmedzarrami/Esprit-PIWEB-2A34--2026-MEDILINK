<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Medicament.php';

class MedicamentController
{
    private Medicament $medicamentModel;
    private int $perPage = 5;

    public function __construct()
    {
        $database = new Database();
        $this->medicamentModel = new Medicament($database->getConnection());
    }

    public function index(): void
    {
        $keyword = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
        $sortBy = $this->sanitizeSortBy((string) ($_GET['sort_by'] ?? 'id'));
        $sortDirection = $this->sanitizeSortDirection((string) ($_GET['sort_dir'] ?? 'desc'));
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $totalItems = $this->medicamentModel->countFiltered($keyword);
        $totalPages = max(1, (int) ceil($totalItems / $this->perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $this->perPage;

        $medicaments = $this->medicamentModel->getPaginated($keyword, $sortBy, $sortDirection, $this->perPage, $offset);

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
                'total' => $this->medicamentModel->countAll(),
                'lowStock' => $this->medicamentModel->countLowStock(),
                'outOfStock' => $this->medicamentModel->countOutOfStock(),
            ],
            'pageTitle' => 'Gestion des médicaments',
        ]);
    }

    public function show(): void
    {
        $id = $this->getIdFromQuery();
        $medicament = $this->medicamentModel->getById($id);
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
                    if ($this->medicamentModel->create($data)) {
                        $this->setFlash('success', 'Le médicament a été ajouté avec succès.');
                        $this->redirect('index.php?action=index');
                    }
                    $formMessage = 'Une erreur est survenue lors de l’ajout du médicament.';
                } catch (Throwable) {
                    $formMessage = 'Impossible d’ajouter le médicament pour le moment.';
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
        $medicament = $this->medicamentModel->getById($id);
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
                    if ($this->medicamentModel->update($id, $data)) {
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
            if ($this->medicamentModel->delete($id)) {
                $this->setFlash('success', 'Le médicament a été supprimé avec succès.');
            } else {
                $this->setFlash('error', 'Le médicament n’existe plus ou n’a pas pu être supprimé.');
            }
        } catch (Throwable) {
            $this->setFlash('error', 'Impossible de supprimer le médicament pour le moment.');
        }

        $this->redirect('index.php?action=index');
    }

    private function sanitize(array $postData): array
    {
        return [
            'nom' => trim((string) ($postData['nom'] ?? '')),
            'description' => trim((string) ($postData['description'] ?? '')),
            'dosage' => trim((string) ($postData['dosage'] ?? '')),
            'forme' => trim((string) ($postData['forme'] ?? '')),
            'fabricant' => trim((string) ($postData['fabricant'] ?? '')),
            'prix' => trim((string) ($postData['prix'] ?? '')),
            'stock' => trim((string) ($postData['stock'] ?? '')),
        ];
    }

    /** @return array<string,string> */
    private function validate(array $data): array
    {
        $errors = [];

        if ($data['nom'] === '') {
            $errors['nom'] = 'Le nom du médicament est obligatoire.';
        } elseif (mb_strlen($data['nom']) < 2) {
            $errors['nom'] = 'Le nom doit contenir au moins 2 caractères.';
        }

        if ($data['description'] === '') {
            $errors['description'] = 'La description du médicament est obligatoire.';
        } elseif (mb_strlen($data['description']) < 10) {
            $errors['description'] = 'La description doit contenir au moins 10 caractères.';
        }

        if ($data['dosage'] === '') {
            $errors['dosage'] = 'Le dosage est obligatoire.';
        }

        if ($data['forme'] === '') {
            $errors['forme'] = 'La forme est obligatoire.';
        }

        if ($data['fabricant'] === '') {
            $errors['fabricant'] = 'Le fabricant est obligatoire.';
        }

        if ($data['prix'] === '') {
            $errors['prix'] = 'Le prix est obligatoire.';
        } elseif (!is_numeric(str_replace(',', '.', $data['prix'])) || (float) str_replace(',', '.', $data['prix']) <= 0) {
            $errors['prix'] = 'Le prix doit être un nombre positif.';
        }

        if ($data['stock'] === '') {
            $errors['stock'] = 'Le stock est obligatoire.';
        } elseif (filter_var($data['stock'], FILTER_VALIDATE_INT) === false || (int) $data['stock'] < 0) {
            $errors['stock'] = 'Le stock doit être un entier positif ou nul.';
        }

        return $errors;
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
            'stock' => 'Stock',
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
            'stock' => '',
        ];
    }
}
