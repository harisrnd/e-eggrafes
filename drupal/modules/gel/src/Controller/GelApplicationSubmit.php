<?php

namespace Drupal\gel\Controller;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;

use Drupal\Core\Database\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

use Drupal\gel\Crypt;

//use Drupal\epal\ClientConsumer;

class GelApplicationSubmit extends ControllerBase
{
    const ERROR_DB = -1;
    const FULL_CLASS = -2;
    const NON_FULL_CLASS = -3;

    const UNIT_TYPE_NIP = 1;
    const UNIT_TYPE_DIM = 2;
    const UNIT_TYPE_GYM = 3;
    const UNIT_TYPE_LYK = 4;
    const UNIT_TYPE_EPAL = 5;

    const CERT_GYM = 'Απολυτήριο Γυμνασίου';
    const CERT_LYK = 'Απολυτήριο Λυκείου';

    const VALID_NAMES_PATTERN = '/^[A-Za-zΑ-ΩΆΈΉΊΙΎΌΏα-ωάέήίΐύόώ \-]*$/mu';
    const VALID_UCASE_NAMES_PATTERN = '/^[A-ZΑ-Ω]{2,}[A-ZΑ-Ω \-]*$/mu';
    const VALID_ADDRESS_PATTERN = '/^[0-9A-Za-zΑ-ΩΆΈΉΊΎΌΏα-ωάέήίύόώ\/\. \-]*$/mu';
    const VALID_ADDRESSTK_PATTERN = '/^[0-9]{5}$/';
    const VALID_DIGITS_PATTERN = '/^[0-9]*$/';
    const VALID_TELEPHONE_PATTERN = '/^2[0-9]{9}$/';
    const VALID_YEAR_PATTERN = '/^(19[6789][0-9]|20[0-1][0-9])$/';
    const VALID_CAPACITY_PATTERN = '/[0-9]*$/';
    const LIMIT_SCHOOL_YEAR = '2013-2014';

