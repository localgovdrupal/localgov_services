<?php

namespace Drupal\localgov_services_status\Plugin\Block;

use Drupal\condition_field\ConditionAccessResolver;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'ServiceStatusMessage' block.
 *
 * @Block(
 *  id = "service_status_message",
 *  admin_label = @Translation("Service status message"),
 * )
 */
class ServiceStatusMessage extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Condition\ConditionManager definition.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $pluginManagerCondition;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Visible status messages.
   *
   * @var \Drupal\node\NodeInterface[]
   */
  protected $statusNodes = [];

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.condition'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Service status message block constructor.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Condition\ConditionManager $plugin_manager_condition
   *   The plugin manager condition service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $plugin_manager_condition, $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->pluginManagerCondition = $plugin_manager_condition;
    $this->entityTypeManager = $entity_type_manager;

    // Load service status nodes to display.
    $nids = $this->entityTypeManager->getStorage('node')->getQuery()
      ->condition('type', 'localgov_services_status')
      ->condition('status', 1)
      ->execute();
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);

    foreach ($nodes as $node) {
      if (!$node->get('localgov_service_status_visibile')->isEmpty()) {
        $conditions = [];
        $conditions_config = $node->get('localgov_service_status_visibile')->getValue()[0]['conditions'];

        foreach ($conditions_config as $condition_id => $values) {
          /** @var \Drupal\Core\Condition\ConditionInterface $condition */
          $conditions[] = $this->pluginManagerCondition->createInstance($condition_id, $values);
        }

        if (ConditionAccessResolver::checkAccess($conditions, 'or')) {
          $this->statusNodes[] = $node;
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $build['#theme'] = 'service_status_message';
    $build['#items'] = [];
    foreach ($this->statusNodes as $node) {
      $build['#items'][] = $this->entityTypeManager->getViewBuilder('node')->view($node, 'message');
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    if (empty($this->statusNodes)) {
      return AccessResult::neutral();
    }
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    // Vary cache by route.
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    // Invalidate cache on changes to localgov_services_status nodes.
    return Cache::mergeTags(parent::getCacheTags(), ['node_list:localgov_services_status']);
  }

}
