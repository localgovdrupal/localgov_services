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
      'create terms in topic',
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

    // Check Services CTA block show on landing pages with external link.
    $edit = [
      'title[0][value]' => 'Test landing page',
      'body[0][summary]' => 'Test landing summary',
      'body[0][value]' => 'Test landing text',
      'status[value]' => 1,
      'field_common_tasks[0][uri]' => 'https://example.com/',
      'field_common_tasks[0][title]' => 'Example button text',
    ];
    $this->drupalPostForm('/node/add/localgov_services_landing', $edit, 'Save');
    $this->drupalGet('/node/1');
    $this->assertSession()->responseContains('href="https://example.com/"');
    $this->assertSession()->pageTextContains('Example button text');

    // Check node title and summary display on service page with internal link.
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
    $this->assertSession()->responseContains('href="/node/1"');
    $this->assertSession()->pageTextContains('Landing page link');

    // Check manually added related links.
    $this->assertSession()->pageTextNotContains('Related Links');
    $edit = [
      'field_override_related_links[value]' => 1,
      'field_related_links[0][uri]' => 'http://test.com/',
      'field_related_links[0][title]' => 'Example related link',
    ];
    $this->drupalPostForm('/node/2/edit', $edit, 'Save');
    $this->drupalGet('/node/2');
    $this->assertSession()->pageTextContains('Related Links');
    $this->assertSession()->responseContains('href="http://test.com/"');
    $this->assertSession()->pageTextContains('Example related link');

    // Check related topics.
    $topic_name = $this->randomMachineName(8);
    $edit = [
      'name[0][value]' => $topic_name,
      'status[value]' => 1,
    ];
    $this->drupalPostForm('/admin/structure/taxonomy/manage/topic/add', $edit, 'Save');
    $this->assertSession()->pageTextNotContains('Related Topics');
    $edit = [
      'field_topic_term[target_id]' => $topic_name . ' (1)',
      'field_hide_related_topics[value]' => 0,
    ];
    $this->drupalPostForm('/node/2/edit', $edit, 'Save');
    $this->drupalGet('/node/2');
    $this->assertSession()->pageTextContains('Related Topics');
    $this->assertSession()->pageTextContains($topic_name);
    $edit = [
      'field_hide_related_topics[value]' => 1,
    ];
    $this->drupalPostForm('/node/2/edit', $edit, 'Save');
    $this->drupalGet('/node/2');
    $this->assertSession()->pageTextNotContains('Related Topics');
    $this->assertSession()->pageTextNotContains($topic_name);
  }

}
