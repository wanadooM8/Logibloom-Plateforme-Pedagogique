# Logibloom

Application web construite sur **Laravel 13**, **Inertia v3** et **Svelte 5**, à partir du starter kit officiel Laravel + Svelte.

## Stack technique

| Couche          | Technologies                                                            |
| --------------- | ----------------------------------------------------------------------- |
| Back-end        | PHP 8.4, Laravel 13, Laravel Fortify (authentification)                 |
| Front-end       | Svelte 5, Inertia v3, TypeScript, Tailwind CSS 4, bits-ui, Lucide       |
| Routage typé    | Laravel Wayfinder (fonctions TypeScript générées depuis les routes)     |
| Build           | Vite+ (`vp`)                                                            |
| Base de données | SQLite par défaut, MariaDB + Valkey via Laravel Sail                    |
| Qualité         | Pest, Larastan (PHPStan), Pint, svelte-check                            |

## Fonctionnalités incluses

- Inscription, connexion, réinitialisation du mot de passe et vérification de l'e-mail
- Authentification à deux facteurs (TOTP) et passkeys
- Tableau de bord protégé (`/dashboard`)
- Paramètres utilisateur : profil, sécurité (mot de passe, 2FA, passkeys) et apparence (thème clair/sombre)

## Prérequis

- PHP 8.4 avec les extensions `pdo_sqlite` et `sqlite3` (et `pdo_mysql` pour MariaDB)
- Composer
- Node.js 22+ et npm
- (Optionnel) Docker, pour utiliser Laravel Sail

### Installer PHP et les extensions (Ubuntu / WSL)

Le projet doit être lancé avec un PHP installé **dans la même machine que le code**. Si le projet est dans WSL, n'utilisez pas le PHP/Composer de Windows (WAMP, ComposerSetup) : ils ne gèrent pas les chemins `\\wsl.localhost\...`.

```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.4-cli php8.4-sqlite3 php8.4-mysql php8.4-mbstring \
  php8.4-xml php8.4-curl php8.4-zip php8.4-bcmath php8.4-intl unzip
```

Puis installez Composer dans WSL (voir [getcomposer.org/download](https://getcomposer.org/download/)) et vérifiez :

```bash
php -m | grep -i pdo   # doit afficher PDO, pdo_sqlite (et pdo_mysql)
which composer         # ne doit pas pointer vers /mnt/c/...
```

### Installer PHP et les extensions (Fedora)

Sur Fedora, les drivers PDO sont dans des paquets séparés : `pdo_sqlite` est fourni par `php-pdo` et `pdo_mysql` par `php-mysqlnd`.

```bash
sudo dnf install php-cli php-pdo php-mysqlnd php-mbstring php-xml \
  php-bcmath php-intl php-process unzip composer
```

Vérifiez ensuite avec `php -m | grep -i pdo` : `PDO`, `pdo_sqlite` et `pdo_mysql` doivent apparaître. Si une extension manque alors que le paquet est installé, vérifiez qu'elle n'est pas désactivée dans `/etc/php.d/`.

> L'erreur `could not find driver` (au moment de `php artisan migrate` dans `composer run setup`) signifie que l'extension `pdo_sqlite` est absente.

## Installation

```bash
git clone <url-du-depot> logibloom
cd logibloom
composer run setup
```

Le script `setup` installe les dépendances PHP et JS, crée le fichier `.env`, génère la clé d'application, exécute les migrations et compile les assets.

> Avec SQLite (configuration par défaut), créez le fichier de base si besoin : `touch database/database.sqlite`.

## Développement

```bash
composer run dev
```

Lance en parallèle le serveur Laravel (`php artisan serve`), le worker de file d'attente et le serveur Vite. L'application est accessible sur [http://localhost:8000](http://localhost:8000).

### Avec Laravel Sail (Docker)

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run dev
```

Pensez à adapter `DB_CONNECTION` et les variables associées dans `.env` pour utiliser MariaDB.

## Tests et qualité du code

| Commande                   | Description                                                  |
| -------------------------- | ------------------------------------------------------------ |
| `composer test`            | Vérifie le style (Pint), l'analyse statique, puis les tests  |
| `php artisan test`         | Lance uniquement la suite de tests Pest                      |
| `composer lint`            | Formate le code PHP avec Pint                                |
| `composer types:check`     | Analyse statique avec Larastan                               |
| `npm run check`            | Lint et format du code front-end                             |
| `npm run types:check`      | Vérification des types Svelte/TypeScript                     |
| `composer ci:check`        | Exécute l'ensemble des vérifications (comme la CI)           |

La CI GitHub Actions (`.github/workflows/tests.yml`) exécute ces vérifications à chaque push sur `main` et sur chaque pull request.

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
npm run build        # ou npm run build:ssr pour le rendu côté serveur
```

## Licence

Projet distribué sous licence MIT.
