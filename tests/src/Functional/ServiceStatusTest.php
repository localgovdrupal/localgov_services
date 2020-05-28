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
    $this->drupalLogin($this->createUser(['access content', 'bypass node access']));

    // Create a landing page.
    $this->drupalGet('/node/add/localgov_services_landing');
    print_r($this->getSession()->getPage()->getContent());
    $edit = [
      'title[0][value]' => 'Test Service',
      'body[0][summary]' => 'Test service summary',
      'body[0][value]' => 'Test service body',
      'status[value]' => 1,
      'path[0][alias]' => '/service',
    ];
    $this->drupalPostForm(NULL, $edit, 'Save');
    // $this->drupalCreateNode([
    //   'type' => 'localgov_services_landing',
    //   'title' => 'Test Service',
    //   'body' => [
    //     'und' => [
    //       'summary' => 'Test service summary',
    //       'value' => 'Test service body',
    //       'format' => filter_default_format(),
    //     ],
    //   ],
    //   'status' => 1,
    //   'path' => [['alias' => 'service']],
    // ]);

    // Create 10 status updates.
    for ($i = 0; $i < 10; $i++) {
      $this->drupalGet('/node/add/localgov_services_status');
      $edit = [
        'title[0][value]' => 'Test Status ' . $i,
        'body[0][value]' => 'Test service body ' . $i,
        'field_service' => 1,
        'field_service_status' => 'severe-impact',
        'field_service_status_on_landing[value]' => 1,
        'field_service_status_on_list[value]' => 1,
        'status[value]' => 1,
      ];
      $this->drupalPostForm(NULL, $edit, 'Save');
      // $this->drupalCreateNode([
      //   'type' => 'localgov_services_status',
      //   'title' => 'Test Status ' . $i,
      //   'body' => [
      //     'und' => [
      //       'value' => 'Test service body ' . $i,
      //       'format' => filter_default_format(),
      //     ],
      //     'field_service_status_on_landing' => ['und' => ['value' => 1]],
      //     'field_service_status_on_list' => ['und' => ['value' => 1]],
      //   ],
      //   'status' => 1,
      // ]);
    }

    // Check service status updates page.
    $this->drupalGet('/service');
    print_r($this->getSession()->getPage()->getContent());
    $this->drupalGet('/service/update');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Test Status 0');
    $this->assertSession()->pageTextContains('Test Status 1');
    $this->assertSession()->pageTextContains('Test Status 2');
    $this->assertSession()->pageTextContains('Test Status 3');
    $this->assertSession()->pageTextContains('Test Status 4');
    $this->assertSession()->pageTextContains('Test Status 5');
    $this->assertSession()->pageTextContains('Test Status 6');
    $this->assertSession()->pageTextContains('Test Status 7');
    $this->assertSession()->pageTextContains('Test Status 8');
    $this->assertSession()->pageTextContains('Test Status 9');
  }

}
