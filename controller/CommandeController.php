<?php
// ════════════════════════════════════════════
//  controller/CommandeController.php
// ════════════════════════════════════════════

class CommandeController {

    public function lister(PDO $pdo): array {
        $stmt = $pdo->query('SELECT id, produit_id, quantite, prix_unitaire, total, nom_produit, mode_paiement, status, client_id, created_at, updated_at FROM commandes ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouter(PDO $pdo, array $body): array {
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
            return ['success' => false, 'message' => 'Client, produit et type de paiement sont obligatoires.'];
        }
        if ($qty <= 0 || $productPrix < 0 || $total < 0) {
            return ['success' => false, 'message' => 'Quantité, prix ou total invalide.'];
        }
        if (!in_array($status, ['En attente','Confirmée','Livrée','Annulée'], true)) {
            $status = 'En attente';
        }

        $productId = $this->resolveProductId($pdo, $productId, $productRef, $productNom);
        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Produit introuvable en base pour la commande.'];
        }

        $stmt = $pdo->prepare('INSERT INTO commandes (produit_id, quantite, prix_unitaire, total, nom_produit, mode_paiement, status, client_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$productId, $qty, $productPrix, $total, $productNom, $payment, $status, $clientId]);

        return ['success' => true, 'data' => ['id' => (int)$pdo->lastInsertId()]];
    }

    public function modifierStatus(PDO $pdo, int $id, string $status): array {
        if ($id <= 0) {
            return ['success' => false, 'message' => 'ID de commande invalide.'];
        }
        if (!in_array($status, ['En attente','Confirmée','Livrée','Annulée'], true) || $status === '') {
            return ['success' => false, 'message' => 'Statut invalide.'];
        }

        $stmt = $pdo->prepare('UPDATE commandes SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);

        return ['success' => true, 'data' => ['id' => $id]];
    }

    public function supprimer(PDO $pdo, int $id): array {
        if ($id <= 0) {
            return ['success' => false, 'message' => 'ID de commande invalide.'];
        }

        $stmt = $pdo->prepare('DELETE FROM commandes WHERE id = ?');
        $stmt->execute([$id]);

        return ['success' => true, 'data' => ['id' => $id]];
    }

    private function resolveProductId(PDO $pdo, int $productId, string $productRef, string $productNom): int {
        if ($productId > 0) {
            $stmt = $pdo->prepare('SELECT id FROM produits WHERE id = ?');
            $stmt->execute([$productId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return (int)$row['id'];
            }
        }

        if ($productRef !== '') {
            $stmt = $pdo->prepare('SELECT id FROM produits WHERE reference = ? LIMIT 1');
            $stmt->execute([$productRef]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return (int)$row['id'];
            }
        }

        if ($productNom !== '') {
            $stmt = $pdo->prepare('SELECT id FROM produits WHERE nom = ? LIMIT 1');
            $stmt->execute([$productNom]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return (int)$row['id'];
            }
        }

        return 0;
    }
}
