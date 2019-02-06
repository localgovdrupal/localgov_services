<?php

namespace Drupal\bhcc_service_info;

/**
 * Interface RelatedLinksInterface
 *
 * @package Drupal\bhcc_service_info
 */
interface RelatedLinksInterface {

  /**
   * Specify whether or not to use the manual override.
   *
   * @return bool
   */
  public function relatedLinksManualOverride();

  /**
   * Returns an array of topics tids which will be used to build a query to find
   * related nodes.
   *
   * @return array
   */
  public function relatedLinksTopics();

  /**
   * Returns an array of links to be used when we are overriding the related
   * links section manually.
   *
   * @return array
   */
  public function relatedLinksOverridden();
}
