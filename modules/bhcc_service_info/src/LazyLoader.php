<?php

namespace Drupal\bhcc_service_info;

use Drupal\bhcc_helper\LazyLoaderBase;
use Drupal\bhcc_helper\LazyLoaderInterface;

/**
 * Class LazyLoader.
 *
 * Provides lazy loader functions for service hub pages.
 *
 * @package Drupal\bhcc_service_info
 */
class LazyLoader extends LazyLoaderBase implements LazyLoaderInterface {

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return 'service_info';
  }
}
