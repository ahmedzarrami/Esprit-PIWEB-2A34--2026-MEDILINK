<?php
/**
 * Mailer — Service d'envoi d'emails via SMTP avec TLS (STARTTLS).
 * Fonctionne avec Gmail, Outlook, Mailtrap, etc.
 * Aucune bibliothèque externe requise (utilise les sockets PHP natifs).
 *
 * ──────────────────────────────────────────────────────────────
 * CONFIGURATION GMAIL (recommandé) :
 *   1. Activer la validation en 2 étapes sur votre compte Google
 *   2. Aller sur : https://myaccount.google.com/apppasswords
 *   3. Générer un "Mot de passe d'application" (type : Courrier)
 *   4. Remplir SMTP_USER et SMTP_PASS ci-dessous
 *
 * CONFIGURATION MAILTRAP (test sans vrai email) :
 *   1. Créer un compte gratuit sur https://mailtrap.io
 *   2. Aller dans Inbox → SMTP Settings → PHP
 *   3. Copier host, port, user, pass dans les constantes ci-dessous
 * ──────────────────────────────────────────────────────────────
 */
class Mailer
{
    // ── À REMPLIR AVEC VOS IDENTIFIANTS SMTP ──────────────────
    private const SMTP_HOST = 'smtp.gmail.com';   // Gmail : smtp.gmail.com | Mailtrap : sandbox.smtp.mailtrap.io
    private const SMTP_PORT = 587;                 // Gmail : 587 (STARTTLS)  | Mailtrap : 2525
    private const SMTP_USER = 'aferjaoui965@gmail.com';  // Votre email
    private const SMTP_PASS = 'zntk riqq okoz lluq'; // Mot de passe d'application Gmail (16 caractères)
    private const FROM_NAME = 'MediLink';
    private const FROM_EMAIL = 'aferjaoui965@gmail.com'; // Même valeur que SMTP_USER pour Gmail
    // ──────────────────────────────────────────────────────────

    /**
     * Point d'entrée principal. Envoie un email HTML via SMTP.
     * En cas d'échec, écrit dans logs/mail_dev.log et retourne false.
     */
    public static function envoyer(string $to, string $subject, string $body): bool
    {
        try {
            return self::sendSmtp($to, $subject, $body);
        } catch (Exception $e) {
            self::logDevMail($to, $subject, $body, $e->getMessage());
            return false;
        }
    }

