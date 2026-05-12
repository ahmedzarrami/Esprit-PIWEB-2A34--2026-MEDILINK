<?php
/**
 * MediLink - Bootstrap de session unifie.
 *
 * Tous les modules INCLUENT ce fichier en TOUTE PREMIERE ligne pour :
 *  - demarrer une session partagee sur tout /files40
 *  - exposer des helpers : current_user(), require_login(), require_role()
 *  - assurer le "bridge" d'identite vers les tables patients/medecins du RDV
 *
 * Format de la session :
 *   $_SESSION['user'] = [
 *       'id'             => int,
 *       'nom'            => string,
 *       'prenom'         => string,
 *       'email'          => string,
 *       'role'           => 'Patient'|'Professionnel'|'Administrateur',
 *       'telephone'      => string,
 *       'rdv_patient_id' => ?int    (si Patient, id dans la table patients)
 *       'rdv_medecin_id' => ?int    (si Professionnel, id dans la table medecins)
 *   ]
 *
 *   Les cles "legacy" sont aussi maintenues pour les modules existants :
 *       $_SESSION['user_id'], ['user_role'], ['user_nom'], ['user_email']
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    // Cookie de session valide pour TOUTE l'application /files40/.
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/files40/',
        'samesite' => 'Lax',
        'httponly' => true,
    ]);
    session_name('MEDILINK_SID');
    session_start();
}

require_once __DIR__ . '/database.php';

// ============================================================
// Helpers d'authentification
// ============================================================

/**
 * Retourne l'utilisateur courant (array) ou null s'il n'est pas connecte.
 */
function current_user(): ?array
{
    if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
        return $_SESSION['user'];
    }
    if (!empty($_SESSION['user_id'])) {
        // Reconstruction a partir des cles legacy (cas ou un module a peuple
        // l'ancien format sans passer par medilink_login()).
        return medilink_hydrate_user((int) $_SESSION['user_id']);
    }
    return null;
}

/**
 * True si un utilisateur est connecte.
 */
function is_logged_in(): bool
{
    return current_user() !== null;
}

/**
 * Retourne le role de l'utilisateur courant ou null.
 */
function current_role(): ?string
{
    $u = current_user();
    return $u['role'] ?? null;
}

/**
 * Bloque la requete si aucun utilisateur n'est connecte.
 * Redirige vers la page de login centrale.
 */
function require_login(): void
{
    if (!is_logged_in()) {
        $back = $_SERVER['REQUEST_URI'] ?? '/files40/';
        $_SESSION['login_redirect'] = $back;
        header('Location: /files40/modules/utilisateur/index.php?page=login');
        exit;
    }
}

/**
 * Bloque la requete si l'utilisateur n'a pas le bon role.
 * $roles peut etre une string ou un tableau de roles autorises.
 */
function require_role($roles): void
{
    require_login();
    $allowed = is_array($roles) ? $roles : [$roles];
    if (!in_array(current_role(), $allowed, true)) {
        http_response_code(403);
        echo '<!doctype html><meta charset="utf-8"><title>Acces refuse</title>';
        echo '<div style="font-family:system-ui;padding:60px;text-align:center;color:#0f172a">';
        echo '<h1 style="color:#dc2626">403 &mdash; Acces refuse</h1>';
        echo '<p>Cette section requiert l\'un des roles : <code>' . htmlspecialchars(implode(', ', $allowed)) . '</code>.</p>';
        echo '<p>Vous etes connecte en tant que <strong>' . htmlspecialchars(current_role() ?? 'invite') . '</strong>.</p>';
        echo '<p><a href="/files40/index.php" style="color:#1d4ed8">Retour a l\'accueil</a></p>';
        echo '</div>';
        exit;
    }
}

// ============================================================
// Construction / mise a jour de $_SESSION['user']
// ============================================================

/**
 * Charge un utilisateur depuis la DB et peuple $_SESSION['user'].
 * Appele apres login (mot de passe / reconnaissance faciale).
 * Synchronise aussi les tables patients/medecins (bridge RDV).
 *
 * @param int $userId  id dans la table utilisateur
 */
function medilink_login(int $userId): array
{
    $u = medilink_hydrate_user($userId);
    if ($u === null) {
        return [];
    }
    $_SESSION['user'] = $u;

    // Compat legacy
    $_SESSION['user_id']    = $u['id'];
    $_SESSION['user_role']  = $u['role'];
    $_SESSION['user_nom']   = $u['prenom'] . ' ' . $u['nom'];
    $_SESSION['user_email'] = $u['email'];

    return $u;
}

/**
 * Vide entierement la session (logout).
 */
function medilink_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/**
 * Reconstruit le tableau user a partir de la DB.
 */
