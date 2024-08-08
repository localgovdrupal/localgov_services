<?php

namespace Drupal\Tests\localgov_services\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests localgov services pages working with LocalGov Workflows.
 *
 * @group localgov_services
 */
class WorkflowsIntegrationTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $profile = 'testing';

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * A user with permission to bypass content access checks.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * The node storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'localgov_core',
    'localgov_services',
    'localgov_services_landing',
    'localgov_services_sublanding',
    'localgov_services_page',
    'localgov_services_navigation',
    'localgov_workflows',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->drupalPlaceBlock('system_breadcrumb_block');
    // LocalGov Workflows includes access and other checks.
    $this->adminUser = $this->drupalCreateUser([
      'bypass node access',
      'administer nodes',
      'use localgov_editorial transition approve',
      'use localgov_editorial transition archive',
      'use localgov_editorial transition archived_draft',
      'use localgov_editorial transition archived_published',
      'use localgov_editorial transition create_new_draft',
      'use localgov_editorial transition publish',
      'use localgov_editorial transition reject',
      'use localgov_editorial transition submit_for_review',
      'view all scheduled transitions',
      'view any unpublished content',
      'view latest version',
    ]);
    $this->nodeStorage = $this->container->get('entity_type.manager')->getStorage('node');
  }

  /**
   * Post and link test, change workflow status.
   *
   * Post a service landing page.
   * Post a service sub landing page, and link to landing page.
   * Link landing page to sublanding page.
   * Post a page, put it in the landing and sublanding services.
   * Link page from sublanding page.
   */
  public function testPostLink() {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('node/add/localgov_services_landing');
    $form = $this->getSession()->getPage();
    $form->fillField('edit-title-0-value', 'Service 1');
    $form->fillField('edit-body-0-summary', 'Service 1 summary');
    $form->fillField('edit-body-0-value', 'Service 1 description');
    $form->pressButton('edit-submit');
    // Should default to Draft.
    $this->drupalGet('node/add/localgov_services_sublanding');
    $form = $this->getSession()->getPage();
    $form->fillField('edit-title-0-value', 'Sub Service 1');
    $form->fillField('edit-body-0-summary', 'Sub Service 1 summary');
    $form->fillField('edit-body-0-value', 'Sub Service 1 description');
    $form->fillField('edit-localgov-services-parent-0-target-id', 'Service 1 (1)');
    $form->pressButton('edit-submit');
    // Also Draft.
    $this->drupalGet('node/1/edit');
    $form = $this->getSession()->getPage();
    // Check is in Draft.
    $state = $form->findField('edit-moderation-state-0-state');
    $this->assertEquals('draft', $state->getValue());
    // Change to Published.
    $state->setValue('published');
    $form->fillField('edit-localgov-destinations-0-target-id', 'Sub landing 1 (2)');
    $form->pressButton('edit-submit');

    $this->drupalGet('node/add/localgov_services_page');
    $assert = $this->assertSession();
    $form = $this->getSession()->getPage();
    $form->fillField('edit-title-0-value', 'Service 1 Page 1');
    $form->fillField('edit-body-0-summary', 'Service 1 summary 1 ');
    $form->fillField('edit-body-0-value', 'Service 1 description 1');
    $form->fillField('edit-localgov-services-parent-0-target-id', 'Service 1 » Sub landing 1 (2)');
    $form->pressButton('edit-submit');

    $this->drupalGet('node/2/edit');
    $form = $this->getSession()->getPage();
    $state = $form->findField('edit-moderation-state-0-state');
    $this->assertEquals('draft', $state->getValue());
    // Change to Published.
    $state->setValue('published');
    $form->fillField('edit-localgov-topics-0-subform-topic-list-links-0-uri', '/node/3');
    $form->pressButton('edit-submit');

    $assert = $this->assertSession();
    $assert->pageTextContains('Service 1 Page 1');

    $this->drupalLogout();
    $this->drupalGet('node/2');
    $assert->pageTextNotContains('Service 1 Page 1');
    $this->drupalGet('node/3');
    $this->assertSession()->statusCodeEquals(403);
  }

}
