-- ================================================
-- MediLink — Script de création de la base de données
-- Module : Gestion des Utilisateurs
-- ================================================

CREATE DATABASE IF NOT EXISTS medilink
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE medilink;

-- ================================================
-- Table principale : utilisateur
-- ================================================
CREATE TABLE IF NOT EXISTS utilisateur (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(100)  NOT NULL,
    prenom          VARCHAR(100)  NOT NULL,
    email           VARCHAR(255)  NOT NULL UNIQUE,
    mot_de_passe    VARCHAR(255)  NOT NULL,
    telephone       VARCHAR(20)   NOT NULL,
    statut_compte   VARCHAR(20)   NOT NULL DEFAULT 'Actif',
    role            VARCHAR(30)   NOT NULL,
    date_creation   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_role CHECK (role IN ('Patient', 'Professionnel', 'Administrateur')),
    CONSTRAINT chk_statut CHECK (statut_compte IN ('Actif', 'Inactif', 'Suspendu', 'En attente'))
) ENGINE=InnoDB;

-- ================================================
-- Table enfant : patient
-- ================================================
CREATE TABLE IF NOT EXISTS patient (
    id              INT PRIMARY KEY,
    date_naissance  DATE          DEFAULT NULL,
    sexe            VARCHAR(1)    DEFAULT NULL,
    adresse         VARCHAR(255)  DEFAULT NULL,
    groupe_sanguin  VARCHAR(5)    DEFAULT NULL,
    CONSTRAINT fk_patient_utilisateur
        FOREIGN KEY (id) REFERENCES utilisateur(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ================================================
-- Table enfant : professionnel_sante
-- ================================================
CREATE TABLE IF NOT EXISTS professionnel_sante (
    id              INT PRIMARY KEY,
    specialite      VARCHAR(100)  NOT NULL,
    numero_ordre    VARCHAR(50)   NOT NULL,
    biographie      TEXT          DEFAULT NULL,
    CONSTRAINT fk_pro_utilisateur
        FOREIGN KEY (id) REFERENCES utilisateur(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ================================================
-- Table enfant : administrateur
-- ================================================
CREATE TABLE IF NOT EXISTS administrateur (
    id              INT PRIMARY KEY,
    CONSTRAINT fk_admin_utilisateur
        FOREIGN KEY (id) REFERENCES utilisateur(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ================================================
-- Données de démonstration
-- ================================================

-- Mot de passe : Pass@1234 (hashé avec password_hash)
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, telephone, statut_compte, role, date_creation) VALUES
('Trabelsi', 'Sarra', 'sarra.t@medilink.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 22 345 678', 'Actif', 'Patient', '2024-01-15 10:00:00'),
('Mansouri', 'Dr. Karim', 'k.mansouri@medilink.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 71 234 567', 'Actif', 'Professionnel', '2024-01-10 09:00:00'),
('Jebali', 'Amine', 'a.jebali@medilink.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 55 123 456', 'En attente', 'Patient', '2024-02-01 14:00:00'),
('Khelifi', 'Dr. Nadia', 'n.khelifi@medilink.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 70 987 654', 'Actif', 'Professionnel', '2023-11-20 08:00:00'),
('Belhaj', 'Omar', 'o.belhaj@medilink.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 99 876 543', 'Suspendu', 'Patient', '2023-12-05 16:00:00'),
('Zahraoui', 'Fatma', 'f.zahraoui@medilink.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 23 456 789', 'Actif', 'Patient', '2024-03-10 11:00:00'),
('Hamouda', 'Dr. Youssef', 'y.hamouda@medilink.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 72 111 222', 'Actif', 'Professionnel', '2024-01-28 13:00:00'),
('Gharbi', 'Rim', 'r.gharbi@medilink.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 44 333 222', 'Inactif', 'Patient', '2023-10-15 15:00:00'),
('Admin', 'Système', 'admin@medilink.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 71 000 000', 'Actif', 'Administrateur', '2023-01-01 00:00:00'),
('Belhaj', 'Sarra', 'patient@medilink.tn', '$2y$10$YEqMOqm5FXVHQ3Wn0CJfRuGJcFnVB4E1w8.yN3ImV6hVGelFmyPfG', '+216 22 345 678', 'Actif', 'Patient', '2024-01-15 10:00:00');

-- Patients
INSERT INTO patient (id, date_naissance, sexe, adresse, groupe_sanguin) VALUES
(1, '1992-05-14', 'F', '12 Rue de la République, Tunis', 'A+'),
(3, NULL, 'M', 'Sfax', NULL),
(5, NULL, 'M', NULL, NULL),
(6, NULL, 'F', 'La Marsa, Tunis', NULL),
(8, NULL, 'F', NULL, NULL),
(10, '1995-07-22', 'F', '24 Avenue Habib Bourguiba, Tunis', 'A+');

-- Professionnels
INSERT INTO professionnel_sante (id, specialite, numero_ordre, biographie) VALUES
(2, 'Cardiologie', 'TN-MED-10245', 'Cardiologue avec 12 ans d''expérience.'),
(4, 'Pédiatrie', 'TN-MED-08832', 'Pédiatre spécialisée en néonatologie.'),
(7, 'Neurologie', 'TN-MED-05521', NULL);

-- Administrateur
INSERT INTO administrateur (id) VALUES (9);
