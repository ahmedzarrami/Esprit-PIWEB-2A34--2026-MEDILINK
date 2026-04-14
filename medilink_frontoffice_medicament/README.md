# MediLink Front Office - Médicaments

Projet PHP MVC simple pour la partie front office de MediLink.

## Fonctionnalités
- page d'accueil inspirée du prototype MediLink
- catalogue public des médicaments
- recherche
- tri
- pagination
- détail d'un médicament
- connexion PDO
- architecture MVC

## Structure
- `config/`
- `controllers/`
- `models/`
- `views/`
- `public/`

## Installation
1. Copier le dossier dans `C:\xampp\htdocs\medilink_frontoffice_medicament`
2. Démarrer Apache et MySQL dans XAMPP
3. Réutiliser la base `medilink` déjà créée pour le back office
4. Vérifier `config/database.php`
5. Ouvrir :

```text
http://localhost/medilink_frontoffice_medicament/public/index.php
```

## Remarque
Le front office lit les médicaments existants dans la table `medicaments`.
La création, modification et suppression restent dans le back office.
