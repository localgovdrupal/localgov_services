<?php

namespace Drupal\Tests\localgov_services\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests localgov services pages working together, and with external modules.
 *
 * @group media_counter
 */
class PagesIntegrationTest extends BrowserTestBase {

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
  public static $modules = [
    'localgov_core',
    'localgov_services',
    'localgov_services_landing',
    'localgov_services_sublanding',
    'localgov_services_page',
    'localgov_services_navigation',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser(['bypass node access', 'administer nodes']);
    $this->nodeStorage = $this->container->get('entity_type.manager')->getStorage('node');
  }

  /**
   * Verifies basic functionality with all modules.
   */
  public function testConfigForm() {
    $this->drupalGet('/admin');
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
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('node/add/localgov_services_landing');
    $form = $this->getSession()->getPage();
    $form->fillField('edit-title-0-value', 'Service 1');
    $form->fillField('edit-body-0-summary', 'Service 1 summary');
    $form->fillField('edit-body-0-value', 'Service 1 description');
    $form->checkField('edit-status-value');
    $form->pressButton('edit-submit');

    $this->drupalGet('node/add/localgov_services_sublanding');
    $form = $this->getSession()->getPage();
    $form->fillField('edit-title-0-value', 'Sub Service 1');
    $form->fillField('edit-body-0-summary', 'Sub Service 1 summary');
    $form->fillField('edit-body-0-value', 'Sub Service 1 description');
    $form->selectFieldOption('edit-field-service', 1);
    $form->checkField('edit-status-value');
    $form->pressButton('edit-submit');

    $this->drupalGet('node/1/edit');
    $form = $this->getSession()->getPage();
    $form->fillField('edit-field-destinations-0-target-id', 'Sub landing 1 (2)');
    $form->pressButton('edit-submit');

    $this->drupalGet('node/add/localgov_services_page');
    $assert = $this->assertSession();
    $form = $this->getSession()->getPage();
    $form->fillField('edit-title-0-value', 'Service 1 Page 1');
    $form->fillField('edit-body-0-summary', 'Service 1 summary 1 ');
    $form->fillField('edit-body-0-value', 'Service 1 description 1');
    $assert->elementTextContains('css', '#edit-field-service', 'Service 1');
    $form->fillField('edit-field-service', 1);
    $assert->elementTextContains('css', '#edit-localgov-services-sublanding', 'Sub Service 1');
    $form->fillField('edit-localgov-services-sublanding', 2);
    $form->checkField('edit-status-value');
    $form->pressButton('edit-submit');

    $this->drupalGet('node/2/edit');
    $form = $this->getSession()->getPage();
    $form->fillField('edit-field-topics-0-subform-topic-list-links-0-uri', '/node/3');
    $form->pressButton('edit-submit');

    $assert = $this->assertSession();
    $assert->pageTextContains('Service 1 Page 1');
  }

}
