<?php
/**
 * @file
 * Contains \Drupal\classier_paragraphs\Plugin\Field\FieldWidget\ClassierParagraphsWidget.
 */

namespace Drupal\classier_paragraphs\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\ElementInfoManagerInterface;
use Drupal\file\Entity\File;
use Drupal\node\NodeForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
class ClassierParagraphsWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, ElementInfoManagerInterface $element_info) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->elementInfo = $element_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($plugin_id, $plugin_definition, $configuration['field_definition'], $configuration['settings'], $configuration['third_party_settings'], $container->get('element_info'));
  }

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
    $element_info = $this->elementInfo->getInfo('managed_file');
    $element['background_file'] = [
      '#type' => 'managed_file',
      '#title' => 'Hintergrundbild',
      '#default_value' => !empty($items[$delta]->background_file) ? $items[$delta]->background_file : NULL,
      '#upload_location' => 'public://paragraphs/',
      '#process' => array_merge($element_info['#process'], [[get_called_class(), 'processElement']]),
    ];
    return $element;
  }

  public static function processElement($element, FormStateInterface $form_state, &$complete_form) {
    if ($form_state->isProcessingInput()) {
      $complete_form['#process_files'] = array($element['#parents']);
    }
    $complete_form['actions']['publish']['#submit'][] = [get_called_class(), 'onPublishNode'];
    $complete_form['actions']['delete']['#submit'][] = [get_called_class(), 'onDeleteNode'];
    $complete_form['actions']['publish']['#submit'][] = [get_called_class(), 'onPublishNode'];
    return $element;
  }

  public static function onPublishNode(&$form, FormStateInterface $form_state) {
    static::processFiles('persist', $form, $form_state);
  }

  public static function onDeleteNode(&$form, FormStateInterface $form_state) {
    static::processFiles('delete', $form, $form_state);
  }

  public static function processFiles($action, $form, FormStateInterface $form_state) {
    if (isset($form['#process_files'])) {
      /**
       * @var $node_form NodeForm
       */
      $node_form = $form_state->getBuildInfo()['callback_object'];
      $entity = $node_form->getEntity();
      foreach ($form['#process_files'] as $parents) {
        $fids = NestedArray::getValue($form_state->getValues(), $parents);
        foreach (File::loadMultiple($fids) as $file) {
          switch ($action) {
            case 'persist':
              $file->setPermanent();
              $file->save();
              \Drupal::service('file.usage')->add($file, 'file', $entity->getEntityTypeId(), $entity->id());
              break;

            case 'delete':
              $file->delete();
              \Drupal::service('file.usage')->delete($file, 'file', $entity->getEntityTypeId(), $entity->id());
              break;
          }
        }
      }
    }
  }
}
