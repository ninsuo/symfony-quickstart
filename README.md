Symfony Quick Start
========================

## What is it?

This is the [Symfony3 Standard Edition](https://github.com/symfony/symfony-standard) with some ready-to-use tools to get started quickly.

You'll find here 3 bundles:

- BaseBundle contains well-known bundles implementations, you'll probably not need to modify those files.

- AppBundle is a skeleton, it will contain your app implementation.

- AdminBundle contains tools to manage the application (administrators, roles, groups, etc).

## Ready-to-use bundles

- BootstrapBundle is preinstalled with a base layout, and will automatically render your forms the right way, and display flash messages using the right color.

- HWIOAuthBundle is ready to let your users log-in using their Google, Facebook or Twitter accounts.

- i18n support with language switcher and specific routes for each supported locales.

- KnpMenu awaits your routes and labels as an associative array for logged and non-logged users in a single class; it is already implemented at the top of the base layout.

- Project already implements 403, 404, 500 and generic error pages, no need to worry about the common pitfalls on the subject.

- CRUD generator generates bootstrap-ready and translated views, because if you need this app, you're not against generating most of the application.

- CKeditor and HTMLPurifier are preinstalled to manage rich-text editors (WYSIWYG), just use CKEditorType in your form and |purify filter in your view.

## Installation

```sh
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar update
php app/console assets:install web --symlink
php app/console doctrine:schema:create
```

## Usage

Most of the features can be configured or disabled inside the `parameters.yml` configuration file.

# External services

To use OAuth login, you need to get your client ID and token on each provider:

- GitHub Login: https://github.com/settings/developers
- StackExchange Login: https://stackapps.com/apps/oauth/
- Google Login: https://console.developers.google.com/project
- Twitter Login: https://apps.twitter.com/
- Facebook Login: https://developers.facebook.com/apps/

To use noCaptcha, you should get your site & secret keys:

- noCaptcha: https://www.google.com/recaptcha/admin

## License

- This project is released under the MIT license

- Fuz logo is © 2013-2016 Alain Tiemblo

