# symfony-mongodb-atlas-rentals

A sample rental booking project built with PHP Symfony and MongoDB 

## Prerequisites
[PHP Symfony](https://symfony.com/) is a modern PHP framework that makes it easy to build web applications. It's flexible, scalable, and easy to use. It's also a great way to learn PHP and web development.

In their own words:

>Symfony is a set of reusable PHP components... ...and a PHP framework for web projects

:::tip
You can even [try thier cloud platform](https://symfony.com/cloud/) without installing anything!
:::

## PHP Tools

- **PHP** - Symfony requires PHP 7.4 or higher. You can download PHP from the [official PHP website](https://www.php.net/downloads).
```
brew install php
```
- **Composer** - Symfony uses [Composer](https://getcomposer.org/) to manage its dependencies. Composer is a dependency manager for PHP. You can download Composer from the [official Composer website](https://getcomposer.org/download/).
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'edb40769019ccf227279e3bdd1f5b2e9950eb000c3233ee85148944e555d97be3ea4f40c3c2fe73b22f875385f6a5155') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```
- **Symfony CLI** - Symfony provides a command-line tool called the Symfony CLI. It's a great way to create new Symfony applications, manage dependencies, and run your application. You can download the Symfony CLI from the [official Symfony website](https://symfony.com/download).
```
wget https://get.symfony.com/cli/installer -O - | bash
```


## MongoDB Atlas cluster

Please follow our [quick start](https://www.mongodb.com/docs/atlas/getting-started/) with Atlas to create a free cluster if you don't have one already.

`-OR-` you can use a local compatible MongoDB deployment.

## Setting up the project

Clone the project
```
git clone https://github.com/mongodb-developer/symfony-mongodb-atlas-rentals.git
```

Setup the symfony .env file at the root level
```
MONGODB_URL=<your_mongodb_atlas_uri>
MONGODB_DB=symfony
```


Install dependencies
```
cd symfony-mongodb-atlas-rentals
composer install
```


## Run the project
```
symfony server:start
```

### Clearing the cache

Sometimes it is useful to clear the console cache.
```
php bin/console cache:clear
```

## Desclaimer 

This is not a MongoDB Inc. official product, use at your own risk.



