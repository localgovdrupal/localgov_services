<?php

namespace Drupal\Tests\localgov_services_sublanding\Functional;

use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\node\Traits\NodeCreationTrait;

/**
 * Tests Topic List Builder Paragraph type.
 *
 * @group localgov_services
 */
class TopicListBuilderTest extends BrowserTestBase {

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
    'localgov_services_sublanding',
  ];

  /**
   * Test topic list builder functionality.
   */
  public function testTopicListBuilderParagraphDisplay() {

    // Create a sub-landing page.
    $page = $this->createNode([
      'type' => 'localgov_services_sublanding',
      'title' => 'Test sub-landing page.',
      'body' => [
        'summary' => 'Test sub-landing page text',
        'value' => '',
      ],
      'status' => NodeInterface::PUBLISHED,
    ]);

    // Check with term and external link.
    $topic_name = $this->randomMachineName(8);
    $topic = Term::create([
      'name' => $topic_name,
      'vid' => 'topic',
    ]);
    $topic->save();
    $tlb_term = Paragraph::create([
      'type' => 'topic_list_builder',
      'topic_list_term' => ['target_id' => $topic->id()],
      'topic_list_links' => [
        'uri' => 'https://example.com/',
        'title' => 'External link text',
      ],
    ]);
    $tlb_term->save();
    $page->field_topics->appendItem($tlb_term);
    $page->save();
    $this->drupalGet('/node/' . $page->id());
    $this->assertSession()->pageTextContains($topic_name);
    $this->assertSession()->pageTextContains('External link text');
    $this->assertSession()->responseContains('href="https://example.com/"');

    // Check with header and link.
    $service_path = '/' . $this->randomMachineName(8);
    $this->createNode([
      'type' => 'localgov_services_page',
      'title' => 'Test services page',
      'body' => [
        'summary' => 'Test services page summary',
        'value' => 'Test services page text',
      ],
      'status' => NodeInterface::PUBLISHED,
      'path' => ['alias' => $service_path],
    ]);
    $header = $this->randomMachineName(12);
    $tlb_header = Paragraph::create([
      'type' => 'topic_list_builder',
      'topic_list_header' => ['value' => $header],
      'topic_list_links' => [
        'uri' => 'internal:' . $service_path,
        'title' => 'Example internal link',
      ],
    ]);
    $tlb_header->save();
    $page->field_topics->appendItem($tlb_header);
    $page->save();
    $this->drupalGet('/node/' . $page->id());
    $this->assertSession()->pageTextContains($header);
    $this->assertSession()->pageTextContains('Example internal link');
    $this->assertSession()->responseContains('href="' . $service_path . '"');
  }

}
