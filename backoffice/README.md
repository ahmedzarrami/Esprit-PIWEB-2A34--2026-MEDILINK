# MediLink - Back Office Médicaments

Projet PHP MVC avec PDO pour gérer uniquement le module **Médicaments** du back office MediLink.

## Fonctionnalités
- Afficher la liste des médicaments
- Ajouter un médicament
- Modifier un médicament
- Rechercher un médicament
- Supprimer un médicament
- Voir le détail d’un médicament
- Trier les médicaments
- Paginer la liste
- Messages de succès et d’erreur
- Validation **JavaScript + PHP**
- Champ **Date d’expiration supprimé**

## Installation avec XAMPP
1. Copier le dossier dans `C:\xampp\htdocs\`.
2. Importer `database/medilink.sql` dans phpMyAdmin.
3. Vérifier la connexion dans `app/config/Database.php`.
4. Ouvrir `http://localhost/medilink_backoffice_medicament/public/index.php`

## Si tu avais déjà l’ancienne base
Deux options :
- réimporter complètement `database/medilink.sql`
- ou exécuter `database/update_existing_table.sql`

## Structure
- `app/config` : connexion PDO
- `app/models` : requêtes SQL
- `app/controllers` : logique MVC
- `app/views` : interface back office
- `public/css` : style
- `public/js` : validation front
- `database` : scripts SQL


Mise à jour: la validation du formulaire de modification applique maintenant les mêmes règles que le formulaire d'ajout (JS + PHP).

## Variante demandée
Dans cette version, le fichier `app/models/Medicament.php` contient seulement la déclaration des fonctions (classe abstraite). L’implémentation réelle se trouve dans `app/models/MedicamentModel.php`.


Version supplémentaire : le contrôleur utilise explicitement les setters/getters du modèle avant create() et update().
