<?php

declare(strict_types=1);

class CommandeController
{
    public function lister(PDO $db): array
    {
        $stmt = $db->query(
            'SELECT id, produit_id, quantite, prix_unitaire, total, nom_produit,
                    mode_paiement, status, client_id, created_at, updated_at
             FROM commandes ORDER BY id DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouter(PDO $db, array $body): array
    {
        $clientId    = trim((string) ($body['clientId']    ?? ''));
        $productId   = (int)   ($body['productId']   ?? 0);
        $productRef  = trim((string) ($body['productRef']  ?? ''));
        $productNom  = trim((string) ($body['productNom']  ?? ''));
        $productPrix = (float) ($body['productPrix'] ?? 0);
        $qty         = (int)   ($body['qty']         ?? 0);
        $total       = (float) ($body['total']       ?? 0);
        $payment     = trim((string) ($body['payment']     ?? ''));
        $status      = trim((string) ($body['status']      ?? 'En attente'));

        if ($clientId === '' || $productNom === '' || $payment === '') {
            return ['success' => false, 'message' => 'Client, produit et paiement sont obligatoires.'];
        }
        if ($qty <= 0 || $productPrix < 0 || $total < 0) {
            return ['success' => false, 'message' => 'Quantité, prix ou total invalide.'];
        }
        if (!in_array($status, ['En attente', 'Confirmée', 'Livrée', 'Annulée'], true)) {
            $status = 'En attente';
        }

        $productId = $this->resolveProductId($db, $productId, $productRef, $productNom);
        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Produit introuvable.'];
        }

        // Vérifier le stock
        $stockStmt = $db->prepare('SELECT stock FROM produits WHERE id = ?');
        $stockStmt->execute([$productId]);
        $row = $stockStmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || (int) $row['stock'] < $qty) {
            return ['success' => false, 'message' => 'Stock insuffisant.'];
        }

        $stmt = $db->prepare(
            'INSERT INTO commandes (produit_id, quantite, prix_unitaire, total, nom_produit, mode_paiement, status, client_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$productId, $qty, $productPrix, $total, $productNom, $payment, $status, $clientId]);

        // Décrémenter le stock
        $db->prepare('UPDATE produits SET stock = stock - ? WHERE id = ?')->execute([$qty, $productId]);

        return ['success' => true, 'data' => ['id' => (int) $db->lastInsertId()]];
    }

    public function modifierStatus(PDO $db, int $id, string $status): array
    {
        if ($id <= 0) {
            return ['success' => false, 'message' => 'ID invalide.'];
        }
        if (!in_array($status, ['En attente', 'Confirmée', 'Livrée', 'Annulée'], true)) {
            return ['success' => false, 'message' => 'Statut invalide.'];
        }

        $db->prepare('UPDATE commandes SET status = ? WHERE id = ?')->execute([$status, $id]);
        return ['success' => true, 'data' => ['id' => $id]];
    }

    public function supprimer(PDO $db, int $id): array
    {
        if ($id <= 0) {
            return ['success' => false, 'message' => 'ID invalide.'];
        }
        $db->prepare('DELETE FROM commandes WHERE id = ?')->execute([$id]);
        return ['success' => true, 'data' => ['id' => $id]];
    }

    public function getWeather(): array
    {
        $url     = 'https://wttr.in/Tunis?format=j1&lang=fr';
        $ctx     = stream_context_create(['http' => ['timeout' => 8]]);
        $resp    = @file_get_contents($url, false, $ctx);
        if ($resp === false) {
            return ['success' => false, 'message' => 'Météo indisponible.'];
        }
        $data = json_decode($resp, true);
        if (!$data || !isset($data['current_condition'])) {
            return ['success' => false, 'message' => 'Données météo invalides.'];
        }
        $c = $data['current_condition'][0];
        return ['success' => true, 'data' => [
            'city'        => 'Tunis',
            'description' => $c['weatherDesc'][0]['value'] ?? '—',
            'temperature' => (float) ($c['temp_C'] ?? 0),
            'humidity'    => (int)   ($c['humidity'] ?? 0),
            'wind_speed'  => (float) ($c['windspeedKmph'] ?? 0),
        ]];
    }

    private function resolveProductId(PDO $db, int $id, string $ref, string $nom): int
    {
        if ($id > 0) {
            $s = $db->prepare('SELECT id FROM produits WHERE id = ?');
            $s->execute([$id]);
            if ($r = $s->fetch(PDO::FETCH_ASSOC)) return (int) $r['id'];
        }
        if ($ref !== '') {
            $s = $db->prepare('SELECT id FROM produits WHERE reference = ? LIMIT 1');
            $s->execute([$ref]);
            if ($r = $s->fetch(PDO::FETCH_ASSOC)) return (int) $r['id'];
        }
        if ($nom !== '') {
            $s = $db->prepare('SELECT id FROM produits WHERE nom = ? LIMIT 1');
            $s->execute([$nom]);
            if ($r = $s->fetch(PDO::FETCH_ASSOC)) return (int) $r['id'];
        }
        return 0;
    }
}
