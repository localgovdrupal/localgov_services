<?php

namespace Drupal\bhcc_service_info\Plugin\Block;

use Drupal\bhcc_helper\CurrentPage;
use Drupal\bhcc_service_info\ListBuilder;
use Drupal\bhcc_service_info\RelatedLinksInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RelatedLinksBlock
 *
 * @todo - This is no longer only used in servide info pages and should
 *         therefore live in a more general module like helper or admin.
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
   * @var \Drupal\bhcc_service_info\RelatedLinksInterface
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
   * Show on all nodes that implement the RelatedLinks interface.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIf($this->node instanceof RelatedLinksInterface);
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
    return $this->node->relatedLinksManualOverride() ? $this->buildManual() : $this->buildAutomated();
  }

  /**
   * Automatically builds a list of links based on the most relevant pages.
   *
   * @return array
   */
  private function buildAutomated() {
    // Convert topics field into an array we can use in the query.
    $topics = [];

    if (empty($this->node->relatedLinksTopics())) {
      return [];
    }

    foreach ($this->node->relatedLinksTopics() as $relatedTopic) {
      $topics[] = $relatedTopic['target_id'];
    }

    if ($relatedTopic) {
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
          ':tids[]' => $topics
        ]
      );

      $list = new ListBuilder();
      foreach ($query->fetchAll() as $result) {
        $node = Node::load($result->entity_id);
        $list->addLink([
          'title' => $node->getTitle(),
          'url' => Url::fromRoute('entity.node.canonical', ['node' => $node->id()]),
          'type' => 'link'
        ]);
      }

      return $list->render();
    }

    return [];
  }

  /**
   * Builds a manual list of links based on the field_related_links field.
   *
   * @return array
   */
  private function buildManual() {
    $list = new ListBuilder();
    $list->addAllFromLinkField($this->node->relatedLinksOverridden());
    return $list->render();
  }
}
