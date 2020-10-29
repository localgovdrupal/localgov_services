<?php

namespace Drupal\localgov_services_landing\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derivative class that provides the menu links for the service landing pages.
 * Source https://www.webomelette.com/dynamic-menu-links-drupal-8-plugin-derivatives
 */
class ServicesLandingLink extends DeriverBase implements ContainerDeriverInterface {

   /**
   * @var EntityTypeManagerInterface $entityTypeManager.
   */
  protected $entityTypeManager;

  /**
   * Creates a ServicesLandingLink instance.
   *
   * @param $base_plugin_id
   * @param EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct($base_plugin_id, EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {

    // Get Node storage and do entity query to find all service landing pages.
    $node_storage = $this->entityTypeManager->getStorage('node');
    $service_landing_nids = $node_storage->getQuery()
                                         ->condition('type', 'localgov_services_landing')
                                         ->execute();

    // We assume we don't have too many...
    $service_landing_nodes = $node_storage->loadMultiple($service_landing_nids);
    $links = [];
    foreach ($service_landing_nodes as $id => $node) {
      $links[$id] = [
        'title' => $node->label(),
        'route_name' => $node->toUrl()->getRouteName(),
        'route_parameters' => ['node' => $node->id()]
      ] + $base_plugin_definition;
    }

    return $links;
  }
}
