<?php

declare(strict_types=1);

class Database
{
    private string $host = 'localhost';
    private string $dbName = 'medilink';
    private string $username = 'root';
    private string $password = '';
    private ?PDO $connection = null;

    public function getConnection(): PDO
    {
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        try {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $this->host, $this->dbName);
            $this->connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            exit('Erreur de connexion à la base de données : ' . $exception->getMessage());
        }

        return $this->connection;
    }
}
