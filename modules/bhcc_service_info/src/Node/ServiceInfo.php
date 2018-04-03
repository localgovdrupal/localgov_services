<?php

namespace Drupal\bhcc_service_info\Node;

use Drupal\bhcc_helper\Node\BHCCNodeInterface;
use Drupal\bhcc_helper\Node\NodeBase;

/**
 * Entity class for Service info Node pages.
 */
class ServiceInfo extends NodeBase implements BHCCNodeInterface {

  /**
   * {@inheritdoc}
   */
  public function getPageDescription() {
    if ($this->getDescription() && key_exists('summary', $this->getDescription())) {
      return [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->getDescription()['summary']
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
   * Returns the field value for field_download_links
   *
   * @return array
   */
  public function getDownloadLinks() {
    return $this->get('field_download_links')->getValue();
  }
}
