<?php

namespace Drupal\ud_course_setup\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'ud_metatag_debug_formatter' formatter.
 *
 * @see https://www.drupal.org/project/metatag/issues/3172117
 *
 * @FieldFormatter(
 *   id = "ud_metatag_debug_formatter",
 *   label = @Translation("UD Debug formatter"),
 *   field_types = {
 *     "metatag"
 *   }
 * )
 */
class UdMetatagDebugFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      // Associative array keyed by the metatag names.
      $metetags = unserialize($item->value);
      if (empty($metetags)) {
        continue;
      }

      ksort($metetags);

      $items = [];
      foreach ($metetags as $name => $value) {
        $parsed = is_array($value) ? implode(', ', $value) : $value;
        $items[] = Html::escape("$name: $parsed");
      }

      $elements[$delta] = [
        '#theme' => 'item_list',
        '#list_type' => 'ul',
        '#items' => $items,
      ];
    }

    return $elements ?: [['#plain_text' => $this->t('No meta tags.')]];
  }

}
