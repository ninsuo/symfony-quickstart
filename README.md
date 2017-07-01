Symfony Quick Start
===================

## What is it?

This is the [Symfony3 Standard Edition](https://github.com/symfony/symfony-standard) with some ready-to-use tools to
get started quickly.

This project is based from my personal experience as a lasy backend developer. I create many websites (let's say,
"tools", they don't aim to generate money so far, but more to ease my life). And the same tools are always required:

- twitter bootstrap for having a decent ui when launching the app, and the ability to overwrite layouts using a
wrapbootstrap template in a couple of hours if needed

- a login system, it uses external providers (facebook, twitter...) to get rid of security matters (bruteforce,
dictionary, password reuse and other kind of attacks)

- and many other useful tools, like multi-language support, nested menu, improved crud generator, pagination and table
sorting macros, ckeditor with image browsing and upload, htmlpurifier for proper wysiwyg rendering, useful form types (like
recaptcha, markdown, toggles...)

## Installation

1) Install and run the project:

```sh
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar install
php app/console doctrine:schema:create
```

2) To use the login system, you need to get your client ID and token on at least one provider:

- GitHub Login: https://github.com/settings/developers
- StackExchange Login: https://stackapps.com/apps/oauth/
- Google Login: https://console.developers.google.com/project
- Twitter Login: https://apps.twitter.com/
- Facebook Login: https://developers.facebook.com/apps/

You can disable the providers you don't use during the configuration, or on `app/config/parameters.yml`.

3) Be admin, once you created your first user, update it:

```sql
SELECT * from users;
UPDATE users SET is_admin = 1 WHERE id = <your user id>;
```

4) Feel home:

- Go to the web/ directory and replace icon, logo and image by yours.

## Usage

Most of the features can be configured or disabled inside the `app/config/parameters.yml` configuration file.

You'll find here 3 bundles:

- AdminBundle contains tools to manage the application (users, groups, settings, uploaded image galleries etc).

- BaseBundle contains well-known bundles implementations as well as helpers, overloads and customizations.

- AppBundle is a skeleton, it will contain your app implementation.

Try to never modify `BaseBundle` and `AdminBundle` by yourself, because this project regularly evolves and it will
be easier to upgrade it. I'll someday export those bundles on separate packages.

## Changes

- 01/07/2017: I removed the permissions / denied permissions feature because that's overkill on such a project. If you create the
group "Bob", you'll be able to use `is_granted('ROLE_BOB')` in your code. That's enough for most cases, and stays easier
to understand if some novices should administrate users and roles.



## License

- This project is released under the MIT license

- Fuz logo is © 2013-2017 Alain Tiemblo

- Default image used on social meta tags is CC0
