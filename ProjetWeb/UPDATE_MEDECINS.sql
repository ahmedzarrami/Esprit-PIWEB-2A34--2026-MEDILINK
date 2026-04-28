-- ============================================
-- Mettre à jour les médecins existants
-- ============================================

-- Option 1: Vider et réinsérer
DELETE FROM medecins;

INSERT INTO medecins (nom, specialite, email, telephone) VALUES
('Dr. Ahmed', 'Cardiologue', 'ahmed@clinic.fr', '01 23 45 67 89'),
('Dr. Sara', 'Dermatologue', 'sara@clinic.fr', '01 23 45 67 90'),
('Dr. Youssef', 'Dentiste', 'youssef@clinic.fr', '01 23 45 67 91');

-- Option 2: Si tu as déjà des rendez-vous, utilise UPDATE à la place (sans DELETE)
-- UPDATE medecins SET nom = 'Dr. Ahmed', specialite = 'Cardiologue', email = 'ahmed@clinic.fr' WHERE id = 1;
-- UPDATE medecins SET nom = 'Dr. Sara', specialite = 'Dermatologue', email = 'sara@clinic.fr' WHERE id = 2;
-- UPDATE medecins SET nom = 'Dr. Youssef', specialite = 'Dentiste', email = 'youssef@clinic.fr' WHERE id = 3;

-- Vérifier que les médecins sont corrects
SELECT * FROM medecins;
