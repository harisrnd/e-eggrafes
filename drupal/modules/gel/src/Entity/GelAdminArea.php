<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Gel admin area entity.
 *
 * @ingroup gel
 *
 * @ContentEntityType(
 *   id = "gel_admin_area",
 *   label = @Translation("Gel admin area"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\gel\GelAdminAreaListBuilder",
 *     "views_data" = "Drupal\gel\Entity\GelAdminAreaViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\gel\Form\GelAdminAreaForm",
 *       "add" = "Drupal\gel\Form\GelAdminAreaForm",
 *       "edit" = "Drupal\gel\Form\GelAdminAreaForm",
 *       "delete" = "Drupal\gel\Form\GelAdminAreaDeleteForm",
 *     },
 *     "access" = "Drupal\gel\GelAdminAreaAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\gel\GelAdminAreaHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "gel_admin_area",
 *   admin_permission = "administer gel admin area entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/gel_admin_area/{gel_admin_area}",
 *     "add-form" = "/admin/structure/gel_admin_area/add",
 *     "edit-form" = "/admin/structure/gel_admin_area/{gel_admin_area}/edit",
 *     "delete-form" = "/admin/structure/gel_admin_area/{gel_admin_area}/delete",
 *     "collection" = "/admin/structure/gel_admin_area",
 *   },
 *   field_ui_base_route = "gel_admin_area.settings"
 * )
 */
class GelAdminArea extends ContentEntityBase implements GelAdminAreaInterface {

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
      ->setLabel(t('ονομασία Διεύθυνσης Δευτεροβάθμιας Εκπαίδευσης'))
      ->setDescription(t('ονομασία Διεύθυνσης Δευτεροβάθμιας Εκπαίδευσης.'))
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


      $fields['registry_no'] = BaseFieldDefinition::create('string')
      ->setLabel(t('κωδικός μονάδας από το μητρώο σχολείων '))
      ->setDescription(t('κωδικός μονάδας από το μητρώο σχολείων .'))
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


       $fields['region_to_belong'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('id Περιφερειακής Διεύθυνσης Εκπαίδευσης στην οποία ανήκει η Δ/νση Δ/θμιας Εκπ/σης'))
      ->setDescription(t('id Περιφερειακής Διεύθυνσης Εκπαίδευσης στην οποία ανήκει η Δ/νση Δ/θμιας Εκπ/σης.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'gel_region')
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




    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Gel admin area is published.'))
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
