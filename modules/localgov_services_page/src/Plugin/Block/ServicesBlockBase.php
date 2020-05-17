<?php

namespace Drupal\localgov_services_page\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Service Pages Block base.
 *
 * @package Drupal\localgov_services_page\Plugin\Block
 */
abstract class ServicesBlockBase extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\node\Entity\Node
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
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->node = $this->getNodeFromRoute($route_match);
  }

  /**
   * Get the node from route.
   *
   * @param RouteMatchInterface $route_match
   *   The route to check for node.
   *
   * @return \Drupal\Node\NodeInterface|bool
   */
  protected function getNodeFromRoute(RouteMatchInterface $route_match) {
    $node_param = $route_match->getParameter('node');
    if ($node_param) {
      if ($node_param instanceof NodeInterface) {
        $node = $node_param;
      }
      else {
        $node = Node::load($node_param);
      }

      return $node;
    }

    return FALSE;
  }

}
