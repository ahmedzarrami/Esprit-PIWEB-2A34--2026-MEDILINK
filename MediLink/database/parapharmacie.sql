-- ══════════════════════════════════════════════════════════
--  Table produits (Parapharmacie)
-- ══════════════════════════════════════════════════════════

USE medilink;

CREATE TABLE IF NOT EXISTS `produits` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `reference`   VARCHAR(50)     NOT NULL UNIQUE,
    `nom`         VARCHAR(100)    NOT NULL,
    `description` TEXT            DEFAULT NULL,
    `prix`        DECIMAL(10,3)   NOT NULL DEFAULT 0.000,
    `stock`       INT             NOT NULL DEFAULT 0,
    `categorie`   VARCHAR(50)     NOT NULL DEFAULT 'Autre',
    `image_path`  VARCHAR(255)    DEFAULT NULL,
    `created_at`  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Migration : ajouter image_path si la table existe déjà
ALTER TABLE `produits` ADD COLUMN IF NOT EXISTS `image_path` VARCHAR(255) DEFAULT NULL;

-- ── Table commandes ──────────────────────────────────────

CREATE TABLE IF NOT EXISTS `commandes` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `produit_id`     INT             NOT NULL,
    `quantite`       INT             NOT NULL DEFAULT 1,
    `prix_unitaire`  DECIMAL(10,3)   NOT NULL,
    `total`          DECIMAL(10,3)   NOT NULL,
    `nom_produit`    VARCHAR(100)    NOT NULL,
    `mode_paiement`  VARCHAR(50)     NOT NULL,
    `status`         ENUM('En attente','Confirmée','Livrée','Annulée') DEFAULT 'En attente',
    `client_id`      VARCHAR(100)    DEFAULT NULL,
    `adresse`        TEXT            DEFAULT NULL,
    `created_at`     TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_commande_produit` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Table ratings ────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `ratings` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `produit_id`  INT          NOT NULL,
    `client_id`   VARCHAR(100) NOT NULL,
    `rating`      TINYINT      NOT NULL CHECK (rating BETWEEN 1 AND 5),
    `comment`     TEXT         DEFAULT NULL,
    `created_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_rating_produit` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Données de démonstration ─────────────────────────────

INSERT INTO `produits` (reference, nom, description, prix, stock, categorie) VALUES
('REF-SV-001', 'Crème Hydratante Visage', 'Crème hydratante pour tous types de peau, enrichie en acide hyaluronique.', 25.900, 45, 'Soins visage'),
('REF-SV-002', 'Sérum Anti-Âge', 'Sérum concentré en rétinol pour lutter contre les signes du vieillissement.', 42.500, 30, 'Soins visage'),
('REF-SC-001', 'Lait Corporel Nourrissant', 'Lait corporel au beurre de karité pour une peau douce et hydratée.', 18.750, 60, 'Soins corps'),
('REF-HY-001', 'Gel Douche Surgras', 'Gel douche doux sans savon pour peaux sensibles.', 12.300, 80, 'Hygiène'),
('REF-CA-001', 'Vitamine D3 1000UI', 'Complément alimentaire vitamine D3 pour renforcer les défenses immunitaires.', 15.900, 100, 'Compléments alimentaires'),
('REF-CA-002', 'Magnésium Marin', 'Magnésium d''origine marine pour réduire la fatigue et le stress.', 19.500, 75, 'Compléments alimentaires'),
('REF-BB-001', 'Crème Change Bébé', 'Crème protectrice pour le change, hypoallergénique.', 14.200, 55, 'Bébé & Maman'),
('REF-CP-001', 'Shampoing Anti-Chute', 'Shampoing fortifiant à la kératine et biotine.', 22.800, 40, 'Capillaire'),
('REF-SO-001', 'Crème Solaire SPF50+', 'Protection solaire haute protection pour visage et corps.', 28.900, 35, 'Solaire'),
('REF-MI-001', 'Draineur Minceur', 'Boisson drainante aux extraits de thé vert et queue de cerise.', 16.500, 50, 'Minceur');
