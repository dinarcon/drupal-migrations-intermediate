# Commands

This is a list of some commands used throughout the course.

```
COMPOSER_MEMORY_LIMIT=-1 composer create-project drupal/recommended-project:^9.0.0 migrations-intermediate

cd migrations-intermediate

COMPOSER_MEMORY_LIMIT=-1 composer require 'drupal/olivero:^1.0' 'drush/drush' 'drupal/migrate_plus:^5.1' 'drupal/migrate_tools:^5.0' 'drupal/paragraphs:^1.12' 'drupal/entity_reference_revisions:^1.8' 'drupal/metatag:^1.14' 'drupal/token:^1.7' 'drupal/devel:^4.0' 'drupal/pathauto:^1.8' 'cweagans/composer-patches'

vim composer.json

composer validate

composer install

composer remove drupal/core-project-message

mkdir -p web/modules/custom

cd web/modules/custom && git clone https://github.com/dinarcon/drupal-migrations-intermediate.git && mv drupal-migrations-intermediate ud_course && rm -rf ud_course/.git && cd ../../..

php web/core/scripts/drupal quick-start standard --site-name "UnderstandDrupal.com/migrations" --suppress-login

./vendor/bin/drush theme-enable olivero && drush --yes config:set olivero.settings debug 0 && drush --yes config:set system.theme default olivero

./vendor/bin/drush theme-enable claro && drush --yes config:set system.theme admin claro

./vendor/bin/drush pm-enable --yes ud_course devel pathauto

# Import all migrations.
./vendor/bin/drush migrate:import --tag='UD Migrations Intermediate Example'

# Roll back all migrations.
./vendor/bin/drush migrate:rollback --tag='UD Migrations Intermediate Example'

# Uninstall example module. This removes the included configuration: content type, fields, paragraph types, media types, view, etc.
./vendor/bin/drush pm:uninstall --yes ud_course ud_course_setup
```

```
./vendor/bin/drush list --filter=migrate
./vendor/bin/drush migrate:import ud_course_json_node_init
./vendor/bin/drush migrate:status --fields=id,status,total,imported,unprocessed,last_imported
./vendor/bin/drush migrate:stop ud_course_json_node_init
./vendor/bin/drush migrate:reset-status ud_course_json_node_init
./vendor/bin/drush migrate:rollback ud_course_json_node_init
./vendor/bin/drush cache:rebuild
./vendor/bin/drush migrate:import ud_course_json_node_init
./vendor/bin/drush migrate:messages ud_course_json_node_init
```

```
./vendor/bin/drush migrate:import ud_course_json_node_init --execute-dependencies
./vendor/bin/drush migrate:import --tag='UD Migrations Intermediate Example'
./vendor/bin/drush migrate:import --all

./vendor/bin/drush migrate:rollback --tag='UD Migrations Intermediate Example'
./vendor/bin/drush migrate:rollback --all
```

```
# Connect to the database.
./vendor/bin/drush sql:cli

# At the SQLite prompt.
.help
.tables
.tables media%
.tables paragraph%
.tables node%

SELECT * FROM media__field_ud_media_image;

.headers ON
.mode column

SELECT * FROM media__field_ud_media_image;

.schema media__field_ud_media_image
SELECT bundle, entity_id, field_ud_media_image_target_id, field_ud_media_image_alt FROM media__field_ud_media_image;

.schema node__field_ud_main_image
SELECT bundle, entity_id, delta, field_ud_main_image_target_id FROM node__field_ud_main_image;

.schema media__field_ud_media_attachment
SELECT bundle, entity_id, field_ud_media_attachment_target_id, field_ud_media_attachment_description FROM media__field_ud_media_attachment;

.schema node__field_ud_attachments
SELECT bundle, entity_id, delta, field_ud_attachments_target_id FROM node__field_ud_attachments;

.schema paragraph__field_ud_review_name
SELECT bundle, entity_id, revision_id, delta, field_ud_review_name_value FROM paragraph__field_ud_review_name;

.schema paragraph__field_ud_review_evaluations
SELECT bundle, entity_id, revision_id, delta, field_ud_review_evaluations_target_id, field_ud_review_evaluations_target_revision_id FROM paragraph__field_ud_review_evaluations;

.schema node__field_ud_reviews
SELECT bundle, entity_id, revision_id, delta, field_ud_reviews_target_id, field_ud_reviews_target_revision_id FROM node__field_ud_reviews;

.schema node__field_ud_meta_tags
SELECT bundle, entity_id, field_ud_meta_tags_value FROM node__field_ud_meta_tags;

.exit
```
