<?php

namespace Drupal\bhcc_service_hub\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ServiceUpdatePageController
 *
 * @package Drupal\bhcc_service_hub\Controller
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
      '#title' => $this->t('Service Status'),
    ];

    $build[] = [
      '#theme' => 'service_status'
    ];

    $build[] = [
      '#theme' => 'service_updates_page',
      '#items' => \Drupal::service('bhcc_service_hub.service_updates')->getUpdatesForPage($node)
    ];

    return $build;
  }
}
