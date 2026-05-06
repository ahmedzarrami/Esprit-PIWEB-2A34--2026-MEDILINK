-- Gestion des ordonnances - MediLink Front Office
-- Exécuter ce script dans phpMyAdmin sur la base 'medilink'

CREATE TABLE IF NOT EXISTS `ordonnances` (
  `id`                INT AUTO_INCREMENT PRIMARY KEY,
  `numero`            VARCHAR(25)  NOT NULL UNIQUE,
  `patient_nom`       VARCHAR(100) NOT NULL,
  `patient_age`       INT          DEFAULT NULL,
  `patient_sexe`      ENUM('M','F') DEFAULT NULL,
  `date_ordonnance`   DATE         NOT NULL,
  `notes`             TEXT         DEFAULT NULL,
  `created_at`        DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
