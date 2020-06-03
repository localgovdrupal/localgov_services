<?php

namespace Drupal\Tests\localgov_services\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Functional tests for LocalGovDrupal install profile.
 */
class ServicesBlockTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'localgov_theme';

  /**
   * {@inheritdoc}
   */
  protected $profile = 'localgov';

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'localgov_services_landing',
    'localgov_services_page',
    'localgov_services_sublanding',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'access content',
      'administer nodes',
      'bypass node access',
      'create localgov_services_landing content',
      'create localgov_services_page content',
      'create localgov_services_sublanding content',
      'edit own localgov_services_landing content',
      'edit own localgov_services_page content',
      'edit own localgov_services_sublanding content',
    ]);
  }

  /**
   * Test blocks display.
   */
  public function testServiceCoreBlocksDisplay() {
    $this->drupalLogin($this->adminUser);

    // Check Services CTA block shows on landing pages with external link.
    $edit = [
      'title[0][value]' => 'Test landing page',
      'body[0][summary]' => 'Test landing text',
      'status[value]' => 1,
      'field_common_tasks[0][uri]' => 'https://example.com/',
      'field_common_tasks[0][title]' => 'Example button text',
    ];
    $this->drupalPostForm('/node/add/localgov_services_landing', $edit, 'Save');
    $this->drupalGet('/node/1');
    print_r($this->getSession()->getPage()->getText());
    $this->assertSession()->pageTextContains('href="https://example.com/"');
    $this->assertSession()->pageTextContains('Example button text');

    // Check node title and summary display on a landing page.
    $edit = [
      'title[0][value]' => 'Test services page',
      'body[0][summary]' => 'Test services summary text',
      'body[0][value]' => 'Test services body text',
      'status[value]' => 1,
      'field_common_tasks[0][uri]' => 'Test landing page (1)',
      'field_common_tasks[0][title]' => 'Landing page link',
    ];
    $this->drupalPostForm('/node/add/localgov_services_page', $edit, 'Save');
    $this->drupalGet('/node/2');
    $this->assertSession()->pageTextContains('href="/node/1"');
    $this->assertSession()->pageTextContains('Landing page link');
  }

}
