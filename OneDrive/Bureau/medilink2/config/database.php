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
     * @return PDO
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8mb4";
                self::$instance = new PDO($dsn, self::$username, self::$password, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    // Empêcher le clonage et la désérialisation
    private function __construct() {}
    private function __clone() {}
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