    /**
     * Construit le corps HTML de l'email de réinitialisation.
     */
    public static function construireEmailReset(string $prenom, string $code): string
    {
        return '<!DOCTYPE html>
<html lang="fr">
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0">
  <tr><td align="center" style="padding:40px 20px">
    <table width="600" cellpadding="0" cellspacing="0" style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08)">
      <tr><td style="background:linear-gradient(135deg,#0ea5e9,#0369a1);padding:32px;text-align:center">
        <h1 style="color:white;margin:0;font-size:28px;letter-spacing:1px">MediLink</h1>
        <p style="color:rgba(255,255,255,.8);margin:6px 0 0;font-size:14px">Plateforme de santé digitale</p>
      </td></tr>
      <tr><td style="padding:40px">
        <h2 style="color:#1e293b;margin:0 0 16px">Bonjour ' . htmlspecialchars($prenom) . ',</h2>
        <p style="color:#475569;line-height:1.6;margin:0 0 24px">
          Vous avez demandé la réinitialisation de votre mot de passe MediLink.<br>
          Utilisez le code ci-dessous pour continuer.
        </p>
        <div style="background:#f0f9ff;border:2px solid #0ea5e9;border-radius:12px;padding:24px;text-align:center;margin:0 0 24px">
          <p style="color:#64748b;margin:0 0 8px;font-size:13px;text-transform:uppercase;letter-spacing:1px">Code de vérification</p>
          <span style="font-size:42px;font-weight:900;color:#0ea5e9;letter-spacing:12px;font-family:monospace">' . $code . '</span>
        </div>
        <p style="color:#64748b;font-size:13px;margin:0 0 8px">⏱ Ce code expire dans <strong>15 minutes</strong>.</p>
        <p style="color:#94a3b8;font-size:12px;margin:0">Si vous n\'avez pas effectué cette demande, ignorez cet email. Votre compte reste sécurisé.</p>
      </td></tr>
      <tr><td style="background:#f8fafc;padding:20px;text-align:center;border-top:1px solid #e2e8f0">
        <p style="color:#94a3b8;font-size:12px;margin:0">© ' . date('Y') . ' MediLink — Tous droits réservés</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body></html>';
    }

    // ──────────────────────────────────────────────────────────
    // Client SMTP interne (STARTTLS, AUTH LOGIN)
    // ──────────────────────────────────────────────────────────

    private static function sendSmtp(string $to, string $subject, string $body): bool
    {
        // Connexion TCP au serveur SMTP
        $socket = @fsockopen('tcp://' . self::SMTP_HOST, self::SMTP_PORT, $errno, $errstr, 10);
        if (!$socket) {
            throw new Exception("Connexion SMTP impossible ({$errno}: {$errstr})");
        }

        stream_set_timeout($socket, 10);

        // Lecture du message de bienvenue (220)
        self::expect($socket, '220', 'Bienvenue');

        // EHLO
        self::cmd($socket, 'EHLO medilink.local', '250');

        // STARTTLS
        self::cmd($socket, 'STARTTLS', '220');

        // Activation TLS sur le socket existant
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new Exception('Impossible d\'activer TLS.');
        }

        // EHLO à nouveau après TLS
        self::cmd($socket, 'EHLO medilink.local', '250');

        // AUTH LOGIN
        self::cmd($socket, 'AUTH LOGIN', '334');
        self::cmd($socket, base64_encode(self::SMTP_USER), '334');
        self::cmd($socket, base64_encode(self::SMTP_PASS), '235');

        // Enveloppe
        self::cmd($socket, 'MAIL FROM: <' . self::FROM_EMAIL . '>', '250');
        self::cmd($socket, 'RCPT TO: <' . $to . '>', '250');
        self::cmd($socket, 'DATA', '354');

        // Encodage UTF-8 du sujet
        $subjectEncoded = '=?UTF-8?B?' . base64_encode($subject) . '?=';

        $message  = 'From: ' . self::FROM_NAME . ' <' . self::FROM_EMAIL . '>' . "\r\n";
        $message .= 'To: <' . $to . '>' . "\r\n";
        $message .= 'Subject: ' . $subjectEncoded . "\r\n";
        $message .= 'MIME-Version: 1.0' . "\r\n";
        $message .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
        $message .= 'Content-Transfer-Encoding: base64' . "\r\n";
        $message .= "\r\n";
        $message .= chunk_split(base64_encode($body));
        $message .= "\r\n.";

        self::cmd($socket, $message, '250');
        self::cmd($socket, 'QUIT', '221');

        fclose($socket);
        return true;
    }

    /**
     * Envoie une commande SMTP et vérifie que la réponse commence par le code attendu.
     */
    private static function cmd($socket, string $command, string $expectedCode): string
    {
        fputs($socket, $command . "\r\n");
        $response = '';
        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            if (strlen($line) >= 4 && $line[3] === ' ') break; // Fin de la réponse multi-lignes
        }
        if (substr(trim($response), 0, 3) !== $expectedCode) {
            throw new Exception("SMTP [{$expectedCode} attendu] : " . trim($response));
        }
        return $response;
    }

    /**
     * Lit la réponse initiale du serveur sans envoyer de commande.
     */
    private static function expect($socket, string $expectedCode, string $context): void
    {
        $response = '';
        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            if (strlen($line) >= 4 && $line[3] === ' ') break;
        }
        if (substr(trim($response), 0, 3) !== $expectedCode) {
            throw new Exception("SMTP [{$context}] : " . trim($response));
        }
    }

    /**
     * Fallback développement : écrit l'email + le code dans un fichier log.
     */
    private static function logDevMail(string $to, string $subject, string $body, string $error = ''): void
    {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $entry = '[' . date('Y-m-d H:i:s') . '] ERREUR SMTP: ' . $error . PHP_EOL
               . 'To: ' . $to . PHP_EOL
               . 'Subject: ' . $subject . PHP_EOL
               . 'Contenu (texte): ' . strip_tags($body) . PHP_EOL
               . str_repeat('-', 60) . PHP_EOL;

        file_put_contents($logDir . '/mail_dev.log', $entry, FILE_APPEND | LOCK_EX);
    }
}
