<?php

namespace Drupal\localgov_services_page\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;

/**
 * Class CTABlock
 *
 * @package Drupal\localgov_services_page\Plugin\Block
 *
 * @Block(
 *  id = "cta_block",
 *  admin_label = @Translation("Call to actions"),
 *  category = @Translation("LocalGov"),
 * )
 */
class CTABlock extends ServicesBlockBase {

  /**
   * We only show this block if the current node contains some CTA actions.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  protected function blockAccess(AccountInterface $account) {
    if ($this->node &&
      $this->node->hasField('field_common_tasks') &&
      count($this->node->get('field_common_tasks')->getValue()) >= 1
    ) {
      return AccessResult::allowedIf(count($this->node->get('field_common_tasks') >= 1));
    }

    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      $this->getCTAButtons(),
      '#cache' => [
        'tags' => ['node:'.$this->node->id()],
        'contexts' => ['url.path']
      ]
    ];
  }

  /**
   * Returns the call to actions attached to the current page.
   *
   * We do not need to run any conditions as Drupal will only get this far if
   * all conditions in $this::blockAccess are met.
   *
   * @return array
   */
  private function getCTAButtons() {
    $buttons = [];
    foreach ($this->node->get('field_common_tasks')->getValue() as $call_to_action) {

      $type = 'cta-blue';
      if (isset($call_to_action['options']['type']) && $call_to_action['options']['type'] === 'action') {
        $type = 'cta-green';
      }

      $buttons[] = [
        '#theme' => 'button',
        '#title' => $call_to_action['title'],
        '#url' => Url::fromUri($call_to_action['uri']),
        '#type' => $type,
        '#grid' => 'col-sm-4'
      ];
    }
    return $buttons;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
}
