<?php

namespace Drupal\localgov_services_landing;

use Drupal\localgov_helper\LazyLoaderBase;
use Drupal\localgov_helper\LazyLoaderInterface;

/**
 * Class LazyLoader.
 *
 * Provides lazy loader functions for service landing page pages.
 *
 * @package Drupal\localgov_services_landing
 */
class LazyLoader extends LazyLoaderBase implements LazyLoaderInterface {

  public $type = 'localgov_services_landing';
}
