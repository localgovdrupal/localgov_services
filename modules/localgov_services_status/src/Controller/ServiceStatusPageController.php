<?php

namespace Drupal\localgov_services_status\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\localgov_services_status\ServiceStatus;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ServiceStatusPageController.
 *
 * @package Drupal\localgov_services_status\Controller
 */
class ServiceStatusPageController extends ControllerBase {

  /**
   * Service status.
   *
   * @var \Drupal\localgov_services_status\ServiceStatus
   */
  protected $serviceStatus;

  /**
   * Constructs a new ServiceStatusPageController object.
   *
   * @param \Drupal\localgov_services_status\ServiceStatus $service_status
   *   The state service.
   */
  public function __construct(ServiceStatus $service_status) {
    $this->serviceStatus = $service_status;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('localgov_services_status.service_status')
    );
  }

  /**
   * Build service status page.
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
      '#theme' => 'service_status_page',
      '#items' => $this->serviceStatus->getStatusForPage($node),
    ];

    return $build;
  }

}
