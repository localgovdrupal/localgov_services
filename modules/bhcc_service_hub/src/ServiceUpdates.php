<?php

namespace Drupal\bhcc_service_hub;

use Drupal\bhcc_service_hub\Node\ServiceUpdatePage;
use Drupal\node\Entity\Node;

/**
 * Class ServiceUpdates
 *
 * @package Drupal\bhcc_service_hub
 */
class ServiceUpdates {

  /**
   * @param \Drupal\node\Entity\Node $node
   * @return array
   */
  public function getUpdatesForBlock(Node $node) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'service_update_page')
      ->condition('field_service', $node->id())
      ->range(0, 10)
      ->execute();

    $service_update = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($query);

    $items = [];
    foreach ($service_update as $node) {
      if ($node instanceof ServiceUpdatePage) {
        $items[] = [
          'date' => $node->getCreatedTime(),
          'description' => $node->getSummary(),
          'url' => $node->toUrl()
        ];
      }
    }

    return $items;
  }
}
