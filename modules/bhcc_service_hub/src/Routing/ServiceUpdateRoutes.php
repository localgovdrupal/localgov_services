<?php

namespace Drupal\bhcc_service_hub\Routing;

use Symfony\Component\Routing\Route;

/**
 * Class ServiceUpdateRoutes
 *
 * @package Drupal\bhcc_service_hub\Routing
 */
class ServiceUpdateRoutes {

  /**
   * Add a route for each service hub to show service update page.
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function routes() {
    $routes = [];

    /** @var \Drupal\bhcc_service_hub\LazyLoader $loader */
    $loader = \Drupal::service('bhcc_service_hub.lazy_loader');

    /** @var \Drupal\bhcc_service_hub\Node\ServiceHUB $node */
    foreach ($loader->loadAll() as $node) {
      $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $node->id());
      $routes['service_update_' . $node->id()] = new Route(
        '/' . $alias . '/update',
        [
          '_controller' => 'Drupal\bhcc_service_hub\Controller\ServiceUpdatePageController::build',
          '_title' => 'Service Status',
          'node' => $node
        ],
        [
          '_permission' => 'access content'
        ]
      );
    }

    return $routes;
  }
}
