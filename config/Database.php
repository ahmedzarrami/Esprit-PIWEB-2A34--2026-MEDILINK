<?php
/**
 * Classe Database — Connexion PDO (Singleton)
 * MediLink — Gestion des Utilisateurs
 */
class Database
{
    // ── Paramètres de connexion ──
    private static string $host   = 'localhost';
    private static string $dbname = 'medilink';
    private static string $user   = 'root';
    private static string $pass   = '';

    // ── Instance unique ──
    private static ?PDO $instance = null;

    /**
     * Constructeur privé (empêche l'instanciation directe)
     */
    private function __construct() {}

    /**
     * Retourne l'instance PDO unique
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = 'mysql:host=' . self::$host
                     . ';dbname='    . self::$dbname
                     . ';charset=utf8mb4';

                self::$instance = new PDO($dsn, self::$user, self::$pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die('Erreur de connexion à la base de données : ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
