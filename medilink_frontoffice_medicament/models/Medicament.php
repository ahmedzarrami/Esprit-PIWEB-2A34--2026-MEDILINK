<?php

declare(strict_types=1);

class Medicament
{
    public function __construct(private PDO $db)
    {
    }

    public function getFeatured(int $limit = 3): array
    {
        $sql = 'SELECT * FROM medicaments ORDER BY created_at DESC, id DESC LIMIT :limit';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAll(?string $keyword = null): int
    {
        if ($keyword !== null && $keyword !== '') {
            $stmt = $this->db->prepare(
                'SELECT COUNT(*) FROM medicaments
                 WHERE nom LIKE :keyword
                    OR description LIKE :keyword
                    OR forme LIKE :keyword
                    OR fabricant LIKE :keyword'
            );
            $stmt->execute([':keyword' => '%' . $keyword . '%']);

            return (int) $stmt->fetchColumn();
        }

        return (int) $this->db->query('SELECT COUNT(*) FROM medicaments')->fetchColumn();
    }

    public function getPaginated(
        int $limit,
        int $offset,
        ?string $keyword = null,
        string $sort = 'nom_asc'
    ): array {
        $sortMap = [
            'nom_asc' => 'nom ASC',
            'nom_desc' => 'nom DESC',
            'prix_asc' => 'prix ASC',
            'prix_desc' => 'prix DESC',
            'date_exp_asc' => 'date_expiration ASC',
            'date_exp_desc' => 'date_expiration DESC',
        ];

        $orderBy = $sortMap[$sort] ?? $sortMap['nom_asc'];

        $sql = 'SELECT * FROM medicaments';
        $params = [];

        if ($keyword !== null && $keyword !== '') {
            $sql .= ' WHERE nom LIKE :keyword
                      OR description LIKE :keyword
                      OR forme LIKE :keyword
                      OR fabricant LIKE :keyword';
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $sql .= ' ORDER BY ' . $orderBy . ' LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM medicaments WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }
}