    protected $entityTypeManager;
    protected $logger;
    protected $connection;
    protected $client; // client consumer

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        Connection $connection,
        LoggerChannelFactoryInterface $loggerChannel
    ) {
        $this->entityTypeManager = $entityTypeManager;
        $this->connection = $connection;
        $this->logger = $loggerChannel->get('gel');

        /*
        $config = $this->config('epal.settings');
        $settings = [];
        foreach (['ws_endpoint', 'ws_username', 'ws_password', 'verbose', 'NO_SAFE_CURL'] as $setting) {
            $settings[$setting] = $config->get($setting);
        }
        $this->client = new ClientConsumer($settings, $entityTypeManager, $loggerChannel);
        */

    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('entity_type.manager'),
            $container->get('database'),
            $container->get('logger.factory')
        );
    }

    public function appSubmit(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return $this->respondWithStatus([
                "error_code" => 2001
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        $applicationForm = array();

        $content = $request->getContent();
        if (!empty($content)) {
            $applicationForm = json_decode($content, true);
        } else {
            return $this->respondWithStatus([
                "error_code" => 5002
            ], Response::HTTP_BAD_REQUEST);
        }

        //user role validation
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if (!$user) {
           return $this->respondWithStatus([
                   'message' => t("User not found"),
               ], Response::HTTP_FORBIDDEN);
        }

 			  $roles = $user->getRoles();
 			  $validRole = false;
 			  foreach ($roles as $role)
 				 if ($role === "applicant") {
 					 $validRole = true;
 					 break;
 				 }
 			  if (!$validRole) {
 					 return $this->respondWithStatus([
 									 'message' => t("User Invalid Role"),
 							 ], Response::HTTP_FORBIDDEN);
 			  }

        // configuration validation
        $eggrafesConfigs = $this->entityTypeManager->getStorage('eggrafes_config')->loadByProperties(array('name' => 'eggrafes_config_gel'));
        $eggrafesConfig = reset($eggrafesConfigs);
        if (!$eggrafesConfig) {
            return $this->respondWithStatus([
                "error_code" => 3001
            ], Response::HTTP_FORBIDDEN);
        }
        if ($eggrafesConfig->lock_application->value) {
            return $this->respondWithStatus([
                "error_code" => 3002
            ], Response::HTTP_FORBIDDEN);
        }

        $crypt = new Crypt();
        try {
            $name_encoded = $crypt->encrypt($applicationForm[0]['name']);
            $studentsurname_encoded = $crypt->encrypt($applicationForm[0]['studentsurname']);
            $fatherfirstname_encoded = $crypt->encrypt($applicationForm[0]['fatherfirstname']);
            $motherfirstname_encoded = $crypt->encrypt($applicationForm[0]['motherfirstname']);
            $regionaddress_encoded = $crypt->encrypt($applicationForm[0]['regionaddress']);
            //$regiontk_encoded = $crypt->encrypt($applicationForm[0]['regiontk']);
            //$regionarea_encoded = $crypt->encrypt($applicationForm[0]['regionarea']);
            $telnum_encoded = $crypt->encrypt($applicationForm[0]['telnum']);
            $guardian_name_encoded = $crypt->encrypt($applicationForm[0]['cu_name']);
            $guardian_surname_encoded = $crypt->encrypt($applicationForm[0]['cu_surname']);
            $guardian_fathername_encoded = $crypt->encrypt($applicationForm[0]['cu_fathername']);
            $guardian_mothername_encoded = $crypt->encrypt($applicationForm[0]['cu_mothername']);
            if ($applicationForm[0]['regiontk'] != null)
              $regiontk_encoded = $crypt->encrypt($applicationForm[0]['regiontk']);
            else
              $regiontk_encoded = $applicationForm[0]['regiontk'];
            if ($applicationForm[0]['regionarea'] != null)
              $regionarea_encoded = $crypt->encrypt($applicationForm[0]['regionarea']);
            else
              $regionarea_encoded = $applicationForm[0]['regionarea'];
            $am_encoded = "";
            if ( $applicationForm[0]['lastschool_schoolyear'] >= self::LIMIT_SCHOOL_YEAR)
                $am_encoded = $crypt->encrypt($applicationForm[0]['am']);

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->respondWithStatus([
                "error_code" => 5001
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        unset($crypt);

        $transaction = $this->connection->startTransaction();
        try {
            //insert records in entity: gel_student
            $authToken = $request->headers->get('PHP_AUTH_USER');
            $applicantUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
            $applicantUser = reset($applicantUsers);
            if (!$applicantUser) {
                return $this->respondWithStatus([
                    "error_code" => 4003
                ], Response::HTTP_FORBIDDEN);
            }

            $second_period = $eggrafesConfig->activate_second_period->value;

            $student = array(
                'langcode' => 'el',
                'user_id' => $applicantUser->user_id->target_id,
                'gel_userid' => $applicantUser->id(),
                'am' => $am_encoded,
                'myschool_id' => $applicationForm[0]['studentId'],
                'name' => $name_encoded,
                'studentsurname' => $studentsurname_encoded,
                'birthdate' => $applicationForm[0]['studentbirthdate'],
                'fatherfirstname' => $fatherfirstname_encoded,
                'motherfirstname' => $motherfirstname_encoded,
                'regionaddress' => $regionaddress_encoded,
                'regionarea' => $regionarea_encoded,
                'regiontk' => $regiontk_encoded,
                'lastschool_registrynumber' => $applicationForm[0]['lastschool_registrynumber'],
                'lastschool_unittypeid' => $applicationForm[0]['lastschool_unittypeid'],
                'lastschool_schoolname' => $applicationForm[0]['lastschool_schoolname'],
                'lastschool_schoolyear' => $applicationForm[0]['lastschool_schoolyear'],
                'lastschool_class' => $applicationForm[0]['lastschool_class'],
                'nextclass' => $applicationForm[0]['nextclass'],
                'guardian_name' => $guardian_name_encoded,
                'guardian_surname' => $guardian_surname_encoded,
                'guardian_fathername' => $guardian_fathername_encoded,
                'guardian_mothername' => $guardian_mothername_encoded,
                'agreement' => $applicationForm[0]['disclaimer_checked'],
                'hasright' => $applicationForm[0]['hasright'],
                'relationtostudent' => $applicationForm[0]['relationtostudent'],
                'telnum' => $telnum_encoded,
                'second_period' => $second_period,
                'myschool_currentsection' => $applicationForm[0]['section_name'],
                'myschool_currentlevelname' => $applicationForm[0]['level_name'],
                'myschool_currentunittype' => $applicationForm[0]['unittype_name'],
            );

            if (($errorCode = $this->validateStudent(array_merge(
                    $student, [
                        'name' => $applicationForm[0]['name'],
                        'studentsurname' => $applicationForm[0]['studentsurname'],
                        'fatherfirstname' => $applicationForm[0]['fatherfirstname'],
                        'motherfirstname' => $applicationForm[0]['motherfirstname'],
                        'regionaddress' => $applicationForm[0]['regionaddress'],
                        'regiontk' => $applicationForm[0]['regiontk'],
                        'regionarea' => $applicationForm[0]['regionarea'],
                        'relationtostudent' => $applicationForm[0]['relationtostudent'],
                        'telnum' => $applicationForm[0]['telnum'],
                        'guardian_name' => $applicationForm[0]['cu_name'],
                        'guardian_surname' => $applicationForm[0]['cu_surname'],
                        'guardian_fathername' => $applicationForm[0]['cu_fathername'],
                        'guardian_mothername' => $applicationForm[0]['cu_mothername']
                    ]),
                    $applicationForm[0]['nextclass'],
                    $applicationForm[1]['choice_id'],
                    $applicationForm[3][0]['choice_id'],
                    $applicationForm[2][0]['choice_id'],
                    $applicantUser, $eggrafesConfig->ws_ident->value, false)) > 0) {
                return $this->respondWithStatus([
                    "error_code" => $errorCode
                ], Response::HTTP_OK);
            }

            //καταχώρηση id σχολείου τελευταίας φοίτησης (ή 0 σε περίπτωση που δεν υπάρχει)
            //λειτουργεί για ΕΠΑΛ. Κάτι ανάλογο στα ΓΕΛ;
            /*
            $lastSchoolRegistryNumber = $student['lastschool_registrynumber'];
            $lastSchoolYear = (int)(substr($student['lastschool_schoolyear'], -4));
            if ((int)date("Y") === $lastSchoolYear && (int)$student['lastschool_unittypeid'] === 5) {
                $epalSchools = $this->entityTypeManager->getStorage('eepal_school')->loadByProperties(array('registry_no' => $lastSchoolRegistryNumber));
                $epalSchool = reset($epalSchools);
                if ($epalSchool) {
                    $student['currentepal'] = $epalSchool->id();
                } else {
                    $student['currentepal'] = 0;
                }
            } else {
                $student['currentepal'] = 0;
            }
            */

            $entity_storage_student = $this->entityTypeManager->getStorage('gel_student');
            $entity_object = $entity_storage_student->create($student);
            $entity_storage_student->save($entity_object);

            $created_student_id = $entity_object->id();
            //ομάδα προσανατολισμού
            $classIds = array("2", "3", "6", "7");
            if (in_array($applicationForm[0]['nextclass'], $classIds))  {
              //if ($applicationForm[0]['nextclass'] === "..."
              $studentchoice = array(
                  'student_id' => $created_student_id,
                  'choice_id' => $applicationForm[1]['choice_id'],
              );
              $entity_storage_studentchoices = $this->entityTypeManager->getStorage('gel_student_choices');
              $entity_object = $entity_storage_studentchoices->create($studentchoice);
              $entity_storage_studentchoices->save($entity_object);
          }

            //μαθήματα επιλογής
            $classIds = array("1", "3", "4");
            if (in_array($applicationForm[0]['nextclass'], $classIds))  {
              //$this->logger->warning("Message1");
              for ($i = 0; $i < sizeof($applicationForm[2]); $i++) {
                  $coursechosen = array(
                      'student_id' => $created_student_id,
                      'choice_id' => $applicationForm[2][$i]['choice_id'],
                      'order_id' => $applicationForm[2][$i]['order_id']
                  );
                  $entity_storage_coursechosen = $this->entityTypeManager->getStorage('gel_student_choices');
                  $entity_object = $entity_storage_coursechosen->create($coursechosen);
                  $entity_storage_coursechosen->save($entity_object);
              }
            }

            //μαθήματα ξένων γλωσσών
            $classIds = array("1","4");
            if (in_array($applicationForm[0]['nextclass'], $classIds))  {
              for ($i = 0; $i < sizeof($applicationForm[3]); $i++) {
                  $coursechosen = array(
                      'student_id' => $created_student_id,
                      'choice_id' => $applicationForm[3][$i]['choice_id'],
                      'order_id' => $applicationForm[3][$i]['order_id']
                  );
                  $entity_storage_langchosen = $this->entityTypeManager->getStorage('gel_student_choices');
                  $entity_object = $entity_storage_langchosen->create($coursechosen);
                  $entity_storage_langchosen->save($entity_object);
              }
            }

            return $this->respondWithStatus([
                "error_code" => 0
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            $transaction->rollback();

            return $this->respondWithStatus([
                "error_code" => 5001
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    public function appUpdate(Request $request, $studentId)
    {
      if (!$request->isMethod('POST')) {
          return $this->respondWithStatus([
              "error_code" => 2001
          ], Response::HTTP_METHOD_NOT_ALLOWED);
      }
      $applicationForm = array();

      $content = $request->getContent();
      if (!empty($content)) {
          $applicationForm = json_decode($content, true);
      } else {
          return $this->respondWithStatus([
              "error_code" => 5002
          ], Response::HTTP_BAD_REQUEST);
      }

      //user role validation
      $authToken = $request->headers->get('PHP_AUTH_USER');
      $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
      $user = reset($users);
      if (!$user) {
         return $this->respondWithStatus([
                 'message' => t("User not found"),
             ], Response::HTTP_FORBIDDEN);
      }

      $roles = $user->getRoles();
      $validRole = false;
      foreach ($roles as $role)
       if ($role === "applicant") {
         $validRole = true;
         break;
       }
      if (!$validRole) {
         return $this->respondWithStatus([
                 'message' => t("User Invalid Role"),
             ], Response::HTTP_FORBIDDEN);
      }

      //configuration validation
      $eggrafesConfigs = $this->entityTypeManager->getStorage('eggrafes_config')->loadByProperties(array('name' => 'eggrafes_config_gel'));
      $eggrafesConfig = reset($eggrafesConfigs);
      if (!$eggrafesConfig) {
          return $this->respondWithStatus([
              "error_code" => 3001
          ], Response::HTTP_FORBIDDEN);
      }
      if ($eggrafesConfig->lock_application->value) {
          return $this->respondWithStatus([
              "error_code" => 3002
          ], Response::HTTP_FORBIDDEN);
      }

      $crypt = new Crypt();
      try {
          $name_encoded = $crypt->encrypt($applicationForm[0]['name']);
          $studentsurname_encoded = $crypt->encrypt($applicationForm[0]['studentsurname']);
          $fatherfirstname_encoded = $crypt->encrypt($applicationForm[0]['fatherfirstname']);
          $motherfirstname_encoded = $crypt->encrypt($applicationForm[0]['motherfirstname']);
          $regionaddress_encoded = $crypt->encrypt($applicationForm[0]['regionaddress']);
          //$regiontk_encoded = $crypt->encrypt($applicationForm[0]['regiontk']);
          //$regionarea_encoded = $crypt->encrypt($applicationForm[0]['regionarea']);
          $telnum_encoded = $crypt->encrypt($applicationForm[0]['telnum']);
          $guardian_name_encoded = $crypt->encrypt($applicationForm[0]['cu_name']);
          $guardian_surname_encoded = $crypt->encrypt($applicationForm[0]['cu_surname']);
          $guardian_fathername_encoded = $crypt->encrypt($applicationForm[0]['cu_fathername']);
          $guardian_mothername_encoded = $crypt->encrypt($applicationForm[0]['cu_mothername']);
          if ($applicationForm[0]['regiontk'] != null)
            $regiontk_encoded = $crypt->encrypt($applicationForm[0]['regiontk']);
          else
            $regiontk_encoded = $applicationForm[0]['regiontk'];
          if ($applicationForm[0]['regionarea'] != null)
            $regionarea_encoded = $crypt->encrypt($applicationForm[0]['regionarea']);
          else
            $regionarea_encoded = $applicationForm[0]['regionarea'];
          $am_encoded = "";
          if ( $applicationForm[0]['lastschool_schoolyear'] >= self::LIMIT_SCHOOL_YEAR)
              $am_encoded = $crypt->encrypt($applicationForm[0]['am']);

      } catch (\Exception $e) {
          $this->logger->error($e->getMessage());
          return $this->respondWithStatus([
              "error_code" => 5001
          ], Response::HTTP_INTERNAL_SERVER_ERROR);
      }
      unset($crypt);

      $transaction = $this->connection->startTransaction();
      try {
          //insert records in entity: gel_student
          $authToken = $request->headers->get('PHP_AUTH_USER');
          $applicantUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
          $applicantUser = reset($applicantUsers);
          if (!$applicantUser) {
              return $this->respondWithStatus([
                  "error_code" => 4003
              ], Response::HTTP_FORBIDDEN);
          }

          $second_period = $eggrafesConfig->activate_second_period->value;

          $student = array(
              'langcode' => 'el',
              'user_id' => $applicantUser->user_id->target_id,
              'gel_userid' => $applicantUser->id(),
              'am' => $am_encoded,
              'myschool_id' => $applicationForm[0]['studentId'],
              'name' => $name_encoded,
              'studentsurname' => $studentsurname_encoded,
              'birthdate' => $applicationForm[0]['studentbirthdate'],
              'fatherfirstname' => $fatherfirstname_encoded,
              'motherfirstname' => $motherfirstname_encoded,
              'regionaddress' => $regionaddress_encoded,
              'regionarea' => $regionarea_encoded,
              'regiontk' => $regiontk_encoded,
              'lastschool_registrynumber' => $applicationForm[0]['lastschool_registrynumber'],
              'lastschool_unittypeid' => $applicationForm[0]['lastschool_unittypeid'],
              'lastschool_schoolname' => $applicationForm[0]['lastschool_schoolname'],
              'lastschool_schoolyear' => $applicationForm[0]['lastschool_schoolyear'],
              'lastschool_class' => $applicationForm[0]['lastschool_class'],
              'nextclass' => $applicationForm[0]['nextclass'],
              'guardian_name' => $guardian_name_encoded,
              'guardian_surname' => $guardian_surname_encoded,
              'guardian_fathername' => $guardian_fathername_encoded,
              'guardian_mothername' => $guardian_mothername_encoded,
              'agreement' => $applicationForm[0]['disclaimer_checked'],
              'hasright' => $applicationForm[0]['hasright'],
              'relationtostudent' => $applicationForm[0]['relationtostudent'],
              'telnum' => $telnum_encoded,
              'second_period' => $second_period,
              'myschool_currentsection' => $applicationForm[0]['section_name'],
              'myschool_currentlevelname' => $applicationForm[0]['level_name'],
              'myschool_currentunittype' => $applicationForm[0]['unittype_name'],
          );

          if (($errorCode = $this->validateStudent(array_merge(
                  $student, [
                      'name' => $applicationForm[0]['name'],
                      'studentsurname' => $applicationForm[0]['studentsurname'],
                      'fatherfirstname' => $applicationForm[0]['fatherfirstname'],
                      'motherfirstname' => $applicationForm[0]['motherfirstname'],
                      'regionaddress' => $applicationForm[0]['regionaddress'],
                      'regiontk' => $applicationForm[0]['regiontk'],
                      'regionarea' => $applicationForm[0]['regionarea'],
                      'relationtostudent' => $applicationForm[0]['relationtostudent'],
                      'telnum' => $applicationForm[0]['telnum'],
                      'guardian_name' => $applicationForm[0]['cu_name'],
                      'guardian_surname' => $applicationForm[0]['cu_surname'],
                      'guardian_fathername' => $applicationForm[0]['cu_fathername'],
                      'guardian_mothername' => $applicationForm[0]['cu_mothername']
                  ]),
                  $applicationForm[0]['nextclass'],
                  $applicationForm[1]['choice_id'],
                  $applicationForm[3][0]['choice_id'],
                  $applicationForm[2][0]['choice_id'],
                  $applicantUser, $eggrafesConfig->ws_ident->value, true)) > 0) {
              return $this->respondWithStatus([
                  "error_code" => $errorCode
              ], Response::HTTP_OK);
          }

          //ενημέρωση (update) του gel_student
          $entity_storage_student = $this->entityTypeManager->getStorage('gel_student');
          $gelStudents = $entity_storage_student->loadByProperties(array('id' => $studentId));
          $gelStudent = reset($gelStudents);
          if (!$gelStudent) {
             return $this->respondWithStatus([
                     'message' => t("GelStudent Enity not found"),
                 ], Response::HTTP_FORBIDDEN);
          }
          else  {
            $gelStudent->set('name', $name_encoded);
            $gelStudent->set('studentsurname', $studentsurname_encoded);
            $gelStudent->set('birthdate', $applicationForm[0]['studentbirthdate']);
            $gelStudent->set('fatherfirstname', $fatherfirstname_encoded);
            $gelStudent->set('motherfirstname', $motherfirstname_encoded);
            $gelStudent->set('regionaddress', $regionaddress_encoded);
            $gelStudent->set('regionarea', $regionarea_encoded);
            $gelStudent->set('regiontk', $regiontk_encoded);
            $gelStudent->set('lastschool_registrynumber', $applicationForm[0]['lastschool_registrynumber']);
            $gelStudent->set('lastschool_unittypeid', $applicationForm[0]['lastschool_unittypeid']);
            $gelStudent->set('lastschool_schoolname', $applicationForm[0]['lastschool_schoolname']);
            $gelStudent->set('lastschool_schoolyear', $applicationForm[0]['lastschool_schoolyear']);
            $gelStudent->set('lastschool_class', $applicationForm[0]['lastschool_class']);
            $gelStudent->set('lastschool_schoolyear', $applicationForm[0]['lastschool_schoolyear']);
            $gelStudent->set('nextclass', $applicationForm[0]['nextclass']);
            $gelStudent->set('guardian_name', $guardian_name_encoded);
            $gelStudent->set('guardian_surname', $guardian_surname_encoded);
            $gelStudent->set('guardian_fathername', $guardian_fathername_encoded);
            $gelStudent->set('guardian_mothername', $guardian_mothername_encoded);
            $gelStudent->set('relationtostudent', $applicationForm[0]['relationtostudent']);
            $gelStudent->set('telnum', $telnum_encoded);

            $gelStudent->set('am', $am_encoded);
            $gelStudent->set('myschool_id', $applicationForm[0]['studentId']);
            $gelStudent->set('myschool_currentsection', $applicationForm[0]['section_name']);
            $gelStudent->set('myschool_currentlevelname', $applicationForm[0]['level_name']);
            $gelStudent->set('myschool_currentunittype', $applicationForm[0]['unittype_name']);

            $gelStudent->save();
          }
          $entity_storage_student->resetCache();

          //διαγραφή αντίστοιχων εγγραφών από gel_student_choices
          $entity_storage_gel_student_choices = $this->entityTypeManager->getStorage('gel_student_choices');
          $ids  = $entity_storage_gel_student_choices->getQuery()
            ->condition('student_id', $studentId, '=')
            ->execute();
          $choices = $entity_storage_gel_student_choices->loadMultiple($ids);
          $entity_storage_gel_student_choices->delete($choices);
          $entity_storage_gel_student_choices->resetCache();

          //εισαγωγή νέων εγγραφών στo gel_student_choices (ομάδα προσανατολισμού)
          $classIds = array("2", "3", "6", "7");
          if (in_array($applicationForm[0]['nextclass'], $classIds))  {
              $orientationchosen = array(
                  'student_id' => $studentId,
                  'choice_id' => $applicationForm[1]['choice_id']
                );
              $entity_storage_studentorientation = $this->entityTypeManager->getStorage('gel_student_choices');
              $entity_object = $entity_storage_studentorientation->create($orientationchosen);
              $entity_storage_studentorientation->save($entity_object);
          }

          //εισαγωγή νέων εγγραφών στo gel_student_choices (μαθήματα επιλογής)
          $classIds = array("1", "3", "4");
          if (in_array($applicationForm[0]['nextclass'], $classIds))  {
              for ($i = 0; $i < sizeof($applicationForm[2]); $i++) {
                  $coursechosen = array(
                      'student_id' => $studentId,
                      'choice_id' => $applicationForm[2][$i]['choice_id'],
                      'order_id' => $applicationForm[2][$i]['order_id']
                  );
                  $entity_storage_studentchoices = $this->entityTypeManager->getStorage('gel_student_choices');
                  $entity_object = $entity_storage_studentchoices->create($coursechosen);
                  $entity_storage_studentchoices->save($entity_object);
              }
          }

          //εισαγωγή νέων εγγραφών στo gel_student_choices (μαθήματα ξένων γλωσσών)
          $classIds = array("1","4");
          if (in_array($applicationForm[0]['nextclass'], $classIds))  {
            for ($i = 0; $i < sizeof($applicationForm[3]); $i++) {
                $coursechosen = array(
                    'student_id' => $studentId,
                    'choice_id' => $applicationForm[3][$i]['choice_id'],
                    'order_id' => $applicationForm[3][$i]['order_id']
                );
                $entity_storage_langchosen = $this->entityTypeManager->getStorage('gel_student_choices');
                $entity_object = $entity_storage_langchosen->create($coursechosen);
                $entity_storage_langchosen->save($entity_object);
            }
          }


          return $this->respondWithStatus([
              "error_code" => 0
          ], Response::HTTP_OK);

      } catch (\Exception $e) {
          $this->logger->warning($e->getMessage());
          $transaction->rollback();

          return $this->respondWithStatus([
              "error_code" => 5001
          ], Response::HTTP_INTERNAL_SERVER_ERROR);
      }


    }





    private function respondWithStatus($arr, $s)
    {
        $res = new JsonResponse($arr);
        $res->setStatusCode($s);
        return $res;
    }

    /**
     *
     * @return int error code ελέγχου; 0 εάν ο έλεγχος επιτύχει, μη μηδενικό εάν αποτύχει:
     *  1001 δεν επιλέχθηκε το πλαίσιο συμφωνης γνώμης
     *  1002 λανθασμένο τελευταίο έτος φοίτησης
     *  1003 λανθασμένη ημερομηνία
     *  1004-> 1023: λανθασμένα πεδία αίτησης
     *  8000 μη αναμενόμενο λάθος
     *  8001 δικτυακό λάθος κλήσης υπηρεσίας επιβεβαίωσης στοιχείων
     *  8002 τα στοιχεία φοίτησης δεν επικυρώθηκαν
     *  8003 τα στοιχεία φοίτησης δεν είναι έγκυρα
     */



    private function validateStudent($student, $chosenClass, $chosenOrientation, $chosenLangCourse, $chosenElectiveCourse, $applicantUser = null, $wsEnabled, $appUpdate)
    {
        if (!$student["hasright"] && $appUpdate == false) {
            return 997;
        }
        if (($chosenClass === "2" || $chosenClass === "3" || $chosenClass === "6" || $chosenClass === "7") && !isset($chosenOrientation)) {
            return 998;
        }
        if ( ($chosenClass === "1" || $chosenClass === "3" || $chosenClass === "4") && !isset($chosenElectiveCourse)) {
            return 999;
        }
        if ( ($chosenClass === "1" || $chosenClass === "4") && !isset($chosenLangCourse)) {
            return 1000;
        }
        if (!$student["agreement"] && $appUpdate == false) {
            return 1001;
        }
        if (!$student["lastschool_schoolyear"] || strlen($student["lastschool_schoolyear"]) !== 9) {
            return 1002;
        }
        // date in YYY-MM-DD, out d-m-Y
        $date_parts = explode('-', $student['birthdate'], 3);
        if ((count($date_parts) !== 3) ||
            (checkdate($date_parts[1], $date_parts[2], $date_parts[0]) !== true)) {
            return 1003;
        }
        if (intval($date_parts[0]) >= ((int)date("Y") - 13)) {
            return 1003;
        }
        $birthdate = "{$date_parts[2]}-{$date_parts[1]}-{$date_parts[0]}";

        if (preg_match(self::VALID_UCASE_NAMES_PATTERN, $student["name"]) !== 1) {
            return 1004;
        }
        if (preg_match(self::VALID_UCASE_NAMES_PATTERN, $student["studentsurname"]) !== 1) {
            return 1005;
        }
        if (preg_match(self::VALID_UCASE_NAMES_PATTERN, $student["fatherfirstname"]) !== 1) {
            return 1006;
        }
        if (preg_match(self::VALID_UCASE_NAMES_PATTERN, $student["motherfirstname"]) !== 1) {
            return 1007;
        }
        if (( ($wsEnabled === 1 && $student["lastschool_schoolyear"] < self::LIMIT_SCHOOL_YEAR) || ($wsEnabled === 0) )
          && preg_match(self::VALID_ADDRESS_PATTERN, $student["regionaddress"]) !== 1) {
            return 1008;
        }
        if (  ( ($wsEnabled === 1 && $student["lastschool_schoolyear"] < self::LIMIT_SCHOOL_YEAR) || ($wsEnabled === 0) )
          && preg_match(self::VALID_ADDRESSTK_PATTERN, $student["regiontk"]) !== 1) {
            return 1009;
        }
        if (  ( ($wsEnabled === 1 && $student["lastschool_schoolyear"] < self::LIMIT_SCHOOL_YEAR) || ($wsEnabled === 0) )
          && preg_match(self::VALID_NAMES_PATTERN, $student["regionarea"]) !== 1) {
            return 1010;
        }
        //if (preg_match(self::VALID_ADDRESSTK_PATTERN, $student["regionarea"]) !== 1) {
        //    return 1010;
        //}

        $classIds = array("1", "2", "3", "4", "5", "6", "7");
        if (!in_array($student["nextclass"], $classIds))  {
            return 1013;
        }
        if (!$student["relationtostudent"]) {
            return 1014;
        }
        if (preg_match(self::VALID_TELEPHONE_PATTERN, $student["telnum"]) !== 1) {
            return 1015;
        }
        if (preg_match(self::VALID_NAMES_PATTERN, $student["guardian_name"]) !== 1) {
            return 1016;
        }
        if (preg_match(self::VALID_NAMES_PATTERN, $student["guardian_surname"]) !== 1) {
            return 1017;
        }
        if (preg_match(self::VALID_NAMES_PATTERN, $student["guardian_fathername"]) !== 1) {
            return 1018;
        }
        if (preg_match(self::VALID_NAMES_PATTERN, $student["guardian_mothername"]) !== 1) {
            return 1019;
        }
        if (!$student["lastschool_registrynumber"]) {
            return 1020;
        }
        if (!$student["lastschool_unittypeid"]) {
            return 1021;
        }
        if (!$student["lastschool_schoolname"]) {
            return 1022;
        }
        /*
        if (( ($wsEnabled === 1 && $student["lastschool_schoolyear"] < self::LIMIT_SCHOOL_YEAR) || ($wsEnabled === 0) )
          && $student["am"] && !$student["lastschool_class"]) {
            return 1023;
        }
        */
        if (!$student["lastschool_class"] || $student["lastschool_class"] == -1) {
            return 1023;
        }
        if ($student["myschool_id"] && $student["lastschool_schoolyear"] < self::LIMIT_SCHOOL_YEAR) {
            return 1024;
        }
        if ($wsEnabled === 1 &&  !$student["myschool_id"] && $student["lastschool_schoolyear"] >= self::LIMIT_SCHOOL_YEAR) {
            return 1025;
        }

        //validate class mobility in GEL schools

        //$this->logger->error($student["lastschool_class"]);
        $isNight = $this->isNightSchool($student["lastschool_registrynumber"]);
        //από ΓΕΛ
        if ( $student["lastschool_unittypeid"] == "4"  )  {
            if ($isNight &&  $student["lastschool_class"] == "1" && $student["nextclass"] != 1 && $student["nextclass"] != 5) {
              //$this->logger->error("ΠΕΡΙΠΤΩΣΗ 1 Ή 8");
              return 1026;
            }
            if (!$isNight &&  $student["lastschool_class"] == "1" && $student["nextclass"] != 2 && $student["nextclass"] != 6)  {
              //$this->logger->error("ΠΕΡΙΠΤΩΣΗ 3 Ή 10");
              return 1026;
            }
            if ($isNight &&  $student["lastschool_class"] == "2" && $student["nextclass"] != 2 && $student["nextclass"] != 6)  {
              //$this->logger->error("ΠΕΡΙΠΤΩΣΗ 4 Ή 9");
              return 1026;
            }
            if (!$isNight &&  $student["lastschool_class"] == "2" && $student["nextclass"] != 3 && $student["nextclass"] != 7)  {
              //$this->logger->error("ΠΕΡΙΠΤΩΣΗ 5 Ή 12");
              return 1026;
            }
            if ($isNight &&  $student["lastschool_class"] == "3" && $student["nextclass"] != 3 && $student["nextclass"] != 7)  {
              //$this->logger->error("ΠΕΡΙΠΤΩΣΗ 6 Ή 11");
              return 1026;
            }
            if (!$isNight &&  $student["lastschool_class"] == "3" )  {
              //$this->logger->error("ΠΕΡΙΠΤΩΣΗ 13");
              return 1026;
            }
        }

        //από Γυμνάσιο
        //Προσοχή! Δεν αντιμετωπίζουμε τα Γυμνάσια με Λυκειακες Τάξεις
        //if ($student["lastschool_unittypeid"] == "3"   && $student["nextclass"] != 1 && $student["nextclass"] != 4)  {
          //$this->logger->error("ΠΕΡΙΠΤΩΣΗ 2 Ή 7");
        //  return 1026;
        //}

        //από ΕΠΑΛ
        //if ($student["lastschool_unittypeid"] == "5"   && $student["nextclass"] != 2 && $student["nextclass"] != 6
        //  &&  (!isNight )  )
        // {
          //$this->logger->error("ΠΕΡΙΠΤΩΣΗ 14 Ή 15");
        //  return 1026;
        //}

        //if ($student["lastschool_unittypeid"] == "5"   && $student["nextclass"] != 2 && $student["nextclass"] != 6
        //  &&  $student["nextclass"] != 4  )
        // {
          //$this->logger->error("ΠΕΡΙΠΤΩΣΗ 14 Ή 15");
        //  return 1026;
        //}

        // check if application exists in eithe gel_student or epal_student entity
        if (/*$student['second_period'] == 1 &&*/ $applicantUser !== null && $appUpdate == false) {
            $retCode = $this->existApp("gel_student", "gel_userid", $applicantUser, $student);
            if ($retCode === -1) {
              $retCode = $this->existApp("epal_student", "epaluser_id", $applicantUser, $student);
            }
            if ($retCode !== -1)
              return $retCode;
        }

        return 0;
    }

    private function isNightSchool($school_regisrty_no)  {
      try {
          $schoolCon = $this->connection->select('gel_school', 'schoolType')
              ->fields('schoolType', array('operation_shift'))
              ->condition('schoolType.registry_no', $school_regisrty_no, '=');
          $results = $schoolCon->execute()->fetchAll(\PDO::FETCH_OBJ);
      } catch (\Exception $e) {
          $this->logger->error($e->getMessage());
          return self::ERROR_DB;
      }

      if (sizeof($results) > 0)  {
        $row = reset($results);
        if ($row->operation_shift === 'ΕΣΠΕΡΙΝΟ')
          return 1;
      }
      return 0;
    }


    private function existApp($entityName, $userIdField, $applicantUser, $student) {
        if (!$student["myschool_id"]) {
            $esQuery = $this->connection->select($entityName, 'es')
                                    ->fields('es',
                                    array('name',
                                            'studentsurname',
                                            'birthdate',
                                        ));
            $esQuery->condition('es.' . $userIdField, $applicantUser->id(), '=');
            $esQuery->condition('es.delapp' , 0, '=');
            $existing = $esQuery->execute()->fetchAll(\PDO::FETCH_OBJ);
            if ($existing && sizeof($existing) > 0) {
                $crypt = new Crypt();
                foreach ($existing as $candidate) {
                    if (($crypt->decrypt($candidate->name) == $student['name'])
                        && ($crypt->decrypt($candidate->studentsurname) == $student['studentsurname'])
                        && ($candidate->birthdate == $student['birthdate'])
                        ) {
                        return 8004;
                    }
                }
            }
            return -1;
      }

      else {
          $esQuery = $this->connection->select($entityName, 'es')
                                      ->fields('es',array('myschool_id'));
          $esQuery->condition('es.myschool_id', $student["myschool_id"], '=');
          $esQuery->condition('es.delapp' , 0, '=');
          $existing = $esQuery->execute()->fetchAll(\PDO::FETCH_OBJ);
          if ($existing && sizeof($existing) > 0)
            return 8004;
          return -1;
      }

    }

}
