<?php

namespace Drupal\bhcc_service_hub\Node;

use Drupal\bhcc_helper\Node\BHCCNodeInterface;
use Drupal\bhcc_helper\Node\NodeBase;
use Drupal\Core\Render\Markup;

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
    $summary = $this->get('body')->first()->getValue()['summary'];

    if (!$summary) {
      $body = strip_tags($this->getBody());
      $body = explode('.', $body)[0];
      $summary = $body . '…';
    }

    return Markup::create($summary);
  }

  public function getBody() {
    return $this->get('body')->first()->getValue()['value'];
  }
}
