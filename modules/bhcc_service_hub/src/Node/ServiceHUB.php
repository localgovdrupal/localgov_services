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
   * Check if node contains phone number for people with hearing disabilities.
   *
   * @return bool
   */
  public function hasHearingDifficultiesPhone() {
    return !$this->get('field_hearing_difficulties_phone')->isEmpty();
  }

  /**
   * Get phone number for people with hearing disabilities field.
   *
   * @return array
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getHearingDifficultiesPhone() {
    return $this->get('field_hearing_difficulties_phone')->first()->getValue();
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

  /**
   * Check if node has Email address field.
   *
   * @return bool
   */
  public function hasEmailAddress() {
    return !$this->get('field_email_address')->isEmpty();
  }

  /**
   * Get Email address field.
   *
   * @return array
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getEmailAddress() {
    return $this->get('field_email_address')->first()->getValue();
  }

  /**
   * Check if node has first line of address field.
   *
   * @return bool
   */
  public function hasAddressFirstLine() {
    return !$this->get('field_address_first_line')->isEmpty();
  }

  /**
   * Get first line of address field.
   *
   * @return array
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getAddressFirstLine() {
    return $this->get('field_address_first_line')->first()->getValue();
  }

  /**
   * Check if node a link to map.
   *
   * @return bool
   */
  public function hasLinkToMap() {
    return !$this->get('field_link_to_map')->isEmpty();
  }

  /**
   * Get the link to a map.
   *
   * @return array
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getLinkToMap() {
    return $this->get('field_link_to_map')->first()->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function getCTAs() {
    return $this->get('field_common_tasks')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function showServiceUpdates() {
    if ($this->get('field_enable_service_updates')->isEmpty()) {
      return false;
    }

    return (bool) $this->get('field_enable_service_updates')->first()->getValue()['value'];
  }
}
