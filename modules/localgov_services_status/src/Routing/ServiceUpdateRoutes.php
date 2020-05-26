<?php

namespace Drupal\localgov_services_status\Routing;

use Drupal\node\Entity\Node;
use Symfony\Component\Routing\Route;

/**
 * Class ServiceUpdateRoutes.
 *
 * @package Drupal\localgov_services_status\Routing
 */
class ServiceUpdateRoutes {

  /**
   * Add a route for each service landing page to show service update page.
   *
   * @return array
   *   Array of routes to service update pages.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function routes() {
    $routes = [];

    $nids = \Drupal::entityQuery('node')->condition('type', 'localgov_services_landing')->execute();
    $nodes = Node::loadMultiple($nids);

    foreach ($nodes as $node) {
      $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $node->id());
      $routes['service_update_' . $node->id()] = new Route(
        '/' . $alias . '/update',
        [
          '_controller' => 'Drupal\localgov_services_status\Controller\ServiceUpdatePageController::build',
          '_title' => 'Latest service updates',
          'node' => $node,
        ],
        [
          '_permission' => 'access content',
        ]
      );
    }

    return $routes;
  }

}
