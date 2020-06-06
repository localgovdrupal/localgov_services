<?php

namespace Drupal\Tests\localgov_services_status\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\path_alias\AliasRepositoryInterface;

/**
 * Tests path alias for status maintained with landing pages.
 *
 * @group localgov_services
 */
class PathTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'field',
    'path',
    'path_alias',
    'node',
    'options',
    'system',
    'text',
    'user',
    'localgov_services_status',
    'localgov_services',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('path_alias');
    $this->installConfig(['node']);
    $this->installConfig(['localgov_services']);
    $this->installConfig(['localgov_services_status']);
  }

  /**
   * Test creating, loading, updating and deleting aliases through PathItem.
   */
  public function testPathItem() {
    $alias_repository = \Drupal::service('path_alias.repository');
    assert($alias_repository instanceof AliasRepositoryInterface);

    $node = Node::create([
      'title' => 'Test Landing Page',
      'type' => 'localgov_services_landing',
      'path' => ['alias' => '/foo'],
    ]);
    $node->save();

    $status_alias = $alias_repository->lookupBySystemPath('/node/' . $node->id() . '/status', $node->language()->getId());
    $this->assertEquals('/foo/status', $status_alias['alias']);

  }

}
