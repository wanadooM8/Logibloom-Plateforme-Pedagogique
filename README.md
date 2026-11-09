# Logibloom

Application web construite sur **Laravel 13**, **Inertia v3** et **Svelte 5**, à partir du starter kit officiel Laravel + Svelte.

## Stack technique

| Couche          | Technologies                                                        |
| --------------- | ------------------------------------------------------------------- |
| Back-end        | PHP 8.4, Laravel 13, Laravel Fortify (authentification)             |
| Front-end       | Svelte 5, Inertia v3, TypeScript, Tailwind CSS 4, bits-ui, Lucide   |
| Routage typé    | Laravel Wayfinder (fonctions TypeScript générées depuis les routes) |
| Build           | Vite+ (`vp`)                                                        |
| Base de données | MariaDB 11 + Valkey, via Laravel Sail (Docker)                      |
| Qualité         | Pest, Larastan (PHPStan), Pint, svelte-check                        |

## Fonctionnalités incluses

- Inscription, connexion, réinitialisation du mot de passe et vérification de l'e-mail
- Authentification à deux facteurs (TOTP) et passkeys
- Tableau de bord protégé (`/dashboard`)
- Paramètres utilisateur : profil, sécurité (mot de passe, 2FA, passkeys) et apparence (thème clair/sombre)

## Environnement de développement

Le projet utilise **Laravel Sail** (Docker) pour que tous les développeurs travaillent dans le même environnement. Chacun a les mêmes versions de PHP, Node, MariaDB et Valkey, sans rien installer d'autre que Docker.

Sail est une surcouche de `docker compose`. Les services sont décrits dans `compose.yaml` :

| Service        | Rôle                                                                             |
| -------------- | -------------------------------------------------------------------------------- |
| `laravel.test` | PHP 8.4, Composer et Node. Sert l'application sur le port 80 et Vite sur le 5173 |
| `mariadb`      | Base MariaDB 11 (base `logibloom`, et base `testing` pour les tests)             |
| `valkey`       | Cache / files d'attente compatible Redis                                         |

Le dossier du projet est monté dans le conteneur : vous éditez le code sur votre machine, il s'exécute dans le conteneur.

## Prérequis

- **Docker** (Docker Desktop sur Windows/macOS, ou Docker Engine sur Linux)
- **Windows** : WSL 2. Clonez le projet **dans le système de fichiers WSL** (ex. `~/projets`), pas dans `/mnt/c/...`, sinon les performances s'effondrent. Activez l'intégration WSL dans Docker Desktop (_Settings → Resources → WSL integration_).

Il n'est pas nécessaire d'installer PHP, Composer ou Node sur votre machine.

Alias conseillé (à ajouter dans `~/.bashrc` ou `~/.zshrc`) :

```bash
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
```

## Installation

```bash
git clone <url-du-depot> logibloom
cd logibloom

# 1. Installer les dépendances PHP via un conteneur temporaire (Sail n'existe qu'après cette étape)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

# 2. Configurer l'environnement
cp .env.example .env

# 3. Démarrer les conteneurs (le premier lancement construit l'image, c'est long)
sail up -d

# 4. Initialiser l'application
sail artisan key:generate
sail artisan migrate
sail npm install
```

> Si les ports 80, 3306, 5173 ou 6379 sont déjà utilisés sur votre machine, changez `APP_PORT`, `FORWARD_DB_PORT`, `VITE_PORT` ou `FORWARD_VALKEY_PORT` dans `.env` (pour `APP_PORT`, adaptez aussi `APP_URL`).

## Développement

```bash
sail up -d          # démarrer les conteneurs
sail npm run dev    # serveur Vite (rechargement à chaud)
```

L'application est accessible sur [http://localhost](http://localhost).

Pour traiter les jobs en file d'attente, lancez dans un autre terminal : `sail artisan queue:listen --tries=1`.

**Toutes les commandes passent par Sail** pour s'exécuter dans le conteneur :

| Au lieu de…       | Utilisez…           |
| ----------------- | ------------------- |
| `php artisan ...` | `sail artisan ...`  |
| `composer ...`    | `sail composer ...` |
| `npm ...`         | `sail npm ...`      |
| `php ...`         | `sail php ...`      |

Autres commandes utiles :

```bash
sail down           # arrêter les conteneurs
sail down -v        # arrêter et supprimer les données (base MariaDB, Valkey)
sail shell          # ouvrir un shell dans le conteneur
sail mariadb        # client MariaDB connecté à la base
sail build --no-cache  # reconstruire l'image (après mise à jour de Sail)
```

Pour se connecter à la base depuis un client graphique (DBeaver, TablePlus…) : hôte `127.0.0.1`, port `3306`, utilisateur `sail`, mot de passe `password`.

Pour activer Xdebug, définissez `SAIL_XDEBUG_MODE=develop,debug` dans `.env` puis relancez `sail up -d`.

## Tests & CI

### Outils utilisés

- **[Pest](https://pestphp.com)** : framework de tests (basé sur PHPUnit) avec le plugin Laravel
- **SQLite en mémoire** : base de données de test, rapide et isolée (voir `phpunit.xml`)
- **Factories Eloquent** : génération des données de test
- **[Laravel Pint](https://laravel.com/docs/pint)** : vérification du style de code
- **[Larastan](https://github.com/larastan/larastan)** : analyse statique (PHPStan pour Laravel)
- **GitHub Actions** : CI exécutée à chaque push et pull request (`.github/workflows/tests.yml`)

### Lancer les tests en local

```bash
sail artisan test                    # tous les tests
sail artisan test --filter=Nom       # un test précis
sail bin pint                        # corriger le style
sail bin pint --test                 # vérifier le style sans modifier
sail bin phpstan analyse             # analyse statique
```

### Workflow TDD

1. **Rouge** : écrire le test d'abord (il doit échouer)
2. **Vert** : écrire le minimum de code pour le faire passer
3. **Refactor** : nettoyer le code, les tests garantissent l'absence de régression

Chaque push déclenche la CI : les trois jobs (**Tests**, **Pint**, **Larastan**) doivent être verts avant de merger sur `main`.

### Conventions

- Les tests vont dans `tests/Feature` (comportement, base de données) ou `tests/Unit` (logique isolée)
- Chaque modèle possède une factory dans `database/factories`
- Les tests de base de données utilisent `RefreshDatabase`

## Structure du projet

```
app/                    Code applicatif (modèles, contrôleurs, actions Fortify)
database/               Migrations, factories et seeders
resources/js/
├── pages/              Pages Inertia (auth, settings, Dashboard, Welcome)
├── components/         Composants Svelte réutilisables
├── layouts/            Layouts de l'application
├── actions/, routes/   Fonctions générées par Wayfinder
routes/                 Routes web (web.php, settings.php)
tests/                  Tests Pest (Feature et Unit)
```

## Build de production

```bash
sail npm run build   # ou sail npm run build:ssr pour le rendu côté serveur
```

## Licence

Projet distribué sous licence MIT.
