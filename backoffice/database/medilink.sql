CREATE DATABASE IF NOT EXISTS medilink CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medilink;

DROP TABLE IF EXISTS medicaments;

CREATE TABLE medicaments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    dosage VARCHAR(50) NOT NULL,
    forme VARCHAR(50) NOT NULL,
    fabricant VARCHAR(100) NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO medicaments (nom, description, dosage, forme, fabricant, prix) VALUES
('Doliprane', 'Antalgique utilisé pour soulager la douleur et réduire la fièvre.', '500 mg', 'Comprimé', 'Sanofi', 8.50),
('Amoxicilline', 'Antibiotique utilisé dans le traitement de plusieurs infections bactériennes.', '1 g', 'Gélule', 'Biogaran', 14.00),
('Toplexil', 'Sirop destiné au soulagement de la toux sèche et des irritations légères.', '0.33 mg/ml', 'Sirop', 'Sanofi', 11.90),
('Ibuprofène', 'Anti-inflammatoire utilisé pour calmer la douleur et la fièvre.', '400 mg', 'Comprimé', 'Advil', 9.90),
('Smecta', 'Poudre orale utilisée pour soulager les troubles digestifs et les diarrhées.', '3 g', 'Sachet', 'Ipsen', 12.20);
