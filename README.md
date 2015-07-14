Symfony2 Quick Start
========================

## What is it?

This is the [Symfony2 Standard Edition](https://github.com/symfony/symfony-standard) with some ready-to-use tools to get started quickly.

Most common stuff, such as login, translations or menus are built-in: no need to configure or code anything. Just overwrite and copy/paste to customize.

You'll find here 2 bundles:

- Fuz\QuickStartBundle contains well-known bundles implementations, you'll probably not need to modify those files.

- AppBundle is a skeleton, it contains your app implementation.

## Installation

```sh
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar update
php app/console assetic:dump
php app/console assets:install web --symlink
php app/console doctrine:schema:create
```

## Usage

[Bootstrap](http://bootstrap.braincrafted.com/) is preinstalled with a base layout

To use it in a view:

```jinja
{% extends 'FuzQuickStartBundle::layout.html.twig' %}
```

Look at the source code to know what are the blocks that can be overwritten.

---

[HWIOAuthBundle](https://github.com/hwi/HWIOAuthBundle) is ready for letting your users log-in using their Google, Facebook and Twitter accounts.

You need to create apps with the minimum permissions at the following urls:

- Google Login: https://console.developers.google.com/project
- Facebook Login: https://developers.facebook.com/apps/
- Twitter Login: https://apps.twitter.com/

To configure supported resource owners, you can overwrite `google_login`, `facebook_login` and/or `twitter_login` blocks in your base layout.

---

[JMSI18nRoutingBundle](http://jmsyst.com/bundles/JMSI18nRoutingBundle) is ready to let you switch between languages.

To configure supported languages:

- in app/config/config.yml, define your locales and default_locale
- in src/Fuz/QuickStartBundle/Resources/views/layout.html.twig are defined available locales

You can overwrite the `translations` twig block to set your custom locales

You'll probably find the right flags for your supported countries in the bundles/fuzquickstart/img/countries directory.

It implements the  "come back where you were" logic for better ergonomics.

---

[KnpMenuBundle](http://symfony.com/doc/master/bundles/KnpMenuBundle/index.html) is ready to let you define your navigation menu.

It automatically manages which part of the menu should be considered as active, and has helpers to quickly add routes and submenus.

a) You can see samples in Fuz\QuickStartBundle\Menu\Builder class and overwrite the menu block to define your own menus.

b) You can create your own menus in view with the following code:

```jinja
{{ knp_menu_render('FuzAppBundle:Builder:myMenu') }}
```

---

Bonus

- project already implements 403, 404, 500 and generic error pages
- CRUD generator generates bootstrap-ready and translated views
- Flash messages are automatically rendered in the base layout

## License

- This project is released under the MIT license

- Fuz logo is © 2013-2015 Alain Tiemblo

