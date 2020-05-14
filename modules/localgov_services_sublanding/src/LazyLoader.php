<?php

namespace Drupal\localgov_services_sublanding;

use Drupal\localgov_helper\LazyLoaderBase;
use Drupal\localgov_helper\LazyLoaderInterface;

/**
 * Class LazyLoader.
 *
 * Provides lazy loader functions for services sublanding pages.
 *
 * @package Drupal\localgov_services
 */
class LazyLoader extends LazyLoaderBase implements LazyLoaderInterface {

  public $type = 'localgov_services_sublanding';

  /**
   * Loads all sub landingpages with the selected service.
   *
   * @param $service_id
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *   Thrown if the entity type doesn't exist.
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Thrown if the storage handler couldn't be loaded.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function loadAllWithService($service_id) {
    return $this->entityTypeManager
      ->getStorage('node')
      ->loadByProperties([
        'type' => $this->type,
        'field_service' => $service_id
      ]);
  }
}
