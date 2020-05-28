<?php

namespace Drupal\localgov_services_status;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Class ServiceUpdates.
 *
 * @package Drupal\localgov_services_status
 *
 * @todo - Update this service to use dependency injection.
 */
class ServiceUpdates {

  /**
   * Entity Type Manager service.
   *
   * @var Drupal\Core\Entity\EntityTypeManager
   */
  private $entityTypeManager;

  /**
   * Initialise a ServiceUpdate.
   *
   * @param Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   *   Entity Type Manager service.
   */
  public function __construct(EntityTypeManager $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Returns the latest 2 status updates for the service landing page.
   *
   * @param \Drupal\node\Entity\Node $node
   *   Service landing page node to get status pages for.
   *
   * @return array
   *   Array of item variables to render in Twig template.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getUpdatesForBlock(Node $node) {
    return $this->getStatusUpdates($node, 2);
  }

  /**
   * Returns the latest 10 status updates for the service landing page.
   *
   * @param \Drupal\node\Entity\Node $node
   *   Service landing page node to get status pages for.
   *
   * @return array
   *   Array of item variables to render in Twig template.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getUpdatesForPage(Node $node) {
    return $this->getStatusUpdates($node, 10);
  }

  /**
   * Returns the latest $n status updates for the service landing page.
   *
   * @param \Drupal\node\Entity\Node $node
   *   Service landing page node to get status pages for.
   * @param int $n
   *   Number of status updates to fetch.
   *
   * @return array
   *   Array of n items to render in Twig template.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getStatusUpdates(Node $node, $n) {
    $query = $this->entityTypeManager->getStorage('node')->getQuery()
      ->condition('type', 'localgov_services_status')
      ->condition('field_service', $node->id())
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('created', 'DESC')
      ->range(0, $n)
      ->execute();

    $service_update = $this->entityTypeManager
      ->getStorage('node')
      ->loadMultiple($query);

    $items = [];
    foreach ($service_update as $node) {
      if ($node->getType() == 'localgov_services_status') {
        $items[] = [
          'date' => $node->getCreatedTime(),
          'title' => $node->label(),
          'description' => $node->get('body')->first()->getValue()['summary'],
          'url' => $node->toUrl(),
        ];
      }
    }
    return $items;
  }

}
