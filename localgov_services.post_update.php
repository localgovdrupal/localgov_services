<?php

/**
 * @file
 * Post update functions for LocalGov Services.
 */

use Drupal\pathauto\Entity\PathautoPattern;
use Drupal\pathauto\PathautoPatternInterface;

/**
 * Update pathauto patterns to avoid inclusion of subdirectory, language, etc.
 */
function localgov_services_post_update_pathauto_parent(): void {
  $alias = PathautoPattern::load('localgov_services_hierarchy');
  if ($alias instanceof PathautoPatternInterface &&
    $alias->getPattern() == '[node:localgov_services_parent:entity:url:relative]/[node:title]'
  ) {
    $alias->setPattern('[node:localgov_services_parent:entity:url:path]/[node:title]');
    $alias->save();
  }
}
