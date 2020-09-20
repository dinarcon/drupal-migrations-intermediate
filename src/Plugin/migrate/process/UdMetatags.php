<?php

namespace Drupal\ud_course\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\metatag\MetatagTagPluginManager;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Processes a custom arrays of metatags.
 *
 * The log plugin will log the values that are being processed by other plugins.
 *
 * Example:
 * @code
 * process:
 *   bar:
 *     plugin: ud_metatags
 *     source: foo
 * @endcode
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "ud_metatags",
 *   handle_multiples = TRUE
 * )
 */
class UdMetatags extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The Metatag tag plugin manager.
   *
   * @var \Drupal\metatag\MetatagTagPluginManager
   */
  protected $tagManager;

  /**
   * Constructs a UdMetatags object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\metatag\MetatagTagPluginManager $tag_manager
   *   The Metatag tag plugin manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MetatagTagPluginManager $tag_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->tagManager = $tag_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.metatag.tag')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_array($value)) {
      $migrate_executable->saveMessage('The ud_metatags process plugin expects an array value.', MigrationInterface::MESSAGE_WARNING);
      throw new MigrateSkipProcessException();
    }

    foreach (['general', 'twitter'] as $expected_key) {
      if (!isset($value[$expected_key]) && !is_array($value[$expected_key])) {
        $migrate_executable->saveMessage("The ud_metatags process plugin expects an array with a '$expected_key' key whose value is another array of corresponding metatags.", MigrationInterface::MESSAGE_INFORMATIONAL);
      }
    }

    $parsed = [];
    $parsed['title'] = $row->get('@title');

    $general_metetags = $value['general'] ?? [];
    foreach ($general_metetags as $name => $val) {
      $parsed[$name] = $this->processMetatagValue($val);
    }

    $twitter_cards_metetags = $value['twitter'] ?? [];
    foreach ($twitter_cards_metetags as $name => $val) {
      $parsed["twitter_cards_$name"] = $this->processMetatagValue($val);
    }

    $parsed['og_type'] = 'article';
    $parsed['article_section'] = $this->configuration['article_section'];
    if ($topics = $row->get($this->configuration['article_tag'])) {
      $parsed['article_tag'] = $this->processMetatagValue($topics);
    }

    if ($this->configuration['remove_unknown_tags']) {
      $allowed_tags = $this->tagManager->getDefinitions();
      $parsed = array_intersect_key($parsed, $allowed_tags);
    }

    $filtered = array_filter($parsed);

    if (empty($filtered)) {
      $migrate_executable->saveMessage('No metatags were set by the ud_metatags process plugin.', MigrationInterface::MESSAGE_WARNING);
      return NULL;
    }

    return serialize($filtered);
  }

  /**
   * Process a metatag value.
   *
   * If the value is an array, return a comma separated string of the array
   * elements. Otherwise, return the value without modifications.
   */
  protected function processMetatagValue($value) {
    return is_array($value) ? implode(', ', $value) : $value;
  }

}
