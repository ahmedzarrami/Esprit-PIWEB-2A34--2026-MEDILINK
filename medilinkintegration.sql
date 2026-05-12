-- ================================================================
-- MediLink - Base de donnees integree (tous modules)
-- Branche : integration
-- ================================================================
-- Fusionne les schemas de :
--   * medilink.sql            (gestion_utilisateur)
--   * projetweb (1).sql       (gestionrdv)
--   * medilink (2).sql        (forum + medicaments + parapharmacie)
--
-- Authentification UNIFIEE :
--   - Tous les modules passent par utilisateur (id, email, mot_de_passe, role).
--   - Les tables patients (zerofill) et medecins du module RDV gardent
--     leur structure pour ne pas casser les requetes existantes, mais
--     elles sont liees a utilisateur via la colonne utilisateur_id.
--   - Aucun compte demo : seul un compte administrateur de bootstrap
--     est cree pour acceder a /admin/. Les autres comptes sont a creer
--     via la page d'inscription.
-- ================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS medilinkintegration;
CREATE DATABASE medilinkintegration
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE medilinkintegration;

-- ================================================================
-- 1. MODULE GESTION UTILISATEUR (table racine + sous-types)
-- ================================================================

CREATE TABLE utilisateur (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(100)  NOT NULL,
    prenom          VARCHAR(100)  NOT NULL,
    email           VARCHAR(255)  NOT NULL UNIQUE,
    mot_de_passe   VARCHAR(255)  NOT NULL,
    telephone       VARCHAR(20)   NOT NULL,
    statut_compte   VARCHAR(20)   NOT NULL DEFAULT 'Actif',
    role            VARCHAR(30)   NOT NULL,
    date_creation   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    face_descriptor TEXT          DEFAULT NULL,
    failed_attempts INT           NOT NULL DEFAULT 0,
    locked_until    DATETIME      DEFAULT NULL,
    CONSTRAINT chk_role   CHECK (role IN ('Patient','Professionnel','Administrateur')),
    CONSTRAINT chk_statut CHECK (statut_compte IN ('Actif','Inactif','Suspendu','En attente'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE patient (
    id              INT PRIMARY KEY,
    date_naissance  DATE          DEFAULT NULL,
    sexe            VARCHAR(1)    DEFAULT NULL,
    adresse         VARCHAR(255)  DEFAULT NULL,
    groupe_sanguin  VARCHAR(5)    DEFAULT NULL,
    CONSTRAINT fk_patient_utilisateur
        FOREIGN KEY (id) REFERENCES utilisateur(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE professionnel_sante (
    id              INT PRIMARY KEY,
    specialite      VARCHAR(100)  NOT NULL,
    numero_ordre    VARCHAR(50)   NOT NULL,
    biographie      TEXT          DEFAULT NULL,
    CONSTRAINT fk_pro_utilisateur
        FOREIGN KEY (id) REFERENCES utilisateur(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE administrateur (
    id              INT PRIMARY KEY,
    CONSTRAINT fk_admin_utilisateur
        FOREIGN KEY (id) REFERENCES utilisateur(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_reset_tokens (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    token       VARCHAR(6) NOT NULL,
    expires_at  DATETIME NOT NULL,
    used        TINYINT(1) NOT NULL DEFAULT 0,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reset_utilisateur
        FOREIGN KEY (user_id) REFERENCES utilisateur(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 2. MODULE RDV (patients/medecins lies a utilisateur via le bridge)
-- ================================================================

CREATE TABLE patients (
    id              BIGINT(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(50)  NOT NULL,
    prenom          VARCHAR(50)  NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    motdepasse      VARCHAR(255) NOT NULL,
    telephone       VARCHAR(15)  NOT NULL,
    datedenaissance DATE         NOT NULL DEFAULT '1970-01-01',
    sexe            ENUM('M','F') NOT NULL DEFAULT 'M',
    adresse         VARCHAR(200) DEFAULT NULL,
    utilisateur_id  INT          DEFAULT NULL,
    date_inscription DATETIME    DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_patients_utilisateur (utilisateur_id),
    KEY idx_email (email),
    KEY idx_telephone (telephone),
    CONSTRAINT fk_patients_utilisateur
        FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE medecins (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL,
    specialite  VARCHAR(100) NOT NULL,
    email       VARCHAR(100) DEFAULT NULL,
    adresse     VARCHAR(255) DEFAULT NULL,
    ville       VARCHAR(100) DEFAULT NULL,
    latitude    DECIMAL(10,7) DEFAULT NULL,
    longitude   DECIMAL(10,7) DEFAULT NULL,
    telephone   VARCHAR(20) DEFAULT NULL,
    utilisateur_id  INT      DEFAULT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_medecins_utilisateur (utilisateur_id),
    CONSTRAINT fk_medecins_utilisateur
        FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE rendezvous (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    medecin_id  INT NOT NULL,
    patient_id  BIGINT(6) UNSIGNED ZEROFILL DEFAULT NULL,
    date_rdv    DATE NOT NULL,
    heure_rdv   TIME NOT NULL,
    statut      VARCHAR(50) DEFAULT 'confirme',
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_rdv (medecin_id, date_rdv, heure_rdv),
    KEY idx_date_rdv (date_rdv),
    KEY idx_medecin_id (medecin_id),
    KEY idx_patient_id (patient_id),
    CONSTRAINT fk_rdv_medecin FOREIGN KEY (medecin_id) REFERENCES medecins(id) ON DELETE CASCADE,
    CONSTRAINT fk_rdv_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE fiche_patient (
    idfiche         INT AUTO_INCREMENT PRIMARY KEY,
    rendezvous_id   INT NOT NULL,
    groupsanguin    VARCHAR(5) DEFAULT NULL,
    allergies       TEXT DEFAULT NULL,
    antecedents     TEXT DEFAULT NULL,
    notesGenerales  TEXT DEFAULT NULL,
    date_creation   DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_rendezvous_id (rendezvous_id),
    KEY idx_date_creation (date_creation),
    CONSTRAINT fk_fiche_rdv FOREIGN KEY (rendezvous_id) REFERENCES rendezvous(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE evaluations (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT NOT NULL,
    medecin_id      INT NOT NULL,
    rendezvous_id   INT NOT NULL,
    note            TINYINT(1) NOT NULL,
    commentaire     TEXT DEFAULT NULL,
    date_eval       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_eval_rdv (rendezvous_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ================================================================
-- 3. MODULE FORUM
-- ================================================================

CREATE TABLE forum (
    id_forum    INT AUTO_INCREMENT PRIMARY KEY,
    titre       VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE post (
    id_post         INT AUTO_INCREMENT PRIMARY KEY,
    contenu         TEXT NOT NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_forum        INT NOT NULL,
    id_auteur       INT NOT NULL,
    KEY id_forum (id_forum),
    KEY id_auteur (id_auteur),
    CONSTRAINT fk_post_forum   FOREIGN KEY (id_forum)  REFERENCES forum(id_forum)    ON DELETE CASCADE,
    CONSTRAINT fk_post_auteur  FOREIGN KEY (id_auteur) REFERENCES utilisateur(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE commentaire (
    id_commentaire   INT AUTO_INCREMENT PRIMARY KEY,
    contenu          TEXT NOT NULL,
    date_commentaire DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_post          INT NOT NULL,
    id_auteur        INT NOT NULL,
    KEY id_post (id_post),
    KEY id_auteur (id_auteur),
    CONSTRAINT fk_com_post    FOREIGN KEY (id_post)   REFERENCES post(id_post)    ON DELETE CASCADE,
    CONSTRAINT fk_com_auteur  FOREIGN KEY (id_auteur) REFERENCES utilisateur(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reaction (
    id_reaction    INT AUTO_INCREMENT PRIMARY KEY,
    type           ENUM('like','dislike') NOT NULL,
    id_post        INT DEFAULT NULL,
    id_commentaire INT DEFAULT NULL,
    id_utilisateur INT NOT NULL,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_reaction_post        (id_post, id_utilisateur),
    UNIQUE KEY unique_reaction_commentaire (id_commentaire, id_utilisateur),
    KEY id_utilisateur (id_utilisateur),
    CONSTRAINT fk_react_post    FOREIGN KEY (id_post)        REFERENCES post(id_post)            ON DELETE CASCADE,
    CONSTRAINT fk_react_com     FOREIGN KEY (id_commentaire) REFERENCES commentaire(id_commentaire) ON DELETE CASCADE,
    CONSTRAINT fk_react_user    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id)         ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 4. MODULE MEDICAMENTS / ORDONNANCES
-- ================================================================

CREATE TABLE medicaments (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(100) NOT NULL,
    description     TEXT NOT NULL,
    dosage          VARCHAR(50) NOT NULL,
    forme           VARCHAR(50) NOT NULL,
    fabricant       VARCHAR(100) NOT NULL,
    prix            DECIMAL(10,2) NOT NULL,
    stock           INT NOT NULL DEFAULT 0,
    date_expiration DATE DEFAULT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE ordonnances (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    numero          VARCHAR(25) NOT NULL UNIQUE,
    patient_nom     VARCHAR(100) NOT NULL,
    patient_age     INT DEFAULT NULL,
    patient_sexe    ENUM('M','F') DEFAULT NULL,
    date_ordonnance DATE NOT NULL,
    notes           TEXT DEFAULT NULL,
    medecin_id      INT DEFAULT NULL,
    patient_user_id INT DEFAULT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_med (medecin_id),
    KEY idx_pat (patient_user_id),
    CONSTRAINT fk_ord_medecin FOREIGN KEY (medecin_id)      REFERENCES utilisateur(id) ON DELETE SET NULL,
    CONSTRAINT fk_ord_patient FOREIGN KEY (patient_user_id) REFERENCES utilisateur(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE ordonnance_lignes (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    ordonnance_id INT NOT NULL,
    medicament_id INT NOT NULL,
    posologie     VARCHAR(255) NOT NULL,
    duree         VARCHAR(100) DEFAULT NULL,
    quantite      INT NOT NULL DEFAULT 1,
    KEY fk_ol_ordonnance (ordonnance_id),
    KEY fk_ol_medicament (medicament_id),
    CONSTRAINT fk_ol_ordonnance FOREIGN KEY (ordonnance_id) REFERENCES ordonnances(id) ON DELETE CASCADE,
    CONSTRAINT fk_ol_medicament FOREIGN KEY (medicament_id) REFERENCES medicaments(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ================================================================
-- 5. MODULE PARAPHARMACIE
-- ================================================================

CREATE TABLE produits (
    id          INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference   VARCHAR(100) NOT NULL UNIQUE,
    nom         VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    prix        DECIMAL(10,3) NOT NULL DEFAULT 0.000,
    stock       INT NOT NULL DEFAULT 0,
    categorie   VARCHAR(120) NOT NULL,
    created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE commandes (
    id                INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id          VARCHAR(50) NOT NULL DEFAULT '',
    produit_id        INT(10) UNSIGNED NOT NULL,
    quantite          INT NOT NULL DEFAULT 1,
    prix_unitaire     DECIMAL(10,3) NOT NULL DEFAULT 0.000,
    total             DECIMAL(12,3) NOT NULL DEFAULT 0.000,
    nom_produit       VARCHAR(255) NOT NULL,
    mode_paiement     VARCHAR(50) NOT NULL,
    status            ENUM('En attente','Confirmee','Livree','Annulee') NOT NULL DEFAULT 'En attente',
    client_id         VARCHAR(80) NOT NULL,
    utilisateur_id    INT DEFAULT NULL,
    created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    adresse_livraison VARCHAR(255) DEFAULT NULL,
    latitude          DECIMAL(10,8) DEFAULT NULL,
    longitude         DECIMAL(11,8) DEFAULT NULL,
    KEY idx_produit_id (produit_id),
    KEY idx_order_id   (order_id),
    KEY idx_user       (utilisateur_id),
    CONSTRAINT fk_cmd_produit FOREIGN KEY (produit_id)     REFERENCES produits(id)    ON UPDATE CASCADE,
    CONSTRAINT fk_cmd_user    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ratings (
    id         INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    produit_id INT(10) UNSIGNED NOT NULL,
    client_id  VARCHAR(80) NOT NULL,
    rating     TINYINT(4) NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment    TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_client_produit (client_id, produit_id),
    KEY idx_produit_rating (produit_id),
    KEY idx_client_rating  (client_id),
    CONSTRAINT fk_ratings_produit FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- DONNEES DE BOOTSTRAP
-- ================================================================
-- Aucun compte demo : seul un administrateur de bootstrap est cree.
-- Mot de passe : Admin@2026
-- Une fois connecte, l'administrateur peut creer les autres comptes
-- ou les utilisateurs peuvent s'inscrire eux-memes via /modules/utilisateur.
-- ================================================================

INSERT INTO utilisateur (id, nom, prenom, email, mot_de_passe, telephone, statut_compte, role, date_creation) VALUES
(1, 'Admin', 'MediLink', 'admin@medilink.tn',
 '$2y$10$3yO92Fb88GhscpBQ.Dn9Xu0xyq/bt7Dj33FHlkVoB3pxRjEy.MNXa',
 '+216 70 000 000', 'Actif', 'Administrateur', NOW());

INSERT INTO administrateur (id) VALUES (1);

-- Catalogue forum (vide de posts, juste les rubriques)
INSERT INTO forum (id_forum, titre, description) VALUES
(1, 'Cardiologie',          'Discussions sur les maladies cardiovasculaires.'),
(2, 'Nutrition et Dietetique','Bonnes pratiques alimentaires et regimes adaptes.'),
(3, 'Sante Mentale',         'Sante psychologique, stress, anxiete.'),
(4, 'Pediatrie',             'Vaccinations, maladies infantiles, developpement.'),
(5, 'Dermatologie',          'Soins de la peau et traitements dermatologiques.');

-- Catalogue medicaments
INSERT INTO medicaments (id, nom, description, dosage, forme, fabricant, prix, stock) VALUES
(1, 'Doliprane',    'Antalgique pour douleur et fievre.', '500 mg', 'Comprime', 'Sanofi',   8.50, 120),
(2, 'Amoxicilline', 'Antibiotique large spectre.',         '1 g',    'Gelule',   'Biogaran',14.00,  85),
(3, 'Toplexil',     'Sirop pour toux seche.',              '0.33 mg/ml','Sirop', 'Sanofi',  11.90,  60),
(4, 'Ibuprofene',   'Anti-inflammatoire.',                 '400 mg', 'Comprime', 'Advil',    9.90, 200),
(5, 'Smecta',       'Troubles digestifs.',                 '3 g',    'Sachet',   'Ipsen',   12.20,  45),
(6, 'Spasfon',      'Antispasmodique.',                    '80 mg',  'Comprime', 'Teva',     6.50, 150),
(7, 'Augmentin',    'Antibiotique.',                       '875 mg', 'Comprime', 'GSK',     18.00,  40),
(8, 'Ventoline',    'Bronchodilatateur.',                  '100 mcg','Aerosol',  'GSK',     25.00,  30),
(9, 'Levothyrox',   'Hormone thyroidienne.',               '50 mcg', 'Comprime', 'Merck',    7.20, 100),
(10,'Kardegic',     'Antiagregant plaquettaire.',          '75 mg',  'Sachet',   'Sanofi',   5.80,  90);

-- Catalogue parapharmacie
INSERT INTO produits (id, reference, nom, description, prix, stock, categorie) VALUES
(1,'PHM-001','Doliprane Boite','Antalgique boite de 16',12.000,40,'Medicaments'),
(2,'PHM-002','Creme Hydratante','Soin visage hydratant',  50.000,25,'Soins visage'),
(3,'PHM-003','Serum Vitamine C','Complement antioxydant', 120.000,15,'Complements alimentaires'),
(4,'PHM-004','Shampoing Doux','Cuir chevelu sensible',    100.000,30,'Capillaire');

SET FOREIGN_KEY_CHECKS = 1;

-- ================================================================
-- FIN DU SCRIPT
-- Import :
--   mysql -u root medilinkintegration < medilinkintegration.sql
-- Connexion admin de bootstrap :
--   email    : admin@medilink.tn
--   password : Admin@2026
-- ================================================================
