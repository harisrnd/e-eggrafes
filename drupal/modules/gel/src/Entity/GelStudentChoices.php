<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Gel student choices entity.
 *
 * @ingroup gel
 *
 * @ContentEntityType(
 *   id = "gel_student_choices",
 *   label = @Translation("Gel student choices"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\gel\GelStudentChoicesListBuilder",
 *     "views_data" = "Drupal\gel\Entity\GelStudentChoicesViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\gel\Form\GelStudentChoicesForm",
 *       "add" = "Drupal\gel\Form\GelStudentChoicesForm",
 *       "edit" = "Drupal\gel\Form\GelStudentChoicesForm",
 *       "delete" = "Drupal\gel\Form\GelStudentChoicesDeleteForm",
 *     },
 *     "access" = "Drupal\gel\GelStudentChoicesAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\gel\GelStudentChoicesHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "gel_student_choices",
 *   admin_permission = "administer gel student choices entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/gel_student_choices/{gel_student_choices}",
 *     "add-form" = "/admin/structure/gel_student_choices/add",
 *     "edit-form" = "/admin/structure/gel_student_choices/{gel_student_choices}/edit",
 *     "delete-form" = "/admin/structure/gel_student_choices/{gel_student_choices}/delete",
 *     "collection" = "/admin/structure/gel_student_choices",
 *   },
 *   field_ui_base_route = "gel_student_choices.settings"
 * )
 */
class GelStudentChoices extends ContentEntityBase implements GelStudentChoicesInterface {

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
      ->setLabel(t('Δημιουργός'))
      ->setDescription(t('Δημιουργός'))
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
      ->setLabel(t('Όνομα'))
      ->setDescription(t('Όνομα'))
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
      ->setDisplayConfigurable('view', TRUE);

       $fields['student_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Id μαθητη '))
      ->setDescription(t('Δώσε το id του μαθητη.'))
      ->setSetting('target_type', 'gel_student')
      ->setSetting('handler', 'default')
        ->setRequired(true)
       ->setDisplayOptions('view', array(
              'label' => 'above',
              'type' => 'author',
              'weight' => 0,
            ))
      ->setDisplayOptions('form', array(
              'type' => 'entity_reference_autocomplete',
              'weight' => 5,
              'settings' => array(
                'match_operator' => 'CONTAINS',
                'size' => '60',
                'autocomplete_type' => 'tags',
                'placeholder' => '',
              ),
            ))
      ->setDisplayConfigurable('form', true)
      ->setDisplayConfigurable('view', true);


       $fields['choice_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Id επιλογής '))
      ->setDescription(t('Δώσε το id της επιλογής.'))
      ->setSetting('target_type', 'gel_choices')
      ->setSetting('handler', 'default')
        ->setRequired(true)
       ->setDisplayOptions('view', array(
              'label' => 'above',
              'type' => 'author',
              'weight' => 0,
            ))
      ->setDisplayOptions('form', array(
              'type' => 'entity_reference_autocomplete',
              'weight' => 5,
              'settings' => array(
                'match_operator' => 'CONTAINS',
                'size' => '60',
                'autocomplete_type' => 'tags',
                'placeholder' => '',
              ),
            ))
      ->setDisplayConfigurable('form', true)
      ->setDisplayConfigurable('view', true);


    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Gel student choices is published.'))
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
