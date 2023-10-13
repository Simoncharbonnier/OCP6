# SnowTricks

Ce projet est réalisé dans le cadre de la formation de développeur d'application PHP/Symfony chez OpenClassrooms.

La mission est de créer un site communautaire pour apprendre les figures de snowboard.

Voici les différentes technologies utilisées dans ce projet :
-   Symfony - PHP - HTML - CSS - Javascript - Bootstrap


## Installation

Cloner mon projet

```bash
gh repo clone https://github.com/Simoncharbonnier/OCP6.git
```

Modifier les variables d'environnement DATABASE_URL & MAILER_DSN dans .env ou .env.local

```bash
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=14&charset=utf8"
MAILER_DSN=smtp://user:pass@smtp.example.com:port
```

Installer les dépendances avec Composer

```bash
composer install
```

Créer la base de données

```bash
php bin/console doctrine:database:create
```

Créer les tables de la base de données

```bash
php bin/console doctrine:schema:update --force
```

Insérer un jeu de données

```bash
php bin/console doctrine:fixtures:load
```

Lancer Symfony

```bash
symfony server:start
```

Se connecter avec les identifiants suivant

```bash
nom d'utilisateur: john
mot de passe: secret
```

Et tout devrait fonctionner sans soucis !


## Fonctionnalités

-   En tant que visiteur, je peux accéder à la page d'accueil contenant la liste des figures.
-   En tant que visiteur, je peux accéder aux détails d'une figure.
-   En tant que visiteur, je peux accéder au profil d'un utilisateur.
-   En tant que visiteur, je peux créer un compte.
-   En tant que visiteur, je peux me connecter.

-   En tant qu'utilisateur, je peux ajouter un commentaire.
-   En tant qu'utilisateur, je peux ajouter une figure.
-   En tant qu'utilisateur, je peux modifier mes figures.
-   En tant qu'utilisateur, je peux supprimer mes figures.
-   En tant qu'utilisateur, je peux modifier mes informations.
-   En tant qu'utilisateur, je peux me déconnecter.
