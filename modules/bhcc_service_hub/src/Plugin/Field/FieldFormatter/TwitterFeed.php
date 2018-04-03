<?php

namespace Drupal\bhcc_service_hub\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Class TwitterFeed
 *
 * @package Drupal\bhcc_service_hub\Plugin\Field\FieldFormatter
 *
 * @FieldFormatter(
 *   id = "twitter_feed",
 *   module = "bhcc_service_hub",
 *   label = @Translation("Twitter feed"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class TwitterFeed extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'html_tag',
        '#tag' => 'a',
        '#value' => 'Twitter timeline - ' . $item->getUrl()->toString(),
        '#attributes' => [
          'class' => ['twitter-timeline'],
          'href' => $item->getUrl()->toString(),
          'height' => 500
        ],
        '#attached' => [
          'library' => ['bhcc_service_hub/twitter_timeline']
        ]
      ];
    }

    return $elements;
  }
}
