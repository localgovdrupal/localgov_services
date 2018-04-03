<?php

namespace Drupal\bhcc_service_info;

use Drupal\Core\Url;

class ListBuilder {

  /**
   * @var array
   */
  private $links;

  /**
   * Manually add a link to the builder.
   *
   * @see theme.inc:template_preprocess_links().
   *
   * @code
   * $listBuilder->addLink([
   *  'title' => 'Link title',
   *  'url' => 'https://example.com',
   *  'options' => ''
   * ]);
   * @endcode
   *
   * @param $link
   * @return \Drupal\bhcc_service_info\ListBuilder
   */
  public function addLink($link) {
    $this->links[] = $link;
    return $this;
  }

  /**
   * Adds all links from a link field to the builder.
   *
   * This is useful mainly when you're building a list of links populated by a
   * field in the node field.
   *
   * @code
   * $listBuilder->addAllFromLinkField($node->get('field_links')->getValue());
   * @endcode
   *
   * @param $field
   * @return \Drupal\bhcc_service_info\ListBuilder
   */
  public function addAllFromLinkField($field) {
    foreach ($field as $item) {
      $this->links[] = [
        'title' => $item['title'],
        'url' => Url::fromUri($item['uri']),
        'options' => $item['options']
      ];
    }

    return $this;
  }

  /**
   * Get the links from the builder.
   *
   * @return array
   */
  public function getLinks() {
    return $this->links;
  }

  /**
   * Returns a render array of the links.
   *
   * @return array
   */
  public function render() {
    return [
      '#theme' => 'links',
      '#links' => $this->getLinks()
    ];
  }
}
