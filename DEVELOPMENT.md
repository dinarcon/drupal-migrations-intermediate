# Development

This module can be used in any development environment: DrupalVM, DDEV, Lando, Docksal, LAMP, MAMP, WAMP, etc. Another option is to use the `quick-start` command that comes with Drupal core. Follow the instructions below to use this approach.

## Using Drupal core's quick-start command

Drupal core comes with a [quick-start command](https://www.drupal.org/docs/installing-drupal/drupal-quick-start-command) that can be used for local development. It will install Drupal using a SQLite database and start PHP's built-in server.

These instructions also use the [recommended-project composer template](https://www.drupal.org/docs/develop/using-composer/starting-a-site-using-drupal-composer-project-templates) for installing Drupal 9. This setup assumes you have [Composer](https://getcomposer.org/) installed. To run Drupal 9 with this set you need to meet the following [minimum requirements](https://www.drupal.org/docs/understanding-drupal/how-drupal-9-was-made-and-what-is-included/environment-requirements-of):

* PHP 7.3. Check with this command: `php --version`
* SQLite 3.26. Check with this command: `sqlite3 --version`
* Drush 10 for running the migrations. Check with this command: `./vendor/bin/drush --version`

```
# Get Drupal 9 via composer.
composer create-project drupal/recommended-project:^9.0.0 migrations-intermediate

# If you get memory limit errors when running composer, prepend the command with
# COMPOSER_MEMORY_LIMIT=-1
# More information at https://getcomposer.org/doc/articles/troubleshooting.md#memory-limit-errors
COMPOSER_MEMORY_LIMIT=-1 composer create-project drupal/recommended-project:^9.0.0 migrations-intermediate

# Change directory.
cd migrations-intermediate

# Add Olivero. This is necessary for Drupal 9.1 onwards.
composer require 'drupal/olivero:^1.0'

# Add Drush.
composer require 'drush/drush'

# Add contrib modules.
composer require 'drupal/migrate_plus:^5.1' 'drupal/migrate_tools:^5.0' 'drupal/paragraphs:^1.12' 'drupal/entity_reference_revisions:^1.8' 'drupal/metatag:^1.14' 'drupal/token:^1.7'

# At the time of publication, a patch is needed for migrate_tools module.
# The following 3 commands are only needed until this issue is resolved:
# https://www.drupal.org/node/3117485

# Add ability to patch modules.
composer require 'cweagans/composer-patches'

# Edit composer.json file per instructions in patching migrate_tools module
# section.
vim composer.json

# Apply the patch.
composer install

# Create modules/custom folder.
mkdir -p web/modules/custom

# Get demo module with GIT over HTTPS.
cd web/modules/custom && git clone https://github.com/dinarcon/drupal-migrations-intermediate.git && mv drupal-migrations-intermediate ud_course && rm -rf ud_course/.git && cd ../../..

# Or using GIT over SSH.
cd web/modules/custom && git clone git@github.com:dinarcon/drupal-migrations-intermediate.git && mv drupal-migrations-intermediate ud_course && rm -rf ud_course/.git && cd ../../..

# Or using CURL.
cd web/modules/custom && curl -LO https://github.com/dinarcon/drupal-migrations-intermediate/archive/main.zip && unzip main.zip && rm main.zip && mv drupal-migrations-intermediate-main ud_course && cd ../../..

# Or using WGET.
cd web/modules/custom && wget https://github.com/dinarcon/drupal-migrations-intermediate/archive/main.zip && unzip main.zip && rm main.zip && mv drupal-migrations-intermediate-main ud_course && cd ../../..

# Install Drupal. The built-in server might stop working from time to time. If
# that is the case, press Ctrl-C to quit the Drupal development server. Then run
# the same command again to restart the development server.
php web/core/scripts/drupal quick-start standard --site-name "UnderstandDrupal.com/migrations" --suppress-login

# Enable the modules.
./vendor/bin/drush pm-enable --yes ud_course ud_course_setup migrate migrate_plus migrate_tools media media_library paragraphs entity_reference_revisions metatag metatag_open_graph metatag_twitter_cards token

# Set Olivero as the default (frontend) theme.
drush theme-enable olivero && drush --yes config:set olivero.settings debug 0 && drush --yes config:set system.theme default olivero

# Set Claro as the admin theme.
drush theme-enable claro && drush --yes config:set system.theme admin claro

# Import content.
./vendor/bin/drush migrate:import --tag='UD Migrations Intermediate Example'

# Check the imported content at /ud-courses
# See TROUBLESHOOTING.md if there are any issues.

# Rollback content.
./vendor/bin/drush migrate:rollback --tag='UD Migrations Intermediate Example'

```

If you are using a different development environment, make sure to meet Drupal's [system requirements](https://www.drupal.org/docs/system-requirements).


## Patching migrate_tools module

Add the following snippet as a direct child under the `extra` section in `composer.json`:

```
"patches": {
  "drupal/migrate_tools": {
    "Migrate import --execute-dependencies fix": "https://www.drupal.org/files/issues/2020-03-06/fetch-migration-requirements-3117485-2.patch"
  }
}
```
