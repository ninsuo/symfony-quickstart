Symfony Quick Start
========================

## What is it?

This is the [Symfony3 Standard Edition](https://github.com/symfony/symfony-standard) with some ready-to-use tools to get started quickly.

You'll find here 3 bundles:

- BaseBundle contains well-known bundles implementations, you'll probably not need to modify those files.

- AppBundle is a skeleton, it will contain your app implementation.

- AdminBundle contains tools to manage the application (administrators, roles, groups, etc).

## Ready-to-use bundles and tools

- BootstrapBundle is preinstalled with a base layout seo and social friendly, and will automatically render your forms and display your flash messages, paginations and filters the right way.

- HWIOAuthBundle is ready to let your users log-in using their GitHub, Stack Exchange, Google, Facebook or Twitter accounts, each one enabled or not in configuration.

- i18n support with language switcher and specific routes for each supported locales and the whole interfaces already translated in French and English.

- KnpMenu awaits your routes and labels with the right helpers to make menus as simple as possible to manage; it is already implemented at the top of the main layout.

- Project already implements 403, 404, 500 and generic error pages, no need to worry about the common pitfalls on the subject.

- CRUD generator generates bootstrap-ready and translated views, because if you need this app, you're not against generating most of the application. It also integrates an ajax-CRUD generator.

- CKeditor (with image browsing and upload integrated) and HTMLPurifier are preinstalled to manage rich-text editors (WYSIWYG), just use CKEditorType in your form and |purify filter in your view.

- EWZRecaptchaBundle gives a nice ReCaptcha type and validator for sensible parts (registration or contact forms)

- Administration: interfaces to manage users and groups (roles `ROLE_GROUP_<group name>` automatically set for users of a group). You'll need to manually set is_admin = 1 on database for your first admin.

- Settings on the fly: if your app requires global settings set/stored by arbitrary people, use the Setting entity/repository/twig function to quickly get the job done.

- A bunch of utilities to render filters, paginations, absolute urls, get real random numbers, manipulate images...

- Integrated layouts are so simple that you can get all functionalities working in a totally different bootstrap template in a matter of minutes.

## Installation

```sh
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar update
php app/console assets:install web --symlink
php app/console doctrine:schema:create
```

Go to the web/ directory and replace icon, logo and image by yours.

## Usage

Most of the features can be configured or disabled inside the `parameters.yml` configuration file.

# External services

To use OAuth login, you need to get your client ID and token on each provider:

- GitHub Login: https://github.com/settings/developers
- StackExchange Login: https://stackapps.com/apps/oauth/
- Google Login: https://console.developers.google.com/project
- Twitter Login: https://apps.twitter.com/
- Facebook Login: https://developers.facebook.com/apps/

To use reCaptcha, you should get your site & secret keys:

- reCaptcha: https://www.google.com/recaptcha/admin

## License

- This project is released under the MIT license

- Fuz logo is © 2013-2016 Alain Tiemblo

- Default image used on social meta tags is CC0
