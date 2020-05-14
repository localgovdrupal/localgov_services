<?php

namespace Drupal\localgov_services_landing\Node;

use Drupal\localgov_helper\Node\LocalGovNodeInterface;
use Drupal\localgov_helper\Node\NodeBase;
use Drupal\Core\Render\Markup;

/**
 * @package Drupal\localgov_services_landing\Node
 */
class ServiceUpdatePage extends NodeBase implements LocalGovNodeInterface {

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
