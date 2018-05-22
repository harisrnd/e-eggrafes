<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Gelstudenthighschool entity.
 *
 * @ingroup gel
 *
 * @ContentEntityType(
 *   id = "gelstudenthighschool",
 *   label = @Translation("Gelstudenthighschool"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\gel\gelstudenthighschoolListBuilder",
 *     "views_data" = "Drupal\gel\Entity\gelstudenthighschoolViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\gel\Form\gelstudenthighschoolForm",
 *       "add" = "Drupal\gel\Form\gelstudenthighschoolForm",
 *       "edit" = "Drupal\gel\Form\gelstudenthighschoolForm",
 *       "delete" = "Drupal\gel\Form\gelstudenthighschoolDeleteForm",
 *     },
 *     "access" = "Drupal\gel\gelstudenthighschoolAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\gel\gelstudenthighschoolHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "gelstudenthighschool",
 *   admin_permission = "administer gelstudenthighschool entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/gelstudenthighschool/{gelstudenthighschool}",
 *     "add-form" = "/admin/structure/gelstudenthighschool/add",
 *     "edit-form" = "/admin/structure/gelstudenthighschool/{gelstudenthighschool}/edit",
 *     "delete-form" = "/admin/structure/gelstudenthighschool/{gelstudenthighschool}/delete",
 *     "collection" = "/admin/structure/gelstudenthighschool",
 *   },
 *   field_ui_base_route = "gelstudenthighschool.settings"
 * )
 */
class gelstudenthighschool extends ContentEntityBase implements gelstudenthighschoolInterface {

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

 public function getschool_id() 
 {
   return $this->get('school_id')->value;
 }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Gelstudenthighschool entity.'))
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
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Gelstudenthighschool entity.'))
      ->setSettings([
        'max_length' => 50,
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
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);


      $fields['student_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Id Μαθητή'))
      ->setDescription(t('Δώσε το id μαθητή.'))
      ->setSetting('target_type', 'gel_student')
            ->setSetting('handler', 'default')
      ->setRequired(true)
            ->setTranslatable(TRUE)
            ->setDisplayOptions('view', array(
              'label' => 'above',
              'type' => 'author',
              'weight' => -4,
            ))
      ->setDisplayOptions('form', array(
             'type' => 'entity_reference_autocomplete',
             'weight' => -4,
             'settings' => array(
                'match_operator' => 'CONTAINS',
                'size' => '60',
                'autocomplete_type' => 'tags',
                'placeholder' => '',
              ),
            ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

   $fields['school_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Id σχολείου'))
      ->setDescription(t(' το id του σχολείου.'))
      ->setSetting('target_type', 'gel_school')
            ->setSetting('handler', 'default')
      ->setRequired(true)
            ->setTranslatable(TRUE)
            ->setDisplayOptions('view', array(
              'label' => 'above',
              'type' => 'author',
              'weight' => -4,
            ))
      ->setDisplayOptions('form', array(
             'type' => 'entity_reference_autocomplete',
             'weight' => -4,
             'settings' => array(
                'match_operator' => 'CONTAINS',
                'size' => '60',
                'autocomplete_type' => 'tags',
                'placeholder' => '',
              ),
            ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['taxi'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Class_id'))
      ->setDescription(t('The Class_id of the Gelstudenthighschool.'))
      ->setSettings([
        'max_length' => 50,
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
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);


$fields['dide'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('registry_no_dide'))
      ->setDescription(t(' registry_no της ΔΙΔΕ.'))
     ->setSettings([
        'max_length' => 50,
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
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);


    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Gelstudenthighschool is published.'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
