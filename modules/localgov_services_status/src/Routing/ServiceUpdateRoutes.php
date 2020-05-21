<?php

namespace Drupal\localgov_services_landing\Routing;

use Symfony\Component\Routing\Route;

/**
 * Class ServiceUpdateRoutes
 *
 * @package Drupal\localgov_services_landing\Routing
 */
class ServiceUpdateRoutes {

  /**
   * Add a route for each service landing page to show service update page.
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function routes() {
    $routes = [];

    /** @var \Drupal\localgov_services_landing\LazyLoader $loader */
    $loader = \Drupal::service('localgov_services_landing.lazy_loader');

    /** @var \Drupal\localgov_services_landing\Node\ServiceHUB $node */
    foreach ($loader->loadAll() as $node) {
      $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $node->id());
      $routes['service_update_' . $node->id()] = new Route(
        '/' . $alias . '/update',
        [
          '_controller' => 'Drupal\localgov_services_landing\Controller\ServiceUpdatePageController::build',
          '_title' => 'Latest service updates',
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
