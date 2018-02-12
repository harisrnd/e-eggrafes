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
                    $config_storage = $this->entityTypeManager->getStorage('epal_config');
                    $epalConfigs = $config_storage->loadByProperties(array('name' => 'epal_config'));
                    $epalConfig = reset($epalConfigs);
                    if (!$epalConfig) {
                       return $this->respondWithStatus([
                               'message' => t("EpalConfig Enity not found"),
                           ], Response::HTTP_FORBIDDEN);
                    }
                    else {
                       $applicantsAppDelDisabled = $epalConfig->lock_delete->value;
                    }

                    // $gelStudentClasses = $this->entityTypeManager->getStorage('gel_student_class')->loadByProperties(array('student_id' => $object->id()));
                    // $gelStudentClass = reset($gelStudentClasses);
                    // if (!$gelStudentClass && !$applicantsAppDelDisabled) {
                    //     $canDelete = 1;
                    // }
                    // else {
                    //     $canDelete = 0;
                    // }

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

            $config_storage = $this->entityTypeManager->getStorage('epal_config');
            $epalConfigs = $config_storage->loadByProperties(array('name' => 'epal_config'));
            $epalConfig = reset($epalConfigs);
            if (!$epalConfig) {
               return $this->respondWithStatus([
                       'message' => t("EpalConfig Enity not found"),
                   ], Response::HTTP_FORBIDDEN);
            }
            else {
               $applicantsResultsDisabled = $epalConfig->lock_results->value;
               $applicantsAppModifyDisabled = $epalConfig->lock_modify->value;
            }

            $status = "-1";
            $schoolName = '';
            $schoolAddress = '';
            $schoolTel = '';

            $esQuery = $this->connection->select('gel_student', 'gs')
                                    ->fields('gs',
                                    array('name',
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
                        'choice_name' => $gelstu->gel_ch_name,
                        'choice_type' => $gelstu->choicetype,
                        'order_id'=> $gelstu->order_id,
                      ));
                }

                $gelStudent = reset($gelStudents);
                $list = array();

                    $crypt = new Crypt();
                    try {
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
