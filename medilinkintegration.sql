-- ================================================================
-- MediLink — Base de donnees integree (tous modules)
-- Branche : integration
-- ================================================================
-- Fusionne les schemas de :
--   * medilink.sql            (gestion_utilisateur)
--   * projetweb (1).sql       (gestionrdv)
--   * medilink (2).sql        (forum + medicaments + parapharmacie)
--
-- Le module gestion_utilisateur reste la source de verite pour la
-- table utilisateur. Les tables patients (zerofill) et medecins du
-- module RDV sont conservees telles quelles - le module RDV utilise
-- ses propres tables pour ne pas casser ses requetes existantes.
-- ================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS medilinkintegration;
CREATE DATABASE medilinkintegration
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE medilinkintegration;

-- ================================================================
-- 1. MODULE GESTION UTILISATEUR
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

-- Donnees demo : mot de passe = Pass@1234
INSERT INTO utilisateur (id, nom, prenom, email, mot_de_passe, telephone, statut_compte, role, date_creation) VALUES
(1,'Trabelsi','Sarra','sarra.t@medilink.tn','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','+216 22 345 678','Actif','Patient','2024-01-15 10:00:00'),
(2,'Mansouri','Dr. Karim','k.mansouri@medilink.tn','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','+216 71 234 567','Actif','Professionnel','2024-01-10 09:00:00'),
(3,'Jebali','Amine','a.jebali@medilink.tn','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','+216 55 123 456','En attente','Patient','2024-02-01 14:00:00'),
(4,'Khelifi','Dr. Nadia','n.khelifi@medilink.tn','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','+216 70 987 654','Actif','Professionnel','2023-11-20 08:00:00'),
(5,'Belhaj','Omar','o.belhaj@medilink.tn','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','+216 99 876 543','Suspendu','Patient','2023-12-05 16:00:00'),
(6,'Zahraoui','Fatma','f.zahraoui@medilink.tn','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','+216 23 456 789','Actif','Patient','2024-03-10 11:00:00'),
(7,'Hamouda','Dr. Youssef','y.hamouda@medilink.tn','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','+216 72 111 222','Actif','Professionnel','2024-01-28 13:00:00'),
(8,'Gharbi','Rim','r.gharbi@medilink.tn','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','+216 44 333 222','Inactif','Patient','2023-10-15 15:00:00'),
(9,'Admin','Systeme','admin@medilink.tn','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','+216 71 000 000','Actif','Administrateur','2023-01-01 00:00:00'),
(10,'Belhaj','Sarra','patient@medilink.tn','$2y$10$YEqMOqm5FXVHQ3Wn0CJfRuGJcFnVB4E1w8.yN3ImV6hVGelFmyPfG','+216 22 345 678','Actif','Patient','2024-01-15 10:00:00');

INSERT INTO patient (id, date_naissance, sexe, adresse, groupe_sanguin) VALUES
(1,'1992-05-14','F','12 Rue de la Republique, Tunis','A+'),
(3,NULL,'M','Sfax',NULL),
(5,NULL,'M',NULL,NULL),
(6,NULL,'F','La Marsa, Tunis',NULL),
(8,NULL,'F',NULL,NULL),
(10,'1995-07-22','F','24 Avenue Habib Bourguiba, Tunis','A+');

INSERT INTO professionnel_sante (id, specialite, numero_ordre, biographie) VALUES
(2,'Cardiologie','TN-MED-10245','Cardiologue avec 12 ans d''experience.'),
(4,'Pediatrie','TN-MED-08832','Pediatre specialisee en neonatologie.'),
(7,'Neurologie','TN-MED-05521',NULL);

INSERT INTO administrateur (id) VALUES (9);

-- ================================================================
-- 2. MODULE GESTION RENDEZ-VOUS
-- Tables independantes (patients zerofill + medecins) du module RDV.
-- ================================================================

