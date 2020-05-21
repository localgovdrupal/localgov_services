<?php

namespace Drupal\localgov_services_landing\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ServiceUpdatePageController
 *
 * @todo - This route should only be active for services with available updates.
 *
 * @package Drupal\localgov_services_landing\Controller
 */
class ServiceUpdatePageController extends ControllerBase {

  /**
   * Build service update page.
   *
   * @param $node
   *
   * @return array
   */
  public function build($node) {
    $build = [];

    $build[] = [
      '#theme' => 'page_header',
      '#title' => $this->t('Latest service updates'),
    ];

//    $build[] = [
//      '#theme' => 'service_status'
//    ];

    $build[] = [
      '#theme' => 'service_updates_page',
      '#items' => \Drupal::service('localgov_services_landing.service_updates')->getUpdatesForPage($node)
    ];

    return $build;
  }
}
