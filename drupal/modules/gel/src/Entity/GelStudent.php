<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Gel student entity.
 *
 * @ingroup gel
 *
 * @ContentEntityType(
 *   id = "gel_student",
 *   label = @Translation("Gel student"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\gel\GelStudentListBuilder",
 *     "views_data" = "Drupal\gel\Entity\GelStudentViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\gel\Form\GelStudentForm",
 *       "add" = "Drupal\gel\Form\GelStudentForm",
 *       "edit" = "Drupal\gel\Form\GelStudentForm",
 *       "delete" = "Drupal\gel\Form\GelStudentDeleteForm",
 *     },
 *     "access" = "Drupal\gel\GelStudentAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\gel\GelStudentHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "gel_student",
 *   admin_permission = "administer gel student entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/gel_student/{gel_student}",
 *     "add-form" = "/admin/structure/gel_student/add",
 *     "edit-form" = "/admin/structure/gel_student/{gel_student}/edit",
 *     "delete-form" = "/admin/structure/gel_student/{gel_student}/delete",
 *     "collection" = "/admin/structure/gel_student",
 *   },
 *   field_ui_base_route = "gel_student.settings"
 * )
 */
class GelStudent extends ContentEntityBase implements GelStudentInterface {

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

      $fields['gel_userid'] = BaseFieldDefinition::create('entity_reference')
        ->setLabel(t('Id χρήστη '))
        ->setDescription(t('Δώσε το id του αντίστοιχου Applicant_User.'))
        ->setSetting('target_type', 'applicant_users')
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

