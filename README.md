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

### Bundles

You'll find here 3 bundles:

- AdminBundle contains tools to manage the application (users, groups, settings, uploaded image galleries etc).

- BaseBundle contains well-known bundles implementations as well as helpers, overloads and customizations.

- AppBundle is a skeleton, it will contain your app implementation.

Try to never modify `BaseBundle` and `AdminBundle` by yourself, because this project regularly evolves and it will
be easier to upgrade it. I'll someday export those bundles on separate packages.

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

    # Database
    database_driver:   pdo_mysql
    database_host:     127.0.0.1
    database_port:     ~
    database_name:     twigfiddle
    database_user:     twigfiddle
    database_password: ¯\_(ツ)_/¯

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
    # If you enable it and use twitter provider, explicitely configure your app to request email permission.
    registration_restriction: ~

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

    # When user connects using a provider (Facebook, ...), your app can synchronize user info (firstname, email...)
    # automatically. If you prefer your admins having the ability to edit user names, this option should be set
    # to false.
    user_info_auto_update: true
```

## License

- This project is released under the MIT license

- Fuz logo is © 2013-2017 Alain Tiemblo

- Default image used on social meta tags is CC0
