<?php

namespace Drupal\bhcc_service_info\Plugin\Block;

use Drupal\bhcc_helper\CurrentPage;
use Drupal\bhcc_service_info\ListBuilder;
use Drupal\bhcc_service_info\RelatedTopicsInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RelatedTopicsBlock
 *
 * @package Drupal\bhcc_service_info\Plugin\Block
 *
 * @Block(
 *   id = "related_topics_block",
 *   admin_label = @Translation("Related topics"),
 * )
 */
class RelatedTopicsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\bhcc_service_info\RelatedTopicsInterface
   */
  protected $node;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('bhcc_helper.current_page')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentPage $currentPage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->node = $currentPage->getNode();
  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIf(
      $this->node instanceof RelatedTopicsInterface &&
      $this->node->relatedTopicsDisplay()
    );
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
    $list = new ListBuilder();

    foreach ($this->node->relatedTopicsList() as $relatedTopic) {
      if ($term = Term::load($relatedTopic['target_id'])) {
        $list->addLink([
          'title' => $term->label(),
          'url' => Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $term->id()])
        ]);
      }
    }

    return $list->render();
  }
}
