<?php

namespace Drupal\localgov_services_landing\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Language\LanguageInterface;
use Drupal\link\Plugin\Field\FieldType\LinkItem;

/**
 * Class ButtonsInsideWell
 *
 * @package Drupal\localgov_services_landing\Plugin\Field\FieldFormatter
 *
 * @FieldFormatter(
 *   id = "button_inside_well",
 *   module = "localgov_services_landing",
 *   label = @Translation("Buttons inside well"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class ButtonsInsideWell extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $type = 'cta-blue';
      if (isset($item->getValue()['options']['type']) && $item->getValue()['options']['type'] === 'basic') {
        $type = 'cta-green';
      }

      $elements[$delta] = [
        '#theme' => 'button',
        '#title' => $item->getValue()['title'],
        '#url' => $item->getUrl(),
        '#type' => $type
      ];
    }

    return $elements;
  }
}
