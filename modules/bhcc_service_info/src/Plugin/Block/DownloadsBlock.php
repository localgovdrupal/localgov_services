<?php

namespace Drupal\bhcc_service_info\Plugin\Block;

use Drupal\bhcc_helper\CurrentPage;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DownloadsBlock
 *
 * @package Drupal\bhcc_service_info\Plugin\Block
 *
 * @Block(
 *   id = "downloads_block",
 *   admin_label = @Translation("Downloads"),
 * )
 */
class DownloadsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\node\NodeInterface
   */
  private $node;

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
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), array('node:' . $this->node->id()));
  }
}
