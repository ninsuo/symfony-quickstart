Symfony2 Quick Starter
========================

This is the [Symfony2 Standard Edition](https://github.com/symfony/symfony-standard) with some ready-to-use tools to get started quickly:

## Installation

```sh
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar update
php app/console assetic:dump
php app/console doctrine:schema:create
```

## Usage

### [Bootstrap](http://bootstrap.braincrafted.com/) is preinstalled with a base layout

To use it in a view:

```jinja
{% extends 'FuzQuickStartBundle::base.html.twig' %}
```

Look at the source code to know what are the blocks that can be overwritten.

### [HWIOAuthBundle](https://github.com/hwi/HWIOAuthBundle) is ready for letting your users log-in using their Google, Facebook and Twitter accounts.

You need to create apps with the minimum permissions at the following urls:

- Google Login: https://console.developers.google.com/project
- Facebook Login: https://developers.facebook.com/apps/
- Twitter Login: https://apps.twitter.com/

