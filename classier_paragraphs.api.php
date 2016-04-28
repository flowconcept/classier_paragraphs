<?php

/**
 * Implements hook_classier_paragraphs_form().
 */
function hook_classier_paragraphs_form($entity_type, $bundle, $item) {
  $element = [];

  switch ($bundle) {
    case '2_column':
      $element['layout'] = [
        '#type' => 'radios',
        '#title' => 'Layout',
        '#options' => [
          'layout-50-50' => 'Ratio 1/2 : 1/2',
          'layout-33-66' => 'Ratio 1/3 : 2/3',
          'layout-66-33' => 'Ratio 2/3 : 1/3',
        ],
        '#default_value' => isset($item->layout) ? $item->layout : 'layout-50-50',
      ];
      break;
  }

  return $element;
}
