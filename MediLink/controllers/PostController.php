<?php
/**
 * PostController — Contrôleur MVC pour la gestion des posts
 * Gère les actions Front Office et Back Office
 */
class PostController {

    // ===== FRONT OFFICE =====

    /**
     * Afficher un post avec ses commentaires (Front Office)
     * @param int $id
     */
    public function show($id): void {
        $pdo = Database::getConnection();
        
        $stmtPost = $pdo->prepare("
            SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role
            FROM post p
            JOIN utilisateur u ON p.id_auteur = u.id
            WHERE p.id_post = :id
        ");
        $stmtPost->execute([':id' => $id]);
        $post = $stmtPost->fetch();

        if (!$post) {
            header('Location: /medilink_medicament/MediLink/index.php?module=forum&controller=forum&action=list');
            exit;
        }

        // Filtre de mots injurieux
        $post['contenu'] = BadWordsFilter::filter($post['contenu']);

        // Réactions (Like/Dislike) pour le post
        $post['likes'] = 0;
        $post['dislikes'] = 0;
        $post['user_reaction'] = null;
        try {
            $stmtReactPost = $pdo->prepare("SELECT type, COUNT(*) as count FROM reaction WHERE id_post = :id GROUP BY type");
            $stmtReactPost->execute([':id' => $id]);
            $reactPostRaw = $stmtReactPost->fetchAll(PDO::FETCH_KEY_PAIR);
            $post['likes'] = $reactPostRaw['like'] ?? 0;
            $post['dislikes'] = $reactPostRaw['dislike'] ?? 0;
            
            if (isset($_SESSION['user'])) {
                $stmtUserReact = $pdo->prepare("SELECT type FROM reaction WHERE id_post = :id AND id_utilisateur = :id_user");
                $stmtUserReact->execute([':id' => $id, ':id_user' => $_SESSION['user']['id']]);
                $userReact = $stmtUserReact->fetch();
                if ($userReact) {
                    $post['user_reaction'] = $userReact['type'];
                }
            }
        } catch (PDOException $e) {
            // Ignore si la table n'existe pas encore
        }

        $stmtCom = $pdo->prepare("
            SELECT c.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role
            FROM commentaire c
            JOIN utilisateur u ON c.id_auteur = u.id
            WHERE c.id_post = :id_post
            ORDER BY c.date_commentaire ASC
        ");
        $stmtCom->execute([':id_post' => $id]);
        $commentaires = $stmtCom->fetchAll();

        // Réactions et Filtres pour les commentaires
        $comIds = array_column($commentaires, 'id_commentaire');
        $comReactions = [];
        $userComReactions = [];
        
        try {
            if (!empty($comIds)) {
                $inQuery = implode(',', array_fill(0, count($comIds), '?'));
                
                $stmtReactCom = $pdo->prepare("SELECT id_commentaire, type, COUNT(*) as count FROM reaction WHERE id_commentaire IN ($inQuery) GROUP BY id_commentaire, type");
                $stmtReactCom->execute($comIds);
                while ($row = $stmtReactCom->fetch()) {
                    $comReactions[$row['id_commentaire']][$row['type']] = $row['count'];
                }
                
                if (isset($_SESSION['user'])) {
                    $params = $comIds;
                    $params[] = $_SESSION['user']['id'];
                    $stmtUserReactCom = $pdo->prepare("SELECT id_commentaire, type FROM reaction WHERE id_commentaire IN ($inQuery) AND id_utilisateur = ?");
                    $stmtUserReactCom->execute($params);
                    while ($row = $stmtUserReactCom->fetch()) {
                        $userComReactions[$row['id_commentaire']] = $row['type'];
                    }
                }
            }
        } catch (PDOException $e) {}

        foreach ($commentaires as &$c) {
            $c['contenu'] = BadWordsFilter::filter($c['contenu']);
            $c['likes'] = $comReactions[$c['id_commentaire']]['like'] ?? 0;
            $c['dislikes'] = $comReactions[$c['id_commentaire']]['dislike'] ?? 0;
            $c['user_reaction'] = $userComReactions[$c['id_commentaire']] ?? null;
        }
        unset($c);

        $stmtForum = $pdo->prepare("SELECT * FROM forum WHERE id_forum = :id");
        $stmtForum->execute([':id' => $post['id_forum']]);
        $dataForum = $stmtForum->fetch();
        
        // Le view s'attend à un objet avec getTitre()
        $forum = new Forum(
            $dataForum['id_forum'] ?? null,
            $dataForum['titre'] ?? '',
            $dataForum['description'] ?? null,
            $dataForum['created_at'] ?? null
        );

        require __DIR__ . '/../views/front/post/show.php';
    }

    /**
     * Créer un nouveau post (Front Office — professionnels uniquement)
     */
    public function create(): void {
        $idForum = $_GET['id_forum'] ?? null;
        if (!$idForum) {
            header('Location: /medilink_medicament/MediLink/index.php?module=forum&controller=forum&action=list');
            exit;
        }

        $pdo = Database::getConnection();
        $stmtForum = $pdo->prepare("SELECT * FROM forum WHERE id_forum = :id");
        $stmtForum->execute([':id' => $idForum]);
        $dataForum = $stmtForum->fetch();

        if (!$dataForum) {
            header('Location: /medilink_medicament/MediLink/index.php?module=forum&controller=forum&action=list');
            exit;
        }
        
        $forum = new Forum(
            $dataForum['id_forum'],
            $dataForum['titre'],
            $dataForum['description'],
            $dataForum['created_at']
        );

        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenu = trim($_POST['contenu'] ?? '');
            $idAuteur = $_SESSION['user']['id'] ?? 0;

            // Validation côté serveur
            if (empty($contenu)) {
                $errors[] = "Le contenu du post est obligatoire.";
            }
            if (strlen($contenu) < 10) {
                $errors[] = "Le contenu doit contenir au moins 10 caractères.";
            }
            if (strlen($contenu) > 5000) {
                $errors[] = "Le contenu ne doit pas dépasser 5000 caractères.";
            }

            if (empty($errors)) {
                $stmtIns = $pdo->prepare("INSERT INTO post (contenu, id_forum, id_auteur) VALUES (:contenu, :id_forum, :id_auteur)");
                $result = $stmtIns->execute([
                    ':contenu'   => $contenu,
                    ':id_forum'  => $idForum,
                    ':id_auteur' => $idAuteur
                ]);
                
                if ($result) {
                    header('Location: /medilink_medicament/MediLink/index.php?module=forum&controller=forum&action=show&id=' . $idForum);
                    exit;
                } else {
                    $errors[] = "Erreur lors de la publication du post.";
                }
            }
        }

        require __DIR__ . '/../views/front/post/create.php';
    }

    /**
     * Gérer les réactions (Like/Dislike) via AJAX
     */
    public function react(): void {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $type = $input['type'] ?? '';
        $idPost = $input['id_post'] ?? null;
        
        if (!in_array($type, ['like', 'dislike']) || !$idPost) {
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            exit;
        }
        
        $idUser = $_SESSION['user']['id'];
        $pdo = Database::getConnection();
        
        try {
            $stmtCheck = $pdo->prepare("SELECT type FROM reaction WHERE id_post = :id_post AND id_utilisateur = :id_user");
            $stmtCheck->execute([':id_post' => $idPost, ':id_user' => $idUser]);
            $existing = $stmtCheck->fetch();
            
            if ($existing) {
                if ($existing['type'] === $type) {
                    $stmtDel = $pdo->prepare("DELETE FROM reaction WHERE id_post = :id_post AND id_utilisateur = :id_user");
                    $stmtDel->execute([':id_post' => $idPost, ':id_user' => $idUser]);
                    $action = 'removed';
                } else {
                    $stmtUpd = $pdo->prepare("UPDATE reaction SET type = :type WHERE id_post = :id_post AND id_utilisateur = :id_user");
                    $stmtUpd->execute([':type' => $type, ':id_post' => $idPost, ':id_user' => $idUser]);
                    $action = 'updated';
                }
            } else {
                $stmtIns = $pdo->prepare("INSERT INTO reaction (type, id_post, id_utilisateur) VALUES (:type, :id_post, :id_user)");
                $stmtIns->execute([':type' => $type, ':id_post' => $idPost, ':id_user' => $idUser]);
                $action = 'added';
            }
            
            $stmtCounts = $pdo->prepare("SELECT type, COUNT(*) as count FROM reaction WHERE id_post = :id GROUP BY type");
            $stmtCounts->execute([':id' => $idPost]);
            $counts = $stmtCounts->fetchAll(PDO::FETCH_KEY_PAIR);
            
            echo json_encode([
                'success' => true,
                'action' => $action,
                'likes' => $counts['like'] ?? 0,
                'dislikes' => $counts['dislike'] ?? 0
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur BDD: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Métier avancé : Analyser un post (Décrire) via AJAX
     */
    public function describeAjax(): void {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $idPost = $input['id_post'] ?? null;
        
        if (!$idPost) {
            echo json_encode(['success' => false, 'message' => 'Post invalide']);
            exit;
        }
        
        $pdo = Database::getConnection();
        
        try {
            $stmt = $pdo->prepare("SELECT contenu, date_publication FROM post WHERE id_post = :id");
            $stmt->execute([':id' => $idPost]);
            $post = $stmt->fetch();
            
            if (!$post) {
                echo json_encode(['success' => false, 'message' => 'Post non trouvé']);
                exit;
            }
            
            // Clean html tags to analyze text only
            $text = strip_tags($post['contenu']);
            
            // Count words and chars
            $wordCount = str_word_count($text);
            $charCount = mb_strlen($text);
            
            // Reading time (approx 200 words per min)
            $readingTime = ceil($wordCount / 200);
            
            // Extract keywords (words with more than 4 chars)
            $words = str_word_count(strtolower($text), 1);
            $filteredWords = array_filter($words, function($w) {
                return mb_strlen($w) > 4;
            });
            $wordFreq = array_count_values($filteredWords);
            arsort($wordFreq);
            $topKeywords = array_slice(array_keys($wordFreq), 0, 5);
            
            // Engagement metrics
            $stmtReact = $pdo->prepare("SELECT type, COUNT(*) as count FROM reaction WHERE id_post = :id GROUP BY type");
            $stmtReact->execute([':id' => $idPost]);
            $reactions = $stmtReact->fetchAll(PDO::FETCH_KEY_PAIR);
            $likes = $reactions['like'] ?? 0;
            $dislikes = $reactions['dislike'] ?? 0;
            
            $stmtCom = $pdo->prepare("SELECT COUNT(*) as total FROM commentaire WHERE id_post = :id");
            $stmtCom->execute([':id' => $idPost]);
            $comments = $stmtCom->fetch()['total'];
            
            // Calculate Quality / Engagement Score (0 to 100)
            $score = 50; // Base score
            if ($wordCount > 50) $score += 10;
            if ($wordCount > 100) $score += 10;
            $score += ($likes * 5);
            $score -= ($dislikes * 5);
            $score += ($comments * 10);
            $score = max(0, min(100, $score)); // limit to 0-100
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'word_count' => $wordCount,
                    'char_count' => $charCount,
                    'reading_time' => $readingTime,
                    'keywords' => $topKeywords,
                    'score' => $score,
                    'engagement' => [
                        'likes' => $likes,
                        'dislikes' => $dislikes,
                        'comments' => $comments
                    ]
                ]
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Assistant Médical : Analyser les discussions ou répondre à une question via Gemini AI
     */
    public function analyzeAssistantAjax(): void {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $idPost = $input['id_post'] ?? null;
        $userPrompt = $input['prompt'] ?? null;
        
        if (!$idPost) {
            echo json_encode(['success' => false, 'message' => 'Post invalide']);
            exit;
        }
        
        $pdo = Database::getConnection();
        
        try {
            if ($userPrompt) {
                // Cas 1 : Chat direct (Question de l'utilisateur) - AUCUNE LIMITE
                $system = "Tu es l'assistant intelligent officiel du site web MediLink.
                Tu as l'autorisation de répondre à TOUTES les questions de l'utilisateur sans aucune limite ou restriction.
                Tu dois l'aider avec tout ce qui concerne le site web, ses fonctionnalités, ou n'importe quel autre sujet qu'il aborde.
                Sois très amical, ouvert, et réponds toujours en Français.";
                
                $response = $this->callAIAPI($userPrompt, $system);
                
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'type' => 'chat',
                        'response' => $response
                    ]
                ]);
            } else {
                // Cas 2 : Analyse globale du post et des commentaires
                $stmt = $pdo->prepare("SELECT contenu FROM commentaire WHERE id_post = :id");
                $stmt->execute([':id' => $idPost]);
                $commentaires = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (empty($commentaires)) {
                    echo json_encode([
                        'success' => true,
                        'data' => [
                            'type' => 'analysis',
                            'response' => "Il n'y a pas encore de commentaires à analyser. Soyez le premier à engager la discussion ou posez-moi une question directement !"
                        ]
                    ]);
                    exit;
                }

                $allText = implode("\n---\n", $commentaires);
                $system = "Tu es un expert médical analysant une discussion sur un forum. 
                Voici une liste de commentaires. Fais-en un résumé synthétique, identifie les points de vue ou préoccupations majeures des patients, et fournis un mini-glossaire si des termes techniques sont utilisés.
                Sois concis et utilise un ton rassurant. Réponds en Français.";
                
                $response = $this->callAIAPI("Voici les commentaires du post à analyser :\n" . $allText, $system);

                echo json_encode([
                    'success' => true,
                    'data' => [
                        'type' => 'analysis',
                        'response' => $response,
                        'nb_comments' => count($commentaires)
                    ]
                ]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Appeler l'API OpenRouter (IA Gratuite et Accessible)
     * @param string $prompt
     * @param string $systemInstruction
     * @return string
     */
    private function callAIAPI(string $prompt, string $systemInstruction = ""): string {
        $configFile = __DIR__ . '/../config/ai_config.php';
        if (!file_exists($configFile)) {
            return $this->simulateMedicalAssistant($prompt);
        }
        
        $config = require $configFile;
        $apiKey = $config['openrouter_api_key'] ?? '';
        
        if (!$apiKey || $apiKey === 'YOUR_OPENROUTER_API_KEY_HERE') {
            return $this->simulateMedicalAssistant($prompt);
        }

        $url = "https://openrouter.ai/api/v1/chat/completions";

        // Liste de modèles gratuits, du plus rapide au plus complexe en fallback
        $models = [
            "meta-llama/llama-3.2-3b-instruct:free",
            "google/gemma-4-31b-it:free",
            "openrouter/free"
        ];

        foreach ($models as $model) {
            $data = [
                "model" => $model,
                "messages" => [
                    ["role" => "system", "content" => $systemInstruction],
                    ["role" => "user", "content" => $prompt]
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
                'HTTP-Referer: http://localhost/medilink2', 
                'X-Title: MediLink Assistant'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_ENCODING, ""); // Supporte GZIP
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200) {
                $result = json_decode($response, true);
                $text = $result['choices'][0]['message']['content'] ?? null;
                if ($text) {
                    return $text;
                } else {
                    return "*(Erreur Technique)* : La réponse de l'IA n'a pas pu être lue correctement.";
                }
            }
        }

        // Si aucun modèle n'a fonctionné, on affiche l'erreur du dernier
        $errorData = json_decode($response, true);
        $msg = $errorData['error']['message'] ?? "Erreur HTTP $httpCode ou timeout sur tous les modèles. Réponse brute : " . htmlspecialchars($response);
        return "*(Erreur API)* : " . $msg . "\n\nEn attendant, voici une réponse simulée : \n\n" . $this->simulateMedicalAssistant($prompt);
    }

    /**
     * Mode Simulation (Gratuit & Facile) : Fournit des réponses intelligentes basées sur des mots-clés
     * Utilisé comme fallback si la clé API est absente ou invalide.
     */
    private function simulateMedicalAssistant(string $prompt): string {
        $prompt = strtolower($prompt);
        
        // Simulation d'un délai de réflexion pour le réalisme
        usleep(500000); 

        // Base de connaissances simplifiée
        $responses = [
            'doliprane' => "Le **Doliprane** (Paracétamol) est un médicament utilisé pour soulager la douleur et faire baisser la fièvre. La dose standard pour un adulte est de 500mg à 1g toutes les 6 heures. **Attention** : Ne dépassez jamais 4g par jour pour éviter des dommages au foie.",
            'paracétamol' => "Le **Paracétamol** est la molécule active du Doliprane ou de l'Efferalgan. Il est efficace contre les douleurs légères et la fièvre. Respectez toujours un intervalle de 4 à 6 heures entre deux prises.",
            'grippe' => "La **grippe** est une infection virale. Reposez-vous, hydratez-vous abondamment et surveillez votre température. Si vous avez des difficultés respiratoires ou une fièvre persistante au-delà de 3 jours, consultez un médecin.",
            'rhume' => "Le **rhume** guérit généralement seul en une semaine. Nettoyez votre nez avec du sérum physiologique et restez au chaud. Si les sécrétions deviennent colorées ou si vous avez mal aux oreilles, consultez.",
            'tension' => "L'**hypertension** artérielle est souvent silencieuse. Il est recommandé de la mesurer au repos. Une tension normale se situe autour de 12/8. Une activité physique régulière et une réduction du sel aident à la stabiliser.",
            'cœur' => "La santé **cardiaque** dépend de votre hygiène de vie. Pratiquez 30 min de marche par jour et évitez le tabac. Si vous ressentez une douleur oppressante dans la poitrine, contactez immédiatement le 15 (SAMU).",
            'diabète' => "Le **diabète** nécessite un suivi régulier du taux de sucre (glycémie). Une alimentation équilibrée pauvre en sucres rapides est essentielle. N'oubliez pas vos rendez-vous de contrôle.",
            'vaccin' => "Les **vaccins** vous protègent contre des maladies graves. Vérifiez votre carnet de santé pour voir si vos rappels (DTP, Coqueluche...) sont à jour.",
            'résumé' => "D'après l'analyse de cette discussion, les patients s'interrogent sur les traitements et les symptômes. Il est conseillé de suivre les recommandations de votre praticien et de ne pas pratiquer l'automédication pour des symptômes persistants."
        ];

        foreach ($responses as $key => $res) {
            if (strpos($prompt, $key) !== false) {
                return "*(Note: Assistant en mode local)* \n\n" . $res;
            }
        }

        // Réponse générique si aucun mot-clé n'est trouvé
        return "*(Note: Assistant en mode local)* \n\nJe comprends votre question concernant '" . htmlspecialchars($prompt) . "'. En tant qu'assistant MediLink, je vous suggère de consulter les publications de nos professionnels de santé dans ce forum pour plus de détails. \n\nSouhaitez-vous que j'analyse les commentaires de ce post pour vous faire un résumé ?";
    }

    // ===== BACK OFFICE =====

    /**
     * Afficher la liste de tous les posts (Back Office Admin)
     */
    public function adminList(): void {
        $pdo = Database::getConnection();
        
        // --- 1. Filtres & Tri (Partie Métier : Recherche et Trie) ---
        $search = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'date_desc';

        $query = "
            SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role,
                   f.titre AS forum_titre,
                   (SELECT COUNT(*) FROM commentaire c WHERE c.id_post = p.id_post) AS nb_commentaires
            FROM post p
            JOIN utilisateur u ON p.id_auteur = u.id
            JOIN forum f ON p.id_forum = f.id_forum
        ";

        $params = [];
        if ($search !== '') {
            $query .= " WHERE p.contenu LIKE :search1 OR u.nom LIKE :search2 OR u.prenom LIKE :search3 OR f.titre LIKE :search4 ";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
            $params[':search3'] = '%' . $search . '%';
            $params[':search4'] = '%' . $search . '%';
        }

        switch ($sort) {
            case 'date_asc':
                $query .= " ORDER BY p.date_publication ASC";
                break;
            case 'comments_desc':
                $query .= " ORDER BY nb_commentaires DESC";
                break;
            case 'date_desc':
            default:
                $query .= " ORDER BY p.date_publication DESC";
                break;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $posts = $stmt->fetchAll();
        
        // --- 2. Statistiques (Partie Métier : Statistique) ---
        $stmtStats1 = $pdo->query("SELECT COUNT(*) as total FROM post");
        $totalPosts = $stmtStats1->fetch()['total'];

        $stmtStats2 = $pdo->query("SELECT COUNT(*) as total FROM commentaire");
        $totalComments = $stmtStats2->fetch()['total'];
        
        $stmtTopPost = $pdo->query("SELECT p.id_post, COUNT(c.id_commentaire) as nb
                                    FROM post p
                                    LEFT JOIN commentaire c ON p.id_post = c.id_post
                                    GROUP BY p.id_post
                                    ORDER BY nb DESC LIMIT 1");
        $topPost = $stmtTopPost->fetch();

        require __DIR__ . '/../views/back/post/list.php';
    }

    /**
     * Créer un post (Back Office Admin)
     */
    public function adminCreate(): void {
        $pdo = Database::getConnection();
        
        // Listes pour les sélecteurs
        $stmtForums = $pdo->query("SELECT id_forum, titre FROM forum ORDER BY titre ASC");
        $forums = $stmtForums->fetchAll();
        
        $stmtUsers = $pdo->query("SELECT id, nom, prenom, role FROM utilisateur ORDER BY nom ASC");
        $utilisateurs = $stmtUsers->fetchAll();

        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenu = trim($_POST['contenu'] ?? '');
            $idForum = (int)($_POST['id_forum'] ?? 0);
            $idAuteur = (int)($_POST['id_auteur'] ?? 0);

            // Validation (Côté serveur, côté client géré en JS)
            if (empty($contenu)) {
                $errors[] = "Le contenu du post est obligatoire.";
            }
            if (strlen($contenu) < 10) {
                $errors[] = "Le contenu doit contenir au moins 10 caractères.";
            }
            if ($idForum <= 0) {
                $errors[] = "Veuillez sélectionner un forum valide.";
            }
            if ($idAuteur <= 0) {
                $errors[] = "Veuillez sélectionner un auteur valide.";
            }

            if (empty($errors)) {
                $stmtIns = $pdo->prepare("INSERT INTO post (contenu, id_forum, id_auteur) VALUES (:contenu, :id_forum, :id_auteur)");
                $result = $stmtIns->execute([
                    ':contenu'   => $contenu,
                    ':id_forum'  => $idForum,
                    ':id_auteur' => $idAuteur
                ]);
                
                if ($result) {
                    header('Location: /medilink_medicament/MediLink/index.php?module=forum&controller=post&action=adminList');
                    exit;
                } else {
                    $errors[] = "Erreur lors de la création du post.";
                }
            }
        }

        require __DIR__ . '/../views/back/post/create.php';
    }

    /**
     * Modifier un post (Back Office Admin)
     * @param int $id
     */
    public function edit($id): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom, u.role AS auteur_role
            FROM post p
            JOIN utilisateur u ON p.id_auteur = u.id
            WHERE p.id_post = :id
        ");
        $stmt->execute([':id' => $id]);
        $post = $stmt->fetch();

        if (!$post) {
            header('Location: /medilink_medicament/MediLink/index.php?module=forum&controller=post&action=adminList');
            exit;
        }

        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenu = trim($_POST['contenu'] ?? '');

            // Validation côté serveur
            if (empty($contenu)) {
                $errors[] = "Le contenu du post est obligatoire.";
            }
            if (strlen($contenu) < 10) {
                $errors[] = "Le contenu doit contenir au moins 10 caractères.";
            }

            if (empty($errors)) {
                $updateStmt = $pdo->prepare("UPDATE post SET contenu = :contenu WHERE id_post = :id");
                $result = $updateStmt->execute([
                    ':contenu' => $contenu,
                    ':id'      => $id
                ]);
                
                if ($result) {
                    $success = "Post modifié avec succès !";
                    // Rafraîchir les données
                    $stmt->execute([':id' => $id]);
                    $post = $stmt->fetch();
                } else {
                    $errors[] = "Erreur lors de la modification du post.";
                }
            }
        }

        require __DIR__ . '/../views/back/post/edit.php';
    }

    /**
     * Supprimer un post (Back Office Admin)
     * @param int $id
     */
    public function delete($id): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM post WHERE id_post = :id");
        $stmt->execute([':id' => $id]);
        
        header('Location: /medilink_medicament/MediLink/index.php?module=forum&controller=post&action=adminList');
        exit;
    }
}
