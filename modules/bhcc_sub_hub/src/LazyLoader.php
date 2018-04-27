<?php

namespace Drupal\bhcc_sub_hub;

use Drupal\bhcc_helper\LazyLoaderBase;
use Drupal\bhcc_helper\LazyLoaderInterface;

/**
 * Class LazyLoader.
 *
 * Provides lazy loader functions for service hub pages.
 *
 * @package Drupal\bhcc_service_hub
 */
class LazyLoader extends LazyLoaderBase implements LazyLoaderInterface {

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return 'sub_hub';
  }
}
