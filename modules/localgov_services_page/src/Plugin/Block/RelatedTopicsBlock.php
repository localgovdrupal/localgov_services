<?php

namespace Drupal\localgov_services_page\Plugin\Block;

use Drupal\localgov_helper\CurrentPage;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RelatedTopicsBlock
 *
 * @package Drupal\localgov_services_page\Plugin\Block
 *
 * @Block(
 *   id = "related_topics_block",
 *   admin_label = @Translation("Related topics"),
 * )
 */
class RelatedTopicsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\node\NodeInterface
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
      $container->get('localgov_helper.current_page')
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
  public function build() {
    $build = [];

    $links = $this->buildLinks();

    if ($links && !$this->hideRelatedTopics()) {
      $build[] = [
        '#theme' => 'related_topics',
        '#links' => $this->buildLinks(),
      ];
    }

    return $build;
  }

  /**
   * Build links array for the related topics block.
   *
   * @throws
   * @return array
   */
  private function buildLinks() {
    $links = [];

    if ($this->node->hasField('field_topic_term')) {
      /** @var \Drupal\taxonomy\TermInterface $term_info */
      foreach ($this->node->get('field_topic_term')->getValue() as $term_info) {
        $term = Term::load($term_info['target_id']);

        // Add link only if an actual taxonomy term,
        // deleted topics can return NULL if still present.
        if ($term instanceof TermInterface) {
          $links[] = [
            'title' => $term->label(),
            'url' => $term->toUrl(),
          ];
        }
      }
    }

    return $links;
  }

  /**
   * Gets the boolean value for field_hide_related_topics.
   *
   * @throws
   *
   * @return bool
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  private function hideRelatedTopics() {
    if ($this->node->hasField('field_hide_related_topics') && !$this->node->get('field_hide_related_topics')->isEmpty()) {
      return (bool) $this->node->get('field_hide_related_topics')->first()->getValue()['value'];
    }

    return false;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), array('node:' . $this->node->id()));
  }
}
