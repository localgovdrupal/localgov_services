<?php

namespace Drupal\localgov_services_page\Plugin\Block;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
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
  public function build(): array {
    $build = [];

    $links = $this->displayLinks() ? $this->getLinks() : [];

    if (count($links)) {
      $build[] = [
        '#theme' => 'services_related_links_block',
        '#links' => $links,
      ];
    }

    return $build;
  }

  /**
   * Builds a manual list of links based on the localgov_related_links field.
   *
   * @return array
   *   Array of links.
   */
  private function getLinks(): array {
    $links = [];

    if ($this->node->hasField('localgov_related_links')) {
      foreach ($this->node->get('localgov_related_links')->getValue() as $link) {
        if (isset($link['title']) && isset($link['uri'])) {
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
   * Legacy: Decide if we should use a manual override.
   *
   * Field has been removed from new installs.
   * https://github.com/localgovdrupal/localgov_services/pull/291
   *
   * @return bool
   *   Should manual links be displayed?
   */
  private function displayLinks(): bool {
    if ($this->node->hasField('localgov_override_related_links') && !$this->node->get('localgov_override_related_links')->isEmpty()) {
      return $this->node->get('localgov_override_related_links')->first()->getValue()['value'];
    }

    return TRUE;
  }

}
