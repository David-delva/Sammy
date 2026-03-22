# Gestion Scolaire ETP

Application Laravel de gestion scolaire pour l'Ecole Technique et Professionnelle: eleves, classes, matieres, notes par semestre, classements, bulletins PDF et suivi des annees academiques.

## Fonctionnalites principales

- Gestion des eleves avec inscription par annee academique
- Gestion des classes et des matieres avec coefficients
- Saisie des notes par semestre et en masse
- Calcul des moyennes, rangs et classements
- Generation de bulletins PDF et de listes de classe
- Gestion des acces par role: `admin` et `secretariat`
- Verification d'e-mail active pour l'acces au back-office

## Prerequis

- PHP 8.2+
- Composer
- Node.js 20+
- npm
- MySQL ou MariaDB pour la production

## Installation locale

1. Copier la configuration: `copy .env.example .env`
2. Adapter la base de donnees et l'URL dans `.env`
3. Installer les dependances PHP: `composer install`
4. Installer les dependances front: `npm install`
5. Generer la cle d'application: `php artisan key:generate`
6. Lancer les migrations: `php artisan migrate`
7. Charger des donnees de demonstration si besoin: `php artisan db:seed`
8. Compiler les assets: `npm run build`

Pour le developpement, vous pouvez aussi utiliser `npm run dev` en parallele du serveur Laravel.

## Comptes et acces

Roles disponibles:

- `admin`: acces complet aux modules, parametres, notes, classes et annees
- `secretariat`: acces au tableau de bord, eleves, bulletins et classement

Comptes de demonstration crees par `DatabaseSeeder`:

- `admin@ecole.com` / `password`
- `secretariat@ecole.com` / `password`

Ces comptes sont marques comme verifies pour accelerer la recette locale.

## Semestres et bulletins

- Les notes sont saisies avec un semestre `1` ou `2`
- Les bulletins sont generes par semestre depuis la fiche eleve
- La moyenne annuelle depend de la presence des deux semestres
- Le bulletin affiche maintenant le lieu de naissance de l'eleve

## Deploiement production

Checklist recommandee:

1. Copier `.env.example` vers `.env` et renseigner les vraies valeurs production
2. Mettre `APP_ENV=production` et `APP_DEBUG=false`
3. Configurer SMTP pour la verification d'e-mail
4. Lancer `composer install --no-dev --optimize-autoloader`
5. Lancer `php artisan migrate --force`
6. Compiler les assets avec `npm install` puis `npm run build`
7. Optimiser Laravel: `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`
8. Verifier que `ALLOW_PUBLIC_REGISTRATION=false`
9. Configurer les sauvegardes de base de donnees et du dossier `storage/app`

## Qualite et validation

Commandes utiles avant livraison:

- `php artisan test`
- `php artisan view:cache`
- `npm run build`
- `php artisan route:list`

## Points d'exploitation

- Ouvrir l'annee academique courante avant la saisie metier
- Completer les informations eleve, y compris le lieu de naissance
- Verifier les coefficients des matieres par classe et par annee
- Saisir les notes en distinguant bien le semestre 1 et le semestre 2

## Inscription publique

L'inscription publique est desactivee par defaut via `ALLOW_PUBLIC_REGISTRATION=false`.
Ne l'activez que si l'etablissement veut volontairement ouvrir la creation de comptes depuis l'ecran public.