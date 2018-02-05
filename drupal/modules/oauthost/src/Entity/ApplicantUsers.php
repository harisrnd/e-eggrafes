<?php

namespace Drupal\oauthost\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\oauthost\ApplicantUsersInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Applicant users entity.
 *
 * @ingroup oauthost
 *
 * @ContentEntityType(
 *   id = "applicant_users",
 *   label = @Translation("Applicant users"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\oauthost\ApplicantUsersListBuilder",
 *     "views_data" = "Drupal\oauthost\Entity\ApplicantUsersViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\oauthost\Form\ApplicantUsersForm",
 *       "add" = "Drupal\oauthost\Form\ApplicantUsersForm",
 *       "edit" = "Drupal\oauthost\Form\ApplicantUsersForm",
 *       "delete" = "Drupal\oauthost\Form\ApplicantUsersDeleteForm",
 *     },
 *     "access" = "Drupal\oauthost\ApplicantUsersAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\oauthost\ApplicantUsersHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "applicant_users",
 *   admin_permission = "administer applicant users entities",
 *    entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *     "name" = "name",
 *     "surname" = "surname",
 *     "requesttoken" = "requesttoken",
 *     "accesstoken" = "accesstoken",
 *     "authtoken" = "authtoken",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/applicant_users/{applicant_users}",
 *     "add-form" = "/admin/structure/applicant_users/add",
 *     "edit-form" = "/admin/structure/applicant_users/{applicant_users}/edit",
 *     "delete-form" = "/admin/structure/applicant_users/{applicant_users}/delete",
 *     "collection" = "/admin/structure/applicant_users",
 *   },
 *   field_ui_base_route = "applicant_users.settings"
 * )
 */
class ApplicantUsers extends ContentEntityBase implements ApplicantUsersInterface {
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
   * get / set methods for aditional fields
   */

  public function getTaxis_userid() {
    return $this->get('taxis_userid')->value;
  }

  public function setTaxis_userid($name) {
    $this->set('taxis_userid', $name);
    return $this;
  }

  public function getSurname() {
    return $this->get('surname')->value;
  }

  public function setSurname($name) {
    $this->set('surname', $name);
    return $this;
  }

  public function getFathername() {
    return $this->get('fathername')->value;
  }

  public function setFathername($name) {
    $this->set('fathername', $name);
    return $this;
  }

  public function getMothername() {
    return $this->get('mothername')->value;
  }

  public function setMothername($name) {
    $this->set('mothername', $name);
    return $this;
  }


  public function getAccesstoken() {
    return $this->get('accesstoken')->value;
  }

  public function setAccesstoken($name) {
    $this->set('accesstoken', $name);
    return $this;
  }

  public function getAuthtoken() {
    return $this->get('authtoken')->value;
  }

  public function setAuthtoken($name) {
    $this->set('authtoken', $name);
    return $this;
  }

  public function getTimelogin() {
    return $this->get('timelogin')->value;
  }

  public function setTimelogin($name) {
    $this->set('timelogin', $name);
    return $this;
  }

  public function getTimeregistration() {
    return $this->get('timeregistration')->value;
  }

  public function setTimeregistration($name) {
    $this->set('timeregistration', $name);
    return $this;
  }

  public function getTimetokeninvalid() {
    return $this->get('timetokeninvalid')->value;
  }

  public function setTimetokeninvalid($name) {
    $this->set('timetokeninvalid', $name);
    return $this;
  }

  public function getUserip() {
    return $this->get('userip')->value;
  }

  public function setUserip($name) {
    $this->set('userip', $name);
    return $this;
  }

  public function getRequestToken() {
    return $this->get('requesttoken')->value;
  }

  public function setRequestToken($requestToken) {
    $this->set('requesttoken', $requestToken);
    return $this;
  }

  public function getRequestTokenSecret() {
    return $this->get('requesttokensecret')->value;
  }

  public function setRequestTokenSecret($requestTokenSecret) {
    $this->set('requesttokensecret', $requestTokenSecret);
    return $this;
  }

  public function getAccessTokenSecret() {
    return $this->get('accesstokensecret')->value;
  }

