<?php
/**
 * FaceAuth — Modèle abstrait pour l'authentification par reconnaissance faciale.
 * Définit le contrat que le contrôleur doit respecter (OOP / contrat d'interface).
 */
abstract class FaceAuth
{
    protected int     $userId;
    protected ?string $faceDescriptor;

    public function getUserId(): int           { return $this->userId; }
    public function getFaceDescriptor(): ?string{ return $this->faceDescriptor; }

    /**
     * Enregistre (ou remplace) le descripteur facial d'un utilisateur.
     * Le descripteur est un tableau JSON de 128 flottants produit par face-api.js.
     */
    abstract public static function sauvegarderDescripteur(int $userId, string $descriptorJson): bool;

    /**
     * Compare le descripteur fourni à tous les descripteurs stockés.
     * Retourne le tableau utilisateur le plus proche si la distance < seuil, null sinon.
     */
    abstract public static function trouverCorrespondance(array $descripteur): ?array;

    /**
     * Supprime le descripteur facial d'un utilisateur (désactiver la feature).
     */
    abstract public static function supprimerDescripteur(int $userId): bool;
}

