<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

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
        default:
            jsonResponse(['success' => false, 'message' => 'Ressource invalide.'], 400);
    }
} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Erreur base de données : ' . $e->getMessage()], 500);
}

function handleProductRequest(PDO $pdo, string $method, array $body): void {
    if ($method === 'GET') {
        $stmt = $pdo->query('SELECT id, reference, nom, description, prix, stock, categorie, created_at, updated_at FROM produits ORDER BY id DESC');
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        jsonResponse(['success' => true, 'data' => $produits]);
    }

    if ($method === 'POST') {
        $reference = trim((string)($body['reference'] ?? ''));
        $nom       = trim((string)($body['nom'] ?? ''));
        $description = trim((string)($body['description'] ?? ''));
        $prix      = (float)($body['prix'] ?? 0);
        $stock     = (int)($body['stock'] ?? 0);
        $categorie = trim((string)($body['categorie'] ?? ''));

        if ($reference === '' || $nom === '' || $categorie === '') {
            jsonResponse(['success' => false, 'message' => 'Référence, nom et catégorie sont obligatoires.'], 400);
        }
        if ($prix < 0 || $stock < 0) {
            jsonResponse(['success' => false, 'message' => 'Le prix et le stock ne peuvent pas être négatifs.'], 400);
        }

        $stmt = $pdo->prepare('INSERT INTO produits (reference, nom, description, prix, stock, categorie) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$reference, $nom, $description, $prix, $stock, $categorie]);
        $id = (int)$pdo->lastInsertId();
        jsonResponse(['success' => true, 'data' => ['id' => $id]]);
    }

    if ($method === 'PUT') {
        $id = isset($body['id']) ? (int)$body['id'] : 0;
        if ($id <= 0) {
            jsonResponse(['success' => false, 'message' => 'ID de produit invalide.'], 400);
        }
        $reference = trim((string)($body['reference'] ?? ''));
        $nom       = trim((string)($body['nom'] ?? ''));
        $description = trim((string)($body['description'] ?? ''));
        $prix      = (float)($body['prix'] ?? 0);
        $stock     = (int)($body['stock'] ?? 0);
        $categorie = trim((string)($body['categorie'] ?? ''));

        if ($reference === '' || $nom === '' || $categorie === '') {
            jsonResponse(['success' => false, 'message' => 'Référence, nom et catégorie sont obligatoires.'], 400);
        }
        if ($prix < 0 || $stock < 0) {
            jsonResponse(['success' => false, 'message' => 'Le prix et le stock ne peuvent pas être négatifs.'], 400);
        }

        $stmt = $pdo->prepare('UPDATE produits SET reference = ?, nom = ?, description = ?, prix = ?, stock = ?, categorie = ? WHERE id = ?');
        $stmt->execute([$reference, $nom, $description, $prix, $stock, $categorie, $id]);
        jsonResponse(['success' => true, 'data' => ['id' => $id]]);
    }

    if ($method === 'DELETE') {
        $id = isset($body['id']) ? (int)$body['id'] : 0;
        if ($id <= 0) {
            jsonResponse(['success' => false, 'message' => 'ID de produit invalide.'], 400);
        }
        $stmt = $pdo->prepare('DELETE FROM produits WHERE id = ?');
        $stmt->execute([$id]);
        jsonResponse(['success' => true]);
    }

    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}

function handleOrderRequest(PDO $pdo, string $method, array $body): void {
    if ($method === 'GET') {
        $stmt = $pdo->query('SELECT id, produit_id, quantite, prix_unitaire, total, nom_produit, mode_paiement, status, client_id, created_at, updated_at FROM commandes ORDER BY id DESC');
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        jsonResponse(['success' => true, 'data' => $orders]);
    }

    if ($method === 'POST') {
        $clientId   = trim((string)($body['clientId'] ?? ''));
        $productId  = (int)($body['productId'] ?? 0);
        $productRef = trim((string)($body['productRef'] ?? ''));
        $productNom = trim((string)($body['productNom'] ?? ''));
        $productPrix= (float)($body['productPrix'] ?? 0);
        $qty        = (int)($body['qty'] ?? 0);
        $total      = (float)($body['total'] ?? 0);
        $payment    = trim((string)($body['payment'] ?? ''));
        $status     = trim((string)($body['status'] ?? 'En attente'));

        if ($clientId === '' || $productNom === '' || $payment === '') {
            jsonResponse(['success' => false, 'message' => 'Client, produit et type de paiement sont obligatoires.'], 400);
        }
        if ($qty <= 0 || $productPrix < 0 || $total < 0) {
            jsonResponse(['success' => false, 'message' => 'Quantité, prix ou total invalide.'], 400);
        }
        if (!in_array($status, ['En attente','Confirmée','Livrée','Annulée'], true)) {
            $status = 'En attente';
        }

        $productExists = false;
        if ($productId > 0) {
            $stmt = $pdo->prepare('SELECT id FROM produits WHERE id = ?');
            $stmt->execute([$productId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $productId = (int)$row['id'];
                $productExists = true;
            }
        }

        if (!$productExists && $productRef !== '') {
            $stmt = $pdo->prepare('SELECT id FROM produits WHERE reference = ? LIMIT 1');
            $stmt->execute([$productRef]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $productId = (int)$row['id'];
                $productExists = true;
            }
        }

        if (!$productExists) {
            $stmt = $pdo->prepare('SELECT id FROM produits WHERE nom = ? LIMIT 1');
            $stmt->execute([$productNom]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $productId = (int)$row['id'];
                $productExists = true;
            }
        }

        if (!$productExists) {
            jsonResponse(['success' => false, 'message' => 'Produit introuvable en base pour la commande.'], 400);
        }

        $stmt = $pdo->prepare('INSERT INTO commandes (produit_id, quantite, prix_unitaire, total, nom_produit, mode_paiement, status, client_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$productId, $qty, $productPrix, $total, $productNom, $payment, $status, $clientId]);
        $id = (int)$pdo->lastInsertId();
        jsonResponse(['success' => true, 'data' => ['id' => $id]]);
    }

    if ($method === 'PATCH') {
        $id = isset($body['id']) ? (int)$body['id'] : 0;
        $status = trim((string)($body['status'] ?? ''));
        if ($id <= 0 || $status === '') {
            jsonResponse(['success' => false, 'message' => 'ID et statut obligatoires.'], 400);
        }
        $stmt = $pdo->prepare('UPDATE commandes SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        jsonResponse(['success' => true, 'data' => ['id' => $id]]);
    }

    if ($method === 'DELETE') {
        $id = isset($body['id']) ? (int)$body['id'] : 0;
        if ($id <= 0) {
            jsonResponse(['success' => false, 'message' => 'ID de commande invalide.'], 400);
        }
        $stmt = $pdo->prepare('DELETE FROM commandes WHERE id = ?');
        $stmt->execute([$id]);
        jsonResponse(['success' => true]);
    }

    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}
