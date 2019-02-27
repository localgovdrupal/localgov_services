<?php

namespace Drupal\bhcc_service_info\Plugin\Block;

use Drupal\bhcc_helper\CurrentPage;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RelatedLinksBlock
 *
 * @package Drupal\bhcc_service_info\Plugin\Block
 *
 * @Block(
 *   id = "related_links_block",
 *   admin_label = @Translation("Related links"),
 * )
 */
class RelatedLinksBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\node\NodeInterface
   */
  private $node;

  /**
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('bhcc_helper.current_page'),
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentPage $currentPage, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->node = $currentPage->isNode() ? $currentPage->getNode() : false;
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), array('node:' . $this->node->id()));
  }


  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $links = $this->getShouldUseManual() ? $this->buildManual() : $this->buildAutomated();

    if ($links) {
      $build[] = [
        '#theme' => 'related_links',
        '#links' => $links,
      ];
    }

    return $build;
  }

  /**
   * Builds a manual list of links based on the field_related_links field.
   *
   * @return array
   */
  private function buildManual() {
    $links = [];

    if ($this->node->hasField('field_related_links')) {
      foreach ($this->node->get('field_related_links')->getValue() as $link) {
        $links[] = [
          'title' => $link['title'],
          'url' => Url::fromUri($link['uri']),
        ];
      }
    }

    return $links;
  }

  /**
   * Automatically builds a list of links based on the most relevant pages.
   *
   * @return array
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
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  private function getShouldUseManual() {
    if ($this->node->hasField('field_override_related_links')) {
      return $this->node->get('field_override_related_links')->first()->getValue()['value'];
    }

    return false;
  }


  /**
   * Build links array for the related topics block.
   *
   * @return array
   */
  private function getTopics() {
    $topics = [];

    if ($this->node->hasField('field_topic_term')) {

      /** @var \Drupal\taxonomy\TermInterface $term_info */
      foreach ($this->node->get('field_topic_term')->getValue() as $term_info) {
        $topics[] = Term::load($term_info['target_id']);
      }
    }

    return $topics;
  }
}
