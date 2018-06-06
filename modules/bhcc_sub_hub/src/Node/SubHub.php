<?php

namespace Drupal\bhcc_sub_hub\Node;

use Drupal\bhcc_helper\Node\BHCCNodeInterface;
use Drupal\bhcc_helper\Node\NodeBase;

class SubHub extends NodeBase implements BHCCNodeInterface {

  /**
   * {@inheritdoc}
   */
  public function getPageDescription() {
    return $this->getDescription()['value'];
  }

  /**
   * Get description field value.
   *
   * @return array
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getDescription() {
    return $this->get('body')->first()->getValue();
  }

  public function getService() {
    return $this->get('field_service')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function getCTAs() {
    return $this->get('field_common_tasks')->getValue();
  }
}
