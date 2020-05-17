<?php

namespace Drupal\localgov_services_page\Node;

use Drupal\localgov_helper\Node\LocalGovNodeInterface;
use Drupal\localgov_helper\Node\NodeBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Entity class for Service info Node pages.
 */
class ServiceInfo extends NodeBase implements LocalGovNodeInterface {

  /**
   * We programmatically set field_parent.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *
   * @throws \Exception
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    // Initially set the field to match the selected service.
    if ($this->getService()) {
      $this->get('field_parent_content')->set(0, $this->getService()[0]['target_id']);
    }

    // If there is a Sub HUB attached to this node, we can update that now.
    if ($this->getSubHub()) {
      $this->get('field_parent_content')->set(0, $this->getSubHub()[0]['target_id']);
    }

    // If neither Service not Sub services landing exists (shouldn't ever happen), then we
    // unset the field.
    if (!$this->getService() && !$this->getSubHub()) {
      $this->get('field_parent_content')->set(0, []);
    }

  }

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
   * {@inheritdoc}
   */
  public function getCTAs() {
    return $this->get('field_common_tasks')->getValue();
  }

  /**
   * Returns the field value for field_override_related_links.
   *
   * @return array
   */
  public function getOverrideRelatedLinks() {
    return $this->getFirst('field_override_related_links');
  }

  /**
   * Returns the field value for field_related_links
   *
   * @return array
   */
  public function getRelatedLinks() {
    return $this->get('field_related_links')->getValue();
  }

  /**
   * Returns the field value for field_hide_related_topics
   *
   * @return array
   */
  public function getHideRelatedTopics() {
    return $this->getFirst('field_hide_related_topics');
  }

  /**
   * Returns the field value for field_topic_term
   *
   * @return array
   */
  public function getRelatedTopics() {
    return $this->get('field_topic_term')->getValue();
  }

  /**
   * Returns the field value for field_private_topics
   *
   * @return array
   */
  public function getPrivateTopics() {
    return $this->get('field_private_topics')->getValue();
  }

  /**
   * Returns the field value for field_download_links
   *
   * @return array
   */
  public function getDownloadLinks() {
    return $this->get('field_download_links')->getValue();
  }

  /**
   * Gets the service field value.
   *
   * @return mixed
   */
  public function getService() {
    return $this->get('field_service')->getValue();
  }

  /**
   * Gets the sub services landing field value.
   *
   * @return mixed
   */
  public function getSubHub() {
    return $this->get('localgov_services_sublanding')->getValue();
  }

  /**
   * Gets the parent content field value.
   *
   * @return mixed
   */
  public function getParentContent() {
    return $this->get('field_parent_content')->getValue();
  }
}
