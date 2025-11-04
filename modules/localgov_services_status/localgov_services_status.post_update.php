<?php

/**
 * @file
 * Post update functions for LocalGov Services Status.
 */

use Drupal\pathauto\Entity\PathautoPattern;
use Drupal\pathauto\PathautoPatternInterface;

/**
 * Update pathauto patterns to avoid inclusion of subdirectory, language, etc.
 */
function localgov_services_status_post_update_pathauto_parent(): void {
  $alias = PathautoPattern::load('localgov_service_status');
  if ($alias instanceof PathautoPatternInterface &&
    $alias->getPattern() == '[node:localgov_services_parent:entity:url:relative]/status/[node:title]'
  ) {
    $alias->setPattern('[node:localgov_services_parent:entity:url:path]/status/[node:title]');
    $alias->save();
  }
}
