Symfony Quick Start
===================

## Disclaimer

This project might have a better audience if I worked on it to create a CMS-like stuff that would do everything once
installed. But it first aims to help myself: I don't want to bother install a login system and the many
things required when I just want to solve a problem my colleagues, friends or I have.

This website helps me create useful and value-added things in a matter of hours instead of days or weeks.
That's the point. As this project uses many well-known bundles, you may adopt it as well for the same purpose.

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
git clone https://github.com/ninsuo/symfony-quickstart.git
cd symfony-quickstart
rm -r .git
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar install
php app/console doctrine:schema:create
```

2) To use the login system, you need to get your client ID and secret on at least one provider:

| Provider       | Setup URL                                     |
| -------------- | --------------------------------------------- |
| GitHub         | https://github.com/settings/developers        |
| StackExchange  | https://stackapps.com/apps/oauth/             |
| Google         | https://console.developers.google.com/project |
| Twitter        | https://apps.twitter.com/                     |
| Facebook       | https://developers.facebook.com/apps/         |

3) Be enabled and admin! Once you created your first user, you can run the following commands:

```sh
# list your users, from here you can find your id (should be 1, but stay safe)
php app/console user:list

# enable user with id = 42
php app/console user:enable 42

# set user with id = 42 as admin
php app/console user:admin 42
```

Note that those commands are toggles, so running `php app/console user:admin 42` a second time will remove admin
privileges for the given user.

4) Feel home:

Go to the web/ directory and replace icon, logo and image by yours.

## Usage

### Configuration

Most of the features can be configured or disabled inside the `app/config/parameters.yml` configuration file.

I give you an example with how twigfiddle.com would be configured in a quickstart application.

```yaml
parameters:

    # General
    site_url: https://twigfiddle.com
    site_brand: twigfiddle
    site_description: twigfiddle.com provides a small development environment to develop, run, store and access Twig code online
    site_title: Develop, run, store and access Twig code online
    site_keywords: twig,symfony,fiddle
    site_author: Alain Tiemblo

    # Database (just put pdo_sqlite to switch)
    database_driver:   pdo_mysql
    database_host:     127.0.0.1
    database_port:     ~
    database_name:     twigfiddle
    database_user:     twigfiddle
    database_password: ¯\_(ツ)_/¯
    database_path:     '%kernel.root_dir%/data.db3'

    # Emails (actually unused at all by twigfiddle.com)
    mailer_transport:    smtp
    mailer_encryption:   ssl
    mailer_host:         127.0.0.1
    mailer_port:         465
    mailer_user:         ~
    mailer_password:     ~
    mailer_sender_email: auto@fuz.org
    mailer_sender_name:  fuz.org

    # Choose flag names in: src/BaseBundle/Resources/public/img/countries
    # Implement translations in: src/AppBundle/translations/messages.XX.xlf
    # /!\ Do not forget to add your new locales in config.yml, below jms_i18n_routing
    locale: en
    supported_locales:
        en: {flag: United-Kingdom, menu: English}
        fr: {flag: France, menu: Français}

    # A secret key that's used to generate certain security-related tokens
    secret: ThisTokenIsNotSoSecretChangeIt

    # GitHub Login: https://github.com/settings/developers
    github_enabled:   true
    github_client_id: <your client id>
    github_secret:    <your secret>

    # StackExchange Login: https://stackapps.com/apps/oauth/
    stack_enabled: true
    stack_client_id: <your client id>
    stack_secret:    <your secret>
    stack_key:       <your key>

    # Google Login: https://console.developers.google.com/project
    google_enabled: true
    google_client_id: <your client id>
    google_secret:    <your secret>

    # Twitter Login: https://apps.twitter.com/
    twitter_enabled: true
    twitter_client_id: <your client id>
    twitter_secret:    <your secret>

    # Facebook Login: https://developers.facebook.com/apps/
    facebook_enabled: true
    facebook_client_id: <your client id>
    facebook_secret:    <your secret>

    # User can only join if he has an email that matches the following regex (ex: '!@example\.org$!')
    user_email_restriction: ~

    # If you wish to create a private website but can't use the restriction above, you can
    # let admins enable users manually by setting this option to false.
    user_auto_enabled: true

    # When user connects using a provider (Facebook, ...), your app can synchronize user info (firstname, email...)
    # automatically. If you prefer your admins having the ability to edit user names, this option should be set
    # to false.
    user_info_auto_update: true

    # Website menus at the top
    menu_left_enabled: true
    menu_right_enabled: true

    # Members can remove their account (there are no explicit links in the application to do it, you'll
    # have to add a `<a href="{{ path('unsubscribe') }}>Unsubscribe</a>` somewhere in your app).
    accounts_removable: true

    # Recaptcha (if you wish to use the RecaptchaType on a form, you should set up an application
    # at https://www.google.com/recaptcha/admin and set your private and public keys here)
    recaptcha_public: <your recaptcha site key>
    recaptcha_private: <your recaptcha secret>

    # CKEditor has been integrated to support image uploads. To ensure that anybody can't add anything,
    # this feature requires a role. If you wish your users from the "writers" group to access this feature,
    # you should set ROLE_WRITERS here.
    role_file_upload: ROLE_ADMIN
```
### Bundles

You'll find here 3 bundles:

- AdminBundle contains tools to manage the application (users, groups, settings, uploaded image galleries etc).

- BaseBundle contains well-known bundles implementations as well as helpers, overloads and customizations.

- AppBundle is a skeleton, it will contain your app implementation.

Try to never modify `BaseBundle` and `AdminBundle` by yourself, because this project regularly evolves and it will
be easier to upgrade it. I'll someday export those bundles on separate packages.

## License

- This project is released under the MIT license

- Fuz logo is © 2013-2017 Alain Tiemblo

- Default image used on social meta tags is CC0

