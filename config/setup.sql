-- =====================================================
-- MediLink — Script de création de la base de données
-- Module : Forum, Posts, Commentaires
-- =====================================================

CREATE DATABASE IF NOT EXISTS medilink CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medilink;

-- =====================================================
-- TABLE : utilisateur
-- =====================================================
CREATE TABLE IF NOT EXISTS utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    statut_compte VARCHAR(20) DEFAULT 'actif',
    role ENUM('patient','professionnel','administrateur') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE : forum
-- =====================================================
CREATE TABLE IF NOT EXISTS forum (
    id_forum INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE : post
-- =====================================================
CREATE TABLE IF NOT EXISTS post (
    id_post INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT NOT NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_forum INT NOT NULL,
    id_auteur INT NOT NULL,
    FOREIGN KEY (id_forum) REFERENCES forum(id_forum) ON DELETE CASCADE,
    FOREIGN KEY (id_auteur) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE : commentaire
-- =====================================================
CREATE TABLE IF NOT EXISTS commentaire (
    id_commentaire INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT NOT NULL,
    date_commentaire DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_post INT NOT NULL,
    id_auteur INT NOT NULL,
    FOREIGN KEY (id_post) REFERENCES post(id_post) ON DELETE CASCADE,
    FOREIGN KEY (id_auteur) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DONNÉES DE DÉMONSTRATION
-- =====================================================

-- Utilisateurs (mot de passe = 'password' hashé)
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, telephone, role) VALUES
('Dupont', 'Jean', 'jean.dupont@medilink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0601020304', 'administrateur'),
('Martin', 'Sophie', 'sophie.martin@medilink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0611223344', 'professionnel'),
('Bernard', 'Lucas', 'lucas.bernard@medilink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0622334455', 'patient'),
('Leroy', 'Marie', 'marie.leroy@medilink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0633445566', 'professionnel'),
('Moreau', 'Pierre', 'pierre.moreau@medilink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0644556677', 'patient');

-- Forums
INSERT INTO forum (titre, description) VALUES
('Cardiologie', 'Discussions sur les maladies cardiovasculaires, la prévention, les traitements et le suivi cardiologique. Posez vos questions à nos spécialistes.'),
('Nutrition et Diététique', 'Échangez sur les bonnes pratiques alimentaires, les régimes adaptés aux pathologies et les conseils nutritionnels de nos diététiciens.'),
('Santé Mentale', 'Un espace bienveillant pour discuter de la santé psychologique, du stress, de l''anxiété et des approches thérapeutiques disponibles.'),
('Pédiatrie', 'Forum dédié à la santé des enfants : vaccinations, maladies infantiles, développement et conseils pour les parents.'),
('Dermatologie', 'Questions et discussions autour des problèmes de peau, des traitements dermatologiques et des soins quotidiens.');

-- Posts
INSERT INTO post (contenu, id_forum, id_auteur, date_publication) VALUES
('Bonjour à tous, je suis cardiologue et je souhaite partager quelques conseils essentiels pour maintenir une bonne santé cardiaque. Premièrement, l''activité physique régulière est fondamentale : au moins 30 minutes de marche rapide par jour peuvent réduire significativement les risques cardiovasculaires. Deuxièmement, surveillez votre alimentation en limitant les graisses saturées et le sel. N''hésitez pas à poser vos questions !', 1, 2, '2026-04-10 09:30:00'),
('J''ai récemment été diagnostiqué avec de l''hypertension. Mon médecin m''a prescrit un traitement, mais j''aimerais savoir si des changements de mode de vie peuvent également aider ? Quels aliments dois-je éviter ? Merci pour vos conseils.', 1, 3, '2026-04-10 14:15:00'),
('En tant que diététicienne, je recommande vivement le régime méditerranéen pour ses nombreux bienfaits sur la santé. Riche en fruits, légumes, poissons et huile d''olive, ce régime a prouvé scientifiquement ses effets positifs sur la longévité et la prévention des maladies chroniques. Je suis disponible pour répondre à vos questions sur l''alimentation adaptée.', 2, 4, '2026-04-11 10:00:00'),
('Quels sont les aliments à privilégier pour un enfant de 3 ans qui refuse de manger des légumes ? J''ai tout essayé et je suis un peu découragée. Des astuces de parents ou de professionnels seraient les bienvenues !', 2, 5, '2026-04-11 16:45:00'),
('La méditation de pleine conscience est un outil thérapeutique de plus en plus reconnu pour la gestion du stress et de l''anxiété. En tant que psychiatre, je la recommande régulièrement à mes patients en complément du suivi classique. Voici quelques exercices simples que vous pouvez pratiquer au quotidien...', 3, 2, '2026-04-12 08:20:00'),
('Je suis parent d''un enfant de 5 ans et j''ai des questions sur le calendrier vaccinal. Quels sont les vaccins obligatoires et ceux qui sont recommandés ? Y a-t-il des effets secondaires courants à surveiller ?', 4, 3, '2026-04-12 11:30:00'),
('Conseils pour protéger votre peau en été : utilisez une crème solaire SPF50 minimum, évitez l''exposition entre 12h et 16h, et hydratez-vous régulièrement. N''oubliez pas de vérifier régulièrement vos grains de beauté et de consulter un dermatologue en cas de changement suspect.', 5, 4, '2026-04-13 09:00:00');

-- Commentaires
INSERT INTO commentaire (contenu, id_post, id_auteur, date_commentaire) VALUES
('Merci docteur pour ces précieux conseils ! Je vais essayer d''intégrer plus d''activité physique dans mon quotidien. Est-ce que la natation est aussi bénéfique que la marche ?', 1, 3, '2026-04-10 10:45:00'),
('Absolument, la natation est excellente pour le cœur car c''est un exercice complet qui sollicite tout le corps sans impact sur les articulations. Je la recommande particulièrement.', 1, 2, '2026-04-10 11:20:00'),
('Pour l''hypertension, réduisez votre consommation de sel (moins de 5g/jour), augmentez les fruits et légumes, et pratiquez une activité physique régulière. Le régime DASH est particulièrement recommandé dans votre cas.', 2, 2, '2026-04-10 15:30:00'),
('Merci beaucoup pour cette réponse détaillée ! Je vais me renseigner sur le régime DASH.', 2, 3, '2026-04-10 16:00:00'),
('Le régime méditerranéen a vraiment changé ma vie. Depuis que je le suis, j''ai plus d''énergie et mes analyses sanguines se sont nettement améliorées. Merci pour vos conseils !', 3, 5, '2026-04-11 14:20:00'),
('Pour les enfants qui refusent les légumes, essayez de les incorporer dans des préparations ludiques : purées colorées, soupes mixées, gratins. La patience est la clé, il faut parfois présenter un aliment 15 fois avant qu''un enfant l''accepte.', 4, 4, '2026-04-11 18:00:00'),
('Je pratique la méditation depuis 3 mois et les résultats sont remarquables. Mon niveau de stress a considérablement diminué. Merci de promouvoir cette approche !', 5, 5, '2026-04-12 10:15:00'),
('Pourriez-vous recommander une application de méditation guidée pour les débutants ?', 5, 3, '2026-04-12 12:00:00'),
('Pour les vaccins à 5 ans, le rappel DTP (diphtérie, tétanos, poliomyélite) est obligatoire. Le ROR (rougeole, oreillons, rubéole) devrait déjà avoir été administré. Consultez votre pédiatre pour vérifier le carnet de santé.', 6, 4, '2026-04-12 14:00:00'),
('Super conseils pour l''été ! J''ajouterais qu''il est important de porter des vêtements anti-UV pour les enfants, surtout à la plage.', 7, 5, '2026-04-13 10:30:00');
