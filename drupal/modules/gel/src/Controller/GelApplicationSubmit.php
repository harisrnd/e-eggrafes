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
    const VALID_UCASE_NAMES_PATTERN = '/^[A-ZΑ-Ω]{3,}[A-ZΑ-Ω \-]*$/mu';
    const VALID_ADDRESS_PATTERN = '/^[0-9A-Za-zΑ-ΩΆΈΉΊΎΌΏα-ωάέήίύόώ\/\. \-]*$/mu';
    const VALID_ADDRESSTK_PATTERN = '/^[0-9]{5}$/';
    const VALID_DIGITS_PATTERN = '/^[0-9]*$/';
    const VALID_TELEPHONE_PATTERN = '/^2[0-9]{9}$/';
    const VALID_YEAR_PATTERN = '/^(19[6789][0-9]|20[0-1][0-9])$/';
    const VALID_CAPACITY_PATTERN = '/[0-9]*$/';

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
        $this->logger = $loggerChannel->get('epal');

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

        $epalConfigs = $this->entityTypeManager->getStorage('epal_config')->loadByProperties(array('name' => 'epal_config'));
        $epalConfig = reset($epalConfigs);
        if (!$epalConfig) {
            return $this->respondWithStatus([
                "error_code" => 3001
            ], Response::HTTP_FORBIDDEN);
        }
        if ($epalConfig->lock_application->value) {
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
            $regiontk_encoded = $crypt->encrypt($applicationForm[0]['regiontk']);
            $regionarea_encoded = $crypt->encrypt($applicationForm[0]['regionarea']);
            $relationtostudent = $applicationForm[0]['relationtostudent'];
            $telnum_encoded = $crypt->encrypt($applicationForm[0]['telnum']);
            $guardian_name_encoded = $crypt->encrypt($applicationForm[0]['cu_name']);
            $guardian_surname_encoded = $crypt->encrypt($applicationForm[0]['cu_surname']);
            $guardian_fathername_encoded = $crypt->encrypt($applicationForm[0]['cu_fathername']);
            $guardian_mothername_encoded = $crypt->encrypt($applicationForm[0]['cu_mothername']);
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

            $second_period = $epalConfig->activate_second_period->value;

            $student = array(
                'langcode' => 'el',
                'user_id' => $applicantUser->user_id->target_id,
                'gel_userid' => $applicantUser->id(),
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
                'relationtostudent' => $relationtostudent,
                'telnum' => $telnum_encoded,
                'second_period' => $second_period,
            );

            /*
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
                    //sizeof($applicationForm[1]),
                    $applicationForm[0]['currentclass'],
                    //$applicationForm[3]['sectorfield_id'],
                    //$applicationForm[3]['coursefield_id'],
                    $applicantUser, false)) > 0) {
                return $this->respondWithStatus([
                    "error_code" => $errorCode
                ], Response::HTTP_OK);
            }
            */

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

            /*
            $created_student_id = $entity_object->id();

            for ($i = 0; $i < sizeof($applicationForm[1]); $i++) {
                $epalchosen = array(
                    'student_id' => $created_student_id,
                    'epal_id' => $applicationForm[1][$i]['epal_id'],
                    'choice_no' => $applicationForm[1][$i]['choice_no']
                );
                $entity_storage_epalchosen = $this->entityTypeManager->getStorage('epal_student_epal_chosen');
                $entity_object = $entity_storage_epalchosen->create($epalchosen);
                $entity_storage_epalchosen->save($entity_object);
            }

            if ($applicationForm[0]['currentclass'] === "3" || $applicationForm[0]['currentclass'] === "4") {
                $course = array(
                    'student_id' => $created_student_id,
                    'coursefield_id' => $applicationForm[3]['coursefield_id']
                );
                $entity_storage_course = $this->entityTypeManager->getStorage('epal_student_course_field');
                $entity_object = $entity_storage_course->create($course);
                $entity_storage_course->save($entity_object);
            } elseif ($applicationForm[0]['currentclass'] === "2") {
                $sector = array(
                    'student_id' => $created_student_id,
                    'sectorfield_id' => $applicationForm[3]['sectorfield_id']
                );
                $entity_storage_sector = $this->entityTypeManager->getStorage('epal_student_sector_field');
                $entity_object = $entity_storage_sector->create($sector);
                $entity_storage_sector->save($entity_object);
            }
          */
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



    /*
    public function appUpdate(Request $request, $studentId, $schNonCheckOccup)
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

      $epalConfigs = $this->entityTypeManager->getStorage('epal_config')->loadByProperties(array('name' => 'epal_config'));
      $epalConfig = reset($epalConfigs);
      if (!$epalConfig) {
          return $this->respondWithStatus([
              "error_code" => 3001
          ], Response::HTTP_FORBIDDEN);
      }
      if ($epalConfig->lock_application->value) {
          return $this->respondWithStatus([
              "error_code" => 3002
          ], Response::HTTP_FORBIDDEN);
      }

      //έλεγχος πληρότητας τμήματος
      if ( $epalConfig->activate_second_period->value === "1")
      {
        $classIdChecked = $applicationForm[0]['currentclass'];
        $secIdChecked = "-1";

        if ($classIdChecked === "2")
          $secIdChecked = $applicationForm[3]['sectorfield_id'];
        else if ($classIdChecked === "3" || $classIdChecked === "4")
          $secIdChecked =  $applicationForm[3]['coursefield_id'];

        for ($i = 0; $i < sizeof($applicationForm[1]); $i++) {
            $epalIdChecked = $applicationForm[1][$i]['epal_id'];
            //αν δεν βρει το σχολείο στη λίστα που είναι προς μη έλεγχο πληρότητας)
            if  (strpos($schNonCheckOccup, "$" . $epalIdChecked . "$") === false)
            {
              $retval = $this->isFull($epalIdChecked, $classIdChecked, $secIdChecked);
              if ($retval !== self::NON_FULL_CLASS) {
                  if ($retval === self::FULL_CLASS) {
                    $err_code = 9001;
                    $schoolName = $this->retrieveSchoolName($epalIdChecked);

                    if ($schoolName !== self::ERROR_DB)
                        return $this->respondWithStatus([
                              "error_code" => $err_code,
                              "school_name" => $schoolName
                          ], Response::HTTP_OK);
                    else {
                      $err_code = 9002;
                      return $this->respondWithStatus([
                            "error_code" => $err_code,
                        ], Response::HTTP_FORBIDDEN);
                    }
                  }

                  else {  //if ($retval === self::ERROR_DB)  {
                    $err_code = 9002;
                    return $this->respondWithStatus([
                          "error_code" => $err_code,
                      ], Response::HTTP_FORBIDDEN);
                  }

                }
            }
        } //end for
      }
      //τέλος ελέγχου πληρότητας


      $crypt = new Crypt();
      try {
          $name_encoded = $crypt->encrypt($applicationForm[0]['name']);
          $studentsurname_encoded = $crypt->encrypt($applicationForm[0]['studentsurname']);
          $fatherfirstname_encoded = $crypt->encrypt($applicationForm[0]['fatherfirstname']);
          $motherfirstname_encoded = $crypt->encrypt($applicationForm[0]['motherfirstname']);
          $regionaddress_encoded = $crypt->encrypt($applicationForm[0]['regionaddress']);
          $regiontk_encoded = $crypt->encrypt($applicationForm[0]['regiontk']);
          $regionarea_encoded = $crypt->encrypt($applicationForm[0]['regionarea']);
          //$relationtostudent_encoded = $crypt->encrypt($applicationForm[0]['relationtostudent']);
          $relationtostudent = $applicationForm[0]['relationtostudent'];
          $telnum_encoded = $crypt->encrypt($applicationForm[0]['telnum']);
          $guardian_name_encoded = $crypt->encrypt($applicationForm[0]['cu_name']);
          $guardian_surname_encoded = $crypt->encrypt($applicationForm[0]['cu_surname']);
          $guardian_fathername_encoded = $crypt->encrypt($applicationForm[0]['cu_fathername']);
          $guardian_mothername_encoded = $crypt->encrypt($applicationForm[0]['cu_mothername']);
      } catch (\Exception $e) {
          $this->logger->error($e->getMessage());
          return $this->respondWithStatus([
              "error_code" => 5001
          ], Response::HTTP_INTERNAL_SERVER_ERROR);
      }
      unset($crypt);

      $transaction = $this->connection->startTransaction();
      try {
          //insert records in entity: epal_student
          $authToken = $request->headers->get('PHP_AUTH_USER');
          $epalUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
          $epalUser = reset($epalUsers);
          if (!$epalUser) {
              return $this->respondWithStatus([
                  "error_code" => 4003
              ], Response::HTTP_FORBIDDEN);
          }

          //$second_period = $epalConfig->activate_second_period->value;
          $student = array(
              'langcode' => 'el',
              'user_id' => $epalUser->user_id->target_id,
              'epaluser_id' => $epalUser->id(),
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
              'currentclass' => $applicationForm[0]['currentclass'],
              'guardian_name' => $guardian_name_encoded,
              'guardian_surname' => $guardian_surname_encoded,
              'guardian_fathername' => $guardian_fathername_encoded,
              'guardian_mothername' => $guardian_mothername_encoded,
              'agreement' => $applicationForm[0]['disclaimer_checked'],
              'relationtostudent' => $relationtostudent,
              'telnum' => $telnum_encoded,
              'second_period' => $epalConfig->activate_second_period->value,
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
                  ]), sizeof($applicationForm[1]), $applicationForm[0]['currentclass'],
                  $applicationForm[3]['sectorfield_id'],
                  $applicationForm[3]['coursefield_id'],
                  $epalUser, true)) > 0) {
              return $this->respondWithStatus([
                  "error_code" => $errorCode
              ], Response::HTTP_OK);
          }

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


           //NEW!!!
           //ενημέρωση (update) του epal_student
           $entity_storage_student = $this->entityTypeManager->getStorage('epal_student');
           $epalStudents = $entity_storage_student->loadByProperties(array('id' => $studentId));
           $epalStudent = reset($epalStudents);
           if (!$epalStudent) {
       				return $this->respondWithStatus([
       								'message' => t("EpalStudent Enity not found"),
       						], Response::HTTP_FORBIDDEN);
       		 }
           else  {
             $epalStudent->set('name', $name_encoded);
             $epalStudent->set('studentsurname', $studentsurname_encoded);
             $epalStudent->set('birthdate', $applicationForm[0]['studentbirthdate']);
             $epalStudent->set('fatherfirstname', $fatherfirstname_encoded);
             $epalStudent->set('motherfirstname', $motherfirstname_encoded);
             $epalStudent->set('regionaddress', $regionaddress_encoded);
             $epalStudent->set('regionarea', $regionarea_encoded);
             $epalStudent->set('regiontk', $regiontk_encoded);
             $epalStudent->set('lastschool_registrynumber', $applicationForm[0]['lastschool_registrynumber']);
             $epalStudent->set('lastschool_unittypeid', $applicationForm[0]['lastschool_unittypeid']);
             $epalStudent->set('lastschool_schoolname', $applicationForm[0]['lastschool_schoolname']);
             $epalStudent->set('lastschool_schoolyear', $applicationForm[0]['lastschool_schoolyear']);
             $epalStudent->set('lastschool_class', $applicationForm[0]['lastschool_class']);
             $epalStudent->set('lastschool_schoolyear', $applicationForm[0]['lastschool_schoolyear']);
             $epalStudent->set('currentclass', $applicationForm[0]['currentclass']);
             $epalStudent->set('guardian_name', $guardian_name_encoded);
             $epalStudent->set('guardian_surname', $guardian_surname_encoded);
             $epalStudent->set('guardian_fathername', $guardian_fathername_encoded);
             $epalStudent->set('guardian_mothername', $guardian_mothername_encoded);
             //$epalStudent->set('relationtostudent', $relationtostudent_encoded);
             $epalStudent->set('relationtostudent', $relationtostudent);
             $epalStudent->set('telnum', $telnum_encoded);

             $epalStudent->save();
           }
           $entity_storage_student->resetCache();

           //διαγραφή αντίστοιχων εγγραφών από epal_student_epal_chosen
           $entity_storage_epal_chosen = $this->entityTypeManager->getStorage('epal_student_epal_chosen');
           $idsEpals  = $entity_storage_epal_chosen->getQuery()
             ->condition('student_id', $studentId, '=')
             ->execute();
           $epals = $entity_storage_epal_chosen->loadMultiple($idsEpals);
           $entity_storage_epal_chosen->delete($epals);
           $entity_storage_epal_chosen->resetCache();

           //διαγραφή αντίστοιχων (πιθανών) εγγραφών από epal_student_sector_field
           $entity_storage_sector_field = $this->entityTypeManager->getStorage('epal_student_sector_field');
           $idsSectors  = $entity_storage_sector_field->getQuery()
             ->condition('student_id', $studentId, '=')
             ->execute();
           $sectors = $entity_storage_sector_field->loadMultiple($idsSectors);
           $entity_storage_sector_field->delete($sectors);
           $entity_storage_sector_field->resetCache();

           //διαγραφή αντίστοιχων (πιθανών) εγγραφών από epal_student_course_field
           $entity_storage_course_field = $this->entityTypeManager->getStorage('epal_student_course_field');
           $idsCourses  = $entity_storage_course_field->getQuery()
             ->condition('student_id', $studentId, '=')
             ->execute();
           $courses = $entity_storage_course_field->loadMultiple($idsCourses);
           $entity_storage_course_field->delete($courses);
           $entity_storage_course_field->resetCache();

           //εισαγωγή νέων εγγραφών στα epal_student_epal_chosen, epal_student_sector_field, epal_student_course_field
           for ($i = 0; $i < sizeof($applicationForm[1]); $i++) {
               $epalchosen = array(
                   'student_id' => $studentId,
                   'epal_id' => $applicationForm[1][$i]['epal_id'],
                   'choice_no' => $applicationForm[1][$i]['choice_no']
               );
               $entity_storage_epalchosen = $this->entityTypeManager->getStorage('epal_student_epal_chosen');
               $entity_object = $entity_storage_epalchosen->create($epalchosen);
               $entity_storage_epalchosen->save($entity_object);
           }

           if ($applicationForm[0]['currentclass'] === "3" || $applicationForm[0]['currentclass'] === "4") {
               $course = array(
                   'student_id' => $studentId,
                   'coursefield_id' => $applicationForm[3]['coursefield_id']
               );
               $entity_storage_course = $this->entityTypeManager->getStorage('epal_student_course_field');
               $entity_object = $entity_storage_course->create($course);
               $entity_storage_course->save($entity_object);
           } elseif ($applicationForm[0]['currentclass'] === "2") {
               $sector = array(
                   'student_id' => $studentId,
                   'sectorfield_id' => $applicationForm[3]['sectorfield_id']
               );
               $entity_storage_sector = $this->entityTypeManager->getStorage('epal_student_sector_field');
               $entity_object = $entity_storage_sector->create($sector);
               $entity_storage_sector->save($entity_object);
           }

           //END NEW!




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
    */




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


     /*
    private function validateStudent($student, $numberOfSchools, $chosenClass, $chosenSector, $chosenCourse, $epalUser = null, $appUpdate)
    {
        $error_code = 0;
        if (!$student["hasright"] && $appUpdate == false) {
            return 997;
        }
        if (($chosenClass === "3" || $chosenClass === "4") && !isset($chosenCourse)) {
            return 998;
        }
        if ($chosenClass === "2" && !isset($chosenSector)) {
            return 999;
        }
        if ($numberOfSchools < 1) {
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
        if (preg_match(self::VALID_ADDRESS_PATTERN, $student["regionaddress"]) !== 1) {
            return 1008;
        }
        if (preg_match(self::VALID_ADDRESSTK_PATTERN, $student["regiontk"]) !== 1) {
            return 1009;
        }
        if (preg_match(self::VALID_NAMES_PATTERN, $student["regionarea"]) !== 1) {
            return 1010;
        }
        if (!$student["currentclass"] || ($student["currentclass"] !== "1" && $student["currentclass"] !== "2" && $student["currentclass"] !== "3" && $student["currentclass"] !== "4")) {
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
        if (!$student["lastschool_class"]) {
            return 1023;
        }

        // second period: check if application exists
        if ($student['second_period'] == 1 && $epalUser !== null && $appUpdate == false) {

            $esQuery = $this->connection->select('epal_student', 'es')
                                    ->fields('es',
                                    array('name',
                                            'studentsurname',
                                            'birthdate',
                                        ));
            $esQuery->condition('es.epaluser_id', $epalUser->id(), '=');

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
        }

        return $error_code;
    }
    */







}
