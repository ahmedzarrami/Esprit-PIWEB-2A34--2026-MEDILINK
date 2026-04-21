<?php

declare(strict_types=1);

class Medicament
{
    /** @var array<string,string> */
    private array $allowedSortColumns = [
        'id' => 'id',
        'nom' => 'nom',
        'forme' => 'forme',
        'fabricant' => 'fabricant',
        'prix' => 'prix',
        'stock' => 'stock',
    ];

    public function __construct(private PDO $db)
    {
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function getPaginated(string $keyword, string $sortBy, string $sortDirection, int $limit, int $offset): array
    {
        $sortColumn = $this->allowedSortColumns[$sortBy] ?? 'id';
        $direction = strtolower($sortDirection) === 'asc' ? 'ASC' : 'DESC';

        $sql = 'SELECT * FROM medicaments';
        $conditions = [];
        $params = [];

        if ($keyword !== '') {
            $conditions[] = '(nom LIKE :keyword OR description LIKE :keyword OR forme LIKE :keyword OR fabricant LIKE :keyword OR dosage LIKE :keyword)';
            $params['keyword'] = '%' . $keyword . '%';
        }

        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= sprintf(' ORDER BY %s %s LIMIT :limit OFFSET :offset', $sortColumn, $direction);
        $statement = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function countFiltered(string $keyword): int
    {
        $sql = 'SELECT COUNT(*) FROM medicaments';
        $params = [];

        if ($keyword !== '') {
            $sql .= ' WHERE nom LIKE :keyword OR description LIKE :keyword OR forme LIKE :keyword OR fabricant LIKE :keyword OR dosage LIKE :keyword';
            $params['keyword'] = '%' . $keyword . '%';
        }

        $statement = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM medicaments')->fetchColumn();
    }

    public function countLowStock(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM medicaments WHERE stock BETWEEN 1 AND 10')->fetchColumn();
    }

    public function countOutOfStock(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM medicaments WHERE stock = 0')->fetchColumn();
    }

    public function getById(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM medicaments WHERE id = :id');
        $statement->execute(['id' => $id]);
        $medicament = $statement->fetch();
        return $medicament ?: null;
    }

    public function create(array $data): bool
    {
        $sql = 'INSERT INTO medicaments (nom, description, dosage, forme, fabricant, prix, stock)
                VALUES (:nom, :description, :dosage, :forme, :fabricant, :prix, :stock)';
        $statement = $this->db->prepare($sql);
        return $statement->execute([
            'nom' => $data['nom'],
            'description' => $data['description'],
            'dosage' => $data['dosage'],
            'forme' => $data['forme'],
            'fabricant' => $data['fabricant'],
            'prix' => $data['prix'],
            'stock' => $data['stock'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE medicaments SET
                    nom = :nom,
                    description = :description,
                    dosage = :dosage,
                    forme = :forme,
                    fabricant = :fabricant,
                    prix = :prix,
                    stock = :stock
                WHERE id = :id';
        $statement = $this->db->prepare($sql);
        return $statement->execute([
            'id' => $id,
            'nom' => $data['nom'],
            'description' => $data['description'],
            'dosage' => $data['dosage'],
            'forme' => $data['forme'],
            'fabricant' => $data['fabricant'],
            'prix' => $data['prix'],
            'stock' => $data['stock'],
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM medicaments WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->rowCount() > 0;
    }
}
