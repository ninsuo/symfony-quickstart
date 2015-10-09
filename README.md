Symfony2 Quick Start
========================

## What is it?

This is the [Symfony2 Standard Edition](https://github.com/symfony/symfony-standard) with some ready-to-use tools to get started quickly.

Most common stuff, such as login, translations or menus are built-in: no need to configure or code anything. Just overwrite and copy/paste to customize.

You'll find here 2 bundles:

- Fuz\QuickStartBundle contains well-known bundles implementations, you'll probably not need to modify those files.

- AppBundle is a skeleton, it will contain your app implementation.

## Ready-to-use bundles

- bootstrap is preinstalled with a base layout, and will automatically render your forms the right way
- hwioauthbundle is ready for letting your users log-in using their Google, Facebook or Twitter accounts
- fosuserbundle is also available if your users prefer registering an account on your website directly
- i18n support with language switcher and specific routes for each supported locales
- knpmenu awaits your routes and labels as an associative array for logged and non-logged users in a single class
- project already implements 403, 404, 500 and generic error pages
- CRUD generator generates bootstrap-ready and translated views
- Flash messages are automatically rendered in the base layout
- noCaptcha is implemented to protect your registration form

## Installation

```sh
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar update
php app/console assets:install web --symlink
php app/console doctrine:schema:create
```

## Usage

Most of the features can be configured or disabled inside the `parameters.yml` configuration file.


(to be continued)


## License

- This project is released under the MIT license

- Fuz logo is © 2013-2015 Alain Tiemblo

