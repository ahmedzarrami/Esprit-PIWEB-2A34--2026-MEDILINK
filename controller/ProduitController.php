<?php
// ════════════════════════════════════════════
//  controller/ProduitController.php
// ════════════════════════════════════════════
// Ce contrôleur est prévu pour une intégration
// future avec une base de données.
// En attendant, le stockage est géré côté
// client via localStorage (JavaScript).
// ════════════════════════════════════════════

require_once __DIR__ . '/../model/Produit.php';

class ProduitController {

    // ── Retourne tous les produits (JSON) ──
    public function lister(): void {
        // Exemple d'utilisation avec PDO :
        // $stmt = $pdo->query("SELECT * FROM produits ORDER BY id DESC");
        // $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // header('Content-Type: application/json');
        // echo json_encode($produits);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Endpoint /produits — à connecter à la BDD.']);
    }

    // ── Ajouter un produit ──────────────────
    public function ajouter(array $data): array {
        $produit = new Produit(
            0,
            $data['reference']   ?? '',
            $data['nom']         ?? '',
            $data['description'] ?? '',
            (float)($data['prix']   ?? 0),
            (int)  ($data['stock']  ?? 0),
            $data['categorie']   ?? ''
        );

        $erreurs = $produit->valider();
        if (!empty($erreurs)) {
            return ['succes' => false, 'erreurs' => $erreurs];
        }

        // Insérer en BDD (exemple PDO) :
        // $stmt = $pdo->prepare("INSERT INTO produits (reference, nom, description, prix, stock, categorie) VALUES (?, ?, ?, ?, ?, ?)");
        // $stmt->execute([$produit->reference, $produit->nom, $produit->description, $produit->prix, $produit->stock, $produit->categorie]);

        return ['succes' => true, 'message' => 'Produit ajouté avec succès.'];
    }

    // ── Modifier un produit ─────────────────
    public function modifier(int $id, array $data): array {
        $produit = new Produit(
            $id,
            $data['reference']   ?? '',
            $data['nom']         ?? '',
            $data['description'] ?? '',
            (float)($data['prix']   ?? 0),
            (int)  ($data['stock']  ?? 0),
            $data['categorie']   ?? ''
        );

        $erreurs = $produit->valider();
        if (!empty($erreurs)) {
            return ['succes' => false, 'erreurs' => $erreurs];
        }

        // Mettre à jour en BDD (exemple PDO) :
        // $stmt = $pdo->prepare("UPDATE produits SET reference=?, nom=?, description=?, prix=?, stock=?, categorie=? WHERE id=?");
        // $stmt->execute([$produit->reference, $produit->nom, $produit->description, $produit->prix, $produit->stock, $produit->categorie, $id]);

        return ['succes' => true, 'message' => 'Produit modifié avec succès.'];
    }

    // ── Supprimer un produit ────────────────
    public function supprimer(int $id): array {
        if ($id <= 0) {
            return ['succes' => false, 'erreurs' => ['ID invalide.']];
        }

        // Supprimer en BDD (exemple PDO) :
        // $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
        // $stmt->execute([$id]);

        return ['succes' => true, 'message' => 'Produit supprimé avec succès.'];
    }

    // ── Récupérer un produit par ID ─────────
    public function getById(int $id): ?Produit {
        // Exemple PDO :
        // $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
        // $stmt->execute([$id]);
        // $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // if (!$row) return null;
        // return new Produit(...$row);
        return null;
    }
}
