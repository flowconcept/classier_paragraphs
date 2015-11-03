<?php
/**
 * @file
 * Contains \Drupal\classier_paragraphs\Plugin\Field\FieldWidget\ClassierParagraphsWidget.
 */

namespace Drupal\classier_paragraphs\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'classier_paragraphs' widget.
 *
 * @FieldWidget(
 *   id = "classier_paragraphs_form",
 *   label = @Translation("Subform"),
 *   field_types = {
 *     "classier_paragraphs"
 *   }
 * )
 */
class ClassierParagraphsWidget extends WidgetBase {
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['title'] = [
      '#type' => 'radios',
      '#title' => 'Ausrichtung Titel',
      '#options' => [
        'title_left' => 'linksbündig',
        'title_center' => 'zentriert',
      ],
      '#default_value' => isset($items[$delta]->title) ? $items[$delta]->title : 'title_left',
    ];
    $element['background'] = [
      '#type' => 'radios',
      '#title' => 'Hintergrund',
      '#options' => [
        'background_none' => 'keiner / weiß',
        'background_color_signature_green_light' => 'hellgrün',
        'background_image_wood' => 'Bühne mit Holz',
      ],
      '#default_value' => isset($items[$delta]->background) ? $items[$delta]->background : 'background_none',
//      '#theme_wrappers' => [
//        'my_form_element' // for markup in #title?
//      ]
    ];
    return $element;
  }
}
