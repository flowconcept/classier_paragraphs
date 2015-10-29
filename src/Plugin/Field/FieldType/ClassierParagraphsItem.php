<?php
/**
 * @file
 * Contains \Drupal\classier_paragraphs\Plugin\Field\FieldType\ClassierParagraphsItem.
 */

namespace Drupal\classier_paragraphs\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'classier_paragraphs' entity field type.
 *
 * @FieldType(
 *   id = "classier_paragraphs",
 *   label = @Translation("Classier Paragraphs"),
 *   description = @Translation("An entity field for storing CSS class values."),
 *   category = @Translation("Paragraphs"),
 *   default_widget = "classier_paragraphs_form",
 *   default_formatter = "basic_string"
 * )
 */
class ClassierParagraphsItem extends FieldItemBase {
  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['_value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Serialized classes'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        '_value' => array(
          'type' => 'blob',
          'size' => 'big',
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE) {
    if (isset($values['_value'])) {
      $values += unserialize($values['_value']);
    }
    else {
      unset($values['_value']);
      $values['_value'] = serialize($values);
    }
    parent::setValue($values, $notify);
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    if (isset($this->_value)) {
      return count(unserialize($this->_value)) == 0;
    }
    else {
      return count($this->values) == 0;
    }
  }
}
