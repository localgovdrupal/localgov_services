<?php

namespace Drupal\localgov_services_status\Routing;

use Drupal\node\NodeInterface;
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

    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'localgov_services_landing')
      ->condition('status', NodeInterface::PUBLISHED)
      ->execute();
    foreach ($nids as $nid) {
      $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $nid);
      $routes['service_update_' . $nid] = new Route(
        '/' . $alias . '/update',
        [
          '_controller' => 'Drupal\localgov_services_status\Controller\ServiceUpdatePageController::build',
          '_title' => 'Latest service updates',
          'node' => $nid,
        ],
        [
          '_permission' => 'access content',
        ]
      );
    }

    return $routes;
  }

}
