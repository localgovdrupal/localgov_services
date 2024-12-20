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
  public function build() {
    $build = [];

    $links = $this->getLinks();

    if ($links) {
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
  private function getLinks() {
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

}
