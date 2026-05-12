<?php
require_once __DIR__ . '/Utilisateur.php';


abstract class Administrateur extends Utilisateur
{
    // ── Constructeur ──
    public function __construct(
        string $nom = '',
        string $prenom = '',
        string $email = '',
        string $motDePasse = '',
        string $telephone = '',
        string $statutCompte = 'Actif'
    ) {
        parent::__construct($nom, $prenom, $email, $motDePasse, $telephone, $statutCompte, 'Administrateur');
    }

    
    abstract public function sInscrire(): int;

    
    abstract public function gererUtilisateurs(): array;

    
    abstract public function getUtilisateurComplet(int $id): ?array;

    
    abstract public function modererForum(): void;

    
    abstract public function gererProduits(): void;
}
