<?php

namespace Drupal\localgov_services_status\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

/**
 * Class ServiceUpdatePageController.
 *
 * @todo - This route should only be active for services with available updates.
 *
 * @package Drupal\localgov_services_status\Controller
 */
class ServiceUpdatePageController extends ControllerBase {

  /**
   * Build service update page.
   *
   * @param Drupal\node\Entity\Node $node
   *   Service node.
   *
   * @return array
   *   A render array.
   */
  public function build(Node $node) {
    $build = [];

    $build[] = [
      '#theme' => 'page_header',
      '#title' => $this->t('Latest service updates'),
    ];

    $build[] = [
      '#theme' => 'service_updates_page',
      '#items' => \Drupal::service('localgov_services_status.service_updates')->getUpdatesForPage($node),
    ];

    return $build;
  }

}
