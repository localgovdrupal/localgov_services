<?php

namespace Drupal\localgov_services\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;

/**
 * Provides a 'Services CTA Block' block.
 *
 * @package Drupal\localgov_services\Plugin\Block
 *
 * @Block(
 *  id = "localgov_service_cta_block",
 *  admin_label = @Translation("Services call to action"),
 *  context_definitions = {
 *    "node" = @ContextDefinition("entity:node", label = @Translation("Node"), required = TRUE)
 *   }
 * )
 */
class ServicesCtaBlock extends ServicesBlockBase {

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    // We only show this block if the current node contains some CTA actions.
    $node = $this->getContextValue('node');
    if ($node &&
      $node->hasField('localgov_common_tasks') &&
      count($node->get('localgov_common_tasks')->getValue()) >= 1
    ) {
      return AccessResult::allowed();
    }
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $buttons = [];
    $node = $this->getContextValue('node');
    if (empty($node)) {
      return [];
    }

    foreach ($node->get('localgov_common_tasks')->getValue() as $call_to_action) {
      $type = 'cta-info';
      if (isset($call_to_action['options']['type']) && $call_to_action['options']['type'] === 'action') {
        $type = 'cta-action';
      }

      if (isset($call_to_action['title']) && isset($call_to_action['uri'])) {
        $buttons[] = [
          'title' => $call_to_action['title'],
          'url' => Url::fromUri($call_to_action['uri']),
          'type' => $type,
        ];
      }
    }

    return [
      '#theme' => 'services_cta_block',
      '#buttons' => $buttons,
      '#cache' => [
        'tags' => ['node:' . $node->id()],
        'contexts' => ['url.path'],
      ],
    ];
  }

}
