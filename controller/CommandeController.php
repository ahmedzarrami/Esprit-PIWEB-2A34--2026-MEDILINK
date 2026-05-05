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

    public function getWeather(): array {
        $city = 'Tunis'; // Default city, can be made configurable

        $url = "https://wttr.in/{$city}?format=j1&lang=fr";

        $context = stream_context_create([
            'http' => [
                'timeout' => 10, // Timeout in seconds
            ]
        ]);

        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            return ['success' => false, 'message' => 'Erreur lors de la récupération des données météo.'];
        }

        $data = json_decode($response, true);

        if (!$data || !isset($data['current_condition'])) {
            return ['success' => false, 'message' => 'Données météo indisponibles.'];
        }

        $current = $data['current_condition'][0];
        $weather = [
            'city' => $city,
            'description' => $current['weatherDesc'][0]['value'] ?? 'Non disponible',
            'temperature' => (float)($current['temp_C'] ?? 0),
            'humidity' => (int)($current['humidity'] ?? 0),
            'wind_speed' => (float)($current['windspeedKmph'] ?? 0),
            'icon' => $this->mapWeatherIcon($current['weatherCode'] ?? '')
        ];

        return ['success' => true, 'data' => $weather];
    }

    private function mapWeatherIcon(string $weatherCode): string {
        // Map wttr.in weather codes to OpenWeatherMap style icons for compatibility
        $iconMap = [
            '113' => '01d', // Sunny
            '116' => '02d', // Partly cloudy
            '119' => '03d', // Cloudy
            '122' => '04d', // Overcast
            '143' => '50d', // Mist
            '176' => '09d', // Patchy rain
            '179' => '13d', // Patchy snow
            '182' => '13d', // Patchy sleet
            '185' => '09d', // Patchy freezing drizzle
            '200' => '11d', // Thundery outbreaks
            '227' => '13d', // Blowing snow
            '230' => '13d', // Blizzard
            '248' => '50d', // Fog
            '260' => '50d', // Freezing fog
            '263' => '09d', // Patchy light drizzle
            '266' => '09d', // Light drizzle
            '281' => '09d', // Freezing drizzle
            '284' => '09d', // Heavy freezing drizzle
            '293' => '09d', // Patchy light rain
            '296' => '09d', // Light rain
            '299' => '09d', // Moderate rain at times
            '302' => '10d', // Moderate rain
            '305' => '10d', // Heavy rain at times
            '308' => '10d', // Heavy rain
            '311' => '09d', // Light freezing rain
            '314' => '10d', // Moderate or heavy freezing rain
            '317' => '13d', // Light sleet
            '320' => '13d', // Moderate or heavy sleet
            '323' => '13d', // Patchy light snow
            '326' => '13d', // Light snow
            '329' => '13d', // Patchy moderate snow
            '332' => '13d', // Moderate snow
            '335' => '13d', // Patchy heavy snow
            '338' => '13d', // Heavy snow
            '350' => '09d', // Ice pellets
            '353' => '09d', // Light rain shower
            '356' => '10d', // Moderate or heavy rain shower
            '359' => '10d', // Torrential rain shower
            '362' => '13d', // Light sleet showers
            '365' => '13d', // Moderate or heavy sleet showers
            '368' => '13d', // Light snow showers
            '371' => '13d', // Moderate or heavy snow showers
            '374' => '09d', // Light showers of ice pellets
            '377' => '13d', // Moderate or heavy showers of ice pellets
            '386' => '11d', // Patchy light rain with thunder
            '389' => '11d', // Moderate or heavy rain with thunder
            '392' => '11d', // Patchy light snow with thunder
            '395' => '11d', // Moderate or heavy snow with thunder
        ];

        return $iconMap[$weatherCode] ?? '01d';
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
