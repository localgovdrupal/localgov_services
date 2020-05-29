<?php

namespace Drupal\Tests\localgov_services\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests localgov services pages working together, and with external modules.
 *
 * @group media_counter
 */
class ServiceStatusTest extends BrowserTestBase {

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
    $this->assertSession()->pageTextContains('field_service');
    $this->assertSession()->pageTextContains('field_service_status_on_landing');
    $this->assertSession()->pageTextContains('field_service_status_on_list');

    // Create a landing page.
    $this->drupalGet('/node/add/localgov_services_landing');
    $this->assertSession()->statusCodeEquals(200);
    $edit = [
      'title[0][value]' => 'Test Service',
      'body[0][summary]' => 'Test service summary',
      'body[0][value]' => 'Test service body',
      'status[value]' => 1,
    ];
    $this->drupalPostForm(NULL, $edit, 'Save');
    $this->assertSession()->pageTextContains('Test Service');
    $this->assertSession()->pageTextContains('Test service body');

    // Create a status page.
    $this->drupalGet('/node/add/localgov_services_status');
    $edit = [
      'title[0][value]' => 'Test Status',
      'body[0][value]' => 'Test status body',
      'field_service' => 1,
      'field_service_status' => 'severe-impact',
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
    $landing_path = '/' . $this->randomMachineName(8);
    $edit = [
      'title[0][value]' => 'Test Service',
      'body[0][summary]' => 'Test service summary',
      'body[0][value]' => 'Test service body',
      'status[value]' => 1,
      'path[0][alias]' => $landing_path,
    ];
    $this->drupalPostForm('/node/add/localgov_services_landing', $edit, 'Save');

    // Create some status updates.
    for ($i = 1; $i <= 3; $i++) {
      $edit = [
        'title[0][value]' => 'Test Status ' . $i,
        'body[0][value]' => 'Test service body ' . $i,
        'field_service' => 1,
        'field_service_status' => 'severe-impact',
        'field_service_status_on_landing[value]' => 1,
        'field_service_status_on_list[value]' => 1,
        'status[value]' => 1,
      ];
      $this->drupalPostForm('/node/add/localgov_services_status', $edit, 'Save');
    }

    // Rebuild caches.
    // This seems to be necessary for the landing page update route to work.
    drupal_flush_all_caches();

    // Check service status updates page.
    $this->drupalGet($landing_path . '/update');
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
      'status[value]' => 0,
    ];
    $this->drupalPostForm('/node/3/edit', $edit, 'Save');
    drupal_flush_all_caches();
    $this->drupalGet($landing_path . '/update');
    $this->assertSession()->pageTextNotContains('Test Status 2');
    $this->drupalGet('/service-status');
    $this->assertSession()->pageTextNotContains('Test Status 2');

    // Check hide from lists.
    $edit = [
      'field_service_status_on_list[value]' => 0,
    ];
    $this->drupalPostForm('/node/2/edit', $edit, 'Save');
    $this->drupalGet('/service-status');
    $this->assertSession()->pageTextNotContains('Test Status 1');

  }

}
