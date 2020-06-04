<?php

namespace Drupal\localgov_services_status\Routing;

use Drupal\node\NodeInterface;
use Symfony\Component\Routing\Route;

/**
 * Class ServiceStatusRoutes.
 *
 * @package Drupal\localgov_services_status\Routing
 */
class ServiceStatusRoutes {

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

    // Load all service landing page nodes.
    $landing_nids = \Drupal::entityQuery('node')
      ->condition('type', 'localgov_services_landing')
      ->condition('status', NodeInterface::PUBLISHED)
      ->execute();
    foreach ($landing_nids as $nid) {

      // Check for service status nodes that can be displayed.
      $status_nids = \Drupal::entityQuery('node')
        ->condition('type', 'localgov_services_status')
        ->condition('field_service_status_on_list', 1)
        ->condition('status', NodeInterface::PUBLISHED)
        ->execute();

      if (!empty($status_nids)) {
        $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $nid);
        $routes['service_status_' . $nid] = new Route(
          '/' . $alias . '/status',
          [
            '_controller' => 'Drupal\localgov_services_status\Controller\ServiceStatusPageController::build',
            '_title' => 'Latest service status',
            'node' => $nid,
          ],
          [
            '_permission' => 'access content',
          ]
        );
      }
    }

    return $routes;
  }

}
