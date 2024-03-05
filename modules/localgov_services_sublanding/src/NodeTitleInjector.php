<?php

namespace Drupal\localgov_services_sublanding;

use Drupal\Core\Security\TrustedCallbackInterface;

class NodeTitleInjector implements TrustedCallbackInterface {
  static function inject(array $element) {
    $element['title'][0]['#context']['value'] = $element['#localgov_services_title_override'];
    return $element;
  }

  public static function trustedCallbacks() {
    $callbacks[] = 'inject';
    return $callbacks;
  }
}
