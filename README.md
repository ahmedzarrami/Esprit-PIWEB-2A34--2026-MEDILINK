# MediLink — Branche `integration`

Plateforme web sante qui regroupe les cinq modules du projet PIWEB :

| Module | Dossier | Origine |
| --- | --- | --- |
| Gestion utilisateurs | `modules/utilisateur/` | branche `gestion_utilisateur` |
| Rendez-vous & fiches | `modules/rdv/` | branche `gestionrdv` |
| Medicaments & ordonnances | `modules/medicaments/` | branche `Médicaments` |
| Parapharmacie (shop) | `modules/parapharmacie/` | branche `Parapharmacie` |
| Forum communautaire | `modules/forum/` | branche `gestion-forum` |

## Installation rapide

1. Cloner sous `C:\xampp\htdocs\files40` (XAMPP).
2. Demarrer Apache + MySQL.
3. Importer `medilinkintegration.sql` via phpMyAdmin (cree la base `medilinkintegration`).
4. Ouvrir [http://localhost/files40/](http://localhost/files40/) pour acceder au hub.

## Architecture

- **Base de donnees unique** : `medilinkintegration` (script `medilinkintegration.sql`).
- **Config DB partagee** : `config/database.php` expose `medilink_pdo()`.
- **Theme commun** : `assets/css/theme.css` (variables, navbar, cards, boutons).
- **Hub d'accueil** : `index.php` route vers les modules.

Chaque module a conserve son organisation propre (controllers/models/views) ; seules
ses configurations DB ont ete repointees sur `medilinkintegration`.

## Comptes de demonstration

| Email | Mot de passe | Role |
| --- | --- | --- |
| `admin@medilink.tn` | `Pass@1234` | Administrateur |
| `k.mansouri@medilink.tn` | `Pass@1234` | Professionnel |
| `sarra.t@medilink.tn` | `Pass@1234` | Patient |

## Strategie d'integration

L'historique de `main` montre que des merges precedents avaient ete tentes puis
nettoyes. Pour cette nouvelle branche `integration`, chaque branche metier a ete
**importee dans son propre sous-dossier** via `git read-tree --prefix=` pour eviter
les conflits structurels (3 branches avaient `index.php`/`config.php`/`api.php` a la
racine avec des contenus differents).

Les schemas SQL des trois fichiers source (`medilink.sql`, `projetweb (1).sql`,
`medilink (2).sql`) ont ete reconcilies dans `medilinkintegration.sql`.
