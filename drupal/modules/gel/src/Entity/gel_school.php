<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Gel_school entity.
 *
 * @ingroup gel
 *
 * @ContentEntityType(
 *   id = "gel_school",
 *   label = @Translation("Gel_school"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\gel\gel_schoolListBuilder",
 *     "views_data" = "Drupal\gel\Entity\gel_schoolViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\gel\Form\gel_schoolForm",
 *       "add" = "Drupal\gel\Form\gel_schoolForm",
 *       "edit" = "Drupal\gel\Form\gel_schoolForm",
 *       "delete" = "Drupal\gel\Form\gel_schoolDeleteForm",
 *     },
 *     "access" = "Drupal\gel\gel_schoolAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\gel\gel_schoolHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "gel_school",
 *   admin_permission = "administer gel_school entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/gel_school/{gel_school}",
 *     "add-form" = "/admin/structure/gel_school/add",
 *     "edit-form" = "/admin/structure/gel_school/{gel_school}/edit",
 *     "delete-form" = "/admin/structure/gel_school/{gel_school}/delete",
 *     "collection" = "/admin/structure/gel_school",
 *   },
 *   field_ui_base_route = "gel_school.settings"
 * )
 */
class gel_school extends ContentEntityBase implements gel_schoolInterface {

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
      ->setDescription(t('The user ID of author of the Gel_school entity.'))
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
        ->setLabel(t('Ονομασία'))
        ->setDescription(t('Ονομασία σχολείου.'))
        ->setSettings(array(
          'max_length' => 200,
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

      $fields['mm_id'] = BaseFieldDefinition::create('string')
        ->setLabel(t('mm_id'))
        ->setDescription(t('Δώσε τον κωδικό mm_id'))
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
        ->setDescription(t('Δώσε τον Τύπο Σχολείου - πχ ΓΥΜΝΑΣΙΟ / ΓΕΝΙΚΟ ΛΥΚΕΙΟ'))
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

      $fields['street_address'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Διεύθυνση'))
        ->setDescription(t('Δώσε την Διεύθυνση Σχολείου'))
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

      $fields['postal_code'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Ταχ. κώδικας'))
        ->setDescription(t('Δώσε τον ΤΚ Σχολείου'))
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

  	$fields['fax_number'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Fax Σχολείου'))
        ->setDescription(t('Δώσε το Fax Σχολείου'))
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

      $fields['phone_number'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Τηλέφωνο Σχολείου'))
        ->setDescription(t('Δώσε το Τηλέφωνο Σχολείου'))
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

  	$fields['maile'] = BaseFieldDefinition::create('string')
        ->setLabel(t('e-mail Σχολείου'))
        ->setDescription(t('Δώσε το e-mail Σχολείου'))
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

  	$fields['region_edu_admin_id'] = BaseFieldDefinition::create('entity_reference')
          ->setLabel(t('ID Περιφερειακής Διεύθυνσης'))
          ->setDescription(t('Δώσε το ID της Περιφερειακής Διεύθυνσης στην οποία ανήκει.'))
          ->setSetting('target_type', 'eepal_region')
          ->setSetting('handler', 'default')
          ->setTranslatable(TRUE)
          ->setDisplayOptions('view', array(
                'label' => 'hidden',
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

  	$fields['edu_admin_id'] = BaseFieldDefinition::create('entity_reference')
          ->setLabel(t('ID Διεύθυνσης Εκπαίδευσης'))
          ->setDescription(t('Δώσε το ID της Διεύθυνσης Εκπαίδευσης στην οποία ανήκει.'))
          ->setSetting('target_type', 'eepal_admin_area')
          ->setSetting('handler', 'default')
          ->setTranslatable(TRUE)
          ->setDisplayOptions('view', array(
                'label' => 'hidden',
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

      $fields['prefecture_id'] = BaseFieldDefinition::create('entity_reference')
          ->setLabel(t('ID Νομαρχίας'))
          ->setDescription(t('Δώσε το ID της Νομαρχίας στην οποία ανήκει.'))
          ->setSetting('target_type', 'eepal_prefecture')
          ->setSetting('handler', 'default')
          ->setTranslatable(TRUE)
          ->setDisplayOptions('view', array(
                'label' => 'hidden',
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

  	$fields['municipality'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Δήμος-Περιοχή Σχολείου'))
        ->setDescription(t('Δώσε το Δήμο-Περιοχή Σχολείου'))
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

  	$fields['operation_shift'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Τύπος Σχολείου με βάση το ωράριο λειτουργίας'))
        ->setDescription(t('Δώσε τον τύπο σχολείου με βάση το ωράριο λειτουργίας-πχ Ημερήσιο'))
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

  	$fields['metathesis_region'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Περιοχή Μετάθεσης Σχολείου'))
        ->setDescription(t('Δώσε την περιοχή μετάθεσης σχολείου'))
        ->setSettings(array(
          'max_length' => 5,
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

      $fields['capacity_class_a'] = BaseFieldDefinition::create('integer')
        ->setLabel(t('Μέγιστος αριθμός τμημάτων γενικής παιδείας'))
        ->setDescription(t('Δώσε τον μέγιστο αριθμό τμημάτων γενικής παιδείας.'))
           ->setSettings(array(
                 'max_length' => 2,
                 'text_processing' => 0,
               ))
           ->setRequired(false)
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


    $fields['approved_a'] = BaseFieldDefinition::create('boolean')
            ->setLabel(t('Εγκεκριμένο'))
            ->setDescription(t('Εγκρίνεται σε περίπτωση ολιγομελούς.'))
            ->setDefaultValue(FALSE)
            ->setSettings(array(
              'text_processing' => 0,
            ))
        ->setRequired(false)
            ->setDisplayOptions('view', array(
              'label' => 'above',
              'type' => 'boolean',
              'weight' => -4,
            ))
            ->setDisplayOptions('form', array(
              'type' => 'boolean',
              'weight' => -4,
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);


  $fields['approv_decision'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Άριθμός Απόφασης'))
            ->setDescription(t('Δώσε τον αριθμός απόφασης έγκρισης ολιγομελούς'))
            ->setSettings(array(
              'max_length' => 1000,
              'text_processing' => 0,
            ))
            ->setRequired(false)
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
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

  $fields['approv_role'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Ο ρόλος του χρήστη που κάνει έγκριση'))
        ->setDescription(t('Ρόλος του χρήστη που κάνει έγκριση'))
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



        $fields['approvdate'] = BaseFieldDefinition::create('datetime')
          ->setLabel(t('Ημερομηνία έγκρισης ολιγομελούς'))
          ->setDescription(t('Δώσε την Ημερομηνία έγκρισης ολιγομελούς'))
          ->setSetting('datetime_type', 'date')
          ->setRequired(false)
          ->setDisplayOptions('view', array(
            'label' => 'above',
            'type' => 'string',
            'weight' => -4,
          ))->setDisplayOptions('form', array(
            'type' => 'string_textfield',
            'weight' => -4,
          ))
          ->setDisplayConfigurable('form', true)
          ->setDisplayConfigurable('view', true);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Gel_school is published.'))
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
