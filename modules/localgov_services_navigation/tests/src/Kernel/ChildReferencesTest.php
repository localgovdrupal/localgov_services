<?php

namespace Drupal\Tests\localgov_services_navigation\Kernel;

use Drupal\field\Entity\FieldConfig;
use Drupal\Tests\pathauto\Functional\PathautoTestHelperTrait;
use Drupal\KernelTests\KernelTestBase;
use Drupal\localgov_services_navigation\EntityChildRelationshipUi;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Tests\field\Traits\EntityReferenceTestTrait;
use Drupal\Tests\node\Traits\ContentTypeCreationTrait;
use Drupal\Tests\node\Traits\NodeCreationTrait;
use Drupal\user\Entity\User;

/**
 * Kernel test check Services Pathauto.
 *
 * @group localgov_services_navigation
 */
class ChildReferencesTest extends KernelTestBase {

  use ContentTypeCreationTrait;
  use EntityReferenceTestTrait;
  use NodeCreationTrait;
  use PathautoTestHelperTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'system',
    'field',
    'field_group',
    'text',
    'link',
    'user',
    'node',
    'path',
    'file',
    'entity_reference_revisions',
    'path_alias',
    'paragraphs',
    'pathauto',
    'taxonomy',
    'token',
    'views',
    'filter',
    'localgov_core',
    'localgov_services',
    'localgov_services_navigation',
    'localgov_services_landing',
    'localgov_services_sublanding',
  ];

  /**
   * Service Landing page.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $serviceLanding;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setup();

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('paragraph');
    $this->installEntitySchema('taxonomy_term');
    $this->installEntitySchema('path_alias');
    $this->installSchema('node', ['node_access']);
    $this->installConfig([
      'filter',
      'pathauto',
      'node',
      'system',
      'localgov_core',
      'localgov_services',
      'localgov_services_navigation',
      'localgov_services_landing',
      'localgov_services_sublanding',
    ]);

    // Create a content type to put into services.
    $this->createContentType(['type' => 'page']);
  }

  /**
   * Test all referenced children returned.
   */
  public function testReferencedChildren() {
    // Workaround https://www.drupal.org/project/drupal/issues/3056234
    User::create([
      'name' => '',
      'uid' => 0,
    ])->save();

    // Create a service.
    $service_landing = $this->createNode([
      'title' => 'Landing Page 1',
      'type' => 'localgov_services_landing',
      'status' => NodeInterface::PUBLISHED,
    ]);

    $this->assertEmpty(EntityChildRelationshipUi::referencedChildren($service_landing));

    // Node in the entity reference fields.
    $node = $this->createNode([
      'title' => 'Page 1',
      'type' => 'page',
    ]);
    $node->save();
    $node = Node::load($node->id());
    $service_landing->localgov_destinations->appendItem(['target_id' => $node->id()]);
    $this->assertEquals(EntityChildRelationshipUi::referencedChildren($service_landing), [$node->id()]);

    // Node in the action link fields.
    $node = $this->createNode([
      'title' => 'Page 2',
      'type' => 'page',
    ]);
    $node->save();
    $node = Node::load($node->id());
    $service_landing->localgov_common_tasks->appendItem(['uri' => 'internal:' . $node->toUrl()->toString()]);
    $ids = EntityChildRelationshipUi::referencedChildren($service_landing);
    $this->assertCount(2, $ids);
    $this->assertTrue(in_array($node->id(), $ids));

    // Node in the action link fields that was entered with the path alias.
    // I can get it to report this in the UI including $link->getValue() being
    // internal:/foo, but ->getUrl returning the routed value but I can't
    // reproduce it in this test.
    // @codingStandardsIgnoreStart
    // $node = $this->createNode([
    //   'title' => 'Page 3',
    //   'type' => 'page',
    //   'path' => ['alias' => '/foo'],
    // ]);
    // $node->save();
    // $node = Node::load($node->id());
    // $this->assertEntityAlias($node, '/foo');
    // $this->assertAliasExists(['path' => '/node/' . $node->id(), 'alias' => '/foo']);
    // $service_landing->localgov_common_tasks->appendItem(['uri' => 'internal:/foo']);
    // $service_landing->save();
    // $service_landing = Node::load($service_landing->id());
    // $ids = EntityChildRelationshipUi::referencedChildren($service_landing);
    // $this->assertCount(3, $ids);
    // $this->assertTrue(in_array($node->id(), $ids));
    // @codingStandardsIgnoreEnd

    $service_sublanding = $this->createNode([
      'title' => 'Sublanding Page 1',
      'type' => 'localgov_services_sublanding',
      'status' => NodeInterface::PUBLISHED,
    ]);
    $this->assertEmpty(EntityChildRelationshipUi::referencedChildren($service_sublanding));
    // Node in the entity reference fields.
    $service_path = '/' . $this->randomMachineName(8);
    $node = $this->createNode([
      'title' => 'Page 1',
      'type' => 'page',
      'path' => ['alias' => $service_path],
    ]);
    $header = $this->randomMachineName(12);
    $tlb_header = Paragraph::create([
      'type' => 'topic_list_builder',
      'topic_list_header' => ['value' => $header],
      'topic_list_links' => [
        'uri' => 'internal:' . $node->toUrl()->toString(),
        'title' => 'Example internal link',
      ],
    ]);
    $tlb_header->save();
    $service_sublanding->localgov_topics->appendItem($tlb_header);
    $service_sublanding->save();

    $ids = EntityChildRelationshipUi::referencedChildren($service_sublanding);
    $this->assertCount(1, $ids);
    $this->assertTrue(in_array($node->id(), $ids));
  }

  /**
   * Allow landing page reference to children.
   */
  public function testAddChildTarget() {
    // New content type. Only the default bundles will be allowed on landing
    // page destinations.
    $destinations = FieldConfig::loadByName('node', 'localgov_services_landing', 'localgov_destinations');
    $settings = $destinations->getSetting('handler_settings');
    $this->assertArrayNotHasKey('page', $settings['target_bundles']);

    // Add 'localgov_services_parent' field to reference landing page.
    $this->createEntityReferenceField(
      'node',
      'page',
      'localgov_services_parent',
      'localgov_services_parent',
      'node',
      'localgov_services',
      [
        'target_bundles' => [
          'localgov_services_landing',
        ],
      ]
    );
    $destinations = FieldConfig::loadByName('node', 'localgov_services_landing', 'localgov_destinations');
    $settings = $destinations->getSetting('handler_settings');
    $this->assertArrayHasKey('page', $settings['target_bundles']);

    // Removing field, should remove it from the bundles list.
    $field = FieldConfig::loadByName('node', 'page', 'localgov_services_parent');
    $field->delete();
    $destinations = FieldConfig::loadByName('node', 'localgov_services_landing', 'localgov_destinations');
    $settings = $destinations->getSetting('handler_settings');
    $this->assertArrayNotHasKey('page', $settings['target_bundles']);
  }

}
