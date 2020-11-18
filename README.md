> This project is archived. Though a good start, I've realised with time that this is not a good structure for what I strive to build. I've not abandonned the idea of this project, but the new version will surely be entirely re-written in another language, while being way more modular. If you wish to take this project, please mail me at `m a i l [ a t ] l y e s [ d o t ] e u` (remove the spaces and replace the `[at]` and `[dot]`) to take over it, I would give you maintainership. Please bear in mind that while using outdated library and horrible graphics, I still think that I've left this project in good shape, with good documentation (even though missing some comments for later code). And it is quite advanced. It shouldn't be too hard to make it thriving again, and the switch to Symfony 5 shouldn't be too painful! Also, e-mail me if you're interested in developping its continuation and if your interested in my plans!

# HAY Project <img src="https://raw.githubusercontent.com/TheOpenMedium/HAY/master/public/resources/HAYlogo.svg?sanitize=true" height="60" width="60" />

[![Version](https://img.shields.io/badge/version-v0.0.1-red.svg?longCache=true&style=flat-square)](https://github.com/TheOpenMedium/HAY/releases) [![Release](https://img.shields.io/badge/release-pre--alpha-red.svg?longCache=true&style=flat-square)](https://github.com/TheOpenMedium/HAY/releases) [![Symfony](https://img.shields.io/badge/symfony-4.3.2-blue.svg?longCache=true&style=flat-square)](https://symfony.com/) [![PHP](https://img.shields.io/badge/php-+7.2.0-blue.svg?longCache=true&style=flat-square)](https://php.net/)

> Code of conduct [here](https://github.com/TheOpenMedium/HAY/blob/master/CODE_OF_CONDUCT.md)

> Si vous parlez français, vous pouvez visionnez ce même document en français [ici](https://github.com/TheOpenMedium/HAY/blob/master/READMEFR.md) !

> Our mother tongue is not English. If you found a grammar mistake, please open an issue.

## What's HAY?

![User](https://github.com/TheOpenMedium/HAY/raw/master/public/screenshots/User.png)
HAY is an acronym of "How Are You". It's also an Open Source social media that respect your privacy and doesn't track you! It's
a website from The Open Medium project.

## Screenshots

<img src="https://github.com/TheOpenMedium/HAY/raw/master/public/screenshots/Sign%20Up.png" width="24%" /> <img src="https://github.com/TheOpenMedium/HAY/raw/master/public/screenshots/Log%20In.png" width="24%" /> <img src="https://github.com/TheOpenMedium/HAY/raw/master/public/screenshots/Home.png" width="24%" /> <img src="https://github.com/TheOpenMedium/HAY/raw/master/public/screenshots/User.png" width="24%" />

## Contributing

More infos [here](https://github.com/TheOpenMedium/HAY/blob/master/CONTRIBUTING.md).

## Quick Installation

> Mode detailed installation instruction [here](https://github.com/TheOpenMedium/HAY/wiki/Home-English), this quick installation is only suitable if you already have all tools installed and you're on Linux.

```bash
git clone https://github.com/TheOpenMedium/HAY.git
cd HAY
composer install
yarn install
yarn encore dev
```

Then, you have to change database information in `.env` using your favorite editor, like nano, vim or emacs.

```bash
./bin/console doctrine:database:create
./bin/console doctrine:migrations:migrate
```

And for using the website, you can either use apache or nginx, if you installed the project in the appropriate directory or run this command to use the website in `localhost:8000` :

```bash
./bin/console server:run
```

## FAQ

### Why do you use Symfony for your project?

> Because having a common convention is essential to have a clear code! And we use Symfony because it's a simple and Open Source framework for php (especially Symfony 4) that have many bundles that simplify coding. It's also a framework that simplify working in group.

### HELP! I don't know how to use Symfony!

> If you don't know how to use Symfony you can use the documentation [here](https://symfony.com/doc/current/index.html). You'll see it's very simple! You only have to know how to code in PHP! (Warning: Symfony 4 and 3 are very different, be sure that you
> know the good one)

### How can I install HAY on my PC?

> A tutorial is available [here](https://github.com/TheOpenMedium/HAY/wiki/Home-English) ;)!
> 
> And a GUI installer is in development and will be available before the alpha release.

### Is there a Docker file I can use?

> Not for now, but I will provide one before the alpha release.

### How to update HAY?

> Well that's suprisingly complicated, as HAY is still in pre-alpha, update instructions might change... from commit to commit. But in alpha, clear and consistent update mechanism will be provided.
> 
> But, it's usually:
> 
> ```bash
> git pull
> composer install
> yarn install
> yarn encore dev
> ./bin/console doctrine:migrations:migrate
> ```

### What's the meaning of life?

> 42!

---

> If you have another question, please open an issue!