     $fields['name'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Όνομα μαθητή'))
        ->setDescription(t('Δώσε το μικρό μαθητή.'))
        ->setSettings(array(
          'max_length' => 1000,
          'text_processing' => 0,
        ))
        ->setRequired(true)
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

      $fields['am'] = BaseFieldDefinition::create('string')
          ->setLabel(t('Αριθμός Μητρώου στο σχολείο'))
          ->setDescription(t('Αριθμός Μητρώου στο σχολείο.'))
          ->setSettings(array(
            'max_length' => 600,
            'text_processing' => 0,
          ))
          ->setRequired(true)
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

      $fields['myschool_id'] = BaseFieldDefinition::create('integer')
          ->setLabel(t('Μοναδικός επιστρεφόμενος αριθμός id από το ΠΣ myschool'))
          ->setDescription(t('Μοναδικός επιστρεφόμενος αριθμός id από το ΠΣ myschool.'))
          ->setSettings(array(
            'text_processing' => 0,
           ))
          ->setRequired(FALSE)
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

      $fields['studentsurname'] = BaseFieldDefinition::create('string')
          ->setLabel(t('Επώνυμο μαθητή'))
          ->setDescription(t('Δώσε το επώνυμο μαθητή.'))
          ->setSettings(array(
            'max_length' => 1000,
            'text_processing' => 0,
          ))
          ->setRequired(true)
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

      $fields['birthdate'] = BaseFieldDefinition::create('datetime')
        ->setLabel(t('Ημερομηνία γέννησης μαθητή'))
        ->setDescription(t('Δώσε την Ημερομηνία γέννησης μαθητή.'))
        ->setSetting('datetime_type', 'date')
        ->setRequired(true)
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


      $fields['fatherfirstname'] = BaseFieldDefinition::create('string')
          ->setLabel(t('Όνομα του πατέρα'))
          ->setDescription(t('Δώσε το όνομα του πατέρα.'))
          ->setSettings(array(
            'max_length' => 1000,
            'text_processing' => 0,
          ))
          ->setRequired(true)
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


      $fields['motherfirstname'] = BaseFieldDefinition::create('string')
          ->setLabel(t('Όνομα μητέρας'))
          ->setDescription(t('Δώσε το όνομα της μητέρας.'))
          ->setSettings(array(
            'max_length' => 1000,
            'text_processing' => 0,
          ))
          ->setRequired(true)
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

     $fields['regionaddress'] = BaseFieldDefinition::create('string')
          ->setLabel(t('Διεύθνση κηδεμόνα'))
          ->setDescription(t('Δώσε τη διεύθυνση κηδεμόνα.'))
          ->setSettings(array(
            'max_length' => 1500,
            'text_processing' => 0,
          ))
          ->setRequired(true)
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

      $fields['regiontk'] = BaseFieldDefinition::create('string')
          ->setLabel(t('ΤΚ περιοχής'))
          ->setDescription(t('Δώσε τον ΤΚ της διεύθυνσης κατοικίας.'))
          ->setSettings(array(
            'max_length' => 500,
            'text_processing' => 0,
          ))
          ->setRequired(true)
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

      $fields['regionarea'] = BaseFieldDefinition::create('string')
          ->setLabel(t('Πόλη-Κοινότητα'))
          ->setDescription(t('Δώσε την πόλη ή κοινότητα που διαμένεις.'))
          ->setSettings(array(
            'max_length' => 1500,
            'text_processing' => 0,
          ))
          ->setRequired(true)
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


     $fields['nextclass'] = BaseFieldDefinition::create('entity_reference')
                         ->setLabel(t('τάξη που θέλει να εγγραφεί ο μαθητής'))
                         ->setDescription(t('τάξη που θέλει να εγγραφεί ο μαθητής.'))
                         ->setSetting('target_type', 'gel_classes')
                         ->setSetting('handler', 'default')
                         ->setRequired(FALSE)
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




      $fields['telnum'] = BaseFieldDefinition::create('string')
          ->setLabel(t('Τηλέφωνο επικοινωνίας'))
          ->setDescription(t('Δώσε το τηλέφωνο επικοινωνίας'))
          ->setSettings(array(
            'max_length' => 600,
            'text_processing' => 0,
          ))
          ->setRequired(true)
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

      $fields['relationtostudent'] = BaseFieldDefinition::create('string')
          ->setLabel(t('Σχέση αιτούντα με μαθητή'))
          ->setDescription(t('Δώσε τη σχέση αιτούντα με μαθητή, πχ  Γονέας - Κηδεμόνας - Μαθητής'))
          ->setSettings(array(
            'max_length' => 50,
            'text_processing' => 0,
          ))
          ->setRequired(true)
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

      $fields['agreement'] = BaseFieldDefinition::create('boolean')
        ->setLabel(t('Συμφωνία όρων συστήματος'))
        ->setDescription(t('Συμφωνία όρων συστήματος.'))
        ->setSettings(array(
          'text_processing' => 0,
        ))
        ->setRequired(true)
        ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'boolean',
          'weight' => -4,
        ))
        ->setDisplayOptions('form', array(
          'type' => 'boolean',
          'weight' => -4,
        ))
        ->setDisplayConfigurable('form', true)
        ->setDisplayConfigurable('view', true);


      $fields['guardian_name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Όνομα κηδεμόνα'))
            ->setDescription(t('Δώσε το όνομα κηδεμόνα.'))
            ->setSettings(array(
              'max_length' => 1000,
              'text_processing' => 0,
            ))
            ->setRequired(true)
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

      $fields['guardian_surname'] = BaseFieldDefinition::create('string')
                ->setLabel(t('Επώνυμο κηδεμόνα'))
                ->setDescription(t('Δώσε το επώνυμο κηδεμόνα.'))
                ->setSettings(array(
                  'max_length' => 1000,
                  'text_processing' => 0,
                ))
              ->setRequired(true)
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

      $fields['guardian_fathername'] = BaseFieldDefinition::create('string')
                    ->setLabel(t('Όνομα πατέρα κηδεμόνα'))
                    ->setDescription(t('Δώσε το όνομα πατέρα του κηδεμόνα.'))
                    ->setSettings(array(
                      'max_length' => 1000,
                      'text_processing' => 0,
                    ))
                  ->setRequired(true)
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

      $fields['guardian_mothername'] = BaseFieldDefinition::create('string')
                        ->setLabel(t('Όνομα μητέρας κηδεμόνα'))
                        ->setDescription(t('Δώσε το όνομα μητέρας του κηδεμόνα.'))
                        ->setSettings(array(
                          'max_length' => 1000,
                          'text_processing' => 0,
                        ))
                      ->setRequired(true)
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

           $fields['lastschool_registrynumber'] = BaseFieldDefinition::create('string')
                            ->setLabel(t('Κωδικός τελευταίου σχολείου'))
                            ->setDescription(t('Κωδικός τελευταίου σχολείου'))
                            ->setSettings(array(
                              'max_length' => 20,
                              'text_processing' => 0,
                            ))
                          ->setRequired(true)
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

      $fields['lastschool_unittypeid'] = BaseFieldDefinition::create('integer')
                             ->setLabel(t('Τύπος τελευταίου σχολείου'))
                             ->setDescription(t('Τύπος τελευταίου σχολείου'))
                             ->setSettings(array(
                                'max_length' => 3,
                                'text_processing' => 0,
                             ))
                            ->setRequired(true)
                             ->setDisplayOptions('view', array(
                            'label' => 'above',
                            'type' => 'string',
                            'weight' => -4,
                             ))
                             ->setDisplayOptions('form', array(
                            'type' => 'integer',
                            'weight' => -4,
                             ))
                             ->setDisplayConfigurable('form', true)
                             ->setDisplayConfigurable('view', true);

      $fields['lastschool_schoolname'] = BaseFieldDefinition::create('string')
                          ->setLabel(t('Ονομασία τελευταίου σχολείου'))
                          ->setDescription(t('Ονομασία τελευταίου σχολείου'))
                          ->setSettings(array(
                            'max_length' => 200,
                            'text_processing' => 0,
                          ))
                        ->setRequired(true)
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

      $fields['lastschool_schoolyear'] = BaseFieldDefinition::create('string')
                            ->setLabel(t('Σχολικό έτος φοίτησης τελευταίου σχολείου'))
                            ->setDescription(t('Σχολικό έτος φοίτησης τελευταίου σχολείου'))
                            ->setSettings(array(
                              'max_length' => 10,
                              'text_processing' => 0,
                            ))
                            ->setRequired(true)
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

      $fields['lastschool_class'] = BaseFieldDefinition::create('string')
                            ->setLabel(t('Τάξη φοίτησης τελευταίου σχολείου'))
                            ->setDescription(t('Τάξη φοίτησης τελευταίου σχολείου'))
                            ->setSettings(array(
                              'max_length' => 10,
                              'text_processing' => 0,
                            ))
                            ->setRequired(true)
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


        $fields['myschool_currentsection'] = BaseFieldDefinition::create('string')
                            ->setLabel(t('Τομέας / Ειδικότητα / Ομάδα Προσανατολισμού στην τρέχουσα περίοδο'))
                            ->setDescription(t('Τομέας / Ειδικότητα / Ομάδα Προσανατολισμού στην τρέχουσα περίοδο'))
                            ->setSettings(array(
                              'max_length' => 200,
                              'text_processing' => 0,
                            ))
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

        $fields['myschool_currentlevelname'] = BaseFieldDefinition::create('string')
                            ->setLabel(t('Τάξη στην τρέχουσα περίοδο'))
                            ->setDescription(t('Τάξη στην τρέχουσα περίοδο'))
                            ->setSettings(array(
                              'max_length' => 40,
                              'text_processing' => 0,
                            ))
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

        $fields['myschool_currentunittype'] = BaseFieldDefinition::create('string')
                            ->setLabel(t('Σχολική μονάδα στην τρέχουσα περίοδο'))
                            ->setDescription(t('Σχολική μονάδα στην τρέχουσα περίοδο'))
                            ->setSettings(array(
                              'max_length' => 100,
                              'text_processing' => 0,
                            ))
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

        $fields['myschool_promoted'] = BaseFieldDefinition::create('boolean')
                            ->setLabel(t('Μαθητής προάχθηκε / απολύθηκε'))
                            ->setDescription(t('Μαθητής προάχθηκε / απολύθηκε.'))
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

       $fields['delapp'] = BaseFieldDefinition::create('boolean')
                            ->setLabel(t('Διαγραφή'))
                            ->setDescription(t('Διαγραφή.'))
                            ->setSettings(array(
                              'text_processing' => 0,
                            ))
                            ->setRequired(false)
                            ->setDefaultValue(false)
                            ->setDisplayOptions('view', array(
                              'label' => 'above',
                              'type' => 'boolean',
                              'weight' => -4,
                            ))
                            ->setDisplayOptions('form', array(
                              'type' => 'boolean',
                              'weight' => -4,
                              ))
                            ->setDisplayConfigurable('form', true)
                            ->setDisplayConfigurable('view', true);

      $fields['delapp_changed'] = BaseFieldDefinition::create('created')
          ->setLabel(t('Timestamp διαγραφής αίτησης'))
          ->setDescription(t('Timestamp διαγραφής αίτησης.'));

      $fields['delapp_role'] = BaseFieldDefinition::create('string')
                          ->setLabel(t('Ρόλος που έκανε διαγραφή'))
                          ->setDescription(t('Ρόλος που έκανε διαγραφή.'))
                          ->setSettings(array(
                              'max_length' => 10,
                              'text_processing' => 0,
                          ))
                          ->setRequired(false)
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

      $fields['delapp_gelid'] = BaseFieldDefinition::create('entity_reference')
                         ->setLabel(t('Id χρήστη που έκανε τη διαγραφή (περίπτωση ρόλου: διευθυντής)'))
                         ->setDescription(t('Id χρήστη που έκανε τη διαγραφή.'))
                         ->setSetting('target_type', 'applicant_users')
                         ->setSetting('handler', 'default')
                         ->setRequired(FALSE)
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

        $fields['delapp_studentid'] = BaseFieldDefinition::create('entity_reference')
                          ->setLabel(t('Id χρήστη που έκανε τη διαγραφή (περίπτωση ρόλου: αιτούντα)'))
                          ->setDescription(t('Id χρήστη που έκανε τη διαγραφή.'))
                          ->setSetting('target_type', 'applicant_users')
                          ->setSetting('handler', 'default')
                          ->setRequired(FALSE)
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



    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Gel student is published.'))
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
