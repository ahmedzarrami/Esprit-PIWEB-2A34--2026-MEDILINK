<?php
/**
 * PasswordReset — Modèle abstrait pour les tokens de réinitialisation
 * Définit le contrat (propriétés + méthodes abstraites) que le contrôleur doit implémenter.
 */
abstract class PasswordReset
{
    protected int    $id;
    protected int    $userId;
    protected string $token;
    protected string $expiresAt;
    protected bool   $used;

    public function getId(): int          { return $this->id; }
    public function getUserId(): int      { return $this->userId; }
    public function getToken(): string    { return $this->token; }
    public function getExpiresAt(): string{ return $this->expiresAt; }
    public function isUsed(): bool        { return $this->used; }

    /**
     * Génère et stocke un token pour l'utilisateur donné.
     * Retourne le code généré.
     */
    abstract public static function creer(int $userId): string;

    /**
     * Vérifie si le token est valide (non expiré, non utilisé, correspond).
     */
    abstract public static function verifier(int $userId, string $token): bool;

    /**
     * Invalide tous les tokens actifs d'un utilisateur (après utilisation ou nouvelle demande).
     */
    abstract public static function invalider(int $userId): void;
}
