<?php

namespace Drupal\localgov_services_page\Plugin\Block;

use Drupal\localgov_services\Plugin\Block\ServicesBlockBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\TermInterface;

/**
 * Provides a 'Services Related Topics Block' block.
 *
 * @package Drupal\localgov_services_page\Plugin\Block
 *
 * @Block(
 *   id = "localgov_services_related_topics_block",
 *   admin_label = @Translation("Service page related topics"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node", label = @Translation("Node"), required = TRUE)
 *   }
 * )
 */
class ServicesRelatedTopicsBlock extends ServicesBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $links = [];
    $node = $this->getContextValue('node');

    if ($node && $node->hasField('localgov_topic_classified')) {
      /** @var \Drupal\taxonomy\TermInterface $term_info */
      foreach ($node->get('localgov_topic_classified')->getValue() as $term_info) {
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

    if ($links && !$this->hideRelatedTopics()) {
      $build[] = [
        '#theme' => 'services_related_topics_block',
        '#links' => $links,
      ];
    }

    return $build;
  }

  /**
   * Gets the boolean value for localgov_hide_related_topics.
   *
   * @return bool
   *   Should related topics be displayed?
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  private function hideRelatedTopics() {
    $node = $this->getContextValue('node');
    if ($node->hasField('localgov_hide_related_topics') && !$node->get('localgov_hide_related_topics')->isEmpty()) {
      return (bool) $node->get('localgov_hide_related_topics')->first()->getValue()['value'];
    }

    return FALSE;
  }

}
