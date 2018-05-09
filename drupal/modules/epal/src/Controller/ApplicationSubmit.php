<?php

namespace Drupal\epal\Controller;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;

use Drupal\Core\Database\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

use Drupal\epal\Crypt;

//use Drupal\epal\ClientConsumer;

class ApplicationSubmit extends ControllerBase
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

        //eggrafes configuration validation
        $eggrafesConfigs = $this->entityTypeManager->getStorage('eggrafes_config')->loadByProperties(array('name' => 'eggrafes_config_epal'));
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

        $second_period = $eggrafesConfig->activate_second_period->value;

        //έλεγχος πληρότητας τμήματος
        if ($second_period === "1") {
          $classIdChecked = $applicationForm[0]['currentclass'];
          $secIdChecked = "-1";
          if ($classIdChecked === "2")
            $secIdChecked = $applicationForm[2]['sectorfield_id'];
          else if ($classIdChecked === "3" || $classIdChecked === "4")
            $secIdChecked =  $applicationForm[2]['coursefield_id'];
          for ($i = 0; $i < sizeof($applicationForm[1]); $i++) {
              $epalIdChecked = $applicationForm[1][$i]['epal_id'];
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

      }
        //τέλος ελέγχου πληρότητας

        $crypt = new Crypt();
        try {

            $name_encoded = $crypt->encrypt($applicationForm[0]['name']);
            $studentsurname_encoded = $crypt->encrypt($applicationForm[0]['studentsurname']);
            $fatherfirstname_encoded = $crypt->encrypt($applicationForm[0]['fatherfirstname']);
            $motherfirstname_encoded = $crypt->encrypt($applicationForm[0]['motherfirstname']);
            $regionaddress_encoded = $crypt->encrypt($applicationForm[0]['regionaddress']);
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
                "error_code" => 500147654956,
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

            //$second_period = $eggrafesConfig->activate_second_period->value;

            $student = array(
                'langcode' => 'el',
                'user_id' => $epalUser->user_id->target_id,
                'epaluser_id' => $epalUser->id(),
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
                'currentclass' => $applicationForm[0]['currentclass'],
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
                    ]), sizeof($applicationForm[1]), $applicationForm[0]['currentclass'],
                    $applicationForm[2]['sectorfield_id'],
                    $applicationForm[2]['coursefield_id'],
                    $epalUser, $eggrafesConfig->ws_ident->value, false)) > 0) {
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

            $entity_storage_student = $this->entityTypeManager->getStorage('epal_student');
            $entity_object = $entity_storage_student->create($student);
            $entity_storage_student->save($entity_object);

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
                    'coursefield_id' => $applicationForm[2]['coursefield_id']
                );
                $entity_storage_course = $this->entityTypeManager->getStorage('epal_student_course_field');
                $entity_object = $entity_storage_course->create($course);
                $entity_storage_course->save($entity_object);
            } elseif ($applicationForm[0]['currentclass'] === "2") {
                $sector = array(
                    'student_id' => $created_student_id,
                    'sectorfield_id' => $applicationForm[2]['sectorfield_id']
                );
                $entity_storage_sector = $this->entityTypeManager->getStorage('epal_student_sector_field');
                $entity_object = $entity_storage_sector->create($sector);
                $entity_storage_sector->save($entity_object);
            }
            return $this->respondWithStatus([
                "error_code" => 0
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            $transaction->rollback();

            return $this->respondWithStatus([
                "error_code" => 5001,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




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

      $eggrafesConfigs = $this->entityTypeManager->getStorage('eggrafes_config')->loadByProperties(array('name' => 'eggrafes_config_epal'));
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

      //έλεγχος πληρότητας τμήματος
      if ( $eggrafesConfig->lock_small_classes->value === "1")
      {
        $classIdChecked = $applicationForm[0]['currentclass'];
        $secIdChecked = "-1";

        if ($classIdChecked === "2")
          $secIdChecked = $applicationForm[2]['sectorfield_id'];
        else if ($classIdChecked === "3" || $classIdChecked === "4")
          $secIdChecked =  $applicationForm[2]['coursefield_id'];

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

      //έλεγχος μη εγκεκριμένων τμημάτων - γίνεται στην τροποποίηση αίτησης και όταν είναι ενεργή η μη προβολή μη εγκεκριμένων τμημάτων
      if ($eggrafesConfig->lock_small_classes) {
        for ($i = 0; $i < sizeof($applicationForm[1]); $i++) {
            if ($applicationForm[0]['currentclass'] === "1")
              $epalSchools = $this->entityTypeManager->getStorage('eepal_school')->loadByProperties(
                array('id' => $applicationForm[1][$i]['epal_id'], 'approved_a' => 1));
            else if ($applicationForm[0]['currentclass'] === "2")
              $epalSchools = $this->entityTypeManager->getStorage('eepal_sectors_in_epal')->loadByProperties(
                array('epal_id' => $applicationForm[1][$i]['epal_id'] ,'sector_id' => $applicationForm[2]['sectorfield_id'], 'approved_sector' => 1));
            else if ($applicationForm[0]['currentclass'] === "3")
              $epalSchools = $this->entityTypeManager->getStorage('eepal_specialties_in_epal')->loadByProperties(
                array('epal_id' => $applicationForm[1][$i]['epal_id'] ,'specialty_id' => $applicationForm[2]['coursefield_id'], 'approved_speciality' => 1));
            else if ($applicationForm[0]['currentclass'] === "4")
              $epalSchools = $this->entityTypeManager->getStorage('eepal_specialties_in_epal')->loadByProperties(
                array('epal_id' => $applicationForm[1][$i]['epal_id'] ,'specialty_id' => $applicationForm[2]['coursefield_id'], 'approved_speciality_d' => 1));

            $epalSchool = reset($epalSchools);
            if (!$epalSchool) {
                $schoolName = $this->retrieveSchoolName($applicationForm[1][$i]['epal_id']);
                $err_code = 9003;
                return $this->respondWithStatus([
                      "error_code" => $err_code,
                      "school_name" => $schoolName
                  ], Response::HTTP_OK);
            }
          }
      }
      //end


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
              "error_code" => 5001,
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

          //$second_period = $eggrafesConfig->activate_second_period->value;
          $student = array(
              'langcode' => 'el',
              'user_id' => $epalUser->user_id->target_id,
              'epaluser_id' => $epalUser->id(),
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
              'currentclass' => $applicationForm[0]['currentclass'],
              'guardian_name' => $guardian_name_encoded,
              'guardian_surname' => $guardian_surname_encoded,
              'guardian_fathername' => $guardian_fathername_encoded,
              'guardian_mothername' => $guardian_mothername_encoded,
              'agreement' => $applicationForm[0]['disclaimer_checked'],
              'relationtostudent' => $applicationForm[0]['relationtostudent'],
              'telnum' => $telnum_encoded,
              'second_period' => $eggrafesConfig->activate_second_period->value,
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
                  ]), sizeof($applicationForm[1]), $applicationForm[0]['currentclass'],
                  $applicationForm[2]['sectorfield_id'],
                  $applicationForm[2]['coursefield_id'],
                  $epalUser, $eggrafesConfig->ws_ident->value, true)) > 0) {
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
             $epalStudent->set('relationtostudent', $applicationForm[0]['relationtostudent']);
             $epalStudent->set('telnum', $telnum_encoded);

             $epalStudent->set('am', $am_encoded);
             $epalStudent->set('myschool_id', $applicationForm[0]['studentId']);
             //if ($applicationForm[0]['section_name'] != null)
             $epalStudent->set('myschool_currentsection', $applicationForm[0]['section_name']);
             //if ($applicationForm[0]['level_name'] != null)
             $epalStudent->set('myschool_currentlevelname', $applicationForm[0]['level_name']);
             //if ($applicationForm[0]['unittype_name'] != null)
              $epalStudent->set('myschool_currentunittype', $applicationForm[0]['unittype_name']);

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
                   'coursefield_id' => $applicationForm[2]['coursefield_id']
               );
               $entity_storage_course = $this->entityTypeManager->getStorage('epal_student_course_field');
               $entity_object = $entity_storage_course->create($course);
               $entity_storage_course->save($entity_object);
           } elseif ($applicationForm[0]['currentclass'] === "2") {
               $sector = array(
                   'student_id' => $studentId,
                   'sectorfield_id' => $applicationForm[2]['sectorfield_id']
               );
               $entity_storage_sector = $this->entityTypeManager->getStorage('epal_student_sector_field');
               $entity_object = $entity_storage_sector->create($sector);
               $entity_storage_sector->save($entity_object);
           }




           /*
          $entity_storage_student = $this->entityTypeManager->getStorage('epal_student');
          $entity_object = $entity_storage_student->create($student);
          $entity_storage_student->save($entity_object);

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
                  'coursefield_id' => $applicationForm[2]['coursefield_id']
              );
              $entity_storage_course = $this->entityTypeManager->getStorage('epal_student_course_field');
              $entity_object = $entity_storage_course->create($course);
              $entity_storage_course->save($entity_object);
          } elseif ($applicationForm[0]['currentclass'] === "2") {
              $sector = array(
                  'student_id' => $created_student_id,
                  'sectorfield_id' => $applicationForm[2]['sectorfield_id']
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
              "error_code" => 5001,
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
    private function validateStudent($student, $numberOfSchools, $chosenClass, $chosenSector, $chosenCourse, $epalUser = null, $wsEnabled, $appUpdate)
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
        if ( ( ($wsEnabled === 1 && $student["lastschool_schoolyear"] < self::LIMIT_SCHOOL_YEAR) || ($wsEnabled === 0) )
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
        /*
        if ( ( ($wsEnabled === 1 && $student["lastschool_schoolyear"] < self::LIMIT_SCHOOL_YEAR) || ($wsEnabled === 0) ) &&
          $student["am"] && !$student["lastschool_class"]) {
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



        // check if application exists in either gel_student or epal_student entity
        if (/*$student['second_period'] == 1 &&*/ $epalUser !== null && $appUpdate == false) {
            $retCode = $this->existApp("epal_student", "epaluser_id", $epalUser, $student );
            if ($retCode === -1) {
              $retCode = $this->existApp("gel_student", "gel_userid", $epalUser, $student);
            }
            if ($retCode !== -1)
              return $retCode;
        }



        // second period: check if application exists
        //old version
        /*
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
        */



        // check as per specs:
        // - can't check certification prior to 2014, pass through
        // - check certification if last passed class is gym
        // - check promotion if last passed class is not gym

