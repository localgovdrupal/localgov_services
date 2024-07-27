<?php

namespace Drupal\Tests\localgov_services\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\workspaces\Functional\WorkspaceTestUtilities;

/**
 * Tests localgov services pages working together, and with external modules.
 *
 * @group media_counter
 */
class WorkspacesIntegrationTest extends BrowserTestBase {

  use WorkspaceTestUtilities;

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
    'block',
    'localgov_core',
    'localgov_services',
    'localgov_services_landing',
    'localgov_services_sublanding',
    'localgov_services_page',
    'localgov_services_navigation',
    'workspaces',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->drupalPlaceBlock('system_breadcrumb_block');
    $this->adminUser = $this->drupalCreateUser([
      'access administration pages',
      'bypass node access',
      'administer nodes',
      'administer workspaces',
    ]);
    $this->nodeStorage = $this->container->get('entity_type.manager')->getStorage('node');
    $this->setupWorkspaceSwitcherBlock();
  }

  /**
   * Post and link test.
   *
   * Post a service landing page.
   * Post a service sub landing page, and link to landing page.
   * Link landing page to sublanding page.
   * Post a page, put it in the landing and sublanding services.
   * Link page from sublanding page.
   */
  public function testPostLink() {
    $assert = $this->assertSession();
    $this->drupalLogin($this->adminUser);

    $test_1 = $this->createWorkspaceThroughUi('Test 1', 'test_1');
    $this->switchToWorkspace($test_1);

    $this->drupalGet('node/add/localgov_services_landing');
    $form = $this->getSession()->getPage();
    $form->fillField('edit-title-0-value', 'Service 1');
    $form->fillField('edit-body-0-summary', 'Service 1 summary');
    $form->fillField('edit-body-0-value', 'Service 1 description');
    $form->checkField('edit-status-value');
    $form->pressButton('edit-submit');
    $service_landing = $this->drupalGetNodeByTitle('Service 1');

    $this->drupalGet('node/add/localgov_services_sublanding');
    $form = $this->getSession()->getPage();
    $form->fillField('edit-title-0-value', 'Sub Service 1');
    $form->fillField('edit-body-0-summary', 'Sub Service 1 summary');
    $form->fillField('edit-body-0-value', 'Sub Service 1 description');
    $form->fillField('edit-localgov-services-parent-0-target-id', 'Service 1 (1)');
    $form->checkField('edit-status-value');
    $form->pressButton('edit-submit');
    $service_sublanding = $this->drupalGetNodeByTitle('Sub Service 1');

    $this->drupalGet($service_landing->toUrl('edit-form')->toString());
    $form = $this->getSession()->getPage();
    $form->fillField('edit-localgov-destinations-0-target-id', 'Sub landing 1 (2)');
    $form->pressButton('edit-submit');

    $this->drupalGet('node/add/localgov_services_page');
    $form = $this->getSession()->getPage();
    $form->fillField('edit-title-0-value', 'Service 1 Page 1');
    $form->fillField('edit-body-0-summary', 'Service 1 summary 1 ');
    $form->fillField('edit-body-0-value', 'Service 1 description 1');
    $form->fillField('edit-localgov-services-parent-0-target-id', 'Service 1 » Sub landing 1 (2)');
    $form->checkField('edit-status-value');
    $form->pressButton('edit-submit');
    $service_page = $this->drupalGetNodeByTitle('Service 1 Page 1');

    $this->drupalGet($service_sublanding->toUrl('edit-form')->toString());
    $form = $this->getSession()->getPage();
    $form->fillField('edit-localgov-topics-0-subform-topic-list-links-0-uri', '/node/3');
    $form->pressButton('edit-submit');
    $assert->pageTextContains('Service 1 Page 1');

    $this->drupalLogout();

    $this->drupalGet($service_landing->toUrl()->toString());
    $assert->statusCodeEquals(403);
    $this->drupalGet($service_sublanding->toUrl()->toString());
    $assert->statusCodeEquals(403);
    $this->drupalGet($service_page->toUrl()->toString());
    $assert->statusCodeEquals(403);

    $this->drupalLogin($this->adminUser);
    $this->drupalGet($test_1->toUrl()->toString());
    $assert->pageTextContains('3 content items');
    $assert->pageTextContains('3 URL aliases');
    $assert->pageTextContains('1 Paragraph');
    $this->drupalGet($test_1->toUrl()->toString() . '/publish');
    $this->getSession()->getPage()->pressButton('Publish 7 items to Live');
    $this->assertSession()->pageTextContains('Successful publication.');

    $this->drupalLogout();

    $this->drupalGet($service_landing->toUrl()->toString());
    $assert->statusCodeEquals(200);
    $this->drupalGet($service_sublanding->toUrl()->toString());
    $assert->statusCodeEquals(200);
    $this->drupalGet($service_page->toUrl()->toString());
    $assert->statusCodeEquals(200);
  }

}