CREATE TABLE patients (
    id              BIGINT(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(50)  NOT NULL,
    prenom          VARCHAR(50)  NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    motdepasse      VARCHAR(255) NOT NULL,
    telephone       VARCHAR(15)  NOT NULL,
    datedenaissance DATE         NOT NULL,
    sexe            ENUM('M','F') NOT NULL,
    adresse         VARCHAR(200) DEFAULT NULL,
    date_inscription DATETIME    DEFAULT CURRENT_TIMESTAMP,
    KEY idx_email (email),
    KEY idx_telephone (telephone)
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
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
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

INSERT INTO patients (id, nom, prenom, email, motdepasse, telephone, datedenaissance, sexe, adresse, date_inscription) VALUES
(1,'Martin','Jean','jean.martin@email.com','hashedpassword123','0612345678','1985-06-15','M','123 Rue de Paris, 75001 Paris','2026-04-17 18:09:54'),
(2,'Durand','Marie','marie.durand@email.com','hashedpassword456','0687654321','1990-03-22','F','456 Avenue de Lyon, 69000 Lyon','2026-04-17 18:09:54'),
(3,'Bernard','Pierre','pierre.bernard@email.com','hashedpassword789','0698765432','1988-11-05','M','789 Boulevard Marseille, 13000 Marseille','2026-04-17 18:09:54'),
(4,'Garnier','Sophie','sophie.garnier@email.com','hashedpassword012','0645678901','1992-09-18','F','321 Chemin Toulouse, 31000 Toulouse','2026-04-17 18:09:54');

INSERT INTO medecins (id, nom, specialite, email, adresse, ville, latitude, longitude, telephone, created_at) VALUES
(4,'Dr. Ahmed','Cardiologue','ahmed@clinic.fr','12 Avenue Habib Bourguiba, Tunis Centre','Tunis',36.8190000,10.1660000,'01 23 45 67 89','2026-04-14 20:02:23'),
(5,'Dr. Sara','Dermatologue','sara@clinic.fr','45 Rue de la Republique, Sfax Medina','Sfax',34.7406000,10.7603000,'01 23 45 67 90','2026-04-14 20:02:23'),
(6,'Dr. Youssef','Dentiste','youssef@clinic.fr','8 Boulevard 14 Janvier, Sousse','Sousse',35.8288000,10.6380000,'01 23 45 67 91','2026-04-14 20:02:23'),
(7,'Dr. Leila','Pediatre','leila@clinic.fr','23 Avenue Habib Bourguiba, Bizerte Centre','Bizerte',37.2744000,9.8739000,'01 23 45 67 92','2026-05-02 11:48:23'),
(8,'Dr. Karim','Generaliste','karim@clinic.fr','17 Rue de l''Independance, Monastir','Monastir',35.7643000,10.8113000,'01 23 45 67 93','2026-05-02 11:48:23'),
(9,'Dr. Fatma','Ophtalmologue','fatma@clinic.fr','5 Avenue Farhat Hached, Nabeul','Nabeul',36.4561000,10.7376000,'01 23 45 67 94','2026-05-02 11:48:23');

INSERT INTO rendezvous (id, medecin_id, patient_id, date_rdv, heure_rdv, statut, created_at) VALUES
(28,4,1,'2026-04-25','08:00:00','confirme','2026-04-19 19:13:10'),
(30,5,2,'2026-04-25','08:30:00','confirme','2026-04-19 19:14:46'),
(31,5,1,'2026-04-30','08:00:00','confirme','2026-04-19 19:16:00'),
(33,6,1,'2026-04-20','08:00:00','confirme','2026-04-19 19:25:46'),
(34,6,2,'2026-04-21','08:00:00','confirme','2026-04-20 21:42:27'),
(35,4,2,'2026-04-24','08:00:00','confirme','2026-04-20 22:07:43'),
(36,6,4,'2026-04-25','08:00:00','confirme','2026-04-20 22:08:43'),
(37,4,3,'2026-04-23','08:00:00','confirme','2026-04-21 14:17:14'),
(38,4,3,'2026-05-04','17:30:00','confirme','2026-04-21 18:50:36'),
(39,4,1,'2026-04-23','10:30:00','confirme','2026-04-21 18:51:05'),
(40,6,3,'2026-04-24','10:00:00','confirme','2026-04-21 22:24:24'),
(41,4,4,'2026-04-23','15:00:00','confirme','2026-04-21 23:25:08'),
(43,5,4,'2026-05-01','10:30:00','confirme','2026-04-21 23:58:34'),
(44,6,1,'2026-04-30','08:30:00','confirme','2026-04-25 17:26:07'),
(45,5,1,'2026-04-29','08:00:00','confirme','2026-04-26 22:57:46'),
(46,5,1,'2026-04-27','17:30:00','confirme','2026-04-26 22:58:22'),
(47,5,3,'2026-04-28','10:30:00','confirme','2026-04-26 23:03:20'),
(48,4,2,'2026-04-28','08:00:00','confirme','2026-04-27 11:51:23'),
(49,5,2,'2026-04-28','17:30:00','confirme','2026-04-27 11:51:34'),
(50,6,2,'2026-04-28','14:00:00','confirme','2026-04-27 11:51:46'),
(51,4,1,'2026-04-28','10:30:00','confirme','2026-04-27 11:52:13'),
(52,5,1,'2026-04-28','15:00:00','confirme','2026-04-27 11:52:22'),
(53,6,1,'2026-04-28','09:00:00','confirme','2026-04-27 11:52:33'),
(54,6,4,'2026-04-28','15:30:00','confirme','2026-04-27 11:54:11'),
(55,6,1,'2026-05-02','08:00:00','confirme','2026-04-28 21:15:54'),
(56,4,1,'2026-05-29','10:00:00','confirme','2026-04-29 08:53:01'),
(57,8,4,'2026-05-04','08:00:00','confirme','2026-05-02 11:53:36'),
(58,9,3,'2026-05-04','12:00:00','confirme','2026-05-02 14:29:29'),
(59,8,1,'2026-05-05','08:00:00','confirme','2026-05-05 17:03:42'),
(60,7,2,'2026-05-05','11:00:00','confirme','2026-05-05 17:05:19'),
(61,4,1,'2026-05-14','10:30:00','confirme','2026-05-12 11:21:16');

INSERT INTO fiche_patient (idfiche, rendezvous_id, groupsanguin, allergies, antecedents, notesGenerales, date_creation) VALUES
(3,33,'O+','Arachides','Asthme','Le patient est en bon etat general.','2026-04-20 23:43:40'),
(4,28,'O-','Penicilline','Hypertension','Examen cardiovasculaire stable.','2026-04-21 20:50:10'),
(5,43,'B+','Penicilline','Hypertension','Examen dermatologique satisfaisant.','2026-04-22 02:02:00'),
(6,31,'O+','','Acne legere.','Acne ; traitement local prescrit.','2026-04-22 11:42:02'),
(7,30,'AB+','Penicilline, pollen','Eczema dans l''enfance','Dermatite probable ; suivi conseille.','2026-04-25 19:50:31'),
(8,38,'B+','','','','2026-04-25 20:06:31'),
(9,58,'AB-','','Myopie legere','Vision floue ; contrôle de la vue recommande.','2026-05-05 19:10:10');

INSERT INTO evaluations (id, patient_id, medecin_id, rendezvous_id, note, commentaire, date_eval) VALUES
(1,1,4,28,4,NULL,'2026-04-26 20:05:10'),
(2,1,6,33,5,NULL,'2026-04-26 20:05:27'),
(3,4,6,36,4,NULL,'2026-04-26 20:11:46'),
(4,4,4,41,3,NULL,'2026-04-26 20:11:51'),
(5,2,5,30,1,'Tres mauvaise experience.','2026-04-26 20:17:42'),
(6,2,4,35,3,NULL,'2026-04-26 20:18:01'),
(7,2,6,34,4,NULL,'2026-04-26 20:18:07'),
(8,1,4,39,5,NULL,'2026-04-27 00:53:21'),
(9,3,6,40,3,NULL,'2026-04-27 01:02:39'),
(10,3,4,37,5,NULL,'2026-04-27 01:02:43'),
(11,3,5,47,3,NULL,'2026-04-29 11:05:01'),
(12,1,5,52,4,NULL,'2026-04-29 16:47:05'),
(13,1,5,46,5,NULL,'2026-04-29 16:48:16'),
(14,1,8,59,3,NULL,'2026-05-06 11:35:20');

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

INSERT INTO forum (id_forum, titre, description, created_at) VALUES
(1,'Cardiologie','Discussions sur les maladies cardiovasculaires.','2026-05-10 21:26:48'),
(2,'Nutrition et Dietetique','Bonnes pratiques alimentaires et regimes adaptes.','2026-05-10 21:26:48'),
(3,'Sante Mentale','Sante psychologique, stress, anxiete.','2026-05-10 21:26:48'),
(4,'Pediatrie','Vaccinations, maladies infantiles, developpement.','2026-05-10 21:26:48'),
(5,'Dermatologie','Soins de la peau et traitements dermatologiques.','2026-05-10 21:26:48');

INSERT INTO post (id_post, contenu, date_publication, id_forum, id_auteur) VALUES
(1,'Bonjour, je souffre de tachycardie depuis quelques semaines. Quels signes doivent m''alerter ?','2026-05-10 21:26:48',1,3),
(2,'Une alimentation riche en omega-3 peut aider a reduire les inflammations.','2026-05-10 21:26:48',2,2),
(3,'Je traverse une periode de burn-out. Quelles techniques de gestion du stress recommandez-vous ?','2026-05-10 21:26:48',3,5),
(4,'Mon enfant de 3 ans a de la fievre depuis 48h. Quand consulter en urgence ?','2026-05-10 21:26:48',4,3),
(5,'J''ai une eruption cutanee depuis une semaine. Allergie alimentaire ?','2026-05-10 21:26:48',5,5),
(6,'Quels aliments eviter en cas d''hypertension ?','2026-05-10 21:26:48',1,4),
(7,'Comment introduire les aliments solides chez un bebe de 6 mois ?','2026-05-10 21:26:48',4,1);

INSERT INTO commentaire (id_commentaire, contenu, date_commentaire, id_post, id_auteur) VALUES
(1,'La tachycardie peut avoir plusieurs causes. Consultez un cardiologue rapidement.','2026-05-10 21:26:48',1,2),
(2,'Le regime mediterraneen est excellent.','2026-05-10 21:26:48',2,4),
(3,'La meditation de pleine conscience aide beaucoup.','2026-05-10 21:26:48',3,2),
(4,'Si la fievre depasse 39C et persiste, consultez sans tarder.','2026-05-10 21:26:48',4,2),
(5,'Eliminez les aliments suspects un par un.','2026-05-10 21:26:48',5,4),
(6,'Reduire le sel est essentiel.','2026-05-10 21:26:48',6,2),
(7,'Commencez par des purees de legumes simples.','2026-05-10 21:26:48',7,4),
(8,'La consultation cardiologique est indispensable.','2026-05-10 21:26:48',1,1),
(9,'Les complements en magnesium aident contre le stress.','2026-05-10 21:26:48',3,4),
(10,'Prenez la temperature rectale chez les nourrissons.','2026-05-10 21:26:48',4,1);

INSERT INTO reaction (id_reaction, type, id_post, id_commentaire, id_utilisateur, created_at) VALUES
(1,'dislike',2,NULL,1,'2026-05-12 12:22:34'),
(2,'dislike',3,NULL,8,'2026-05-12 14:05:39');

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
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
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

INSERT INTO medicaments (id, nom, description, dosage, forme, fabricant, prix, stock, date_expiration, created_at) VALUES
(1,'Doliprane','Antalgique pour douleur et fievre.','500 mg','Comprime','Sanofi',8.50,120,NULL,'2026-04-28 20:18:02'),
(2,'Amoxicilline','Antibiotique large spectre.','1 g','Gelule','Biogaran',14.00,85,NULL,'2026-04-28 20:18:02'),
(3,'Toplexil','Sirop pour toux seche.','0.33 mg/ml','Sirop','Sanofi',11.90,60,NULL,'2026-04-28 20:18:02'),
(4,'Ibuprofene','Anti-inflammatoire.','400 mg','Comprime','Advil',9.90,200,NULL,'2026-04-28 20:18:02'),
(5,'Smecta','Troubles digestifs.','3 g','Sachet','Ipsen',12.20,45,NULL,'2026-04-28 20:18:02'),
(6,'Spasfon','Antispasmodique.','80 mg','Comprime','Teva',6.50,150,NULL,'2026-04-28 20:18:02'),
(7,'Augmentin','Antibiotique.','875 mg','Comprime','GSK',18.00,40,NULL,'2026-04-28 20:18:02'),
(8,'Ventoline','Bronchodilatateur.','100 mcg','Aerosol','GSK',25.00,30,NULL,'2026-04-28 20:18:02'),
(9,'Levothyrox','Hormone thyroidienne.','50 mcg','Comprime','Merck',7.20,100,NULL,'2026-04-28 20:18:02'),
(10,'Kardegic','Antiagregant plaquettaire.','75 mg','Sachet','Sanofi',5.80,90,NULL,'2026-04-28 20:18:02');

INSERT INTO ordonnances (id, numero, patient_nom, patient_age, patient_sexe, date_ordonnance, notes, created_at) VALUES
(1,'ORD-69F1C82BE7CCA','Ahmed Z.',30,'M','2026-04-29','Cure courte','2026-04-29 09:58:19'),
(2,'ORD-6A024F1F3556A','Sarra T.',65,'F','2026-05-11','Surveillance','2026-05-11 22:50:23');

INSERT INTO ordonnance_lignes (id, ordonnance_id, medicament_id, posologie, duree, quantite) VALUES
(1,1,2,'matin','4 jours',1),
(2,2,7,'matin','1 semaine',1);

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
    created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    adresse_livraison VARCHAR(255) DEFAULT NULL,
    latitude          DECIMAL(10,8) DEFAULT NULL,
    longitude         DECIMAL(11,8) DEFAULT NULL,
    KEY idx_produit_id (produit_id),
    KEY idx_order_id   (order_id),
    CONSTRAINT fk_cmd_produit FOREIGN KEY (produit_id) REFERENCES produits(id) ON UPDATE CASCADE
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

INSERT INTO produits (id, reference, nom, description, prix, stock, categorie, created_at, updated_at) VALUES
(1,'PHM-001','Doliprane Boite','Antalgique boite de 16',12.000,40,'Medicaments','2026-04-14 18:43:46','2026-04-14 18:49:28'),
(2,'PHM-002','Creme Hydratante','Soin visage hydratant',50.000,25,'Soins visage','2026-04-14 18:53:26','2026-04-14 18:53:26'),
(3,'PHM-003','Serum Vitamine C','Complement antioxydant',120.000,15,'Complements alimentaires','2026-04-15 10:08:47','2026-04-15 10:08:47'),
(4,'PHM-004','Shampoing Doux','Cuir chevelu sensible',100.000,30,'Capillaire','2026-04-15 10:51:23','2026-04-15 10:51:23');

INSERT INTO commandes (id, order_id, produit_id, quantite, prix_unitaire, total, nom_produit, mode_paiement, status, client_id, created_at, updated_at, adresse_livraison) VALUES
(1,'ORD-001',1,2,12.000,24.000,'Doliprane Boite','paypal','En attente','CLT_DEMO_001','2026-05-01 10:00:00','2026-05-01 10:00:00',NULL),
(2,'ORD-002',2,1,50.000,50.000,'Creme Hydratante','carte_bancaire','Confirmee','CLT_DEMO_001','2026-05-02 14:00:00','2026-05-02 14:00:00','Tunis'),
(3,'ORD-003',3,3,120.000,360.000,'Serum Vitamine C','virement','Livree','CLT_DEMO_002','2026-05-03 09:00:00','2026-05-03 09:00:00','Sfax'),
(4,'ORD-004',4,1,100.000,100.000,'Shampoing Doux','especes','En attente','CLT_DEMO_002','2026-05-04 16:00:00','2026-05-04 16:00:00','Sousse');

INSERT INTO ratings (id, produit_id, client_id, rating, comment, created_at) VALUES
(1,1,'CLT_DEMO_001',4,'Tres efficace','2026-05-05 12:00:00'),
(2,3,'CLT_DEMO_002',5,'Excellent serum','2026-05-06 10:00:00');

SET FOREIGN_KEY_CHECKS = 1;

-- ================================================================
-- FIN DU SCRIPT
-- Pour utiliser :
--   mysql -u root medilinkintegration < medilinkintegration.sql
-- Ou via phpMyAdmin : Importer ce fichier
-- ================================================================