/*        $check_certification = true;
        $check_promotion = true;
        if (intval($student['lastschool_unittypeid']) == self::UNIT_TYPE_GYM) {
            $check_promotion = false;
            $check_certification = true;
        }
        if (intval($student['graduation_year']) < 2014 &&
            intval($student['certificatetype']) == self::CERT_GYM) {
            $check_certification = false;
        }

        // now check service
        $pass = true;
        $error_code = 0;
        if (($check_certification === true) ||
            ($check_promotion === true)) {
            if ($check_promotion === true) {
                $service = 'getStudentEpalPromotion';
            } else {
                $service = 'getStudentEpalCertification';
            }
            try {
                $didactic_year_id = $this->client->getDidacticYear(substr($student["lastschool_schoolyear"], -4, 4));
                $level_name = $this->client->getLevelName($student['lastschool_class']);
                $service_rv = $this->client->$service(
                    $didactic_year_id,
                    $student['studentsurname'],
                    $student['name'],
                    $student['fatherfirstname'],
                    $student['motherfirstname'],
                    $birthdate,
                    $student['lastschool_registrynumber'],
                    $level_name
                );
                $pass = ($service_rv == 'true');
                if ($service_rv == 'true') {
                    $error_code = 0;
                } elseif ($service_rv == 'false') {
                    $error_code = 8002;
                } elseif ($service_rv == 'null') {
                    $error_code = 8003;
                } else {
                    // -1 is an exception and data is already validated
                    $error_code = 8001;
                }
            } catch (\Exception $e) {
                $pass = false;
                $error_code = 8000;
            }
        } */

        return $error_code;
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


    //functionality related to occupancy

    public function isFull($epalId, $classId, $secId)
    {
        $schoolCapacity = $this->retrieveSchoolCapacity($epalId, $classId, $secId);
        if ($schoolCapacity === self::ERROR_DB)
            return self::ERROR_DB;

        $classLimitup = $this->retrieveCapacityLimitUp("1");
        if ($classLimitup === self::ERROR_DB)
            return self::ERROR_DB;

        $numStudentsLimit = $schoolCapacity * $classLimitup;
        $numStudentsFinalized = $this->countStudents($epalId, $classId, $secId);

        if ($numStudentsFinalized === self::ERROR_DB) {
            return self::ERROR_DB;
        }
        if ($numStudentsFinalized >= $numStudentsLimit ) {
            return self::FULL_CLASS;
        } else {
            return self::NON_FULL_CLASS;
        }

    }



    public function retrieveSchoolCapacity($schoolId, $classId, $sectorOrcourseId)
    {
        try {

            if ($classId === "1") {
              $clCon = $this->connection->select('eepal_school_field_data', 'classCapacity')
                  ->fields('classCapacity', array('capacity_class_a'))
                  ->condition('classCapacity.id', $schoolId, '=');
              $results = $clCon->execute()->fetchAll(\PDO::FETCH_OBJ);
              $row = reset($results);
              $capac = $row->capacity_class_a;
            }
            else if ($classId === "2") {
              $clCon = $this->connection->select('eepal_sectors_in_epal_field_data', 'classCapacity')
                  ->fields('classCapacity', array('capacity_class_sector'))
                  ->condition('classCapacity.epal_id', $schoolId, '=')
                  ->condition('classCapacity.sector_id', $sectorOrcourseId, '=');
              $results = $clCon->execute()->fetchAll(\PDO::FETCH_OBJ);
              $row = reset($results);
              $capac = $row->capacity_class_sector;
            }
            else if ($classId === "3") {
              $clCon = $this->connection->select('eepal_specialties_in_epal_field_data', 'classCapacity')
                  ->fields('classCapacity', array('capacity_class_specialty'))
                  ->condition('classCapacity.epal_id', $schoolId, '=')
                  ->condition('classCapacity.specialty_id', $sectorOrcourseId, '=');
              $results = $clCon->execute()->fetchAll(\PDO::FETCH_OBJ);
              $row = reset($results);
              $capac = $row->capacity_class_specialty;
            }
            else if ($classId === "4") {
              $clCon = $this->connection->select('eepal_specialties_in_epal_field_data', 'classCapacity')
                  ->fields('classCapacity', array('capacity_class_specialty_d'))
                  ->condition('classCapacity.epal_id', $schoolId, '=')
                  ->condition('classCapacity.specialty_id', $sectorOrcourseId, '=');
              $results = $clCon->execute()->fetchAll(\PDO::FETCH_OBJ);
              $row = reset($results);
              $capac = $row->capacity_class_specialty_d;
            }

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return self::ERROR_DB;
        }

        return $capac;
    }


    public function retrieveCapacityLimitUp($className)
    {
        try {
            $clCon = $this->connection->select('epal_class_limits', 'classLimits')
                ->fields('classLimits', array('limit_up'))
                ->condition('classLimits.name', $className, '=');
            $results = $clCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            $row = reset($results);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return self::ERROR_DB;
        }

        return $row->limit_up;
    }


    private function countStudents($schoolId, $classId, $sectorOrcourseId)
    {
        try {
            $sCon = $this->connection->select('epal_student_class', 'eStudent')
                ->fields('eStudent', array('id'))
                ->condition('eStudent.epal_id', $schoolId, '=')
                ->condition('eStudent.currentclass', $classId, '=')
                ->condition('eStudent.specialization_id', $sectorOrcourseId, '=')
                ->condition('eStudent.directorconfirm', "1", '=');
            //if ($classId !== "1")
            //  $sCon->condition('eStudent.specialization_id', $sectorOrcourseId, '=');
            return $sCon->countQuery()->execute()->fetchField();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return self::ERROR_DB;
        }
    }

    private function retrieveSchoolName($schoolId)
    {
      try {
          $sCon = $this->connection->select('eepal_school_field_data', 'eSchool')
              ->fields('eSchool', array('id', 'name'))
              ->condition('eSchool.id', $schoolId, '=');
          $results = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
          $row = reset($results);
          return $row->name;
          //return $sCon->countQuery()->execute()->fetchField();
      } catch (\Exception $e) {
          $this->logger->error($e->getMessage());
          return self::ERROR_DB;
      }

    }




}
