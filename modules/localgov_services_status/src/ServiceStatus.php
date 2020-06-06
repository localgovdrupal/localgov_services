<?php

namespace Drupal\localgov_services_status;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Class ServiceStatus.
 *
 * @package Drupal\localgov_services_status
 */
class ServiceStatus {

  /**
   * Entity Type Manager service.
   *
   * @var Drupal\Core\Entity\EntityTypeManager
   */
  private $entityTypeManager;

  /**
   * Initialise a ServiceStatus instance.
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
  public function getStatusForBlock(Node $node) {
    return $this->getStatusUpdates($node, 2, FALSE);
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
  public function getStatusForPage(Node $node) {
    return $this->getStatusUpdates($node, 10, TRUE);
  }

  /**
   * Returns the latest $n status updates for the service landing page.
   *
   * @param \Drupal\node\Entity\Node $landing_node
   *   Service landing page node to get status pages for.
   * @param int $n
   *   Number of status updates to fetch.
   * @param bool $hide_from_list
   *   Whether the statuses array should include items hidden from lists.
   *
   * @return array
   *   Array of n items to render in Twig template.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getStatusUpdates(Node $landing_node, $n, $hide_from_list = FALSE) {
    $query = $this->entityTypeManager->getStorage('node')->getQuery()
      ->condition('type', 'localgov_services_status')
      ->condition('localgov_services_parent', $landing_node->id())
      ->condition('field_service_status_on_list', $hide_from_list)
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('created', 'DESC')
      ->range(0, $n)
      ->execute();
    $service_status = $this->entityTypeManager
      ->getStorage('node')
      ->loadMultiple($query);

    $items = [];
    foreach ($service_status as $node) {
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
