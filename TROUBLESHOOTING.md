# Troubleshooting

## Drush command not defined

This module works with Drupal 8 and 9. All the examples in this demo module assume Drush 10 is used. If you are using another version, the commands and their aliases might be different. Execute `./vendor/bin/drush list --filter=migrate` to verify the available commands for your version of Drush.

If you are using Drush 10, make sure the [Migrate tools](https://www.drupal.org/project/migrate_tools) is **enabled** on the site. This module provides the commands for executing migrations from the command line.

## Memory limit errors when running Composer commands

Read more about this at the [Composer documentation](https://getcomposer.org/doc/articles/troubleshooting.md#memory-limit-errors). One of the proposed solution is prepending the command with `COMPOSER_MEMORY_LIMIT=-1`. For example:

`COMPOSER_MEMORY_LIMIT=-1 composer create-project drupal/recommended-project:^9.0.0 migrations-intermediate`

## Fetching example data from a local JSON file

The `urls` configuration for the URL source plugin can be configured to read from a local file. In those cases it accepts either an absolute path or relative path from the Drupal's root folder. When using a relative path it is assumed that:

1. You have placed this demo module under the `modules/custom` folder.
1. The module's folder itself is renamed to `ud_course`.

Therefore, the JSON file should be located at `modules/custom/ud_course/sources/`. **Whether you clone this repository or download a ZIP file with its content, you need to rename the folder appropriately.** Otherwise, you will get an error similar to:

```
[error]  Could not retrieve source count from ud_course_json_node: file parser plugin: could not retrieve data from modules/custom/ud_course/sources/ud_migrations_intermediate_data.json
```

## Disappearing paragraphs

Paragraphs migrations are affected by a particular behavior of revisioned entities. If the host entity is deleted, and the paragraphs do not have translations, they are scheduled to be deleted in the next cron run. That means that deleting a node will eventually remove its referenced paragraphs. Because of this, if the node migration is rolled back and cron is executed, the paragraphs will be removed without the migrate API being aware of it. In this example, execute the following commands to trigger this behavior:

```
# Roll back the node migration.
./vendor/bin/drush migrate:rollback ud_course_json_node

# Execute cron.
./vendor/bin/drush cron

# Check the status of the paragraph migrations. It reports all items have been imported, but the paragraphs were deleted on cron.
./vendor/bin/drush migrate:status ud_course_json_paragraph_evaluation,ud_course_json_paragraph_review

# Import the node migration again. This operation should fail because entity validation is enabled, but the required paragrahs cannot be set. Those entity references no longer exists.
./vendor/bin/drush migrate:import ud_course_json_node

# Check the messages for the node migration to see the error.
./vendor/bin/drush migrate:messages ud_course_json_node

# Visit the /ud-courses page and notice that there is no migrated content.
```

The purge of orphaned paragraphs is implemented at a [field](https://git.drupalcode.org/project/entity_reference_revisions/-/blob/8.x-1.8/src/Plugin/Field/FieldType/EntityReferenceRevisionsItem.php#L407) and [entity level](https://git.drupalcode.org/project/entity_reference_revisions/-/blob/8.x-1.8/entity_reference_revisions.module#L310) via a [queue worker](https://git.drupalcode.org/project/entity_reference_revisions/-/blob/8.x-1.8/src/Plugin/QueueWorker/OrphanPurger.php). In the past, the deletion was instantaneous. Today, the deletion is deferred to a later moment making this less likely to be noticed. Thanks to Damien McKenna for helping me understand this behavior.

In any migration project, it is common you do roll back operations to test new field mappings or fix errors. Beware of this if you do paragraphs migrations. So, what do you do to recover the deleted paragraphs? You have to roll back the node and paragraphs migrations. Then, you have to import them again. The following snippet shows how to do it:

* Roll back the migrations:
```
./vendor/bin/drush migrate:rollback ud_course_json_node
./vendor/bin/drush migrate:rollback ud_course_json_paragraph_review
./vendor/bin/drush migrate:rollback ud_course_json_paragraph_evaluation
```

* Import the migrations again:
```
./vendor/bin/drush migrate:import ud_course_json_paragraph_evaluation
./vendor/bin/drush migrate:import ud_course_json_paragraph_review
./vendor/bin/drush migrate:import ud_course_json_node
```
