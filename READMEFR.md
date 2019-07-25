# HAY Project <img src="https://raw.githubusercontent.com/TheOpenMedium/HAY/master/public/resources/HAYlogo.svg?sanitize=true" height="60" width="60" />

[![Version](https://img.shields.io/badge/version-v0.0.1-red.svg?longCache=true&style=flat-square)](https://github.com/TheOpenMedium/HAY/releases) [![Release](https://img.shields.io/badge/release-pre--alpha-red.svg?longCache=true&style=flat-square)](https://github.com/TheOpenMedium/HAY/releases) [![Symfony](https://img.shields.io/badge/symfony-4.3.2-blue.svg?longCache=true&style=flat-square)](https://symfony.com/) [![PHP](https://img.shields.io/badge/php-+7.2.0-blue.svg?longCache=true&style=flat-square)](https://php.net/)

> Vous trouverez le code de conduite [ici](https://github.com/TheOpenMedium/HAY/blob/master/CODE_OF_CONDUCT.md)

> You can read this file in english [here](https://github.com/TheOpenMedium/HAY/blob/master/README.md)!

## Qu'est-ce que c'est HAY ?

![User](https://github.com/TheOpenMedium/HAY/raw/master/public/screenshots/User.png)HAY est un acronyme de "How Are You" qui signifie en français "Comment allez-vous ?". Mais c'est avant tout un réseau social
libre (Open Source) qui respecte votre vie privée et qui ne vous espionne pas. Ce site web fait partie du projet The Open Medium.

## Captures d'écran

<img src="https://github.com/TheOpenMedium/HAY/raw/master/public/screenshots/Sign%20Up.png" width="24%" /> <img src="https://github.com/TheOpenMedium/HAY/raw/master/public/screenshots/Log%20In.png" width="24%" /> <img src="https://github.com/TheOpenMedium/HAY/raw/master/public/screenshots/Home.png" width="24%" /> <img src="https://github.com/TheOpenMedium/HAY/raw/master/public/screenshots/User.png" width="24%" />

## Contribuer

Plus d'infos [ici](https://github.com/TheOpenMedium/HAY/blob/master/CONTRIBUTING.md) (en anglais).

## Installation rapide

> Des instructions plus détaillées de l'installation sont disponibles [ici](https://github.com/TheOpenMedium/HAY/wiki/Accueil-Fran%C3%A7ais), cette installation rapide n'est utile que si vous avez tous les outils nécessaires installés et que vous êtes sous Linux.

```bash
git clone https://github.com/TheOpenMedium/HAY.git
cd HAY
composer install
yarn install
yarn encore dev
```

Ensuite, changez les paramètres de la base de donnée dans le `.env` utilisant votre éditeur de texte favoris, comme nano, vim ou emacs.

```bash
./bin/console doctrine:database:create
./bin/console doctrine:migrations:migrate
```

Et pour utiliser le site web vous pouvez soit utiliser apache ou nginx, si vous avez installer le projet dans le dossier approprié ou lancer cette commande pour utiliser le site web dans `localhost:8000` :

```bash
./bin/console server:run
```

## FAQ

### Pourquoi utiliser Symfony pour ce projet ?

> Parce qu'avoir des conventions commune est essentiel pour mener ce projet à bien et à avoir un code clair et compréhensible pour tout le monde ! Mais aussi parce que Symfony est Open Source et simple (en particulier Symfony 4) et qu'il a énormément de "Bundles" qui simplifient la programmation. Enfin, ce Framework simplifie aussi le travail en groupe.

### Au secours ! Je ne sais pas comment utiliser Symfony !

> Si vous ne savez pas comment utiliser Symfony, consulter la documentation [ici](https://symfony.com/doc/current/index.html) qui est organisé sous forme de tutoriel, allez voir, c'est très simple, vous n'avez besoin que de connaitre le PHP ! (PS : Seul
> bémol, c'est uniquement en anglais pour l'instant) (PS2 : Attention, Symfony 4 est vachement différent de Symfony 3, veuillez
> apprendre la bonne version !)

### Comment installer HAY sur mon PC ?

> Un tutoriel est disponible dans le wiki [ici](https://github.com/TheOpenMedium/HAY/wiki/Accueil-Fran%C3%A7ais) ;) !
> 
> Et un installateur graphique est en cours de développement et sera disponible avant la sortie Alpha.

### Y a-t-il un fichier Docker que je pourrais utiliser ?

> Pas pour l'instant mais j'en fournirait un avant la sortie Alpha.

### Comment mettre à jour HAY ?

> Et bah, c'est étonnament compliqué, vu que HAY est toujours en pre-alpha, les instructions de mises à jour peuvent changer... de version à version. Mais, en Alpha, un mechanisme clair et consistant de mise à jour sera mit en place.
> 
> Mais, c'est généralement :
> 
> ```bash
> git pull
> composer install
> yarn install
> yarn encore dev
> ./bin/console doctrine:migrations:migrate
> ```

### Quel est le sens de la vie ?

> À droite !

---

> Si vous avez d'autres questions, posez-les dans la section "issue"
