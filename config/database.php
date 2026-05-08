<?php
/**
 * Classe Database — Singleton PDO
 * Connexion unique à la base de données via PDO
 */
class Database {
    private static ?PDO $instance = null;
    private static string $host = 'localhost';
    private static string $dbname = 'medilink';
    private static string $username = 'root';
    private static string $password = '';

    /**
     * Retourne une instance unique de PDO (singleton)
     * Reconnecte automatiquement si la connexion MySQL est perdue (erreur 2006)
     * @return PDO
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        } else {
            // Vérifier si la connexion est toujours active (correction erreur 2006 "MySQL server has gone away")
            try {
                self::$instance->query('SELECT 1');
            } catch (PDOException $e) {
                // La connexion est perdue — forcer la reconnexion
                self::$instance = null;
                self::$instance = self::createConnection();
            }
        }
        return self::$instance;
    }

    /**
     * Crée et retourne une nouvelle connexion PDO
     * @return PDO
     */
    private static function createConnection(): PDO {
        try {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8mb4";
            return new PDO($dsn, self::$username, self::$password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            ]);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    // Empêcher le clonage et la désérialisation
    private function __construct() {}
    private function __clone() {}
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
