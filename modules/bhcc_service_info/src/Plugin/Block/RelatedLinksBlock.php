<?php

namespace Drupal\bhcc_service_info\Plugin\Block;

use Drupal\bhcc_helper\CurrentPage;
use Drupal\bhcc_service_info\ListBuilder;
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
 * @package Drupal\bhcc_service_info\Plugin\Block
 *
 * @Block(
 *   id = "related_links_block",
 *   admin_label = @Translation("Related links"),
 * )
 */
class RelatedLinksBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\bhcc_helper\CurrentPage
   */
  private $currentPage;

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
    $this->currentPage = $currentPage;
    $this->database = $database;
  }

  /**
   * Only show the form on Service info pages.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  protected function blockAccess(AccountInterface $account) {
    if ($this->currentPage->getNode()->getOverrideRelatedLinks()['value']) {
      // Check we have some links to render.
      return AccessResult::allowedIf(!empty($this->currentPage->getNode()->getRelatedLinks()));
    }
    else {
      // Check we have at least 1 topic set.
      return AccessResult::allowedIf(!empty($this->currentPage->getNode()->getRelatedTopics()));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), array('node:' . $this->currentPage->getNode()->id()));
  }


  /**
   * {@inheritdoc}
   */
  public function build() {
    return $this->currentPage->getNode()->getOverrideRelatedLinks()['value'] ? $this->buildManual() : $this->buildAutomated();
  }

  /**
   * Automatically builds a list of links based on the most relevant pages.
   *
   * @return array
   */
  private function buildAutomated() {
    // Convert topics field into an array we can use in the query.
    $topics = [];
    foreach ($this->currentPage->getNode()->getRelatedTopics() as $relatedTopic) {
      $topics[] = $relatedTopic['target_id'];
    }

    // Add private terms to the query.
    foreach ($this->currentPage->getNode()->getPrivateTopics() as $privateTopic) {
      $topics[] = $privateTopic['target_id'];
    }

    // Perform our query.
    $query = $this->database->query('SELECT entity_id FROM node__field_all_topics
WHERE entity_id != :nid AND field_all_topics_target_id IN (:tids[])
GROUP BY entity_id
ORDER BY count(*)
LIMIT 6;',
      [
        ':nid' => $this->currentPage->getNode()->id(),
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

  /**
   * Builds a manual list of links based on the field_related_links field.
   *
   * @return array
   */
  private function buildManual() {
    $list = new ListBuilder();
    $list->addAllFromLinkField($this->currentPage->getNode()->getRelatedLinks());
    return $list->render();
  }
}
