<?php

namespace Drupal\bhcc_service_hub\Node;

use Drupal\bhcc_helper\Node\BHCCNodeInterface;
use Drupal\bhcc_helper\Node\NodeBase;

/**
 * @package Drupal\bhcc_service_hub\Node
 */
class ServiceUpdatePage extends NodeBase implements BHCCNodeInterface {

  /**
   * Gets node summary.
   *
   * @return mixed
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getSummary() {
    return $this->get('body')->first()->getValue()['summary'];
  }
}
