<?php

namespace Drupal\epalreadydata\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Eepal region entity.
 *
 * @ingroup epalreadydata
 *
 * @ContentEntityType(
 *   id = "eepal_region",
 *   label = @Translation("Eepal region"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\epalreadydata\EepalRegionListBuilder",
 *     "views_data" = "Drupal\epalreadydata\Entity\EepalRegionViewsData",
 *     "translation" = "Drupal\epalreadydata\EepalRegionTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\epalreadydata\Form\EepalRegionForm",
 *       "add" = "Drupal\epalreadydata\Form\EepalRegionForm",
 *       "edit" = "Drupal\epalreadydata\Form\EepalRegionForm",
 *       "delete" = "Drupal\epalreadydata\Form\EepalRegionDeleteForm",
 *     },
 *     "access" = "Drupal\epalreadydata\EepalRegionAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\epalreadydata\EepalRegionHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "eepal_region",
 *   data_table = "eepal_region_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer eepal region entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "registry_no" = "registry_no",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/eepal_region/{eepal_region}",
 *     "add-form" = "/admin/structure/eepal_region/add",
 *     "edit-form" = "/admin/structure/eepal_region/{eepal_region}/edit",
 *     "delete-form" = "/admin/structure/eepal_region/{eepal_region}/delete",
 *     "collection" = "/admin/structure/eepal_region",
 *   },
 *   field_ui_base_route = "eepal_region.settings"
 * )
 */
class EepalRegion extends ContentEntityBase implements EepalRegionInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
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
  public function getRegistry_no() {
    return $this->get('registry_no')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRegistry_no($registry_no) {
    $this->set('registry_no', $registry_no);
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
      ->setDescription(t('The user ID of author of the Eepal region entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
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
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Eepal region entity.'))
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

      $fields['registry_no'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Registry no'))
        ->setDescription(t('The registry number of the Eepal region entity.'))
        ->setSettings(array(
          'max_length' => 50,
          'text_processing' => 0,
        ))
        ->setDefaultValue('0000000')
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

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Eepal region is published.'))
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
