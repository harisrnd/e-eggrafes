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

class GelSubmittedApplications extends ControllerBase
{
    protected $entityTypeManager;
    protected $logger;
    protected $connection;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        Connection $connection,
        LoggerChannelFactoryInterface $loggerChannel)
    {
        $this->entityTypeManager = $entityTypeManager;
        $this->connection = $connection;
        $this->logger = $loggerChannel->get('gel');
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('entity_type.manager'),
            $container->get('database'),
            $container->get('logger.factory')
        );
    }

    //λογική διαγραφή αίτησης μαθητή
    public function gelDeleteApplication(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return $this->respondWithStatus([
                    "error_code" => 2001
                ], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $content = $request->getContent();

        $applicationId = 0;
        if (!empty($content)) {
            $postArr = json_decode($content, TRUE);
            $applicationId = $postArr['applicationId'];
        }
        else {
            return $this->respondWithStatus([
                    "error_code" => 5002
                ], Response::HTTP_BAD_REQUEST);
        }

        $authToken = $request->headers->get('PHP_AUTH_USER');
        $transaction = $this->connection->startTransaction();
        try {
            //ανάκτηση τιμής από ρυθμίσεις διαχειριστή για lock_results
            $config_storage = $this->entityTypeManager->getStorage('eggrafes_config');
            $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config'));
            $eggrafesConfig = reset($eggrafesConfigs);
            if (!$eggrafesConfig) {
               return $this->respondWithStatus([
                       'message' => t("eggrafesConfig Enity not found"),
                   ], Response::HTTP_FORBIDDEN);
            }

            $applicantUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
            $applicantUser = reset($applicantUsers);
            if ($applicantUser) {
                $userid = $applicantUser->id();
                //$applicantStudents = $this->entityTypeManager->getStorage('epal_student')->loadByProperties(array('epaluser_id' => $userid, 'id' => $applicationId));
                $applicantStudents = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(array('gel_userid' => $userid, 'id' => $applicationId));
                $applicantStudent = reset($applicantStudents);

                if ($applicantStudent) {
                    $applicantStudent->set('delapp', 1);
                    $timestamp = strtotime(date("Y-m-d"));
                    $applicantStudent->set('delapp_changed', $timestamp);
                    $applicantStudent->set('delapp_role', 'student');
                    $applicantStudent->set('delapp_studentid',   $userid);
                    $applicantStudent->save();

                    return $this->respondWithStatus([
                      'error_code' => 0,
                  ], Response::HTTP_OK);

                } else {
                    return $this->respondWithStatus([
                    'message' => t('applicant student not found'),
                ], Response::HTTP_FORBIDDEN);
                }
            } else {
                return $this->respondWithStatus([
                'message' => t('applicant user not found'),
                ], Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            $transaction->rollback();

            return $this->respondWithStatus([
                'error_code' => 5001,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    //λογική διαγραφή αίτησης μαθητή από Διευθυντή
    public function gelDeleteApplicationFromDirector(Request $request)
    {
      if (!$request->isMethod('POST')) {
          return $this->respondWithStatus([
                  "error_code" => 2001
              ], Response::HTTP_METHOD_NOT_ALLOWED);
      }
      $authToken = $request->headers->get('PHP_AUTH_USER');

      $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
      $user = reset($users);
      if ($user) {
          /*
          //TO BE DONE: να ανακτείται ο κωδικός σχολείου. Προϋποθέτει: entity με ΓΕΛ σχολεία!!
          $gelId = $user->init->value;

          $schools = $this->entityTypeManager->getStorage('gel_school')->loadByProperties(array('id' => $gelId));
          $school = reset($schools);
          if (!$school) {
              $this->logger->warning('no access to this school='.$user->id());
              return $this->respondWithStatus([
                  "message" => "No access to this school"
              ], Response::HTTP_FORBIDDEN);
          }
          */

          $userRoles = $user->getRoles();
          $userRole = '';
          foreach ($userRoles as $tmpRole) {
              if ($tmpRole === 'epal') {
                  $userRole = $tmpRole;
              }
          }
          if ($userRole === '') {
              return $this->respondWithStatus([
                       'error_code' => 4003,
                   ], Response::HTTP_FORBIDDEN);
          } elseif ($userRole === 'epal') {

          $content = $request->getContent();

          $applicationId = 0;
          if (!empty($content)) {
              $postArr = json_decode($content, TRUE);
              $applicationId = $postArr['applicationId'];
          }
          else {
              return $this->respondWithStatus([
                      "error_code" => 5002
                  ], Response::HTTP_BAD_REQUEST);
          }


      $transaction = $this->connection->startTransaction();
      try {
          //ανάκτηση τιμής από ρυθμίσεις διαχειριστή για lock_delete
          $config_storage = $this->entityTypeManager->getStorage('eggrafes_config');
          $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config'));
          $eggrafesConfig = reset($eggrafesConfigs);
          if (!$eggrafesConfig) {
             return $this->respondWithStatus([
                     'message' => t("eggrafesConfig Enity not found"),
                 ], Response::HTTP_FORBIDDEN);
          }
          /*
          else if ($eggrafesConfig->lock_school_students_view->value) {
              return $this->respondWithStatus([
                      "error_code" => 3002
                  ], Response::HTTP_FORBIDDEN);
          }
          //μη διαγραφή αν δεν είναι ενεργή η περίοδος υποβολής αιτήσεων
          else if ($eggrafesConfig->lock_application->value) {
              return $this->respondWithStatus([
                      "error_code" => 3002
                  ], Response::HTTP_FORBIDDEN);
          }
          */
          else if ($eggrafesConfig->lock_delete->value) {
              return $this->respondWithStatus([
                      "error_code" => 3002
                  ], Response::HTTP_FORBIDDEN);
          }


          $applicantStudents = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(array( 'id' => $applicationId));
          $applicantStudent = reset($applicantStudents);

          if ($applicantStudent) {
              /*
              //ΔΙΑΓΡΑΦΗ ΑΠΟ ΠΙΝΑΚΑ ΑΠΟΤΕΛΕΣΜΑΤΩΝ -> ΘΑ ΥΠΑΡΧΕΙ ΣΤΟ ΓΕΛ??
              $epalStudentClasses = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('student_id' => $applicationId));
              $epalStudentClass = reset($epalStudentClasses);

              if ($epalStudentClass)  {
                if ($epalStudentClass->directorconfirm->value === "1")  {
                  return $this->respondWithStatus([
                          "error_code" => -1
                      ], Response::HTTP_FORBIDDEN);
                }
              }
              */

              $applicantStudent->set('delapp', 1);
              $timestamp = strtotime(date("Y-m-d"));
              $applicantStudent->set('delapp_changed', $timestamp);
              $applicantStudent->set('delapp_role', 'director');
              //ΝΑ ΕΚΤΕΛΕΣΤΕΙ ΟΤΑΝ ΦΤΙΑΧΤΕΙ ΤΟ gel_sschools
              //$epalStudent->set('delapp_epalid', $gelId);
              $epalStudent->save();

              /*
              //ΔΙΑΓΡΑΦΗ ΑΠΟ ΠΙΝΑΚΑ ΑΠΟΤΕΛΕΣΜΑΤΩΝ -> ΘΑ ΥΠΑΡΧΕΙ ΣΤΟ ΓΕΛ??
              $delQuery = $this->connection->delete('epal_student_class');
              $delQuery->condition('student_id', $applicationId);
              $delQuery->execute();
              */

              return $this->respondWithStatus([
                'error_code' => 0,
            ], Response::HTTP_OK);

          } else {
              return $this->respondWithStatus([
              'message' => t('EPAL student not found'),
          ], Response::HTTP_FORBIDDEN);
          }
      } catch (\Exception $e) {
          $this->logger->warning($e->getMessage());
          $transaction->rollback();

          return $this->respondWithStatus([
              'error_code' => 5001,
          ], Response::HTTP_INTERNAL_SERVER_ERROR);
          }
      }
  }

}


    public function getGelSubmittedApplications(Request $request)
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $gelUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
        $gelUser = reset($gelUsers);
        if ($gelUser) {
            $userid = $gelUser->id();

            $gelStudents = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(array('gel_userid' => $userid, 'delapp' => 0));
            $i = 0;
            $list = array();
            if ($gelStudents) {
                $crypt = new Crypt();

                foreach ($gelStudents as $object) {
                    $canDelete = 1;

                    //ανάκτηση τιμής από ρυθμίσεις διαχειριστή για lock_delete
                    $config_storage = $this->entityTypeManager->getStorage('eggrafes_config');
                    $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config'));
                    $eggrafesConfig = reset($eggrafesConfigs);
                    if (!$eggrafesConfig) {
                       return $this->respondWithStatus([
                               'message' => t("eggrafesConfig Enity not found"),
                           ], Response::HTTP_FORBIDDEN);
                    }
                    else {
                       $applicantsAppDelDisabled = $eggrafesConfig->lock_delete->value;
                    }

                    // $gelStudentClasses = $this->entityTypeManager->getStorage('gel_student_class')->loadByProperties(array('student_id' => $object->id()));
                    // $gelStudentClass = reset($gelStudentClasses);
                    // if (!$gelStudentClass && !$applicantsAppDelDisabled) {
                    //     $canDelete = 1;
                    // }
                    // else {
                    //     $canDelete = 0;
                    // }
                    if (!$applicantsAppDelDisabled)
                         $canDelete = 1;
                     else
                         $canDelete = 0;

                    try {
                        $name_decoded = $crypt->decrypt($object->name->value);
                        $studentsurname_decoded = $crypt->decrypt($object->studentsurname->value);
                    } catch (\Exception $e) {
                        unset($crypt);
                        $this->logger->warning($e->getMessage());

                        return $this->respondWithStatus([
                          'message' => t('An unexpected error occured during DECODING data in getSubmittedApplications Method '),
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }

                    $list[] = array(
                            'id' => $object->id(),
                            //'name' => $object -> name ->value,
                            'name' => $name_decoded,
                            //'studentsurname' => $object -> studentsurname ->value);
                            'studentsurname' => $studentsurname_decoded,
                            'candelete' => $canDelete, );
                    ++$i;
                }

                unset($crypt);

                return $this->respondWithStatus(
                        $list, Response::HTTP_OK);
            } else {
                return $this->respondWithStatus(
                        $list, Response::HTTP_OK);
            }
        } else {
            return $this->respondWithStatus([
                    'message' => t('User not found'),
                ], Response::HTTP_FORBIDDEN);
        }
    }




    public function getGelApplicationDetails(Request $request, $studentId)
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $gelUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
        $gelUser = reset($gelUsers);
        if ($gelUser) {

            $config_storage = $this->entityTypeManager->getStorage('eggrafes_config');
            $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config'));
            $eggrafesConfig = reset($eggrafesConfigs);
            if (!$eggrafesConfig) {
               return $this->respondWithStatus([
                       'message' => t("eggrafesConfig Enity not found"),
                   ], Response::HTTP_FORBIDDEN);
            }
            else {
               $applicantsResultsDisabled = $eggrafesConfig->lock_results->value;
               $applicantsAppModifyDisabled = $eggrafesConfig->lock_modify->value;
            }

            $status = "-1";
            $schoolName = '';
            $schoolAddress = '';
            $schoolTel = '';

            $esQuery = $this->connection->select('gel_student', 'gs')
                                    ->fields('gs',
                                    array(  'myschool_id',
                                            'am',
                                            'name',
                                            'studentsurname',
                                            'fatherfirstname',
                                            'motherfirstname',
                                            'regionaddress',
                                            'regiontk',
                                            'regionarea',
                                            'relationtostudent',
                                            'telnum',
                                            'guardian_name',
                                            'guardian_surname',
                                            'guardian_fathername',
                                            'guardian_mothername',
                                            'id',
                                            'lastschool_schoolname',
                                            'lastschool_registrynumber',
                                            'lastschool_unittypeid',
                                            'lastschool_schoolyear',
                                            'lastschool_class',
                                            'nextclass',
                                            'birthdate',
                                            'created',
                                            'changed',
                                        ))
                                        ->fields('gs_ch',
                                        array('choice_id',
                                              'order_id'
                                        ))
                                        ->fields('gel_ch',
                                        array('name',
                                              'choicetype'
                                        ));
            $esQuery->addJoin('left outer', 'gel_student_choices', 'gs_ch', 'gs.id=gs_ch.student_id');
            $esQuery->addJoin('left outer', 'gel_choices', 'gel_ch', 'gs_ch.choice_id=gel_ch.id');
            $esQuery->condition('gs.id', intval($studentId), '=');
            $esQuery->condition('gs.gel_userid', $gelUser->id(), '=');
            $esQuery->orderBy('gs_ch.order_id');

            $gelStudents = $esQuery->execute()->fetchAll(\PDO::FETCH_OBJ);

            if ($gelStudents && sizeof($gelStudents) > 0) {

                $gelStudentChoices = array();

                foreach ($gelStudents as $gelstu) {
                    array_push($gelStudentChoices, array(
                        'choice_id' => $gelstu->choice_id,
                        'choice_name' => $gelstu->gel_ch_name,
                        'choice_type' => $gelstu->choicetype,
                        'order_id'=> $gelstu->order_id,
                      ));
                }

                $gelStudent = reset($gelStudents);
                $list = array();

                    $crypt = new Crypt();
                    try {
                        if (isset($gelStudent->myschool_id ))
                            $am_decoded = $crypt->decrypt($gelStudent->am);
                        $name_decoded = $crypt->decrypt($gelStudent->name);
                        $studentsurname_decoded = $crypt->decrypt($gelStudent->studentsurname);
                        $fatherfirstname_decoded = $crypt->decrypt($gelStudent->fatherfirstname);
                        $motherfirstname_decoded = $crypt->decrypt($gelStudent->motherfirstname);
                        $regionaddress_decoded = $crypt->decrypt($gelStudent->regionaddress);
                        $regiontk_decoded = $crypt->decrypt($gelStudent->regiontk);
                        $regionarea_decoded = $crypt->decrypt($gelStudent->regionarea);
                        $telnum_decoded = $crypt->decrypt($gelStudent->telnum);
                        $guardian_name_decoded = $crypt->decrypt($gelStudent->guardian_name);
                        $guardian_surname_decoded = $crypt->decrypt($gelStudent->guardian_surname);
                        $guardian_fathername_decoded = $crypt->decrypt($gelStudent->guardian_fathername);
                        $guardian_mothername_decoded = $crypt->decrypt($gelStudent->guardian_mothername);
                    } catch (\Exception $e) {
                        unset($crypt);
                        $this->logger->warning($e->getMessage());

                        return $this->respondWithStatus([
                            'message' => t('An unexpected error occured during DECODING data in getStudentApplications Method '),
                                   ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                    unset($crypt);


                    $status = "0";

                    $list[] = array(
                            'applicationId' => $gelStudent->id,
                            'am' => $am_decoded,
                            'name' => $name_decoded,
                            'studentsurname' => $studentsurname_decoded,
                            'fatherfirstname' => $fatherfirstname_decoded,
                            'motherfirstname' => $motherfirstname_decoded,
                            'guardian_name' => $guardian_name_decoded,
                            'guardian_surname' => $guardian_surname_decoded,
                            'guardian_fathername' => $guardian_fathername_decoded,
                            'guardian_mothername' => $guardian_mothername_decoded,
                            'lastschool_schoolname' => $gelStudent->lastschool_schoolname,
                            'lastschool_registrynumber' => $gelStudent->lastschool_registrynumber,
                            'lastschool_unittypeid' => $gelStudent->lastschool_unittypeid,
                            'lastschool_schoolyear' => $gelStudent->lastschool_schoolyear,
                            'lastschool_class' => $gelStudent->lastschool_class,
                            'nextclass' => $gelStudent->nextclass,
                            'regionaddress' => $regionaddress_decoded,
                            'regiontk' => $regiontk_decoded,
                            'regionarea' => $regionarea_decoded,
                            'telnum' => $telnum_decoded,
                            'relationtostudent' => $gelStudent->relationtostudent,
                            'birthdate' => substr($gelStudent->birthdate, 8, 2).'/'.substr($gelStudent->birthdate, 5, 2).'/'.substr($gelStudent->birthdate, 0, 4),
                            'changed' => date('d/m/Y H:i', $gelStudent->changed),
                            'gelStudentChoices' => $gelStudentChoices,
                        );

                return $this->respondWithStatus(
                        $list, Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'message' => t('GEL user not found'),
                ], Response::HTTP_FORBIDDEN);
            }
        } else {
            return $this->respondWithStatus([
                    'message' => t('User not found!!!!'),
                ], Response::HTTP_FORBIDDEN);
        }
    }




private function respondWithStatus($arr, $s)
    {
        $res = new JsonResponse($arr);
        $res->setStatusCode($s);

        return $res;
    }

}
