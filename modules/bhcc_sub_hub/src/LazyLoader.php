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

  public $type = 'sub_hub';

  /**
   * Loads all Sub hub pages with the selected service.
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
