<?php

namespace Drupal\localgov_services_page\Plugin\Block;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\TermInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\localgov_services\Plugin\Block\ServicesBlockBase;

/**
 * Provides a 'Services Related Links Block' block.
 *
 * @package Drupal\localgov_services_page\Plugin\Block
 *
 * @Block(
 *   id = "localgov_services_related_links_block",
 *   admin_label = @Translation("Service page related links"),
 * )
 */
class ServicesRelatedLinksBlock extends ServicesBlockBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    // @todo Replace the empty array below with $this->buildAutomated().
    $links = $this->getShouldUseManual() ? $this->buildManual() : [];

    if ($links) {
      $build[] = [
        '#theme' => 'services_related_links_block',
        '#links' => $links,
      ];
    }

    return $build;
  }

  /**
   * Builds a manual list of links based on the field_related_links field.
   *
   * @return array
   *   Array of links.
   */
  private function buildManual() {
    $links = [];

    if ($this->node->hasField('field_related_links')) {
      foreach ($this->node->get('field_related_links')->getValue() as $link) {
        if (isset($link['title']) and isset($link['uri'])) {
          $links[] = [
            'title' => $link['title'],
            'url' => Url::fromUri($link['uri']),
          ];
        }
      }
    }

    return $links;
  }

  /**
   * Automatically builds a list of links based on the most relevant pages.
   *
   * @return array
   *   Array of links.
   */
  private function buildAutomated() {
    // Convert topics field into an array we can use in the query.
    $topics = [];

    foreach ($this->getTopics() as $topic) {
      $topics[] = $topic->id();
    }

    if ($topics) {
      // Perform our query.
      $query = $this->database->query('SELECT entity_id FROM node__field_all_topics
  LEFT JOIN node_field_data ON node_field_data.nid=node__field_all_topics.entity_id
  WHERE node__field_all_topics.entity_id != :nid
  AND node__field_all_topics.field_all_topics_target_id IN (:tids[])
  AND node_field_data.status=1
  GROUP BY node__field_all_topics.entity_id
  ORDER BY count(*) desc
  LIMIT 6;',
        [
          ':nid' => $this->node->id(),
          ':tids[]' => $topics,
        ]
      );

      $list = [];
      foreach ($query->fetchAll() as $result) {
        $node = Node::load($result->entity_id);
        $list[] = [
          'title' => $node->getTitle(),
          'url' => $node->toUrl(),
        ];
      }

      return $list;
    }

    return [];
  }

  /**
   * Decide if we should use a manual override.
   *
   * @return bool
   *   Should manual links be displayed?
   */
  private function getShouldUseManual() {
    if ($this->node->hasField('field_override_related_links') && !$this->node->get('field_override_related_links')->isEmpty()) {
      return $this->node->get('field_override_related_links')->first()->getValue()['value'];
    }

    return FALSE;
  }

  /**
   * Build links array for the related topics block.
   *
   * @return array
   *   Array of topics.
   */
  private function getTopics() {
    $topics = [];

    if ($this->node->hasField('field_topic_term')) {

      /** @var \Drupal\taxonomy\TermInterface $term_info */
      foreach ($this->node->get('field_topic_term')->getValue() as $term_info) {
        $topicEntity = Term::load($term_info['target_id']);

        // Add topic only if an actual taxonomy term,
        // deleted topics can return NULL if still present on the node.
        if ($topicEntity instanceof TermInterface) {
          $topics[] = $topicEntity;
        }
      }
    }

    return $topics;
  }

}