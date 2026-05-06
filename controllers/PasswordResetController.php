<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Mailer.php';
require_once __DIR__ . '/../models/PasswordReset.php';
require_once __DIR__ . '/Utilisateur.php';

/**
 * PasswordResetController — Implémente le flux complet de réinitialisation de mot de passe.
 * Étapes : demande (email) → vérification code → nouveau mot de passe.
 * Hérite du contrat défini dans le modèle abstrait PasswordReset.
 */
class PasswordResetController extends PasswordReset
{
    // Durée de validité du token en minutes
    private const EXPIRY_MINUTES = 15;
    // Nombre maximum de tentatives de saisie du code
    private const MAX_ATTEMPTS = 5;

    // ──────────────────────────────────────────────────────────
    // Implémentation des méthodes abstraites (contrat du modèle)
    // ──────────────────────────────────────────────────────────

    /**
     * Génère un code à 6 chiffres, invalide les anciens tokens, stocke en base.
     * Retourne le code généré (pour l'envoyer par email).
     */
    public static function creer(int $userId): string
    {
        $pdo = Database::getInstance();

        // Invalider tous les tokens précédents de cet utilisateur
        self::invalider($userId);

        // Générer un code à 6 chiffres (cryptographiquement sûr)
        $code      = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . self::EXPIRY_MINUTES . ' minutes'));

        $stmt = $pdo->prepare(
            "INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (:uid, :token, :exp)"
        );
        $stmt->execute([':uid' => $userId, ':token' => $code, ':exp' => $expiresAt]);

        return $code;
    }

    /**
     * Vérifie qu'un token est valide : non expiré, non utilisé, correspond à l'utilisateur.
     */
    public static function verifier(int $userId, string $token): bool
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            "SELECT id FROM password_reset_tokens
             WHERE user_id = :uid
               AND token = :token
               AND used = 0
               AND expires_at > NOW()
             ORDER BY created_at DESC
             LIMIT 1"
        );
        $stmt->execute([':uid' => $userId, ':token' => $token]);
        return (bool) $stmt->fetch();
    }

    /**
     * Marque tous les tokens actifs d'un utilisateur comme utilisés.
     */
    public static function invalider(int $userId): void
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE password_reset_tokens SET used = 1 WHERE user_id = :uid AND used = 0");
        $stmt->execute([':uid' => $userId]);
    }

    // ──────────────────────────────────────────────────────────
    // Méthodes métier publiques (appelées depuis index.php)
    // ──────────────────────────────────────────────────────────

    /**
     * Étape 1 — Traite la demande de réinitialisation.
     * Valide l'email, génère un code, envoie l'email, stocke l'email en session.
     */
    public function demanderReinitialisation(string $email): array
    {
        $email = trim($email);

        // Validation de l'email
        if (empty($email)) {
            return ['success' => false, 'errors' => ['email' => 'L\'email est obligatoire.']];
        }
        if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
            return ['success' => false, 'errors' => ['email' => 'Format d\'email invalide.']];
        }

        // Chercher l'utilisateur sans révéler son existence (message générique)
        $user = UtilisateurController::getByEmail($email);

        if (!$user) {
            // Message volontairement identique : empêche l'énumération des emails
            return [
                'success' => true,
                'message' => 'Si cet email existe, un code vous a été envoyé.',
                'dev_code' => null
            ];
        }

        // Générer et envoyer le code
        $code = self::creer((int) $user['id']);

        $sujetEmail = 'MediLink — Code de réinitialisation de mot de passe';
        $corpsEmail = Mailer::construireEmailReset($user['prenom'], $code);
        $emailEnvoye = Mailer::envoyer($email, $sujetEmail, $corpsEmail);

        // Stocker l'email en session pour les étapes suivantes
        $_SESSION['reset_email']    = $email;
        $_SESSION['reset_attempts'] = 0;

        return [
            'success'   => true,
            'message'   => 'Un code de vérification a été envoyé à votre adresse email.',
            // dev_code exposé uniquement si mail() a échoué (mode XAMPP sans SMTP)
            'dev_code'  => $emailEnvoye ? null : $code,
        ];
    }

    /**
     * Étape 2 — Vérifie le code saisi par l'utilisateur.
     * Lit l'email depuis la session, compare le code.
     */
    public function verifierCodeSession(string $code): array
    {
        $email = $_SESSION['reset_email'] ?? null;

        if (!$email) {
            return ['success' => false, 'errors' => ['global' => 'Session expirée. Recommencez la procédure.']];
        }

        // Contrôle du nombre de tentatives
        $attempts = (int) ($_SESSION['reset_attempts'] ?? 0);
        if ($attempts >= self::MAX_ATTEMPTS) {
            $this->nettoyerSession();
            return ['success' => false, 'errors' => ['global' => 'Trop de tentatives. Veuillez recommencer.']];
        }

        $code = trim($code);
        if (empty($code)) {
            return ['success' => false, 'errors' => ['code' => 'Le code est obligatoire.']];
        }
        if (!preg_match('/^\d{6}$/', $code)) {
            return ['success' => false, 'errors' => ['code' => 'Le code doit contenir exactement 6 chiffres.']];
        }

        $user = UtilisateurController::getByEmail($email);
        if (!$user) {
            $this->nettoyerSession();
            return ['success' => false, 'errors' => ['global' => 'Compte introuvable.']];
        }

        if (!self::verifier((int) $user['id'], $code)) {
            $_SESSION['reset_attempts'] = $attempts + 1;
            $restants = self::MAX_ATTEMPTS - $attempts - 1;
            return [
                'success' => false,
                'errors'  => ['code' => 'Code invalide ou expiré. ' . $restants . ' tentative(s) restante(s).']
            ];
        }

        // Code valide : autoriser l'étape suivante
        $_SESSION['reset_verified'] = true;

        return ['success' => true];
    }

    /**
     * Étape 3 — Réinitialise le mot de passe après vérification du code.
     * Valide le nouveau mot de passe, met à jour en base, nettoie la session.
     */
    public function reinitialiserMotDePasse(string $code, string $nouveauMdp, string $confirmMdp): array
    {
        $email = $_SESSION['reset_email']    ?? null;
        $verifie = $_SESSION['reset_verified'] ?? false;

        if (!$email || !$verifie) {
            return ['success' => false, 'errors' => ['global' => 'Session invalide. Recommencez la procédure.']];
        }

        // Valider le nouveau mot de passe
        $errors = $this->validerNouveauMotDePasse($nouveauMdp, $confirmMdp);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $user = UtilisateurController::getByEmail($email);
        if (!$user) {
            $this->nettoyerSession();
            return ['success' => false, 'errors' => ['global' => 'Compte introuvable.']];
        }

        // Vérifier à nouveau que le code est toujours valide (double sécurité)
        if (!self::verifier((int) $user['id'], trim($code))) {
            return ['success' => false, 'errors' => ['global' => 'Code expiré. Recommencez la procédure.']];
        }

        // Mettre à jour le mot de passe
        UtilisateurController::changerMotDePasse((int) $user['id'], $nouveauMdp);

        // Invalider le token utilisé
        self::invalider((int) $user['id']);

        // Nettoyer la session de réinitialisation
        $this->nettoyerSession();

        return ['success' => true];
    }

    // ──────────────────────────────────────────────────────────
    // Méthodes privées utilitaires
    // ──────────────────────────────────────────────────────────

    /**
     * Valide la complexité du nouveau mot de passe.
     */
    private function validerNouveauMotDePasse(string $mdp, string $confirm): array
    {
        $errors = [];

        if (empty($mdp)) {
            $errors['nouveau_mdp'] = 'Le nouveau mot de passe est obligatoire.';
        } elseif (strlen($mdp) < 8) {
            $errors['nouveau_mdp'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        } else {
            $score = 0;
            if (preg_match('/[A-Z]/', $mdp)) $score++;
            if (preg_match('/[0-9]/', $mdp)) $score++;
            if (preg_match('/[^A-Za-z0-9]/', $mdp)) $score++;
            if ($score < 1) {
                $errors['nouveau_mdp'] = 'Mot de passe trop faible (ajoutez majuscule, chiffre ou symbole).';
            }
        }

        if ($mdp !== $confirm) {
            $errors['confirm_mdp'] = 'Les mots de passe ne correspondent pas.';
        }

        return $errors;
    }

    /**
     * Supprime les variables de session liées à la réinitialisation.
     */
    private function nettoyerSession(): void
    {
        unset($_SESSION['reset_email'], $_SESSION['reset_verified'], $_SESSION['reset_attempts']);
    }
}
