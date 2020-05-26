<?php

namespace Drupal\localgov_services_landing\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EntityReferenceVerticalList
 *
 * @package Drupal\localgov_services_landing\Plugin\Field\FieldFormatter
 *
 * @FieldFormatter(
 *   id = "entity_reference_vertical_list",
 *   module = "localgov_services_landing",
 *   label = @Translation("Vertical list"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class EntityReferenceVerticalList extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'title' => ''
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['title'] = [
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#default_value' => $this->getSetting('title')
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    if ($this->getSetting('title')) {
      $summary[] = t('Title: @title', ['@title' => $this->getSetting('title')]);
    }
    else {
      $summary[] = t('No title set');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    return [
      '#theme' => 'entity_reference_vertical_list',
      '#title' => $this->getSetting('title'),
      '#items' => $this->getEntitiesToView($items, $langcode)
    ];
  }
}
