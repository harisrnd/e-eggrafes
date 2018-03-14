<?php

namespace Drupal\epal\Controller;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Database\Connection;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\epal\Crypt;

class SubmitedApplications extends ControllerBase
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
        $this->logger = $loggerChannel->get('epal');
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('entity_type.manager'),
            $container->get('database'),
            $container->get('logger.factory')
        );
    }

    public function deleteApplication_HARDDELETE(Request $request)
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
            else if ($eggrafesConfig->lock_application->value) {
                return $this->respondWithStatus([
                        "error_code" => 3002
                    ], Response::HTTP_FORBIDDEN);
            }
            $epalUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
            $epalUser = reset($epalUsers);
            if ($epalUser) {
                $userid = $epalUser->id();

                $epalStudents = $this->entityTypeManager->getStorage('epal_student')->loadByProperties(array('epaluser_id' => $userid, 'id' => $applicationId));
                $epalStudent = reset($epalStudents);

                if ($epalStudent) {
                    if (!$epalStudent->second_period->value && $eggrafesConfig->activate_second_period->value) {
                        return $this->respondWithStatus([
                                "error_code" => 3002
                            ], Response::HTTP_FORBIDDEN);
                    }
                    $epalStudentClasses = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('student_id' => $applicationId));
                    $epalStudentClass = reset($epalStudentClasses);
                    if ($epalStudentClass) {
                        return $this->respondWithStatus([
                                "error_code" => 3002
                            ], Response::HTTP_FORBIDDEN);
                    }

                    $delQuery = $this->connection->delete('epal_student_epal_chosen');
                    $delQuery->condition('student_id', $applicationId);
                    $delQuery->execute();
                    $delQuery = $this->connection->delete('epal_student_sector_field');
                    $delQuery->condition('student_id', $applicationId);
                    $delQuery->execute();
                    $delQuery = $this->connection->delete('epal_student_course_field');
                    $delQuery->condition('student_id', $applicationId);
                    $delQuery->execute();
                    $delQuery = $this->connection->delete('epal_student_class');
                    $delQuery->condition('student_id', $applicationId);
                    $delQuery->execute();
                    $epalStudent->delete();
                    return $this->respondWithStatus([
                      'error_code' => 0,
                  ], Response::HTTP_OK);

                } else {
                    return $this->respondWithStatus([
                    'message' => t('EPAL student not found'),
                ], Response::HTTP_FORBIDDEN);
                }
            } else {
                return $this->respondWithStatus([
                'message' => t('EPAL user not found'),
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

    //λογική διαγραφή αίτησης μαθητή
    public function deleteApplication(Request $request)
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
            /*
            //μη διαγραφή αν δεν είναι ενεργή η περίοδος υποβολής αιτήσεων
            else if ($eggrafesConfig->lock_application->value) {
                return $this->respondWithStatus([
                        "error_code" => 3002
                    ], Response::HTTP_FORBIDDEN);
            }
            */
            $applicantUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
            $applicantUser = reset($applicantUsers);
            if ($applicantUser) {
                $userid = $applicantUser->id();
                //if ($schtype === "ΕΠΑΛ")
                $applicantStudents = $this->entityTypeManager->getStorage('epal_student')->loadByProperties(array('epaluser_id' => $userid, 'id' => $applicationId));
                //else if ($schtype === "ΓΕΛ")
                //  $applicantStudents = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(array('gel_userid' => $userid, 'id' => $applicationId));
                /*
                else {
                  return $this->respondWithStatus([
                  'message' => t('error in school type name'),
                  ], Response::HTTP_FORBIDDEN);
                }
                */

                $applicantStudent = reset($applicantStudents);

                if ($applicantStudent) {
                    /*
                    //άρνηση διαγραφής αν είμαστε στη δεύτερη περίοδο και η αίτηση είναι α' περιόδου
                    if (!$epalStudent->second_period->value && $eggrafesConfig->activate_second_period->value) {
                        return $this->respondWithStatus([
                                "error_code" => 3002
                            ], Response::HTTP_FORBIDDEN);
                    }
                    */
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

    public function getSubmittedApplications(Request $request)
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $epalUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
        $epalUser = reset($epalUsers);
        if ($epalUser) {
            $userid = $epalUser->id();

            $epalStudents = $this->entityTypeManager->getStorage('epal_student')->loadByProperties(array('epaluser_id' => $userid, 'delapp' => 0));
            $i = 0;
            $list = array();
            if ($epalStudents) {
                $crypt = new Crypt();


                foreach ($epalStudents as $object) {
                    $canDelete = 0;

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

                    $epalStudentClasses = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('student_id' => $object->id()));
                    $epalStudentClass = reset($epalStudentClasses);
                    if (!$epalStudentClass && !$applicantsAppDelDisabled) {
                        $canDelete = 1;
                    }
                    else {
                        $canDelete = 0;
                    }
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

    public function getStudentApplications(Request $request, $studentId)
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $epalUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
        $epalUser = reset($epalUsers);
        if ($epalUser) {
            $userid = $epalUser->id();
            $studentIdNew = intval($studentId);
            $epalStudents = $this->entityTypeManager->getStorage('epal_student')->loadByProperties(array('epaluser_id' => $userid, 'id' => $studentIdNew, 'delapp' => 0));
            $i = 0;

            if ($epalStudents) {
                $list = array();

                foreach ($epalStudents as $object) {
                    $sectorName = '';
                    $courseName = '';
                    if ($object->currentclass->value === '2') {
                        $sectors = $this->entityTypeManager->getStorage('epal_student_sector_field')->loadByProperties(array('student_id' => $object->id()));
                        $sector = reset($sectors);
                        if ($sector) {
                            $sectorName = $this->entityTypeManager->getStorage('eepal_sectors')->load($sector->sectorfield_id->target_id)->name->value;
                        }
                    } elseif ($object->currentclass->value === '3' || $object->currentclass->value === '4') {
                        $courses = $this->entityTypeManager->getStorage('epal_student_course_field')->loadByProperties(array('student_id' => $object->id()));
                        $course = reset($courses);
                        if ($course) {
                            $courseName = $this->entityTypeManager->getStorage('eepal_specialty')->load($course->coursefield_id->target_id)->name->value;
                        }
                    }

                    $crypt = new Crypt();
                    try {
                        $name_decoded = $crypt->decrypt($object->name->value);
                        $studentsurname_decoded = $crypt->decrypt($object->studentsurname->value);
                        $fatherfirstname_decoded = $crypt->decrypt($object->fatherfirstname->value);
                        $motherfirstname_decoded = $crypt->decrypt($object->motherfirstname->value);
                        $regionaddress_decoded = $crypt->decrypt($object->regionaddress->value);
                        $regiontk_decoded = $crypt->decrypt($object->regiontk->value);
                        $regionarea_decoded = $crypt->decrypt($object->regionarea->value);
                        //$relationtostudent_decoded = $crypt->decrypt($object->relationtostudent->value);
                        $telnum_decoded = $crypt->decrypt($object->telnum->value);
                        $guardian_name_decoded = $crypt->decrypt($object->guardian_name->value);
                        $guardian_surname_decoded = $crypt->decrypt($object->guardian_surname->value);
                        $guardian_fathername_decoded = $crypt->decrypt($object->guardian_fathername->value);
                        $guardian_mothername_decoded = $crypt->decrypt($object->guardian_mothername->value);
                    } catch (\Exception $e) {
                        //print_r($e->getMessage());
                        unset($crypt);
                        $this->logger->warning($e->getMessage());

                        return $this->respondWithStatus([
                            'message' => t('An unexpected error occured during DECODING data in getStudentApplications Method '),
                                   ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                    unset($crypt);

                    $list[] = array(
                            'applicationId' => $object->id(),
                            'name' => $name_decoded,
                            'studentsurname' => $studentsurname_decoded,
                            'fatherfirstname' => $fatherfirstname_decoded,
                            'motherfirstname' => $motherfirstname_decoded,
                            'guardian_name' => $guardian_name_decoded,
                            'guardian_surname' => $guardian_surname_decoded,
                            'guardian_fathername' => $guardian_fathername_decoded,
                            'guardian_mothername' => $guardian_mothername_decoded,
                            'lastschool_schoolname' => $object->lastschool_schoolname->value,
                            'lastschool_schoolyear' => $object->lastschool_schoolyear->value,
                            'lastschool_class' => $object->lastschool_class->value,
                            'currentclass' => $object->currentclass->value,
                            'currentsector' => $sectorName,
                            'currentcourse' => $courseName,
                            'regionaddress' => $regionaddress_decoded,
                            'regiontk' => $regiontk_decoded,
                            'regionarea' => $regionarea_decoded,
                            'telnum' => $telnum_decoded,
                            'relationtostudent' => $object->relationtostudent->value,
                            'birthdate' => substr($object->birthdate->value, 8, 2).'/'.substr($object->birthdate->value, 5, 2).'/'.substr($object->birthdate->value, 0, 4),
                            'created' => date('d/m/Y H:i', $object->created->value),
                        );

                    ++$i;
                }

                return $this->respondWithStatus(
                        $list, Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'message' => t('EPAL user not found'),
                ], Response::HTTP_FORBIDDEN);
            }
        } else {
            return $this->respondWithStatus([
                    'message' => t('User not found!!!!'),
                ], Response::HTTP_FORBIDDEN);
        }
    }


    public function getApplicationDetails(Request $request, $studentId)
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $epalUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
        $epalUser = reset($epalUsers);
        if ($epalUser) {

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
               //$secondPeriodEnabled = $eggrafesConfig->activate_second_period->value;
               $dateStartInt = strtotime($eggrafesConfig->date_start_b_period->value);
            }

            $status = "-1";
            $schoolName = '';
            $schoolAddress = '';
            $schoolTel = '';

            /*
            $studInDistr = false;
            $resQuery = $this->connection->select('epal_student_class', 'res')
                                    ->fields('res', array('student_id'));
            $resQuery->condition('res.student_id', intval($studentId), '=');
            $epalStudentClass = $resQuery->execute()->fetchAll(\PDO::FETCH_OBJ);
            if ($epalStudentClass && sizeof($epalStudentClass) > 0)
              $studInDistr = true;
            */

            $esQuery = $this->connection->select('epal_student', 'es')
                                    ->fields('es',
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
                                            'currentclass',
                                            'birthdate',
                                            'created',
                                            'changed',
                                            'second_period',
                                        ))
                                    ->fields('esec',
                                    array('choice_no'
                                    ))
                                    ->fields('eesch',
                                    array('id',
                                          'name'
                                    ))
                                    ->fields('eese',
                                    array('id',
                                          'name'
                                    ))
                                    ->fields('eesp',
                                    array('id',
                                          'name',
                                    ))
                                    ->fields('esc',
                                    array('finalized',
                                    ))
                                    ->fields('eeschfin',
                                    array('id',
                                            'name',
                                            'street_address',
                                            'phone_number'
                                    ));

            $esQuery->addJoin('left outer', 'epal_student_epal_chosen', 'esec', 'es.id=esec.student_id');
            $esQuery->addJoin('left outer', 'eepal_school_field_data', 'eesch', 'esec.epal_id=eesch.id');
            $esQuery->addJoin('left outer', 'epal_student_sector_field', 'essf', 'es.id=essf.student_id');
            $esQuery->addJoin('left outer', 'eepal_sectors_field_data', 'eese', 'essf.sectorfield_id=eese.id');
            $esQuery->addJoin('left outer', 'epal_student_course_field', 'escf', 'es.id=escf.student_id');
            $esQuery->addJoin('left outer', 'eepal_specialty_field_data', 'eesp', 'escf.coursefield_id=eesp.id');
            $esQuery->addJoin('left outer', 'epal_student_class', 'esc', 'es.id=esc.student_id');
            $esQuery->addJoin('left outer', 'eepal_school_field_data', 'eeschfin', 'esc.epal_id=eeschfin.id');
            $esQuery->condition('es.id', intval($studentId), '=');
            $esQuery->condition('es.epaluser_id', $epalUser->id(), '=');
            $esQuery->orderBy('esec.choice_no');

            $epalStudents = $esQuery->execute()->fetchAll(\PDO::FETCH_OBJ);

            if ($epalStudents && sizeof($epalStudents) > 0) {
                $epalSchoolsChosen = array();
                foreach ($epalStudents as $es) {
                    array_push($epalSchoolsChosen, array(
                        'id' => $es->eesch_id,
                        'epal_id' => $es->eesch_name,
                        'choice_no' => $es->choice_no,
                      ));
                }

                $epalStudent = reset($epalStudents);
                $list = array();

                    $crypt = new Crypt();
                    try {
                        if (isset($epalStudent->myschool_id ))
                          $am_decoded = $crypt->decrypt($epalStudent->am);
                        $name_decoded = $crypt->decrypt($epalStudent->name);
                        $studentsurname_decoded = $crypt->decrypt($epalStudent->studentsurname);
                        $fatherfirstname_decoded = $crypt->decrypt($epalStudent->fatherfirstname);
                        $motherfirstname_decoded = $crypt->decrypt($epalStudent->motherfirstname);
                        $regionaddress_decoded = $crypt->decrypt($epalStudent->regionaddress);
                        $regiontk_decoded = $crypt->decrypt($epalStudent->regiontk);
                        $regionarea_decoded = $crypt->decrypt($epalStudent->regionarea);
                        $telnum_decoded = $crypt->decrypt($epalStudent->telnum);
                        $guardian_name_decoded = $crypt->decrypt($epalStudent->guardian_name);
                        $guardian_surname_decoded = $crypt->decrypt($epalStudent->guardian_surname);
                        $guardian_fathername_decoded = $crypt->decrypt($epalStudent->guardian_fathername);
                        $guardian_mothername_decoded = $crypt->decrypt($epalStudent->guardian_mothername);
                    } catch (\Exception $e) {
                        unset($crypt);
                        $this->logger->warning($e->getMessage());

                        return $this->respondWithStatus([
                            'message' => t('An unexpected error occured during DECODING data in getStudentApplications Method '),
                                   ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                    unset($crypt);

                    //if ($epalStudent->finalized === null)  {
                    //    $status = "0";
                    //}

                    if ($applicantsResultsDisabled === "0") {
                      //To $epalStudent->finalized === null υποωοεί ότι δεν είναι κατανεμημένος αυτός ο μαθητής <-- ΝΑ ΕΛΕΓΧΘΕΙ
                      if ($epalStudent->finalized === "1")
                          $status = "1";
                      else if ($epalStudent->finalized === "0")
                          $status = "2";
                      else if (   ($epalStudent->second_period === "0" && /*$studInDistr === false*/ $epalStudent->finalized === null)   ||
                                  ($epalStudent->second_period === "1" && $epalStudent->changed < $dateStartInt)  )
                          $status = "3";
                      else if ($epalStudent->second_period === "1" &&
                               $epalStudent->changed >= $dateStartInt)
                          $status = "4";
                    }
                    else
                        $status = "0";

                    $list[] = array(
                            'applicationId' => $epalStudent->id,
                            'am' => $am_decoded,
                            'name' => $name_decoded,
                            'studentsurname' => $studentsurname_decoded,
                            'fatherfirstname' => $fatherfirstname_decoded,
                            'motherfirstname' => $motherfirstname_decoded,
                            'guardian_name' => $guardian_name_decoded,
                            'guardian_surname' => $guardian_surname_decoded,
                            'guardian_fathername' => $guardian_fathername_decoded,
                            'guardian_mothername' => $guardian_mothername_decoded,
                            'lastschool_schoolname' => $epalStudent->lastschool_schoolname,
                            'lastschool_registrynumber' => $epalStudent->lastschool_registrynumber,
                            'lastschool_unittypeid' => $epalStudent->lastschool_unittypeid,
                            'lastschool_schoolyear' => $epalStudent->lastschool_schoolyear,
                            'lastschool_class' => $epalStudent->lastschool_class,
                            'currentclass' => $epalStudent->currentclass,
                            'currentsector' => $epalStudent->eese_name,
                            'currentsector_id' => $epalStudent->eese_id,
                            'currentcourse' => $epalStudent->eesp_name,
                            'currentcourse_id' => $epalStudent->eesp_id,
                            'regionaddress' => $regionaddress_decoded,
                            'regiontk' => $regiontk_decoded,
                            'regionarea' => $regionarea_decoded,
                            'telnum' => $telnum_decoded,
                            'relationtostudent' => $epalStudent->relationtostudent,
                            'birthdate' => substr($epalStudent->birthdate, 8, 2).'/'.substr($epalStudent->birthdate, 5, 2).'/'.substr($epalStudent->birthdate, 0, 4),
                            'changed' => date('d/m/Y H:i', $epalStudent->changed),
                            'epalSchoolsChosen' => $epalSchoolsChosen,
                            'applicantsResultsDisabled' => $applicantsResultsDisabled,
                            'applicantsAppModifyDisabled' => $applicantsAppModifyDisabled,
                            'status' => $status,
                            'schoolName' => $epalStudent->eeschfin_name,
                            'schoolAddress' => $epalStudent->street_address,
                            'schoolTel' => $epalStudent->phone_number,
                            'secondPeriod' => $epalStudent->second_period,
                        );

                return $this->respondWithStatus(
                        $list, Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'message' => t('EPAL user not found'),
                ], Response::HTTP_FORBIDDEN);
            }
        } else {
            return $this->respondWithStatus([
                    'message' => t('User not found!!!!'),
                ], Response::HTTP_FORBIDDEN);
        }
    }

    public function getEpalChosen(Request $request, $studentId)
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $epalUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
        $epalUser = reset($epalUsers);
        if ($epalUser) {
            $userid = $epalUser->user_id->entity->id();
            $studentIdNew = intval($studentId);
            $ecQuery = $this->connection->select('epal_student_epal_chosen', 'ec')
                                                                    ->fields('ec', array('choice_no'))
                                                                    ->fields('es', array('name'));
            $ecQuery->addJoin('inner', 'eepal_school_field_data', 'es', 'ec.epal_id=es.id');
            $ecQuery->condition('ec.user_id', $userid, '=');
            $ecQuery->condition('ec.student_id', $studentIdNew, '=');
            $ecQuery->orderBy('ec.choice_no');
            $epalChosen = $ecQuery->execute()->fetchAll(\PDO::FETCH_OBJ);
            $i = 0;

            if ($epalChosen && sizeof($epalChosen) > 0) {
                $list = array();
                foreach ($epalChosen as $object) {
                    $list[] = array(
                            'epal_id' => $object->name,
                            'choice_no' => $object->choice_no,
                        );
                    ++$i;
                }

                return $this->respondWithStatus(
                        $list, Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'message' => t('EPAL chosen not found!!!'),
                ], Response::HTTP_FORBIDDEN);
            }
        } else {
            return $this->respondWithStatus([
                    'message' => t('User not found'),
                ], Response::HTTP_FORBIDDEN);
        }
    }

    public function getCritiria(Request $request, $studentId, $type)
    {
        //obsolete
        //epal_student_moria / epal_crieria refer to entities that no longer exist
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $epalUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
        $epalUser = reset($epalUsers);
        if ($epalUser) {
            $userid = $epalUser->user_id->entity->id();
            $studentIdNew = intval($studentId);
            $critiriaChosen = $this->entityTypeManager->getStorage('epal_student_moria')->loadByProperties(array('user_id' => $userid, 'student_id' => $studentIdNew));
            $i = 0;

            if ($critiriaChosen) {
                $list = array();
                foreach ($critiriaChosen as $object) {
                    $critirio_id = $object->criterio_id->entity->id();
                    $critiriatype = $this->entityTypeManager->getStorage('epal_criteria')->loadByProperties(array('id' => $critirio_id));
                    $typeofcritiria = reset($critiriatype);
                    $typecrit = $typeofcritiria->category->value;
                    if ($typecrit == 'Κοινωνικό' && $type == 1) {
                        $list[] = array(
                            'critirio' => $object->criterio_id->entity->get('name')->value,
                            'critirio_id' => $critirio_id,
                            );

                        ++$i;
                    }
                    if ($typecrit == 'Εισοδηματικό' && $type == 2) {
                        $list[] = array(
                            'critirio' => $object->criterio_id->entity->get('name')->value,
                            'critirio_id' => $critirio_id,
                            );

                        ++$i;
                    }
                }

                return $this->respondWithStatus(
                        $list, Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'message' => t('EPAL chosen not found!!!'),
                ], Response::HTTP_FORBIDDEN);
            }
        } else {
            return $this->respondWithStatus([
                    'message' => t('User not found'),
                ], Response::HTTP_FORBIDDEN);
        }
    }


    //δεν τη χρησιμοποιούμε πια!
    public function getResults(Request $request, $studentId) {

      //έλεγχος πρόσβασης
      $authToken = $request->headers->get('PHP_AUTH_USER');
      $epalUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
      $epalUser = reset($epalUsers);
      if ($epalUser) {
          $userid = $epalUser->id();
          $studentIdNew = intval($studentId);
          $epalStudents = $this->entityTypeManager->getStorage('epal_student')->loadByProperties(array('epaluser_id' => $userid, 'id' => $studentIdNew));
          $i = 0;
          if ($epalStudents) {
              $list = array();

              //ανάκτηση τιμής από ρυθμίσεις διαχειριστή για lock_results
              $config_storage = $this->entityTypeManager->getStorage('eggrafes_config');
              $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config'));
              $eggrafesConfig = reset($eggrafesConfigs);
              if (!$eggrafesConfig) {
                 return $this->respondWithStatus([
                         'message' => t("eggrafesConfig Enity not found"),
                     ], Response::HTTP_FORBIDDEN);
              }
              else {
                 $applicantsResultsDisabled = $eggrafesConfig->lock_results->getString();
                 //$applicantsAppModifyDisabled = $eggrafesConfig->lock_modify->getString();
                 //$secondPeriodEnabled = $eggrafesConfig->activate_second_period->getString();
              }

              $status = "0";
              // $status : 0 --> δεν υπάρχει στον πίνακα αποτελεσμάτων (είτε δεν έγινε ακόμα κατανομή, είτε έγινε κατανομή, αλλά..δεν κατανεμήθηκε! <-- ΠΡΟΣΟΧΗ!!)
              // $status : 1 --> κατανεμήθηκε και είναι "οριστικοποιημένος" (finalized = 1)
              // $status : 2 --> κατανεμήθηκε και είναι "μη οριστικοποιημένος" (finalized = 0), δηλαδή κατανεμήθηκε σε ολιγομελές τμήμα
              $schoolName = '';
              $schoolAddress = '';
              $schoolTel = '';
              $secondPeriod = "0";

              //ανάκτηση αποτελέσματος
              // εύρεση τοποθέτησης (περίπτωση μαθητή που τοποθετήθηκε "οριστικά")

              if ($applicantsResultsDisabled === "0")   {

                        $escQuery = $this->connection->select('epal_student_class', 'esc')
                                                ->fields('esc', array('student_id', 'epal_id', 'finalized'))
                                                ->fields('esch', array('id', 'name', 'street_address','phone_number'));
                        $escQuery->addJoin('inner', 'eepal_school_field_data', 'esch', 'esc.epal_id=esch.id');
                        $escQuery->condition('esc.student_id', intval($studentId), '=');
                        //$escQuery->condition('esc.second_period', intval($secondPeriodEnabled), '=');

                        $epalStudentClasses = $escQuery->execute()->fetchAll(\PDO::FETCH_OBJ);
                if  (sizeof($epalStudentClasses) === 1) {
                  $epalStudentClass = reset($epalStudentClasses);

                    if ($epalStudentClass->finalized === "1")  {
                      $status = "1";
                      $schoolName = $epalStudentClass->name;
                      $schoolAddress = $epalStudentClass->street_address;
                      $schoolTel = $epalStudentClass->phone_number;
                      //$secondPeriod = $epalStudentClass->second_period;
                      //$secondPeriodEnabled = $secondPeriodEnabled;
                    }
                    else  {
                        $status = "2";
                        //$secondPeriod = $epalStudentClass->second_period;
                        //$secondPeriodEnabled = $secondPeriodEnabled;
                    }
                }
                else  {
                    $status = "0";
                    //$secondPeriod = $epalStudentClass->second_period;
                    //$secondPeriodEnabled = $secondPeriodEnabled;
                }

            } //endif $applicantsResultsDisabled === "0"

            $list[] = array(
                      'applicantsResultsDisabled' => $applicantsResultsDisabled,
                      //'applicantsAppModifyDisabled' => $applicantsAppModifyDisabled,
                      'status' => $status,
                      'schoolName' => $schoolName,
                      'schoolAddress' => $schoolAddress,
                      'schoolTel' => $schoolTel,
                      //'secondPeriod' => $secondPeriod,
                      //'secondPeriodSettingEnabled' => $secondPeriodEnabled,
              );

              return $this->respondWithStatus(
                      $list, Response::HTTP_OK);
          }

          else {
              return $this->respondWithStatus([
                  'message' => t('EPAL user not found'),
              ], Response::HTTP_FORBIDDEN);
          }
        }

    }


    public function deleteApplicationFromDirector_HARDDELETE(Request $request)
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
                $epalId = $user->init->value;
                $schools = $this->entityTypeManager->getStorage('eepal_school')->loadByProperties(array('id' => $epalId));
                $school = reset($schools);
                if (!$school) {
                    $this->logger->warning('no access to this school='.$user->id());
                    return $this->respondWithStatus([
                        "message" => "No access to this school"
                    ], Response::HTTP_FORBIDDEN);
                }

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
            //ανάκτηση τιμής από ρυθμίσεις διαχειριστή για lock_school_students_view, lock_application

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




                //$userid = $epalUser->id();

                $epalStudents = $this->entityTypeManager->getStorage('epal_student')->loadByProperties(array( 'id' => $applicationId));
                $epalStudent = reset($epalStudents);

                if ($epalStudent) {
                    $epalStudentClasses = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('student_id' => $applicationId));
                    $epalStudentClass = reset($epalStudentClasses);

                    if ($epalStudentClass)  {
                      if ($epalStudentClass->directorconfirm->value === "1")  {
                        return $this->respondWithStatus([
                                "error_code" => -1
                            ], Response::HTTP_FORBIDDEN);
                      }
                    }

                    $delQuery = $this->connection->delete('epal_student_epal_chosen');
                    $delQuery->condition('student_id', $applicationId);
                    $delQuery->execute();
                    $delQuery = $this->connection->delete('epal_student_sector_field');
                    $delQuery->condition('student_id', $applicationId);
                    $delQuery->execute();
                    $delQuery = $this->connection->delete('epal_student_course_field');
                    $delQuery->condition('student_id', $applicationId);
                    $delQuery->execute();
                    $delQuery = $this->connection->delete('epal_student_class');
                    $delQuery->condition('student_id', $applicationId);
                    $delQuery->execute();
                    $epalStudent->delete();
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


//λογική διαγραφή αίτησης μαθητή από Διευθυντή
public function deleteApplicationFromDirector(Request $request)
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
            $epalId = $user->init->value;
            //$epalCode = $user->cu_name->value;
            $schools = $this->entityTypeManager->getStorage('eepal_school')->loadByProperties(array('id' => $epalId));
            $school = reset($schools);
            if (!$school) {
                $this->logger->warning('no access to this school='.$user->id());
                return $this->respondWithStatus([
                    "message" => "No access to this school"
                ], Response::HTTP_FORBIDDEN);
            }

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


            $epalStudents = $this->entityTypeManager->getStorage('epal_student')->loadByProperties(array( 'id' => $applicationId));
            $epalStudent = reset($epalStudents);

            if ($epalStudent) {
                $epalStudentClasses = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('student_id' => $applicationId));
                $epalStudentClass = reset($epalStudentClasses);

                if ($epalStudentClass)  {
                  if ($epalStudentClass->directorconfirm->value === "1")  {
                    return $this->respondWithStatus([
                            "error_code" => -1
                        ], Response::HTTP_FORBIDDEN);
                  }
                }

                $epalStudent->set('delapp', 1);
                $timestamp = strtotime(date("Y-m-d"));
                $epalStudent->set('delapp_changed', $timestamp);
                $epalStudent->set('delapp_role', 'director');
                $epalStudent->set('delapp_epalid', $epalId);
                $epalStudent->save();

                $delQuery = $this->connection->delete('epal_student_class');
                $delQuery->condition('student_id', $applicationId);
                $delQuery->execute();

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



private function respondWithStatus($arr, $s)
    {
        $res = new JsonResponse($arr);
        $res->setStatusCode($s);

        return $res;
    }

}
