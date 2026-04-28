-- Base de données MediLink
-- Exécuter dans phpMyAdmin

CREATE DATABASE IF NOT EXISTS medilink CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medilink;

-- ── Table médicaments ────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `medicaments` (
    `id`              INT AUTO_INCREMENT PRIMARY KEY,
    `nom`             VARCHAR(100)    NOT NULL,
    `description`     TEXT            NOT NULL,
    `dosage`          VARCHAR(50)     NOT NULL,
    `forme`           VARCHAR(50)     NOT NULL,
    `fabricant`       VARCHAR(100)    NOT NULL,
    `prix`            DECIMAL(10,2)   NOT NULL,
    `stock`           INT             NOT NULL DEFAULT 0,
    `date_expiration` DATE            DEFAULT NULL,
    `created_at`      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Table ordonnances ────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `ordonnances` (
    `id`              INT AUTO_INCREMENT PRIMARY KEY,
    `numero`          VARCHAR(25)     NOT NULL UNIQUE,
    `patient_nom`     VARCHAR(100)    NOT NULL,
    `patient_age`     INT             DEFAULT NULL,
    `patient_sexe`    ENUM('M','F')   DEFAULT NULL,
    `date_ordonnance` DATE            NOT NULL,
    `notes`           TEXT            DEFAULT NULL,
    `created_at`      DATETIME        DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME        DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Table ordonnance_lignes ──────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `ordonnance_lignes` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `ordonnance_id`  INT          NOT NULL,
    `medicament_id`  INT          NOT NULL,
    `posologie`      VARCHAR(255) NOT NULL,
    `duree`          VARCHAR(100) DEFAULT NULL,
    `quantite`       INT          NOT NULL DEFAULT 1,
    CONSTRAINT `fk_ol_ordonnance` FOREIGN KEY (`ordonnance_id`) REFERENCES `ordonnances` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ol_medicament` FOREIGN KEY (`medicament_id`) REFERENCES `medicaments` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Données de démonstration ─────────────────────────────────────────────────

INSERT INTO `medicaments` (nom, description, dosage, forme, fabricant, prix, stock) VALUES
('Doliprane',    'Antalgique utilisé pour soulager la douleur et réduire la fièvre.',                        '500 mg',    'Comprimé', 'Sanofi',   8.50,  120),
('Amoxicilline', 'Antibiotique utilisé dans le traitement de plusieurs infections bactériennes.',             '1 g',       'Gélule',   'Biogaran', 14.00,  85),
('Toplexil',     'Sirop destiné au soulagement de la toux sèche et des irritations légères.',                '0.33 mg/ml','Sirop',    'Sanofi',   11.90,  60),
('Ibuprofène',   'Anti-inflammatoire utilisé pour calmer la douleur et la fièvre.',                          '400 mg',    'Comprimé', 'Advil',    9.90,  200),
('Smecta',       'Poudre orale utilisée pour soulager les troubles digestifs et les diarrhées.',             '3 g',       'Sachet',   'Ipsen',    12.20,  45);
