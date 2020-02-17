<?php

namespace Drupal\bhcc_sub_hub\Node;

use Drupal\bhcc_helper\Node\BHCCNodeInterface;
use Drupal\bhcc_helper\Node\NodeBase;

class SubHub extends NodeBase implements BHCCNodeInterface {

  /**
   * {@inheritdoc}
   */
  public function getPageDescription() {
    if ($this->getDescription() && key_exists('summary', $this->getDescription())) {
      return [
        '#markup' => '<p>' . $this->getDescription()['summary'] . '</p>',
        '#allowed_tags' => ['iframe', 'p']
      ];
    }

    return [];
  }

  /**
   * Get description field value.
   *
   * @return array
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getDescription() {
    if ($this->get('body')->isEmpty()) {
      return false;
    }

    return $this->get('body')->first()->getValue();
  }

  /**
   * Gets the service related to this Sub HUB.
   *
   * @return array
   */
  public function getService() {
    return $this->get('field_service')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function getCTAs() {
    return [];
  }
}
