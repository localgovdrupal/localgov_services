<?php

namespace Drupal\localgov_services_page\Plugin\Block;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;

/**
 * Class DownloadsBlock
 *
 * @package Drupal\localgov_services_page\Plugin\Block
 *
 * @Block(
 *   id = "downloads_block",
 *   admin_label = @Translation("Downloads"),
 * )
 */
class DownloadsBlock extends ServicesBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $links = $this->buildLinks();

    if ($links) {
      $build[] = [
        '#theme' => 'downloads',
        '#links' => $this->buildLinks(),
      ];
    }

    return $build;
  }

  /**
   * Build links array for the related topics block.
   *
   * @return array
   */
  private function buildLinks() {
    $links = [];

    if ($this->node->hasField('field_download_links')) {
      foreach ($this->node->get('field_download_links')->getValue() as $link) {
        $links[] = [
          'title' => $link['title'],
          'url' => Url::fromUri($link['uri']),
        ];
      }
    }

    return $links;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
}
