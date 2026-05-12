<?php
/**
 * Configuration centrale de la base de donnees integree.
 *
 * Tous les modules pointent vers cette base unique (medilinkintegration).
 * Importer le fichier medilinkintegration.sql a la racine avant utilisation.
 */

if (!defined('DB_HOST'))   define('DB_HOST',   '127.0.0.1');
if (!defined('DB_PORT'))   define('DB_PORT',   '3306');
if (!defined('DB_NAME'))   define('DB_NAME',   'medilinkintegration');
if (!defined('DB_USER'))   define('DB_USER',   'root');
if (!defined('DB_PASS'))   define('DB_PASS',   '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

/**
 * Retourne une instance PDO partagee (singleton de processus).
 */
function medilink_pdo(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}
