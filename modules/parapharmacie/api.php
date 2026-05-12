<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/session.php';
require_login();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controller/ProduitController.php';
require_once __DIR__ . '/controller/CommandeController.php';

header('Content-Type: application/json; charset=utf-8');

function jsonResponse(array $data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function ensureSchema(PDO $pdo): void {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `produits` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `reference` VARCHAR(100) NOT NULL,
            `nom` VARCHAR(255) NOT NULL,
            `description` TEXT NOT NULL,
            `prix` DECIMAL(10,3) NOT NULL DEFAULT 0.000,
            `stock` INT NOT NULL DEFAULT 0,
            `categorie` VARCHAR(120) NOT NULL,
            `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_reference` (`reference`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `commandes` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `produit_id` INT UNSIGNED NOT NULL,
            `quantite` INT NOT NULL DEFAULT 1,
            `prix_unitaire` DECIMAL(10,3) NOT NULL DEFAULT 0.000,
            `total` DECIMAL(12,3) NOT NULL DEFAULT 0.000,
            `nom_produit` VARCHAR(255) NOT NULL,
            `mode_paiement` VARCHAR(50) NOT NULL,
            `status` ENUM('En attente','Confirmée','Livrée','Annulée') NOT NULL DEFAULT 'En attente',
            `client_id` VARCHAR(80) NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_produit_id` (`produit_id`),
            CONSTRAINT `fk_commandes_produit` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `ratings` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `produit_id` INT UNSIGNED NOT NULL,
            `client_id` VARCHAR(80) NOT NULL,
            `rating` TINYINT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
            `comment` TEXT,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_produit_rating` (`produit_id`),
            KEY `idx_client_rating` (`client_id`),
            UNIQUE KEY `uniq_client_produit` (`client_id`, `produit_id`),
            CONSTRAINT `fk_ratings_produit` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
}

try {
    $pdo = Config::getConnexion();
    ensureSchema($pdo);
} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Impossible de se connecter à la base de données.'], 500);
}

$resource = $_GET['resource'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];
$override = strtoupper($body['_method'] ?? $_POST['_method'] ?? '');
if ($method === 'POST' && in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
    $method = $override;
}

try {
    switch ($resource) {
        case 'produits':
            handleProductRequest($pdo, $method, $body);
            break;
        case 'commandes':
            handleOrderRequest($pdo, $method, $body);
            break;
        case 'weather':
            handleWeatherRequest($pdo, $method, $body);
            break;
        case 'ratings':
            handleRatingRequest($pdo, $method, $body);
            break;
        default:
            jsonResponse(['success' => false, 'message' => 'Ressource invalide.'], 400);
    }
} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Erreur base de données : ' . $e->getMessage()], 500);
}

function handleProductRequest(PDO $pdo, string $method, array $body): void {
    $controller = new ProduitController();

    if ($method === 'GET') {
        jsonResponse(['success' => true, 'data' => $controller->lister($pdo)]);
    }

    if ($method === 'POST') {
        $result = $controller->ajouter($pdo, $body);
        jsonResponse($result, $result['success'] ? 200 : 400);
    }

    if ($method === 'PUT') {
        $id = isset($body['id']) ? (int)$body['id'] : 0;
        $result = $controller->modifier($pdo, $id, $body);
        jsonResponse($result, $result['success'] ? 200 : 400);
    }

    if ($method === 'DELETE') {
        $id = isset($body['id']) ? (int)$body['id'] : 0;
        $result = $controller->supprimer($pdo, $id);
        jsonResponse($result, $result['success'] ? 200 : 400);
    }

    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}

function handleOrderRequest(PDO $pdo, string $method, array $body): void {
    $controller = new CommandeController();

    if ($method === 'GET') {
        jsonResponse(['success' => true, 'data' => $controller->lister($pdo)]);
    }

    if ($method === 'POST') {
        $result = $controller->ajouter($pdo, $body);
        jsonResponse($result, $result['success'] ? 200 : 400);
    }

    if ($method === 'PATCH') {
        $id = isset($body['id']) ? (int)$body['id'] : 0;
        $status = trim((string)($body['status'] ?? ''));
        $result = $controller->modifierStatus($pdo, $id, $status);
        jsonResponse($result, $result['success'] ? 200 : 400);
    }

    if ($method === 'DELETE') {
        $id = isset($body['id']) ? (int)$body['id'] : 0;
        $result = $controller->supprimer($pdo, $id);
        jsonResponse($result, $result['success'] ? 200 : 400);
    }

    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}

function handleWeatherRequest(PDO $pdo, string $method, array $body): void {
    $controller = new CommandeController();

    if ($method === 'GET') {
        $result = $controller->getWeather();
        jsonResponse($result, $result['success'] ? 200 : 400);
    }

    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}

function handleRatingRequest(PDO $pdo, string $method, array $body): void {
    if ($method === 'GET') {
        $produitId = isset($_GET['produit_id']) ? (int)$_GET['produit_id'] : 0;
        if ($produitId <= 0) {
            jsonResponse(['success' => false, 'message' => 'ID de produit invalide.'], 400);
        }

        $stmt = $pdo->prepare('SELECT r.rating, r.comment, r.created_at, r.client_id FROM ratings r WHERE r.produit_id = ? ORDER BY r.created_at DESC');
        $stmt->execute([$produitId]);
        $ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate average rating
        $totalRatings = count($ratings);
        $averageRating = 0;
        if ($totalRatings > 0) {
            $sum = array_sum(array_column($ratings, 'rating'));
            $averageRating = round($sum / $totalRatings, 1);
        }

        jsonResponse(['success' => true, 'data' => [
            'ratings' => $ratings,
            'average_rating' => $averageRating,
            'total_ratings' => $totalRatings
        ]]);
    }

    if ($method === 'POST') {
        $produitId = (int)($body['produit_id'] ?? 0);
        $clientId = trim((string)($body['client_id'] ?? ''));
        $rating = (int)($body['rating'] ?? 0);
        $comment = trim((string)($body['comment'] ?? ''));

        if ($produitId <= 0 || $clientId === '' || $rating < 1 || $rating > 5) {
            jsonResponse(['success' => false, 'message' => 'Données invalides.'], 400);
        }

        // Check if product exists
        $stmt = $pdo->prepare('SELECT id FROM produits WHERE id = ?');
        $stmt->execute([$produitId]);
        if (!$stmt->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Produit introuvable.'], 404);
        }

        // Insert or update rating
        $stmt = $pdo->prepare('INSERT INTO ratings (produit_id, client_id, rating, comment) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment), created_at = CURRENT_TIMESTAMP');
        $stmt->execute([$produitId, $clientId, $rating, $comment]);

        jsonResponse(['success' => true, 'message' => 'Note enregistrée.']);
    }

    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}