  public function setAccessTokenSecret($accessTokenSecret) {
    $this->set('accesstokensecret', $accessTokenSecret);
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
    $this->set('status', $published ? NODE_PUBLISHED : NODE_NOT_PUBLISHED);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Δημιουργός'))
      ->setDescription(t('Δημιουργός.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
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
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

  $fields['taxis_userid'] = BaseFieldDefinition::create('string')
      ->setLabel(t('User id χρήστη από taxis'))
      ->setDescription(t('Δώσε το user id του χρήστη από taxis.'))
      ->setSettings(array(
        'max_length' => 400,
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

     $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Όνομα χρήστη'))
      ->setDescription(t('Δώσε το όνομα χρήστη'))
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
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

  $fields['surname'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Επώνυμο χρήστη'))
      ->setDescription(t('Δώσε το επώνυμο του χρήστη.'))
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
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

  $fields['fathername'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Όνομα πατέρα χρήστη'))
      ->setDescription(t('Δώσε το όνομα του πατέρα του χρήστη.'))
      ->setSettings(array(
        'max_length' => 1000,
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

  $fields['mothername'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Όνομα μητέρας χρήστη'))
      ->setDescription(t('Δώσε το όνομα της μητέρας χρήστη.'))
      ->setSettings(array(
        'max_length' => 1000,
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

    $fields['numchildren'] = BaseFieldDefinition::create('integer')
        ->setLabel(t('Αριθμός παιδιών'))
        ->setDescription(t('Δώσε τον αριθμό παιδιών.'))
        ->setSettings(array(
          'text_processing' => 0,
        ))
        ->setRequired(TRUE)
        ->setDefaultValue(0)
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

   $fields['representative'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Εκπρόσωπος για αιτήσεις μαθητών'))
      ->setDescription(t('Εκπρόσωπος για αιτήσεις μαθητών.'))
      ->setSettings(array(
        'text_processing' => 0,
      ))
      ->setRequired(TRUE)
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

   $fields['verificationcode'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Email Verification Code'))
        ->setDescription(t('Generated email verification code'))
        ->setSettings(array(
          'max_length' => 20,
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

    $fields['verificationcodeverified'] = BaseFieldDefinition::create('boolean')
          ->setLabel(t('Email Verification Code Verified'))
          ->setDescription(t('A boolean indicating whether the email verification code was verified.'))
          ->setDefaultValue(FALSE);

  $fields['accesstoken'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Access-Token από taxis'))
      ->setDescription(t('Access-Token από taxis.'))
      ->setSettings(array(
        'max_length' => 1000,
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

      $fields['accesstoken_secret'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Access-Token Secret από taxis'))
        ->setDescription(t('Access-Token Secret από taxis.'))
        ->setSettings(array(
          'max_length' => 1000,
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

  $fields['authtoken'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Authorization Token'))
      ->setDescription(t('Authorization Token που δημιουργείται από την εφαρμογή.'))
      ->setSettings(array(
        'max_length' => 1000,
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

      $fields['requesttoken'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Oauth Request Token'))
        ->setDescription(t('Request Token received by service provider.'))
        ->setSettings(array(
          'max_length' => 1000,
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

        $fields['requesttoken_secret'] = BaseFieldDefinition::create('string')
          ->setLabel(t('Oauth Request Token Secret'))
          ->setDescription(t('Request Token Secret received by service provider.'))
          ->setSettings(array(
            'max_length' => 1000,
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

  $fields['timelogin'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('timeLogin'))
      ->setDescription(t('timeLogin.'))
      ;

  $fields['timeregistration'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('timeRegistration'))
      ->setDescription(t('timeRegistration.'))
      ;

  $fields['timetokeninvalid'] = BaseFieldDefinition::create('integer')
          ->setLabel(t('Χρόνος σε min'))
          ->setDescription(t('Χρόνος σε min μετά τον οποίο γίνεται το token ανενεργό.'))
          ->setSettings(array(
            //'max_length' => 2,
            'text_processing' => 0,
          ))
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

  $fields['userip'] = BaseFieldDefinition::create('string')
      ->setLabel(t('userIP'))
      ->setDescription(t('userIP.'))
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

  $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Appicant users is published.'))
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
