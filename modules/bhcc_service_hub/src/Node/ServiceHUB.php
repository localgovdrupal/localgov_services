<?php

namespace Drupal\bhcc_service_hub\Node;

use Drupal\bhcc_helper\Node\BHCCNodeInterface;
use Drupal\bhcc_helper\Node\NodeBase;

/**
 * Entity class for the Service HUB Node type.
 *
 * @package Drupal\bhcc_service_hub\Node
 */
class ServiceHUB extends NodeBase implements BHCCNodeInterface {

  /**
   * {@inheritdoc}
   */
  public function getPageDescription() {
    return $this->getDescription()['value'];
  }

  /**
   * Check if node contains phone number.
   *
   * @return bool
   */
  public function hasPhoneNumber() {
    return !$this->get('field_phone')->isEmpty();
  }

  /**
   * Get phone number field.
   *
   * @return array
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getPhoneNumber() {
    return $this->get('field_phone')->first()->getValue();
  }

  /**
   * Check if node has Twitter field.
   *
   * @returns bool
   */
  public function hasTwitter() {
    return !$this->get('field_twitter')->isEmpty();
  }

  /**
   * Get Twitter field.
   *
   * @return array
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getTwitter() {
    return $this->get('field_twitter')->first()->getValue();
  }

  /**
   * Check if node has Facebook field.
   *
   * @return bool
   */
  public function hasFacebook() {
    return !$this->get('field_facebook')->isEmpty();
  }

  /**
   * Get Facebook field.
   *
   * @return array
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getFacebook() {
    return $this->get('field_facebook')->first()->getValue();
  }
}
