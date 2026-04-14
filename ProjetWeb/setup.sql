-- ============================================
-- MediLink Database Setup
-- ============================================

-- Create medecins table
CREATE TABLE IF NOT EXISTS medecins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    specialite VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telephone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create rendezvous table
CREATE TABLE IF NOT EXISTS rendezvous (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medecin_id INT NOT NULL,
    date_rdv DATE NOT NULL,
    heure_rdv TIME NOT NULL,
    statut VARCHAR(50) DEFAULT 'confirmé',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medecin_id) REFERENCES medecins(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rdv (medecin_id, date_rdv, heure_rdv)
);

-- Create index for faster queries
CREATE INDEX idx_date_rdv ON rendezvous(date_rdv);
CREATE INDEX idx_medecin ON rendezvous(medecin_id);

-- Insert sample doctors (optional)
INSERT INTO medecins (nom, specialite, email, telephone) VALUES
('Dr. Ahmed', 'Cardiologue', 'ahmed@clinic.fr', '01 23 45 67 89'),
('Dr. Sara', 'Dermatologue', 'sara@clinic.fr', '01 23 45 67 90'),
('Dr. Youssef', 'Dentiste', 'youssef@clinic.fr', '01 23 45 67 91');
