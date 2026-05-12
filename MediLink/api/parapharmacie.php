<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/CommandeController.php';

function jsonOut(array $data, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $db = Database::getConnection();
} catch (Throwable $e) {
    jsonOut(['success' => false, 'message' => 'Erreur de connexion BDD.'], 500);
}

$resource = $_GET['resource'] ?? '';
$method   = $_SERVER['REQUEST_METHOD'];
$body     = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];
$override = strtoupper((string) ($body['_method'] ?? $_POST['_method'] ?? ''));
if ($method === 'POST' && in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
    $method = $override;
}

$ctrl = new CommandeController();

switch ($resource) {

    // ── PRODUITS ───────────────────────────────────────────────
    case 'produits':
        if ($method === 'GET') {
            $cat    = trim((string) ($_GET['categorie'] ?? ''));
            $search = trim((string) ($_GET['search']    ?? ''));
            $sql    = 'SELECT * FROM produits';
            $where  = [];
            $params = [];
            if ($cat !== '') { $where[] = 'categorie = ?'; $params[] = $cat; }
            if ($search !== '') { $where[] = '(nom LIKE ? OR description LIKE ? OR reference LIKE ?)'; $v = "%$search%"; $params[] = $v; $params[] = $v; $params[] = $v; }
            if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
            $sql .= ' ORDER BY nom ASC';
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            jsonOut(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        }
        jsonOut(['success' => false, 'message' => 'Méthode non autorisée.'], 405);

    // ── COMMANDES ──────────────────────────────────────────────
    case 'commandes':
        if ($method === 'GET') {
            jsonOut(['success' => true, 'data' => $ctrl->lister($db)]);
        }
        if ($method === 'POST') {
            $r = $ctrl->ajouter($db, $body);
            jsonOut($r, $r['success'] ? 200 : 400);
        }
        if ($method === 'PATCH') {
            $id     = (int)   ($body['id']     ?? 0);
            $status = trim((string) ($body['status'] ?? ''));
            $r = $ctrl->modifierStatus($db, $id, $status);
            jsonOut($r, $r['success'] ? 200 : 400);
        }
        if ($method === 'DELETE') {
            $id = (int) ($body['id'] ?? 0);
            $r  = $ctrl->supprimer($db, $id);
            jsonOut($r, $r['success'] ? 200 : 400);
        }
        jsonOut(['success' => false, 'message' => 'Méthode non autorisée.'], 405);

    // ── MÉTÉO ──────────────────────────────────────────────────
    case 'weather':
        if ($method === 'GET') {
            $r = $ctrl->getWeather();
            jsonOut($r, $r['success'] ? 200 : 503);
        }
        jsonOut(['success' => false, 'message' => 'Méthode non autorisée.'], 405);

    // ── RATINGS ────────────────────────────────────────────────
    case 'ratings':
        if ($method === 'GET') {
            $pid = (int) ($_GET['produit_id'] ?? 0);
            if ($pid <= 0) jsonOut(['success' => false, 'message' => 'produit_id requis.'], 400);
            $stmt = $db->prepare('SELECT rating, comment, created_at, client_id FROM ratings WHERE produit_id = ? ORDER BY created_at DESC');
            $stmt->execute([$pid]);
            $rows  = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $avg   = count($rows) ? round(array_sum(array_column($rows, 'rating')) / count($rows), 1) : 0;
            jsonOut(['success' => true, 'data' => ['ratings' => $rows, 'average' => $avg, 'total' => count($rows)]]);
        }
        if ($method === 'POST') {
            $pid     = (int)   ($body['produit_id'] ?? 0);
            $client  = trim((string) ($body['client_id']  ?? ''));
            $rating  = (int)   ($body['rating']     ?? 0);
            $comment = trim((string) ($body['comment']    ?? ''));
            if ($pid <= 0 || $client === '' || $rating < 1 || $rating > 5) {
                jsonOut(['success' => false, 'message' => 'Données invalides.'], 400);
            }
            $check = $db->prepare('SELECT id FROM produits WHERE id = ?');
            $check->execute([$pid]);
            if (!$check->fetch()) jsonOut(['success' => false, 'message' => 'Produit introuvable.'], 404);

            $stmt = $db->prepare(
                'INSERT INTO ratings (produit_id, client_id, rating, comment) VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment), created_at = CURRENT_TIMESTAMP'
            );
            $stmt->execute([$pid, $client, $rating, $comment]);
            jsonOut(['success' => true, 'message' => 'Avis enregistré.']);
        }
        jsonOut(['success' => false, 'message' => 'Méthode non autorisée.'], 405);

    // ── ALL RATINGS (back-office) ──────────────────────────────
    case 'all_ratings':
        if ($method === 'GET') {
            $stmt = $db->query(
                'SELECT r.id, r.produit_id, p.nom AS produit_nom, r.client_id,
                        r.rating, r.comment, r.created_at
                 FROM ratings r
                 JOIN produits p ON p.id = r.produit_id
                 ORDER BY r.created_at DESC'
            );
            jsonOut(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        }
        if ($method === 'DELETE') {
            $id = (int) ($body['id'] ?? 0);
            if ($id <= 0) jsonOut(['success' => false, 'message' => 'ID invalide.'], 400);
            $db->prepare('DELETE FROM ratings WHERE id = ?')->execute([$id]);
            jsonOut(['success' => true]);
        }
        jsonOut(['success' => false, 'message' => 'Méthode non autorisée.'], 405);

    default:
        jsonOut(['success' => false, 'message' => "Ressource '$resource' inconnue."], 404);
}
