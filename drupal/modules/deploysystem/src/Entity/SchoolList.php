<?php

namespace Drupal\deploysystem\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the School list entity.
 *
 * @ingroup deploysystem
 *
 * @ContentEntityType(
 *   id = "school_list",
 *   label = @Translation("School list"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\deploysystem\SchoolListListBuilder",
 *     "views_data" = "Drupal\deploysystem\Entity\SchoolListViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\deploysystem\Form\SchoolListForm",
 *       "add" = "Drupal\deploysystem\Form\SchoolListForm",
 *       "edit" = "Drupal\deploysystem\Form\SchoolListForm",
 *       "delete" = "Drupal\deploysystem\Form\SchoolListDeleteForm",
 *     },
 *     "access" = "Drupal\deploysystem\SchoolListAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\deploysystem\SchoolListHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "school_list",
 *   admin_permission = "administer school list entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/school_list/{school_list}",
 *     "add-form" = "/admin/structure/school_list/add",
 *     "edit-form" = "/admin/structure/school_list/{school_list}/edit",
 *     "delete-form" = "/admin/structure/school_list/{school_list}/delete",
 *     "collection" = "/admin/structure/school_list",
 *   },
 *   field_ui_base_route = "school_list.settings"
 * )
 */
class SchoolList extends ContentEntityBase implements SchoolListInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the School list entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Ονομασία σχολείου'))
      ->setDescription(t('Ονομασία σχολείου.'))
      ->setSettings([
        'max_length' => 200,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

      $fields['registry_no'] = BaseFieldDefinition::create('string')
          ->setLabel(t('Κωδικός Σχολείου'))
          ->setDescription(t('Δώσε τον Κωδικό Σχολείου'))
          ->setSettings(array(
            'max_length' => 50,
            'text_processing' => 0,
          ))
          ->setDefaultValue('')
          ->setDisplayOptions('view', array(
            'label' => 'above',
            'type' => 'string',
            'weight' => -4,
          ))
          ->setDisplayOptions('form', array(
            'type' => 'string_textfield',
            'weight' => -4,
          ))
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);

      $fields['unit_type'] = BaseFieldDefinition::create('string')
          ->setLabel(t('Τύπος σχολείου'))
          ->setDescription(t('Δώσε τον Τύπο Σχολείου - πχ ΓΕΛ / ΕΠΑΛ / Γυμνάσιο'))
          ->setSettings(array(
            'max_length' => 80,
            'text_processing' => 0,
          ))
          ->setDefaultValue('')
          ->setDisplayOptions('view', array(
            'label' => 'above',
            'type' => 'string',
            'weight' => -4,
          ))
          ->setDisplayOptions('form', array(
            'type' => 'string_textfield',
            'weight' => -4,
          ))
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);

       $fields['unit_type_id'] = BaseFieldDefinition::create('integer')
          ->setLabel(t('Id τύπου σχολείου'))
          ->setDescription(t('Δώσε το Id τύπου σχολείου.'))
              ->setSettings(array(
                'max_length' => 2,
                'text_processing' => 0,
              ))
          ->setRequired(true)
              ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'integer',
                'weight' => -4,
              ))
          ->setDisplayOptions('form', array(
                'type' => 'integer',
                'weight' => -4,
              ))
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the School list is published.'))
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
