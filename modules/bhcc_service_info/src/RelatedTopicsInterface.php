<?php

namespace Drupal\bhcc_service_info;

/**
 * Interface RelatedTopicsInterface
 *
 * @package Drupal\bhcc_service_info
 */
interface RelatedTopicsInterface {

  /**
   * Return bool whether or not to display the related topics block.
   *
   * @return bool
   */
  public function relatedTopicsDisplay();

  /**
   * Return a list of related topics.
   *
   * @return array
   */
  public function relatedTopicsList();

}