function medilink_hydrate_user(int $userId): ?array
{
    try {
        $pdo = medilink_pdo();
    } catch (Throwable $e) {
        return null;
    }

    $st = $pdo->prepare('SELECT id, nom, prenom, email, telephone, role, statut_compte FROM utilisateur WHERE id = ?');
    $st->execute([$userId]);
    $row = $st->fetch();
    if (!$row) {
        return null;
    }
    $user = [
        'id'             => (int) $row['id'],
        'nom'            => $row['nom'],
        'prenom'         => $row['prenom'],
        'email'          => $row['email'],
        'telephone'      => $row['telephone'],
        'role'           => $row['role'],
        'statut_compte'  => $row['statut_compte'],
        'rdv_patient_id' => null,
        'rdv_medecin_id' => null,
    ];

    // Bridge RDV : assurer une ligne patients / medecins pour cet utilisateur.
    if ($row['role'] === 'Patient') {
        $user['rdv_patient_id'] = medilink_bridge_patient($pdo, $user);
    } elseif ($row['role'] === 'Professionnel') {
        $user['rdv_medecin_id'] = medilink_bridge_medecin($pdo, $user);
    }
    return $user;
}

/**
 * Trouve ou cree une ligne patients liee a utilisateur_id.
 * Retourne l'id (BIGINT zerofill cote DB) en int PHP.
 */
function medilink_bridge_patient(PDO $pdo, array $u): int
{
    $st = $pdo->prepare('SELECT id FROM patients WHERE utilisateur_id = ? LIMIT 1');
    $st->execute([$u['id']]);
    $id = $st->fetchColumn();
    if ($id !== false) {
        return (int) $id;
    }

    // Recuperer infos complementaires si presentes dans patient (child)
    $st = $pdo->prepare('SELECT date_naissance, sexe, adresse FROM patient WHERE id = ?');
    $st->execute([$u['id']]);
    $extra = $st->fetch() ?: [];

    $ins = $pdo->prepare(
        'INSERT INTO patients (nom, prenom, email, motdepasse, telephone, datedenaissance, sexe, adresse, utilisateur_id)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $ins->execute([
        $u['nom'],
        $u['prenom'],
        $u['email'],
        '__bridge__',                                   // motdepasse legacy, non utilise
        $u['telephone'] ?: '',
        $extra['date_naissance'] ?? '1970-01-01',
        ($extra['sexe'] ?? 'M') ?: 'M',
        $extra['adresse'] ?? null,
        $u['id'],
    ]);
    return (int) $pdo->lastInsertId();
}

/**
 * Recupere la ligne medecins de l'utilisateur courant (Professionnel).
 * Retourne null si non applicable.
 */
function current_medecin(): ?array
{
    $u = current_user();
    if (!$u || $u['role'] !== 'Professionnel' || empty($u['rdv_medecin_id'])) {
        return null;
    }
    try {
        $pdo = medilink_pdo();
        $st = $pdo->prepare('SELECT * FROM medecins WHERE id = ?');
        $st->execute([$u['rdv_medecin_id']]);
        $row = $st->fetch();
        return $row ?: null;
    } catch (Throwable $e) {
        return null;
    }
}

/**
 * Met a jour les champs localisation/coordonnees de la ligne medecins
 * de l'utilisateur courant. Utilise par le formulaire profil professionnel.
 */
function medilink_update_medecin_address(int $userId, array $data): bool
{
    try {
        $pdo = medilink_pdo();
        $st  = $pdo->prepare(
            'UPDATE medecins
                SET nom = ?, specialite = ?, email = ?, telephone = ?,
                    adresse = ?, ville = ?, latitude = ?, longitude = ?
              WHERE utilisateur_id = ?'
        );
        return $st->execute([
            $data['nom'] ?? '',
            $data['specialite'] ?? 'Generaliste',
            $data['email'] ?? '',
            $data['telephone'] ?? null,
            $data['adresse'] ?? null,
            $data['ville']   ?? null,
            $data['latitude']  !== '' && $data['latitude']  !== null ? (float)$data['latitude']  : null,
            $data['longitude'] !== '' && $data['longitude'] !== null ? (float)$data['longitude'] : null,
            $userId,
        ]);
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * Trouve ou cree une ligne medecins liee a utilisateur_id.
 */
function medilink_bridge_medecin(PDO $pdo, array $u): int
{
    $st = $pdo->prepare('SELECT id FROM medecins WHERE utilisateur_id = ? LIMIT 1');
    $st->execute([$u['id']]);
    $id = $st->fetchColumn();
    if ($id !== false) {
        return (int) $id;
    }

    $st = $pdo->prepare('SELECT specialite FROM professionnel_sante WHERE id = ?');
    $st->execute([$u['id']]);
    $extra = $st->fetch() ?: [];

    $ins = $pdo->prepare(
        'INSERT INTO medecins (nom, specialite, email, telephone, utilisateur_id)
         VALUES (?, ?, ?, ?, ?)'
    );
    $ins->execute([
        'Dr. ' . $u['prenom'] . ' ' . $u['nom'],
        $extra['specialite'] ?? 'Generaliste',
        $u['email'],
        $u['telephone'] ?: null,
        $u['id'],
    ]);
    return (int) $pdo->lastInsertId();
}
