<?php

namespace Drupal\deploysystem\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Eggrafes config entity.
 *
 * @ingroup deploysystem
 *
 * @ContentEntityType(
 *   id = "eggrafes_config",
 *   label = @Translation("Eggrafes config"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\deploysystem\EggrafesConfigListBuilder",
 *     "views_data" = "Drupal\deploysystem\Entity\EggrafesConfigViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\deploysystem\Form\EggrafesConfigForm",
 *       "add" = "Drupal\deploysystem\Form\EggrafesConfigForm",
 *       "edit" = "Drupal\deploysystem\Form\EggrafesConfigForm",
 *       "delete" = "Drupal\deploysystem\Form\EggrafesConfigDeleteForm",
 *     },
 *     "access" = "Drupal\deploysystem\EggrafesConfigAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\deploysystem\EggrafesConfigHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "eggrafes_config",
 *   admin_permission = "administer eggrafes config entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/eggrafes_config/{eggrafes_config}",
 *     "add-form" = "/admin/structure/eggrafes_config/add",
 *     "edit-form" = "/admin/structure/eggrafes_config/{eggrafes_config}/edit",
 *     "delete-form" = "/admin/structure/eggrafes_config/{eggrafes_config}/delete",
 *     "collection" = "/admin/structure/eggrafes_config",
 *   },
 *   field_ui_base_route = "eggrafes_config.settings"
 * )
 */
class EggrafesConfig extends ContentEntityBase implements EggrafesConfigInterface {

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
      ->setDescription(t('The user ID of author of the Eggrafes config entity.'))
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
      ->setDescription(t('Ονομασία του Eggrafes config entity.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('eggrafes_config')
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

    $fields['lock_school_capacity'] = BaseFieldDefinition::create('boolean')
          ->setLabel(t('Απενεργοποίηση δυνατότητας τροποποίησης χωρητικότητας από τους Διευθυντές σχολείων'))
          ->setDescription(t('Απενεργοποίηση δυνατότητας τροποποίησης χωρητικότητας από τους Διευθυντές σχολείων.'))
          ->setSettings(array(
            'text_processing' => 0,
          ))
          ->setRequired(FALSE)
          ->setDefaultValue(TRUE)
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

    $fields['lock_school_students_view'] = BaseFieldDefinition::create('boolean')
          ->setLabel(t('Απενεργοποίηση δυνατότητας προβολής κατανομής μαθητών από τους Διευθυντές σχολείων'))
          ->setDescription(t('Απενεργοποίηση δυνατότητας προβολής κατανομής μαθητών από τους Διευθυντές σχολείων.'))
          ->setSettings(array(
            'text_processing' => 0,
          ))
          ->setRequired(FALSE)
          ->setDefaultValue(TRUE)
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

    $fields['lock_application'] = BaseFieldDefinition::create('boolean')
          ->setLabel(t('Απενεργοποίηση δυνατότητας υποβολής δήλωσης προτίμησης'))
          ->setDescription(t('Απενεργοποίηση δυνατότητας υποβολής δήλωσης προτίμησης.'))
          ->setSettings(array(
            'text_processing' => 0,
          ))
          ->setRequired(FALSE)
          ->setDefaultValue(TRUE)
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

    $fields['lock_modify'] = BaseFieldDefinition::create('boolean')
          ->setLabel(t('Απενεργοποίηση δυνατότητας τροποποίησης αίτησης'))
          ->setDescription(t('Απενεργοποίηση δυνατότητας τροποποίησης αίτησης.'))
          ->setSettings(array(
            'text_processing' => 0,
          ))
          ->setRequired(FALSE)
          ->setDefaultValue(FALSE)
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

    $fields['lock_delete'] = BaseFieldDefinition::create('boolean')
          ->setLabel(t('Απενεργοποίηση δυνατότητας διαγραφής αίτησης'))
          ->setDescription(t('Απενεργοποίηση δυνατότητας διαγραφής αίτησης.'))
          ->setSettings(array(
            'text_processing' => 0,
          ))
          ->setRequired(FALSE)
          ->setDefaultValue(FALSE)
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

    $fields['lock_results'] = BaseFieldDefinition::create('boolean')
          ->setLabel(t('Απενεργοποίηση δυνατότητας προβολής αποτελεσμάτων κατανομής από τους μαθητές'))
          ->setDescription(t('Απενεργοποίηση δυνατότητας προβολής αποτελεσμάτων κατανομής από τους μαθητές.'))
          ->setSettings(array(
            'text_processing' => 0,
          ))
          ->setRequired(FALSE)
          ->setDefaultValue(TRUE)
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

    $fields['activate_second_period'] = BaseFieldDefinition::create('boolean')
              ->setLabel(t('Ενεργοποίηση δεύτερης περιόδου αιτήσεων'))
              ->setDescription(t('Ενεργοποίηση δεύτερης περιόδου αιτήσεων.'))
              ->setSettings(array(
                'text_processing' => 0,
              ))
              ->setRequired(FALSE)
              ->setDefaultValue(FALSE)
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

    $fields['date_start_b_period'] = BaseFieldDefinition::create('datetime')
            ->setLabel(t('Ημερομηνία έναρξης για κατανομή Β περιόδου'))
            ->setDescription(t('Ημερομηνία έναρξης για κατανομή Β περιόδου.'))
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

      $fields['lock_small_classes'] = BaseFieldDefinition::create('boolean')
              ->setLabel(t('Ενεργοποίηση της επιλογής για περιορισμό των μη εγκεκριμένων ολιγομελών τμημάτων'))
              ->setDescription(t('Ενεργοποίηση της επιλογής για περιορισμό των μη εγκεκριμένων ολιγομελών τμημάτων'))
              ->setSettings(array(
                'text_processing' => 0,
              ))
              ->setRequired(FALSE)
              ->setDefaultValue(FALSE)
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

      $fields['ws_ident'] = BaseFieldDefinition::create('boolean')
              ->setLabel(t('Ενεργοποίηση web service για ταυτοποίηση μαθητή'))
              ->setDescription(t('Ενεργοποίηση web service για ταυτοποίηση μαθητή'))
              ->setSettings(array(
                'text_processing' => 0,
              ))
              ->setRequired(FALSE)
              ->setDefaultValue(FALSE)
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

      $fields['gsis_ident'] = BaseFieldDefinition::create('boolean')
              ->setLabel(t('Ενεργοποίηση χρήσης στοιχείων από ΓΓΠΣ'))
              ->setDescription(t('Ενεργοποίηση χρήσης στοιχείων από ΓΓΠΣ'))
              ->setSettings(array(
                'text_processing' => 0,
              ))
              ->setRequired(FALSE)
              ->setDefaultValue(FALSE)
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

       $fields['guardian_ident'] = BaseFieldDefinition::create('boolean')
                ->setLabel(t('Ενεργοποίηση ταυτοποίησης κηδεμόνα'))
                ->setDescription(t('Ενεργοποίηση ταυτοποίησης κηδεμόνα'))
                ->setSettings(array(
                  'text_processing' => 0,
                ))
                ->setRequired(FALSE)
                ->setDefaultValue(FALSE)
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

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Eggrafes config is published.'))
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
