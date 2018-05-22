<?php

namespace Drupal\bhcc_service_hub;

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

  public $type = 'service_hub';
}
