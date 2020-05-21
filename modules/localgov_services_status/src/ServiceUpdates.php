<?php

namespace Drupal\localgov_services_landing;

use Drupal\localgov_services_landing\Node\ServiceUpdatePage;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Class ServiceUpdates
 *
 * @package Drupal\localgov_services_landing
 *
 * @todo - Update this service to use dependency injection.
 */
class ServiceUpdates {

  /**
   * Returns the latest 2 updates for the service parameter.
   *
   * @param \Drupal\node\Entity\Node $node
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getUpdatesForBlock(Node $node) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'service_update_page')
      ->condition('field_service', $node->id())
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('created', 'DESC')
      ->range(0, 2)
      ->execute();

    $service_update = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($query);

    $items = [];
    foreach ($service_update as $node) {
      if ($node instanceof ServiceUpdatePage) {
        $items[] = [
          'date' => $node->getCreatedTime(),
          'title' => $node->label(),
          'description' => $node->getSummary(),
          'url' => $node->toUrl()
        ];
      }
    }

    return $items;
  }

  /**
   * Returns the latest 10 updates for the service parameter.
   *
   * @param \Drupal\node\Entity\Node $node
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getUpdatesForPage(Node $node) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'service_update_page')
      ->condition('field_service', $node->id())
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('created', 'DESC')
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
          'title' => $node->label(),
          'description' => $node->getSummary(),
          'url' => $node->toUrl()
        ];
      }
    }

    return $items;
  }
}
