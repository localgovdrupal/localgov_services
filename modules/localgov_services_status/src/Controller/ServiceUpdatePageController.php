<?php

namespace Drupal\localgov_services_status\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\localgov_services_status\ServiceUpdates;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ServiceUpdatePageController.
 *
 * @todo - This route should only be active for services with available updates.
 *
 * @package Drupal\localgov_services_status\Controller
 */
class ServiceUpdatePageController extends ControllerBase {

  /**
   * Service updates.
   *
   * @var \Drupal\localgov_services_status\ServiceUpdates
   */
  protected $serviceUpdates;

  /**
   * Constructs a new ServiceUpdatePageController object.
   *
   * @param \Drupal\localgov_services_status\ServiceUpdates $serviceUpdate
   *   The state service.
   */
  public function __construct(ServiceUpdates $serviceUpdate) {
    $this->serviceUpdates = $serviceUpdate;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('localgov_services_status.service_updates')
    );
  }

  /**
   * Build service update page.
   *
   * @param Drupal\node\Entity\Node $node
   *   Service node.
   *
   * @return array
   *   A render array.
   */
  public function build(Node $node) {
    $build = [];

    $build[] = [
      '#theme' => 'page_header',
      '#title' => $this->t('Latest service updates'),
    ];

    $build[] = [
      '#theme' => 'service_updates_page',
      '#items' => $this->serviceUpdates->getUpdatesForPage($node),
    ];

    return $build;
  }

}
