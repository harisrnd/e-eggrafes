<?php

namespace Drupal\epal\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the EPAL Student entity.
 *
 * @ingroup epal
 *
 * @ContentEntityType(
 *   id = "epal_student",
 *   label = @Translation("EPAL Student"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\epal\EpalStudentListBuilder",
 *     "views_data" = "Drupal\epal\Entity\EpalStudentViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\epal\Form\EpalStudentForm",
 *       "add" = "Drupal\epal\Form\EpalStudentForm",
 *       "edit" = "Drupal\epal\Form\EpalStudentForm",
 *       "delete" = "Drupal\epal\Form\EpalStudentDeleteForm",
 *     },
 *     "access" = "Drupal\epal\EpalStudentAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\epal\EpalStudentHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "epal_student",
 *   admin_permission = "administer epal student entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *     "epaluser_id" = "epaluser_id",
 *     "name" = "name",
 *     "studentsurname" = "studentsurname",
 *   },
 *   links = {
 *     "canonical" = "/admin/epal/epal_student/{epal_student}",
 *     "add-form" = "/admin/epal/epal_student/add",
 *     "edit-form" = "/admin/epal/epal_student/{epal_student}/edit",
 *     "delete-form" = "/admin/epal/epal_student/{epal_student}/delete",
 *     "collection" = "/admin/epal/epal_student",
 *   },
 *   field_ui_base_route = "epal_student.settings"
 * )
 */
class EpalStudent extends ContentEntityBase implements EpalStudentInterface
{
    use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values)
  {
      parent::preCreate($storage_controller, $values);
      $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

    public function getName()
    {
        return $this->get('name')->value;
    }

  /**
   * {@inheritdoc}
   */
  public function setName($name)
  {
      $this->set('name', $name);

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUser_id()
  {
      return $this->get('user_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setUser_id($name)
  {
      $this->set('user_id', $name);

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime()
  {
      return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp)
  {
      $this->set('created', $timestamp);

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus()
  {
      return $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($name)
  {
      $this->set('status', $name);

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner()
  {
      return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account)
  {
      $this->set('user_id', $account->id());

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId()
  {
      return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid)
  {
      $this->set('user_id', $uid);

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished()
  {
      return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published)
  {
      $this->set('status', $published ? true : false);

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEpaluser_id()
  {
      return $this->get('epaluser_id')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function setEpaluser_id($name)
  {
      $this->set('epaluser_id', $name);

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStudentSurname()
  {
      return $this->get('studentsurname')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setStudentSurname($name)
  {
      $this->set('Studentsurname', $name);

      return $this;
  }

    public function getBirthdate()
    {
        return $this->get('birthdate')->value;
    }

    public function setBirthdate($name)
    {
        $this->set('birthdate', $name);

        return $this;
    }

    public function getFatherFirstname()
    {
        return $this->get('fatherfirstname')->value;
    }

    public function setFatherFirstname($name)
    {
        $this->set('fatherfirstname', $name);

        return $this;
    }

  /**
   * {@inheritdoc}
   */

  /**
   * {@inheritdoc}
   */
  public function getRegionAddress()
  {
      return $this->get('regionaddress')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRegionAddress($name)
  {
      $this->set('regionaddress', $name);

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionTK()
  {
      return $this->get('regiontk')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRegionTK($name)
  {
      $this->set('regiontk', $name);

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionArea()
  {
      return $this->get('regionarea')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRegionArea($name)
  {
      $this->set('regionarea', $name);

      return $this;
  }

  /**
   * {@inheritdoc}
   */

  /**
   * {@inheritdoc}
   */
  public function getCurrentclass()
  {
      return $this->get('currentclass')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCurrentclass($name)
  {
      $this->set('currentclass', $name);

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentepal()
  {
      return $this->get('currentepal')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCurrentepal($name)
  {
      $this->set('currentepal', $name);

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTelnum()
  {
      return $this->get('telnum')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTelnum($name)
  {
      $this->set('telnum', $name);

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRelationToStudent()
  {
      return $this->get('relationtostudent')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRelationToStudent($name)
  {
      $this->set('relationtostudent', $name);

      return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
  {
      $fields = parent::baseFieldDefinitions($entity_type);

      $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
          ->setLabel(t('Δημιουργός'))
          ->setDescription(t('Δημιουργός.'))
          ->setRevisionable(true)
          ->setSetting('target_type', 'user')
          ->setSetting('handler', 'default')
          ->setTranslatable(true)
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

      $fields['epaluser_id'] = BaseFieldDefinition::create('entity_reference')
          ->setLabel(t('Id χρήστη ΕΠΑΛ'))
          ->setDescription(t('Δώσε το id του αντίστοιχου Epal User.'))
          ->setSetting('target_type', 'applicant_users')
          ->setSetting('handler', 'default')
            ->setRequired(true)
     //     ->setTranslatable(TRUE)
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

      $fields['currentclass'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Τάξη παρακολούθησης'))
            ->setDescription(t('Δώσε την τρέχουσα τάξη παρακολούθησης'))
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
            ->setDisplayConfigurable('form', true)
            ->setDisplayConfigurable('view', true);

      $fields['currentepal'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('ΕΠΑΛ παρακολούθησης'))
            ->setDescription(t('Δώσε το τρέχον ΕΠΑΛ παρακολούθησης.'))
            ->setSetting('target_type', 'eepal_school')
            ->setSetting('handler', 'default')
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

      $fields['second_period'] = BaseFieldDefinition::create('boolean')
        ->setLabel(t('Δεύτερη περίοδος αιτήσεων'))
        ->setDescription(t('Δεύτερη περίοδος αιτήσεων.'))
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

      $fields['delapp_epalid'] = BaseFieldDefinition::create('entity_reference')
                         ->setLabel(t('Id ΕΠΑΛ που έκανε τη διαγραφή'))
                         ->setDescription(t('Id ΕΠΑΛ που έκανε τη διαγραφή.'))
                         ->setSetting('target_type', 'eepal_school')
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
                          ->setLabel(t('Id Μαθητή που έκανε τη διαγραφή'))
                          ->setDescription(t('Id Μαθητή που έκανε τη διαγραφή.'))
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
          ->setDescription(t('A boolean indicating whether the EPAL Student is published.'))
            ->setDefaultValue(true);

      $fields['created'] = BaseFieldDefinition::create('created')
          ->setLabel(t('Created'))
          ->setDescription(t('The time that the entity was created.'));

      $fields['changed'] = BaseFieldDefinition::create('changed')
          ->setLabel(t('Changed'))
          ->setDescription(t('The time that the entity was last edited.'));

      return $fields;
  }
}
