<?php
/**
 * @file
 * Contains \Drupal\classier_paragraphs\Plugin\Field\FieldWidget\ClassierParagraphsWidget.
 */

namespace Drupal\classier_paragraphs\Plugin\Field\FieldWidget;

use Drupal;
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
    /* @var $entity \Drupal\Core\Entity\FieldableEntityInterface */
    $entity = $items->getParent()->getValue();
    /* @var $form_display Drupal\Core\Entity\Entity\EntityFormDisplay */
    $form_display = $form_state->getStorage()['form_display'];
    return $this->subForm($form_display, $entity->bundle(), $items[$delta]);
  }

  /**
   * Builds the user-specific subform.
   *
   * @param Drupal\Core\Entity\Entity\EntityFormDisplay $form_display
   *   Provides access to form display properties, including targetEntityType,
   *   targetBundle, and display mode.
   * @param string $bundle
   *   The paragraph bundle to build the form for.
   * @param Drupal\classier_paragraphs\Plugin\Field\FieldType\ClassierParagraphsItem $item
   *   The paragraph field item.
   */
  public function subForm($form_display, $bundle, $item) {
    // Invoke hook_classier_paragraphs_form
    $element = Drupal::moduleHandler()->invokeAll('classier_paragraphs_form', array($form_display, $bundle, $item));

    // Let the themes play too, because classes for paragraphs is quite a themey thing.
    $theme_handler = Drupal::service('theme_handler');
    /* @var $theme_handler Drupal\Core\Extension\ThemeHandlerInterface */
    $default_theme = $theme_handler->getDefault();
    $themes = array_keys($theme_handler->getBaseThemes($theme_handler->listInfo(), $default_theme));
    $themes[] = $default_theme;
    foreach ($themes as $theme_name) {
      $function = $theme_name . '_classier_paragraphs_form';
      $theme_handler->getTheme($theme_name)->load();
      if (function_exists($function)) {
        $element = $function($form_display, $bundle, $item);
      }
    }

    $context = array(
      'form_display' => $form_display,
      'bundle' => $bundle,
      'item' => $item
    );
    Drupal::moduleHandler()->alter('classier_paragraphs_form', $element, $context);

    return $element;
  }
}
