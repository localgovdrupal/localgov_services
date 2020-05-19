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
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'localgov_services',
    'localgov_services_landing',
    'localgov_services_sublanding',
    'localgov_services_page',
  ];  
 
  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }
 
  /**
   * Verifies basic functionality with all modules.
   */
  public function testConfigForm() {
    $this->drupalGet('/admin');
  }

}
