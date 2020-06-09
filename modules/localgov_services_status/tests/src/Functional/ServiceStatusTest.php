<?php

namespace Drupal\Tests\localgov_services_status\Functional;

use Drupal\node\NodeInterface;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\node\Traits\NodeCreationTrait;

/**
 * Tests localgov services pages working together, and with external modules.
 *
 * @group localgov_services
 */
class ServiceStatusTest extends BrowserTestBase {

  use NodeCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'field_ui',
    'path',
    'localgov_services_status',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'access administration pages',
      'access content',
      'access content overview',
      'administer content types',
      'administer node fields',
      'administer nodes',
      'bypass node access',
      'create url aliases',
    ]);
  }

  /**
   * Test necessary fields have been added.
   */
  public function testServiceStatusFields() {
    $this->drupalLogin($this->adminUser);

    // Check all fields exist.
    $this->drupalGet('/admin/structure/types/manage/localgov_services_status/fields');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('body');
    $this->assertSession()->pageTextContains('field_service_status');
    $this->assertSession()->pageTextContains('localgov_services_parent');
    $this->assertSession()->pageTextContains('field_service_status_on_landing');
    $this->assertSession()->pageTextContains('field_service_status_on_list');

    // Create a landing page.
    $landing = $this->createNode([
      'title' => 'Test Service',
      'type' => 'localgov_services_landing',
      'status' => NodeInterface::PUBLISHED,
    ]);

    // Create a status page.
    $this->drupalGet('/node/add/localgov_services_status');
    $edit = [
      'title[0][value]' => 'Test Status',
      'body[0][value]' => 'Test status body',
      'localgov_services_parent' => $landing->id(),
      'field_service_status' => '0-severe-impact',
      'field_service_status_on_landing[value]' => 1,
      'field_service_status_on_list[value]' => 1,
      'status[value]' => 1,
    ];
    $this->drupalPostForm(NULL, $edit, 'Save');
    $this->assertSession()->pageTextContains('Test Status');
    $this->assertSession()->pageTextContains('Test status body');

    // See a status message on a landing page.
    // Hide a status message on a landing page.
  }

  /**
   * Test listings.
   */
  public function testServiceListingPages() {
    $this->drupalLogin($this->adminUser);

    // Create a landing page.
    $landing = $this->createNode([
      'type' => 'localgov_services_landing',
      'status' => NodeInterface::PUBLISHED,
    ]);

    // Create some status updates.
    for ($i = 1; $i <= 3; $i++) {
      $this->createNode([
        'type' => 'localgov_services_status',
        'title' => 'Test Status ' . $i,
        'body' => 'Test service body ' . $i,
        'localgov_services_parent' => $landing->id(),
        'field_service_status' => '0-severe-impact',
        'field_service_status_on_landing' => 1,
        'field_service_status_on_list' => 1,
        'status' => NodeInterface::PUBLISHED,
      ]);
    }

    // Check service status updates page.
    $this->drupalGet('node/' . $landing->id() . '/status');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Test Status 1');
    $this->assertSession()->pageTextContains('Test Status 2');
    $this->assertSession()->pageTextContains('Test Status 3');

    // Check the service-status page.
    $this->drupalGet('/service-status');
    $this->assertSession()->statusCodeEquals(200);
    $xpath = '//ul[@id="tabs"]/li/a';
    /** @var \Behat\Mink\Element\NodeElement[] $results */
    $results = $this->xpath($xpath);
    $this->assertContains('Test Status 1', $results[0]->getText());
    $this->assertContains('Test Status 2', $results[1]->getText());
    $this->assertContains('Test Status 3', $results[2]->getText());

    // Check sticky on top works.
    $edit = [
      'sticky[value]' => 1,
    ];
    $this->drupalPostForm('/node/4/edit', $edit, 'Save');
    $this->drupalGet('/service-status');
    $xpath = '//ul[@id="tabs"]/li/a';
    /** @var \Behat\Mink\Element\NodeElement[] $results */
    $results = $this->xpath($xpath);
    $this->assertContains('Test Status 3', $results[2]->getText());
    $this->assertContains('Test Status 1', $results[0]->getText());
    $this->assertContains('Test Status 2', $results[1]->getText());

    // Check unpublish.
    $edit = [
      'status[value]' => NodeInterface::NOT_PUBLISHED,
    ];
    $this->drupalPostForm('/node/3/edit', $edit, 'Save');

    $this->drupalGet('/node/' . $landing->id() . '/status');
    $this->assertSession()->pageTextNotContains('Test Status 2');
    $this->drupalGet('/service-status');
    $this->assertSession()->pageTextNotContains('Test Status 2');

    // Check hide from lists.
    $edit = [
      'field_service_status_on_list[value]' => 0,
    ];
    $this->drupalPostForm('/node/2/edit', $edit, 'Save');

    $this->drupalGet('/node/' . $landing->id() . '/status');
    $this->assertSession()->pageTextNotContains('Test Status 1');
    $this->drupalGet('/service-status');
    $this->assertSession()->pageTextNotContains('Test Status 1');

    // Check service status updates page with no valid statuses.
    $edit = [
      'field_service_status_on_list[value]' => 0,
    ];
    $this->drupalPostForm('/node/4/edit', $edit, 'Save');
    $this->drupalGet('node/' . $landing->id() . '/status');
    $this->assertSession()->statusCodeEquals(403);
  }

  /**
   * Test paths.
   *
   * @see \Drupal\Tests\localgov_services_status\Kernel\PathTest
   */
  public function testServiceStatusPath() {
    // Create a landing page.
    $landing = $this->createNode([
      'type' => 'localgov_services_landing',
      'status' => NodeInterface::PUBLISHED,
    ]);

    $this->createNode([
      'type' => 'localgov_services_status',
      'title' => 'Test Status',
      'body' => 'Test service body',
      'localgov_services_parent' => $landing->id(),
      'field_service_status' => '0-severe-impact',
      'field_service_status_on_landing' => 1,
      'field_service_status_on_list' => 1,
      'status' => NodeInterface::PUBLISHED,
    ]);

    $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $landing->id());
    $this->drupalGet($alias . '/status');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Test Status');
  }

}
