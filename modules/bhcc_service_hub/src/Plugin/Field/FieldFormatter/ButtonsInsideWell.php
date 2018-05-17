<?php

namespace Drupal\bhcc_service_hub\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Language\LanguageInterface;

/**
 * Class ButtonsInsideWell
 *
 * @package Drupal\bhcc_service_hub\Plugin\Field\FieldFormatter
 *
 * @FieldFormatter(
 *   id = "button_inside_well",
 *   module = "bhcc_service_hub",
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
      $elements[$delta] = [
        '#theme' => 'button',
        '#title' => $item->getValue()['title'],
        '#url' => $item->getUrl(),
        '#type' => $item->getValue()['options']['type'] === 'basic' ? 'cta-green' : 'cta-blue'
      ];
    }

    return $elements;
  }
}
