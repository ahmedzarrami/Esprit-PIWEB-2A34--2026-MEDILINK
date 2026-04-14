<?php
/**
 * Configuration de la base de données
 * Base de données : pharmashop
 */
class Config {
    // ── Paramètres de connexion ──────────────────
    private static string $host    = 'localhost';
    private static string $dbname  = 'pharmashop';
    private static string $user    = 'root';
    private static string $pass    = '';          // Laissez vide si XAMPP/WAMP sans mot de passe
    private static string $charset = 'utf8mb4';

    private static ?PDO $pdo = null;

    /**
     * Retourne une instance PDO (singleton)
     */
    public static function getConnexion(): PDO {
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::$host,
                self::$dbname,
                self::$charset
            );
            try {
                self::$pdo = new PDO($dsn, self::$user, self::$pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // En production : logguer l'erreur sans l'afficher
                http_response_code(500);
                die(json_encode([
                    'success' => false,
                    'message' => 'Erreur de connexion à la base de données.'
                ]));
            }
        }
        return self::$pdo;
    }

    /** Ferme la connexion (utile en CLI) */
    public static function closeConnexion(): void {
        self::$pdo = null;
    }
}