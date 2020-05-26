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
    'localgov_services_status',
  ];

  /**
   * Test necessary fields have been added.
   */
  public function testServiceStatusFields() {
    $web_user = $this->drupalCreateUser([
      'access administration pages',
      'access content overview',
      'administer content types',
      'administer node fields',
      'administer nodes',
      'bypass node access',
    ]);
    $this->drupalLogin($web_user);

    // // Check all fields exist.
    $this->drupalGet('/admin/structure/types/manage/localgov_services_status/fields');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('body');
    $this->assertSession()->pageTextContains('field_localgov_services_status');
    $this->assertSession()->pageTextContains('field_service');
    $this->drupalGet('/admin/structure/types/manage/localgov_services_landing/fields');
    $this->assertSession()->pageTextContains('field_enable_service_updates');

    // Create a landing page.
    $this->drupalGet('/node/add/localgov_services_landing');
    $this->assertSession()->statusCodeEquals(200);
    $edit = [
      'title[0][value]' => 'Test Service',
      'body[0][summary]' => 'Test service summary',
      'body[0][value]' => 'Test service body',
      'field_enable_service_updates[value]' => 1,
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
      'status[value]' => 1,
    ];
    $this->drupalPostForm(NULL, $edit, 'Save');
    $this->assertSession()->pageTextContains('Test Status');
    $this->assertSession()->pageTextContains('Test status body');

  }

}
