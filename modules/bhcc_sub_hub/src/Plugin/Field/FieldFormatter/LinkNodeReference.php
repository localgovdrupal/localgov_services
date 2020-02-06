<?php

namespace Drupal\bhcc_sub_hub\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\link\Plugin\Field\FieldType\LinkItem;
use http\Exception\UnexpectedValueException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LinkNodeReference
 *
 * @package Drupal\bhcc_sub_hub\Plugin\Field\FieldFormatter
 *
 * @FieldFormatter(
 *   id = "link_node_reference",
 *   module = "bhcc_sub_hub",
 *   label = @Translation("Node reference"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class LinkNodeReference extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, \Drupal\Core\Field\FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $build = [];

    foreach ($items as $item) {
      if ($item->isExternal()) {
        $build[] = $this->buildExternal($item);
      }
      else {
        $build[] = $this->buildInternal($item);
      }
    }

    return $build;
  }

  /**
   * Build the render array for external links.
   *
   * @param $item
   *
   * @return array
   */
  private function buildExternal(LinkItem $item) {
    return [
      '#theme' => 'dummy_teaser',
      '#title' => $item->getValue()['title'],
      '#url' => Url::fromUri($item->getValue()['uri'])
    ];
  }

  /**
   * Build the render array for internal links.
   *
   * @param $item
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  private function buildInternal(LinkItem $item) {
    try {
      $params = $item->getUrl()->getRouteParameters();
      $entity_type = key($params);
      $entity = $this->entityTypeManager->getStorage($entity_type)->load($params[$entity_type]);

      if ($entity) {
        $view_builder = $this->entityTypeManager->getViewBuilder($entity->getEntityTypeId());
        return $view_builder->view($entity, 'teaser', $entity->language()->getId());
      } else {
        return [];
      }
    }
    // Fallback to buildExternal() if the internal route is not valid.
    catch (\UnexpectedValueException $exception) {
      return $this->buildExternal($item);
    }
  }
}
