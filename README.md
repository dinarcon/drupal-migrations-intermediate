# Understand Drupal Migrations Course - Intermediate package

A demo module created by [Mauricio Dinarte](https://www.drupal.org/u/dinarcon) ([@dinarcon](https://twitter.com/dinarcon)) to explain migrations concepts in Drupal.

This module is part of the [Understand Drupal Migrations Course](https://understanddrupal.com/migrations) which can be purchased at https://udrupal.com/get-migrations-course

## Dependencies

The following projects are required to run this demo. The numbers indicate which version of the project was last used for testing.

* [Drupal](https://www.drupal.org/project/drupal) 9.1.5
* [Migrate plus](https://www.drupal.org/project/migrate_plus) 8.x-5.1
* [Migrate tools](https://www.drupal.org/project/migrate_tools) 8.x-5.0
* [Paragraphs](https://www.drupal.org/project/paragraphs) 8.x-1.12
* [Entity reference revisions](https://www.drupal.org/project/entity_reference_revisions) 8.x-1.9
* [Metatag](https://www.drupal.org/project/metatag) 8.x-1.15
* [Token](https://www.drupal.org/project/token) 8.x-1.9
* [Drush](https://github.com/drush-ops/drush) 10.3.6

### Specific Drush version required

Drush `10.4` and later is not compatible with `migrate_tools <= 5`. Until a `6.x` branch is released for `migrate_tools`, Drush needs to be pinned to `^10.3.0` via Composer.

## Examples

This demo includes eight migrations. All of them use a JSON file as the source.

* `ud_course_json_paragraph_evaluation` for importing data into paragraphs entities. `evaluations` are nested under `reviews` paragraphs. There are no dependencies on other migrations.
* `ud_course_json_paragraph_review` for importing data into paragraphs entities. Depends on `ud_course_json_paragraph_evaluation`.
* `ud_course_json_file_main_image` for importing file entities. The images will be associated with media entities. There are no dependencies on other migrations.
* `ud_course_json_file_attachment` for importing file entities. The images will be associated with media entities. There are no dependencies on other migrations.
* `ud_course_json_media_main_image` for importing data into media entities. Depends on `ud_course_json_file_main_image`.
* `ud_course_json_media_attachment` for importing data into media entities. Depends on `ud_course_json_file_attachment`.
* `ud_course_json_node` for importing data into node entities. Depends on `ud_course_json_paragraph_evaluation`, `ud_course_json_file_main_image`, and `ud_course_json_file_attachment`.
* `ud_course_json_path_alias_node` for importing Path alias entities. This migration is **not** detected out of the box because the file extension is `.txt` instead of `.yml`. The aliases are actually imported in the `ud_course_json_node` migration. This migration was added for reference only.

## Instructions

* Install module dependencies via Composer: `composer require 'drupal/migrate_plus:^5.1' 'drupal/migrate_tools:^5.0' 'drupal/paragraphs:^1.12' 'drupal/entity_reference_revisions:^1.9' 'drupal/metatag:^1.15' 'drupal/token:^1.9'`
* Install the **Drush 10.3.x** via Composer: `composer require 'drush/drush:^10.3.0'`. After this step, you may call it via `./vendor/bin/drush`.
* Make sure your Drupal installation has a `/modules/custom` folder. The `modules` folder should exist, but the `custom` sub-folder might not. Create it if needed.
* Download the demo module contained in this repository into the `/modules/custom` folder. You can do this by cloning this repository or [downloading a ZIP file](https://github.com/dinarcon/drupal-migrations-intermediate/archive/main.zip). **Important:** In either case, you need to rename the folder to `ud_course`. Otherwise, the migrations will not find the JSON file used as source data.
* Enable the *UD Course Example Migration* (`ud_course`) module.
* Run the migrations using Drush. See instructions below.
* Check out the [DEVELOPMENT.md](DEVELOPMENT.md) file for instructions on configuring a local development environment.
* Check out the [COMMANDS.md](COMMANDS.md) file for some commands that were used throughout the course.
* Check out the [TROUBLESHOOTING.md](TROUBLESHOOTING.md) file for solutions to common problems.

### Importing the migrations

Import all migrations using the following Drush command:

`./vendor/bin/drush migrate:import --tag='UD Migrations Intermediate Example'`

### Rolling back the migrations

Rollback all migrations using the following Drush command:

`./vendor/bin/drush migrate:rollback --tag='UD Migrations Intermediate Example'`
