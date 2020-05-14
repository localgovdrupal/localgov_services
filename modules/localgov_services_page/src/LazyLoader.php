<?php

namespace Drupal\localgov_services_page;

use Drupal\localgov_helper\LazyLoaderBase;
use Drupal\localgov_helper\LazyLoaderInterface;

/**
 * Class LazyLoader.
 *
 * Provides lazy loader functions for service services landing pages.
 *
 * @package Drupal\localgov_services_page
 */
class LazyLoader extends LazyLoaderBase implements LazyLoaderInterface {

  /**
   * {@inheritdoc}
   */
  public $type = 'localgov_services_page';
}
