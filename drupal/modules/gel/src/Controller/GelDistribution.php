<?php

namespace Drupal\gel\Controller;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Database\Database;
use Drupal\Core\Database\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

//use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\TypedData\Plugin\DataType\TimeStamp;

use Drupal\Core\Language\LanguageManagerInterface;

use Drupal\gel\Crypt;

class GelDistribution extends ControllerBase
{
    protected $entity_query;
    protected $entityTypeManager;
    protected $logger;
    protected $connection;
    protected $language;
    protected $currentuser;
    protected $globalCounterId = 1;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        QueryFactory $entity_query,
        Connection $connection,
        LoggerChannelFactoryInterface $loggerChannel
    ) {
        $this->entityTypeManager = $entityTypeManager;
            $this->entity_query = $entity_query;
            $connection = Database::getConnection();
            $this->connection = $connection;
        $this->logger = $loggerChannel->get('gel');
    		$this->language =  \Drupal::languageManager()->getCurrentLanguage()->getId();
        $this->currentuser = \Drupal::currentUser()->id();

    }

   public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('entity_type.manager'),
            $container->get('entity.query'),
            $container->get('database'),
            $container->get('logger.factory')
        );
    }

    public function getJuniorHighSchoolperDide(Request $request)
    {

        $authToken = $request->headers->get('PHP_AUTH_USER');

        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if ($user) {
            $selectionId = $user->init->value;
            $userRoles = $user->getRoles();
            $userRole = '';
            foreach ($userRoles as $tmpRole) {
                if (($tmpRole === 'regioneduadmin') || ($tmpRole === 'eduadmin')) {
                    $userRole = $tmpRole;
                }
            }

            if ($userRole === '') {
                return $this->respondWithStatus([
                    'error_code' => 4003,
                ], Response::HTTP_FORBIDDEN);
            } elseif ($userRole === 'regioneduadmin') {
                $sCon = $this->connection->select('gel_school', 'eSchool')
                              ->fields('eSchool', array('id', 'name', 'unit_type_id','edu_admin_id'))
                              ->condition('eSchool.edu_admin_id', $selectionId , '=')
                              ->condition('eSchool.unit_type_id', 3 , '=');
                 $sCon -> orderBy('eSchool.name', 'ASC');
                 $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            } elseif ($userRole === 'eduadmin') {

                 $sCon = $this->connection->select('gel_school', 'eSchool')
                              ->fields('eSchool', array('id', 'name', 'unit_type_id','edu_admin_id'))
                              ->condition('eSchool.edu_admin_id', $selectionId , '=')
                              ->condition('eSchool.unit_type_id', 3 , '=');
                 $sCon -> orderBy('eSchool.name', 'ASC');
                 $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

            }



            else {
                $schools = [];
            }

            if ($schools) {
                $list = array();

                foreach ($schools as $object) {
                    $status = 1;
                    $list[] = array(
                        'id' => $object ->id,
                        'name' => $object->name,
                        'status' => $status,
                    );
                }

                return $this->respondWithStatus($list, Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'message' => t('No schools found!'),
                ], Response::HTTP_FORBIDDEN);
            }
        } else {
            return $this->respondWithStatus([
                'message' => t('User not found!'),
            ], Response::HTTP_FORBIDDEN);
        }

    }




public function getHighSchoolperDide(Request $request)
    {

         $authToken = $request->headers->get('PHP_AUTH_USER');

        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if ($user) {
            $selectionId = $user->init->value;
            $userRoles = $user->getRoles();
            $userRole = '';
            foreach ($userRoles as $tmpRole) {
                if (($tmpRole === 'regioneduadmin') || ($tmpRole === 'eduadmin')) {
                    $userRole = $tmpRole;
                }
            }

            if ($userRole === '') {
                return $this->respondWithStatus([
                    'error_code' => 4003,
                    "message" => t("1")
                ], Response::HTTP_FORBIDDEN);
            } elseif ($userRole === 'regioneduadmin') {
                 $sCon = $this->connection->select('gel_school', 'eSchool')
                              ->fields('eSchool', array('id', 'name', 'unit_type_id','edu_admin_id'))
                              ->condition('eSchool.edu_admin_id', $selectionId , '=')
                              ->condition('eSchool.unit_type_id', 4 , '=');
                 $sCon -> orderBy('eSchool.name', 'ASC');
                 $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            } elseif ($userRole === 'eduadmin') {
                $sCon = $this->connection->select('gel_school', 'eSchool')
                              ->fields('eSchool', array('id', 'name', 'unit_type_id','edu_admin_id'))
                              ->condition('eSchool.edu_admin_id', $selectionId , '=')
                              ->condition('eSchool.unit_type_id', 4 , '=');
                 $sCon -> orderBy('eSchool.name', 'ASC');
                 $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            } else {
                $schools = [];
            }

            if ($schools) {
                $list = array();

                foreach ($schools as $object) {
                    $status = 1;
                    $list[] = array(
                        'id' => $object->id,
                        'name' => $object->name,
                        'status' => $status,
                    );
                }

                return $this->respondWithStatus($list, Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'message' => t('No schools found!'),
                ], Response::HTTP_FORBIDDEN);
            }
        } else {
            return $this->respondWithStatus([
                'message' => t('User not found!'),
            ], Response::HTTP_FORBIDDEN);
        }

    }


    public function getHighSchoolperDideSmart(Request $request, $schsearch)
    {
            $authToken = $request->headers->get('PHP_AUTH_USER');

            $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
            $user = reset($users);
            if ($user) {
                $selectionId = $user->init->value;
                $userRoles = $user->getRoles();
                $userRole = '';
                foreach ($userRoles as $tmpRole) {
                    if (($tmpRole === 'regioneduadmin') || ($tmpRole === 'eduadmin')) {
                        $userRole = $tmpRole;
                    }
                }

                if ($userRole === '') {
                    return $this->respondWithStatus([
                        'error_code' => 4003,
                    ], Response::HTTP_FORBIDDEN);
                }

                else
                {
                  try {

                      if ($userRole === 'regioneduadmin') {
                          $sCon = $this->connection->select('gel_school', 'eSchool')
                              ->fields('eSchool', array('id', 'name', 'region_edu_admin_id'));
                          $sCon->condition('eSchool.region_edu_admin_id', $selectionId , '=');
                      }
                      elseif ($userRole === 'eduadmin') {
                          $sCon = $this->connection->select('gel_school', 'eSchool')
                              ->fields('eSchool', array('id', 'name', 'unit_type_id','edu_admin_id'))
                              ->condition('eSchool.edu_admin_id', $selectionId , '=')
                              ->condition('eSchool.unit_type_id', 4 , '=');
                      }
                      else {
                          $schools = [];
                          return $this->respondWithStatus([
                              'message' => t('No schools found!'),
                          ], Response::HTTP_FORBIDDEN);
                      }

                      if ($schsearch != "ΟΛΑ") {
                  			$words = preg_split('/[\s]+/', $schsearch);
                  			foreach ($words as $word)
                  					$sCon->condition('eSchool.name', '%' . db_like($word) . '%', 'LIKE');
                        }

                				$schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                				$list = array();
                				foreach ($schools as $object) {
                						$list[] = array(
                								'id' => $object->id,
                								'name' => $object->name,
                								'status' => 1,
                						);
                				}
                				return $this->respondWithStatus($list, Response::HTTP_OK);

                    }
                    catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
              					return $this->respondWithStatus([
              									'message' => t("error in getSchoolList function"),
              							], Response::HTTP_FORBIDDEN);
                    }

                }

            } else {
                return $this->respondWithStatus([
                    'message' => t('User not found!'),
                ], Response::HTTP_FORBIDDEN);
            }

      }


public function getStudentsPerSchool(Request $request, $schoolid)
    {

        try {


            $authToken = $request->headers->get('PHP_AUTH_USER');
            $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
            $user = reset($users);
            if ($user) {
                $schools = $this->entityTypeManager->getStorage('gel_school')->loadByProperties(array('id' => $schoolid));
                $school = reset($schools);
                if (!$school) {
                    $this->logger->warning('no access to this school='.$user->id());
                    return $this->respondWithStatus([
                        "message" => "No access to this school"
                    ], Response::HTTP_FORBIDDEN);
                }
                $regno = $school -> registry_no ->value;
                $userRoles = $user->getRoles();
                $userRole = '';
                foreach ($userRoles as $tmpRole) {
                    if ($tmpRole === 'eduadmin') {
                        $userRole = $tmpRole;
                    }
                }
                if ($userRole === '') {
                    return $this->respondWithStatus([
                             'error_code' => 4003,
                         ], Response::HTTP_FORBIDDEN);
                } elseif ($userRole === 'eduadmin') {

                    $studentPerSchool = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(array('lastschool_registrynumber' => $regno, 'lastschool_unittypeid' => 3, 'lastschool_class' => "3", 'delapp' => '0'));
                }
                if ($studentPerSchool) {
                    $list = array();
                    foreach ($studentPerSchool as $object) {
                            $crypt = new Crypt();
                            try {
                                $name_decoded = $object->name->value;
                                if ($object->am->value != "")
                                  $am_decoded = $crypt ->decrypt($object->am->value);
                                else
                                  $am_decoded ="Παλιός απόφοιτος";
                                $regionaddress_decoded = $crypt->decrypt($object->regionaddress->value);
                                $regiontk_decoded = $crypt->decrypt($object->regiontk->value);
                                $regionarea_decoded = $crypt->decrypt($object->regionarea->value);

                            } catch (\Exception $e) {
                                $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                                return $this->respondWithStatus([
                                "message" => t("An unexpected error occured during DECODING data in getStudentPerSchool Method ")
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }

                            $list[] = array(
                                'id' => $object->id(),
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'oldschool' => $this -> gethighschoolperstudent($object->id()),


                            );

                    }
                    return $this->respondWithStatus($list, Response::HTTP_OK);
                } else {
                    return $this->respondWithStatus([
                        'message' => t('Students not found!'),
                    ], Response::HTTP_NOT_FOUND);
                }
            } else {
                return $this->respondWithStatus([
                    'message' => t('User not found!'),
                ], Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                'message' => t('Unexpected Error'),
            ], Response::HTTP_FORBIDDEN);
        }
    }

 public function SaveHighSchoolSelection(Request $request, $studentid, $schoolid, $oldschool)
 {
     if (!$request->isMethod('GET')) {
            return $this->respondWithStatus([
                    'message' => t('Method Not Allowed'),
                ], Response::HTTP_METHOD_NOT_ALLOWED);
        }
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
         if ($role === "eduadmin") {
           $validRole = true;
           break;
         }
        if (!$validRole) {
           return $this->respondWithStatus([
                   'message' => t("User Invalid Role"),
               ], Response::HTTP_FORBIDDEN);
        }

        $chunks = explode(",", $studentid);
       // $chunks = preg_split(',', $studentid);

           $this->logger->warning($studentid."1");
        foreach ($chunks as $studId =>$value )
        {

        $transaction = $this->connection->startTransaction();
        try {


            $this->connection->delete('gelstudenthighschool')
                            ->condition('student_id', $value, '=')
                            ->execute();

            $student = array(
                'langcode' => 'el',
                'student_id' => $value,
                'school_id' => $schoolid,
                'taxi' => 'Α'

            );

            $entity_storage_student = $this->entityTypeManager->getStorage('gelstudenthighschool');
            $entity_object = $entity_storage_student->create($student);
            $entity_storage_student->save($entity_object);

        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            $transaction->rollback();

            return $this->respondWithStatus([
                "error_code" => 5001
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
     return $this->respondWithStatus('ok', Response::HTTP_OK);

    }


public function gethighschoolperstudent($id)
    {

/*                $schools = $this->entityTypeManager->getStorage('gelstudenthighschool')->loadByProperties(array('student_id' => $id));
                //$school = reset($schools);

                if ($schools) {
                    $list = array();
                    foreach ($schools as $sch)
                    {
                     $tagid = $sch-> school_id -> value;
                     $schname = $this->entityTypeManager->getStorage('gel_school')->load($tagid)->name->value;

                     return $schname;
                    }
                }
                  else
                 {
                  return null;
                 } */

         $sCon = $this->connection->select('gelstudenthighschool', 'eStudent')
                ->fields('eStudent', array('school_id'))
                ->condition('eStudent.student_id', $id, '=');
            $res1 =  intval($sCon->execute()->fetchField());


             $sCon1 = $this->connection->select('gel_school', 'gels')
                ->fields('gels', array('name'))
                ->condition('gels.id', $res1, '=');
             return $sCon1->execute()->fetchField();





    }


public function FindCoursesPerSchoolGel(Request $request)
    {
        $i = 0;
        $authToken = $request->headers->get('PHP_AUTH_USER');

        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if ($user) {
            $schoolid = $user->init->value;
            $schools = $this->entityTypeManager->getStorage('gel_school')->loadByProperties(array('id' => $schoolid));
            $school = reset($schools);
            if (!$school) {
                $this->logger->warning('no access to this school='.$user->id());
                $response = new Response();
                $response->setContent('No access to this school');
                $response->setStatusCode(Response::HTTP_FORBIDDEN);
                $response->headers->set('Content-Type', 'application/json');

                return $response;
            }
            $operation_shift = $school -> operation_shift -> value;
            $userRoles = $user->getRoles();
            $userRole = '';
            foreach ($userRoles as $tmpRole) {
                if ($tmpRole === 'gel') {
                    $userRole = $tmpRole;
                }
            }
            if ($userRole === '') {
                return $this->respondWithStatus([
                             'error_code' => 4003,
                         ], Response::HTTP_FORBIDDEN);
            } elseif ($userRole === 'gel') {
                $categ = $school->metathesis_region->value;
                $list = array();

                $Courses = $this->entityTypeManager->getStorage('gel_school')->loadByProperties(array('id' => $schoolid));
                if ($Courses) {
                   $studentPerSchool = $this->entityTypeManager->getStorage('gelstudenthighschool')->loadByProperties(array('school_id' => $schoolid , 'taxi' => 'Α'));

                    $list[] = array(
                        'class' => 1,
                        'taxi' => 'Ά Λυκείου',
                        'globalindex' => $i,
                        'size' => sizeof($studentPerSchool),
                       );
                     $studentPerSchool = $this->entityTypeManager->getStorage('gelstudenthighschool')->loadByProperties(array('school_id' => $schoolid, 'taxi' => 'Β' ));

                    $list[] = array(
                        'class' => 2,
                        'taxi' => 'Β Λυκείου',
                        'globalindex' => $i,
                        'size' => sizeof($studentPerSchool),
                       );
                     $studentPerSchool = $this->entityTypeManager->getStorage('gelstudenthighschool')->loadByProperties(array('school_id' => $schoolid , 'taxi' => 'Γ'));

                    $list[] = array(
                        'class' => 3,
                        'taxi' => 'Γ Λυκείου',
                        'globalindex' => $i,
                        'size' => sizeof($studentPerSchool),
                       );
                    if ($operation_shift != 'ΗΜΕΡΗΣΙΟ'){
                     $studentPerSchool = $this->entityTypeManager->getStorage('gelstudenthighschool')->loadByProperties(array('school_id' => $schoolid, 'taxi' => 'Δ' ));


                    $list[] = array(
                        'class' => 4,
                        'taxi' => 'Δ Λυκείου',
                        'globalindex' => $i,
                        'size' => sizeof($studentPerSchool),
                       );
                    }
                }
                ++$i;



                return $this->respondWithStatus($list, Response::HTTP_OK);
            }
        } else {
            return $this->respondWithStatus([
                    'message' => t('EPAL user not found'),
                ], Response::HTTP_FORBIDDEN);
        }
    }



public function getStudentPerSchoolGel(Request $request, $classId)
    {
        if ($classId == 1)
        {
            $classId = 'Α';
        }
        elseif ($classId == 2)
        {
            $classId = 'Β';
        }
        elseif ($classId == 3)
        {
            $classId = 'Γ';
        }
        else
        {
            $classId = 'Δ';

        }

        try {
            $authToken = $request->headers->get('PHP_AUTH_USER');

            $eggrafesConfigs = $this->entityTypeManager->getStorage('eggrafes_config')->loadByProperties(array('name' => 'eggrafes_config_gel'));
            $eggrafesConfig = reset($eggrafesConfigs);
            if (!$eggrafesConfig) {
                return $this->respondWithStatus([
                        "error_code" => 3001
                    ], Response::HTTP_FORBIDDEN);
            }

            else
              $lock_delete = $eggrafesConfig->lock_delete->value;



            $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
            $user = reset($users);
            if ($user) {
                $gelId = $user->init->value;
                $this->logger->warning($gelId."kvdikos sxoleiou".$classId);
                $schools = $this->entityTypeManager->getStorage('gel_school')->loadByProperties(array('id' => $gelId));
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
                    if ($tmpRole === 'gel') {
                        $userRole = $tmpRole;
                    }
                }
                if ($userRole === '') {
                    return $this->respondWithStatus([
                             'error_code' => 4003,
                         ], Response::HTTP_FORBIDDEN);
                } elseif ($userRole === 'gel') {

                    $studentPerSchool = $this->entityTypeManager->getStorage('gelstudenthighschool')->loadByProperties(array('school_id' => $gelId, 'taxi' => $classId));
                }
                if ($studentPerSchool) {
                    $list = array();
                    foreach ($studentPerSchool as $object) {
                        $studentId = $object->student_id->target_id;
                        $gelStudents = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(array('id' => $studentId));
                        $gelStudent = reset($gelStudents);
                        if ($gelStudents) {
                            $studentIdNew = $gelStudent->id();
                            $choices = "";
                            $studentchoices = $this->entityTypeManager->getStorage('gel_student_choices')->loadByProperties(array('student_id' => $studentId));

                            foreach ($studentchoices as $object) {

                                    $choices = $choices."  ".($object -> choice_id ->entity->get('name')->value)."/" ;
                                }

                            $crypt = new Crypt();
                            try {
                                $name_decoded = $crypt->decrypt($gelStudent->name->value);
                                $studentsurname_decoded = $crypt->decrypt($gelStudent->studentsurname->value);
                                $fatherfirstname_decoded = $crypt->decrypt($gelStudent->fatherfirstname->value);
                                $motherfirstname_decoded = $crypt->decrypt($gelStudent->motherfirstname->value);
                                $regionaddress_decoded = $crypt->decrypt($gelStudent->regionaddress->value);
                                $regiontk_decoded = $crypt->decrypt($gelStudent->regiontk->value);
                                $regionarea_decoded = $crypt->decrypt($gelStudent->regionarea->value);
                                $telnum_decoded = $crypt->decrypt($gelStudent->telnum->value);
                                $guardian_name_decoded = $crypt->decrypt($gelStudent->guardian_name->value);
                                $guardian_surname_decoded = $crypt->decrypt($gelStudent->guardian_surname->value);
                                $guardian_fathername_decoded = $crypt->decrypt($gelStudent->guardian_fathername->value);
                                $guardian_mothername_decoded = $crypt->decrypt($gelStudent->guardian_mothername->value);
                            } catch (\Exception $e) {
                                $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                                return $this->respondWithStatus([
                                "message" => t("An unexpected error occured during DECODING data in getStudentPerSchool Method ")
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }

                            $list[] = array(
                                'id' => $gelStudent->id(),
                                'name' => $name_decoded,
                                'studentsurname' => $studentsurname_decoded,
                                'fatherfirstname' => $fatherfirstname_decoded,
                                'motherfirstname' => $motherfirstname_decoded,
                                'guardian_name' => $guardian_name_decoded,
                                'guardian_surname' => $guardian_surname_decoded,
                                'guardian_fathername' => $guardian_fathername_decoded,
                                'guardian_mothername' => $guardian_mothername_decoded,
                                'lastschool_schoolname' => $gelStudent->lastschool_schoolname->value,
                                'lastschool_schoolyear' => $gelStudent->lastschool_schoolyear->value,
                                'lastschool_class' => $gelStudent->lastschool_class->value,
                                'currentclass' => $classId,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                //'graduation_year' => $gelStudent->graduation_year->value,
                                'telnum' => $telnum_decoded,
                                'relationtostudent' => $relationtostudent_decoded,
                                //'birthdate' => substr($gelStudent->birthdate->value, 8, 10) . '/' . substr($gelStudent->birthdate->value, 6, 8) . '/' . substr($gelStudent->birthdate->value, 0, 4),
                                'birthdate' => date("d-m-Y", strtotime($gelStudent->birthdate->value)),
                               // 'checkstatus' => $checkstudentstatus -> directorconfirm ->value,
                                'lock_delete' => $lock_delete,
                                'created' => date('d/m/Y H:i', $gelStudent -> created ->value),
                                'choices' => $choices

                            );
                        }
                    }
                    return $this->respondWithStatus($list, Response::HTTP_OK);
                } else {
                    return $this->respondWithStatus([
                        'message' => t('Students not found!'),
                    ], Response::HTTP_NOT_FOUND);
                }
            } else {
                return $this->respondWithStatus([
                    'message' => t('User not found!'),
                ], Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                'message' => t('Unexpected Error'),
            ], Response::HTTP_FORBIDDEN);
        }
    }

public function getSchoolGel(Request $request)
    {

        $authToken = $request->headers->get('PHP_AUTH_USER');

        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if ($user) {
            $selectionId = $user->init->value;
            $userRoles = $user->getRoles();
            $userRole = '';
            foreach ($userRoles as $tmpRole) {
                if (($tmpRole === 'regioneduadmin') || ($tmpRole === 'eduadmin')) {
                    $userRole = $tmpRole;
                }
            }

            if ($userRole === '') {
                return $this->respondWithStatus([
                    'error_code' => 4003,
                    "message" => t("1")
                ], Response::HTTP_FORBIDDEN);
            } elseif ($userRole === 'regioneduadmin') {
                $sCon = $this->connection->select('gel_school', 'eSchool')
                              ->fields('eSchool', array('id', 'name', 'unit_type_id','edu_admin_id'))
                              ->condition('eSchool.edu_admin_id', $selectionId , '=')
                              ->condition('eSchool.unit_type_id', 4 , '=');
                 $sCon -> orderBy('eSchool.name', 'ASC');
                 $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            } elseif ($userRole === 'eduadmin') {

                 $sCon = $this->connection->select('gel_school', 'eSchool')
                              ->fields('eSchool', array('id', 'name', 'unit_type_id','edu_admin_id'))
                              ->condition('eSchool.edu_admin_id', $selectionId , '=')
                              ->condition('eSchool.unit_type_id',4  , '=');
                 $sCon -> orderBy('eSchool.name', 'ASC');
                 $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

            }



            else {
                $schools = [];
            }

            if ($schools) {
                $list = array();

                foreach ($schools as $object) {
                    $status = 1;
                    $list[] = array(
                        'id' => $object ->id,
                        'name' => $object->name,
                        'status' => $status,
                    );
                }

                return $this->respondWithStatus($list, Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'message' => t('No schools found!'),
                ], Response::HTTP_FORBIDDEN);
            }
        } else {
            return $this->respondWithStatus([
                'message' => t('User not found!'),
            ], Response::HTTP_FORBIDDEN);
        }

    }

      public function getCoursesGel(Request $request, $schoolid)
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');

        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if ($user) {
            $newid = $user->init->value;
            $userRoles = $user->getRoles();
            $userRole = '';
            foreach ($userRoles as $tmpRole) {
                if (($tmpRole === 'regioneduadmin') || ($tmpRole === 'eduadmin')) {
                    $userRole = $tmpRole;
                }
            }
            if ($userRole === '') {
                return $this->respondWithStatus([
                    'error_code' => 4003,
                ], Response::HTTP_FORBIDDEN);
            } elseif ($userRole === 'regioneduadmin') {
                $SchoolCats = $this->entityTypeManager->getStorage('gel_school')
                    ->loadByProperties(array('id' => $schoolid, 'region_edu_admin_id' => $newid));
            } elseif ($userRole === 'eduadmin') {
                $SchoolCats = $this->entityTypeManager->getStorage('gel_school')
                    ->loadByProperties(array('id' => $schoolid, 'edu_admin_id' => $newid));
            }

            $SchoolCat = reset($SchoolCats);
            if ($SchoolCat) {
                $categ = $SchoolCat->metathesis_region->value;
                $operation_shift = $SchoolCat->operation_shift->value;

            } else {
                return $this->respondWithStatus([
                    'message' => t('No school located'),
                ], Response::HTTP_FORBIDDEN);
            }

            $list = array();
            $limit = -1;
            $CourseA = $this->entityTypeManager->getStorage('gel_school')
                ->loadByProperties(array('id' => $schoolid));
            if ($CourseA) {
                $studentPerSchool = $this->entityTypeManager->getStorage('gelstudenthighschool')
                    ->loadByProperties(array('school_id' => $schoolid, 'taxi' => 'Α'));

                foreach ($CourseA as $object) {
                    $list[] = array(
                        'id' => '1',
                        'name' => 'Α Λυκείου',
                        'size' => sizeof($studentPerSchool),
                        'categ' => $categ,
                        'classes' => 1,

                    );
                }
                $studentPerSchool = $this->entityTypeManager->getStorage('gelstudenthighschool')
                    ->loadByProperties(array('school_id' => $schoolid, 'taxi' => 'Β'));

                foreach ($CourseA as $object) {
                    $list[] = array(
                        'id' => '2',
                        'name' => 'Β Λυκείου',
                        'size' => sizeof($studentPerSchool),
                        'categ' => $categ,
                        'classes' => 1,

                    );
                }
                $studentPerSchool = $this->entityTypeManager->getStorage('gelstudenthighschool')
                    ->loadByProperties(array('school_id' => $schoolid, 'taxi' => 'Γ'));

                foreach ($CourseA as $object) {
                    $list[] = array(
                        'id' => '3',
                        'name' => 'Γ Λυκείου',
                        'size' => sizeof($studentPerSchool),
                        'categ' => $categ,
                        'classes' => 1,

                    );
                }

                if ( $operation_shift != 'ΗΜΕΡΗΣΙΟ') {
                $studentPerSchool = $this->entityTypeManager->getStorage('gelstudenthighschool')
                    ->loadByProperties(array('school_id' => $schoolid, 'taxi' => 'Δ'));

                foreach ($CourseA as $object) {
                    $list[] = array(
                        'id' => '4',
                        'name' => 'Δ Λυκείου',
                        'size' => sizeof($studentPerSchool),
                        'categ' => $categ,
                        'classes' => 1,

                    );
                }
              }






                     $selectionPerSchool = $this->entityTypeManager->getStorage('gel_choices')
                    ->loadByProperties(array( 'choicetype' => 'ΕΠΙΛΟΓΗ'));
                foreach ($selectionPerSchool as $object) {
                    $desc = $object -> name->value;
                    $this->logger->error($object ->id()."gel_choices");
                    $classchoice = $this->entityTypeManager->getStorage('gel_class_choices')
                    ->loadByProperties(array( 'class_id'=> 1, 'choice_id'=>$object->id()));
                    if ($classchoice)
                    {
                    foreach ($classchoice as $choices)
                    {
                       $this->logger->error($choices ->id());
                     $selectionsize = $this->entityTypeManager->getStorage('gel_student_choices')
                    ->loadByProperties(array( 'choice_id'=> $object ->id()));
                    if ($selectionsize)
                    {
                      $list[] = array(
                        'id' => '5',
                        'name' => "Ά Λυκείου  ".$desc,
                        'size' => sizeof($selectionsize),
                        'classes' => 1,
                         );
                    }

                    }
                    }
                    }


                          $selectionPerSchool = $this->entityTypeManager->getStorage('gel_choices')
                    ->loadByProperties(array( 'choicetype' => 'ΟΠ'));
                foreach ($selectionPerSchool as $object) {
                    $desc = $object -> name->value;
                    $this->logger->error($object ->id()."gel_choices");
                    $classchoice = $this->entityTypeManager->getStorage('gel_class_choices')
                    ->loadByProperties(array( 'class_id'=> 1, 'choice_id'=>$object->id()));
                    if ($classchoice)
                    {
                    foreach ($classchoice as $choices)
                    {
                       $this->logger->error($choices ->id());
                     $selectionsize = $this->entityTypeManager->getStorage('gel_student_choices')
                    ->loadByProperties(array( 'choice_id'=> $object ->id()));
                    if ($selectionsize)
                    {
                      $list[] = array(
                        'id' => '5',
                        'name' => "Ά Λυκείου  ".$desc,
                        'size' => sizeof($selectionsize),
                        'classes' => 1,
                         );
                    }

                    }
                    }
                    }



                $selectionPerSchool = $this->entityTypeManager->getStorage('gel_choices')
                    ->loadByProperties(array( 'choicetype' => 'ΕΠΙΛΟΓΗ'));
                foreach ($selectionPerSchool as $object) {
                    $desc = $object -> name->value;
                    $this->logger->error($object ->id()."gel_choices");
                    $classchoice = $this->entityTypeManager->getStorage('gel_class_choices')
                    ->loadByProperties(array( 'class_id'=> 2, 'choice_id'=>$object->id()));
                    if ($classchoice)
                    {
                    foreach ($classchoice as $choices)
                    {
                       $this->logger->error($choices ->id());
                     $selectionsize = $this->entityTypeManager->getStorage('gel_student_choices')
                    ->loadByProperties(array( 'choice_id'=> $object ->id()));
                    if ($selectionsize)
                    {
                      $list[] = array(
                        'id' => '5',
                        'name' => "Β Λυκείου  ".$desc,
                        'size' => sizeof($selectionsize),
                        'classes' => 1,
                         );
                    }

                    }
                    }
                    }


                          $selectionPerSchool = $this->entityTypeManager->getStorage('gel_choices')
                    ->loadByProperties(array( 'choicetype' => 'ΟΠ'));
                foreach ($selectionPerSchool as $object) {
                    $desc = $object -> name->value;
                    $this->logger->error($object ->id()."gel_choices");
                    $classchoice = $this->entityTypeManager->getStorage('gel_class_choices')
                    ->loadByProperties(array( 'class_id'=> 2, 'choice_id'=>$object->id()));
                    if ($classchoice)
                    {
                    foreach ($classchoice as $choices)
                    {
                       $this->logger->error($choices ->id());
                     $selectionsize = $this->entityTypeManager->getStorage('gel_student_choices')
                    ->loadByProperties(array( 'choice_id'=> $object ->id()));
                    if ($selectionsize)
                    {
                      $list[] = array(
                        'id' => '5',
                        'name' => "Β Λυκείου  ".$desc,
                        'size' => sizeof($selectionsize),
                        'classes' => 1,
                         );
                    }

                    }
                    }
                    }



                    $selectionPerSchool = $this->entityTypeManager->getStorage('gel_choices')
                    ->loadByProperties(array( 'choicetype' => 'ΕΠΙΛΟΓΗ'));
                foreach ($selectionPerSchool as $object) {
                    $desc = $object -> name->value;
                    $this->logger->error($object ->id()."gel_choices");
                    $classchoice = $this->entityTypeManager->getStorage('gel_class_choices')
                    ->loadByProperties(array( 'class_id'=> 3, 'choice_id'=>$object->id()));
                    if ($classchoice)
                    {
                    foreach ($classchoice as $choices)
                    {
                       $this->logger->error($choices ->id());
                     $selectionsize = $this->entityTypeManager->getStorage('gel_student_choices')
                    ->loadByProperties(array( 'choice_id'=> $object ->id()));
                    if ($selectionsize)
                    {
                      $list[] = array(
                        'id' => '5',
                        'name' => "Γ Λυκείου  ".$desc,
                        'size' => sizeof($selectionsize),
                        'classes' => 1,
                         );
                    }

                    }
                    }
                    }


                          $selectionPerSchool = $this->entityTypeManager->getStorage('gel_choices')
                    ->loadByProperties(array( 'choicetype' => 'ΟΠ'));
                foreach ($selectionPerSchool as $object) {
                    $desc = $object -> name->value;
                    $this->logger->error($object ->id()."gel_choices");
                    $classchoice = $this->entityTypeManager->getStorage('gel_class_choices')
                    ->loadByProperties(array( 'class_id'=> 3, 'choice_id'=>$object->id()));
                    if ($classchoice)
                    {
                    foreach ($classchoice as $choices)
                    {
                       $this->logger->error($choices ->id());
                     $selectionsize = $this->entityTypeManager->getStorage('gel_student_choices')
                    ->loadByProperties(array( 'choice_id'=> $object ->id()));
                    if ($selectionsize)
                    {
                      $list[] = array(
                        'id' => '5',
                        'name' => "Γ Λυκείου  ".$desc,
                        'size' => sizeof($selectionsize),
                        'classes' => 1,
                         );
                    }

                    }
                    }
                    }




            }




            if ($CourseA) {
                return $this->respondWithStatus($list, Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'message' => t('No courses found!'),
                ], Response::HTTP_FORBIDDEN);
            }
        } else {
            return $this->respondWithStatus([
                'message' => t('User not found!'),
            ], Response::HTTP_FORBIDDEN);
        }
    }


public function FindStudentsPerSchoolGym(Request $request){
    
    try{    

/*         $eggrafesConfigs = $this->entityTypeManager->getStorage('eggrafes_config')->loadByProperties(array('name' => 'eggrafes_config_gel'));
        $eggrafesConfig = reset($eggrafesConfigs);
        if (!$eggrafesConfig) {
            return $this->respondWithStatus([
                    "error_code" => 3001
                ], Response::HTTP_FORBIDDEN);
        }
        else{
          $lock_delete = $eggrafesConfig->lock_delete->value;
        }*/

        $authToken = $request->headers->get('PHP_AUTH_USER');
        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if ($user) {
            $gymId = $user->init->value;
            $schools = $this->entityTypeManager->getStorage('gel_school')->loadByProperties(array('id' => $gymId));
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
                $this->logger->warning('tmpRole='.$tmpRole);

                if ($tmpRole === 'gym') {
                    $userRole = $tmpRole;
                }
            }
            if ($userRole === '') {
                return $this->respondWithStatus([
                         'error_code' => "school registry_no value",
                     ], Response::HTTP_FORBIDDEN);
            } elseif ($userRole === 'gym') {

                $studentPerSchool_gel = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(['lastschool_registrynumber'=> "".$school->registry_no->value]);// '9ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΑΙΓΑΛΕΩ']);
                $studentPerSchool_epal = $this->entityTypeManager->getStorage('epal_student')->loadByProperties(['lastschool_registrynumber'=> "".$school->registry_no->value]);// '9ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΑΙΓΑΛΕΩ']);
            }

            $list = array();

            if ($studentPerSchool_epal) {
                foreach ($studentPerSchool_epal as $epalStudent) {

                    $studentId=intval($epalStudent->id->value);

                    $assignedEpals = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(['student_id'=> $studentId]);
                    $assignedEpal= reset($assignedEpals);
                    $asignedschool=$assignedEpal->epal_id->entity->get('name')->value;   


                    $crypt = new Crypt();
                    try {
                        if (!empty($epalStudent->am->value)){
                            $am_decoded = $crypt->decrypt($epalStudent->am->value);
                        }
                        else{
                            $am_decoded="";
                        }
                        $name_decoded = $crypt->decrypt($epalStudent->name->value);
                        $studentsurname_decoded = $crypt->decrypt($epalStudent->studentsurname->value);
                        $fatherfirstname_decoded = $crypt->decrypt($epalStudent->fatherfirstname->value);
                        $motherfirstname_decoded = $crypt->decrypt($epalStudent->motherfirstname->value);
                        $regionaddress_decoded = $crypt->decrypt($epalStudent->regionaddress->value);
                        $regiontk_decoded = $crypt->decrypt($epalStudent->regiontk->value);
                        $regionarea_decoded = $crypt->decrypt($epalStudent->regionarea->value);
                        $telnum_decoded = $crypt->decrypt($epalStudent->telnum->value);
                        $guardian_name_decoded = $crypt->decrypt($epalStudent->guardian_name->value);
                        $guardian_surname_decoded = $crypt->decrypt($epalStudent->guardian_surname->value);
                        $guardian_fathername_decoded = $crypt->decrypt($epalStudent->guardian_fathername->value);
                        $guardian_mothername_decoded = $crypt->decrypt($epalStudent->guardian_mothername->value);
                    } catch (\Exception $e) {
                        $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                        return $this->respondWithStatus([
                        "message" => t("An unexpected error occured during DECODING data in getStudentPerSchool Method ")
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }

                    $list[] = array(
                        'am' => $am_decoded,
                        'id' => $gelStudent->id->value,
                        'name' => $name_decoded,
                        'studentsurname' => $studentsurname_decoded,
                        'fatherfirstname' => $fatherfirstname_decoded,
                        'motherfirstname' => $motherfirstname_decoded,
                        'guardian_name' => $guardian_name_decoded,
                        'guardian_surname' => $guardian_surname_decoded,
                        'guardian_fathername' => $guardian_fathername_decoded,
                        'guardian_mothername' => $guardian_mothername_decoded,
                        'regionaddress' => $regionaddress_decoded,
                        'regiontk' => $regiontk_decoded,
                        'regionarea' => $regionarea_decoded,
                        'telnum' => $telnum_decoded,
                        'gel' => $asignedschool,

                    );
                }                
            }
             


            if ($studentPerSchool_gel) {
                foreach ($studentPerSchool_gel as $gelStudent) {

                    $studentId=intval($gelStudent->id->value);

                    $assignedGels = $this->entityTypeManager->getStorage('gelstudenthighschool')->loadByProperties(['student_id'=> $studentId]);
                    $assignedGel= reset($assignedGels);
                    $asignedschool=$assignedGel->school_id->entity->get('name')->value;                   

                    $crypt = new Crypt();
                    try {
                        if (!empty($gelStudent->am->value)){
                            $am_decoded = $crypt->decrypt($gelStudent->am->value);
                        }
                        else{
                            $am_decoded="";
                        }
                        $name_decoded = $crypt->decrypt($gelStudent->name->value);
                        $studentsurname_decoded = $crypt->decrypt($gelStudent->studentsurname->value);
                        $fatherfirstname_decoded = $crypt->decrypt($gelStudent->fatherfirstname->value);
                        $motherfirstname_decoded = $crypt->decrypt($gelStudent->motherfirstname->value);
                        $regionaddress_decoded = $crypt->decrypt($gelStudent->regionaddress->value);
                        $regiontk_decoded = $crypt->decrypt($gelStudent->regiontk->value);
                        $regionarea_decoded = $crypt->decrypt($gelStudent->regionarea->value);
                        $telnum_decoded = $crypt->decrypt($gelStudent->telnum->value);
                        $guardian_name_decoded = $crypt->decrypt($gelStudent->guardian_name->value);
                        $guardian_surname_decoded = $crypt->decrypt($gelStudent->guardian_surname->value);
                        $guardian_fathername_decoded = $crypt->decrypt($gelStudent->guardian_fathername->value);
                        $guardian_mothername_decoded = $crypt->decrypt($gelStudent->guardian_mothername->value);
                    } catch (\Exception $e) {
                        $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                        return $this->respondWithStatus([
                        "message" => t("An unexpected error occured during DECODING data in getStudentPerSchool Method ")
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }

                    $list[] = array(
                        'am' => $am_decoded,
                        'id' => $gelStudent->id->value,
                        'name' => $name_decoded,
                        'studentsurname' => $studentsurname_decoded,
                        'fatherfirstname' => $fatherfirstname_decoded,
                        'motherfirstname' => $motherfirstname_decoded,
                        'guardian_name' => $guardian_name_decoded,
                        'guardian_surname' => $guardian_surname_decoded,
                        'guardian_fathername' => $guardian_fathername_decoded,
                        'guardian_mothername' => $guardian_mothername_decoded,
                        'regionaddress' => $regionaddress_decoded,
                        'regiontk' => $regiontk_decoded,
                        'regionarea' => $regionarea_decoded,
                        'telnum' => $telnum_decoded,
                        'gel' => $asignedschool,

                    );
                }
                return $this->respondWithStatus($list, Response::HTTP_OK);

            }
            else {
                return $this->respondWithStatus([
                    'message' => t('Students not found!'),
                ], Response::HTTP_NOT_FOUND);
            }

        }
    } catch (\Exception $e) {
        $this->logger->warning($e->getMessage());
        return $this->respondWithStatus([
            'message' => t('Unexpected Error'),
        ], Response::HTTP_FORBIDDEN);
   }
}

/* 
                    $gelStudents = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(array('id' => $studentId));
                    $gelStudent = reset($gelStudents);
                    if ($gelStudents) {
                        $studentIdNew = $gelStudent->id();
                        $choices = "";
                        $studentchoices = $this->entityTypeManager->getStorage('gel_student_choices')->loadByProperties(array('student_id' => $studentId));

                        foreach ($studentchoices as $object) {

                                $choices = $choices."  ".($object -> choice_id ->entity->get('name')->value)."/" ;
                            }

                        $crypt = new Crypt();
                        try {
                            $name_decoded = $crypt->decrypt($gelStudent->name->value);
                            $studentsurname_decoded = $crypt->decrypt($gelStudent->studentsurname->value);
                            $fatherfirstname_decoded = $crypt->decrypt($gelStudent->fatherfirstname->value);
                            $motherfirstname_decoded = $crypt->decrypt($gelStudent->motherfirstname->value);
                            $regionaddress_decoded = $crypt->decrypt($gelStudent->regionaddress->value);
                            $regiontk_decoded = $crypt->decrypt($gelStudent->regiontk->value);
                            $regionarea_decoded = $crypt->decrypt($gelStudent->regionarea->value);
                            $telnum_decoded = $crypt->decrypt($gelStudent->telnum->value);
                            $guardian_name_decoded = $crypt->decrypt($gelStudent->guardian_name->value);
                            $guardian_surname_decoded = $crypt->decrypt($gelStudent->guardian_surname->value);
                            $guardian_fathername_decoded = $crypt->decrypt($gelStudent->guardian_fathername->value);
                            $guardian_mothername_decoded = $crypt->decrypt($gelStudent->guardian_mothername->value);
                        } catch (\Exception $e) {
                            $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                            return $this->respondWithStatus([
                            "message" => t("An unexpected error occured during DECODING data in getStudentPerSchool Method ")
                            ], Response::HTTP_INTERNAL_SERVER_ERROR);
                        }

                        $list[] = array(
                            'id' => $gelStudent->id(),
                            'name' => $name_decoded,
                            'studentsurname' => $studentsurname_decoded,
                            'fatherfirstname' => $fatherfirstname_decoded,
                            'motherfirstname' => $motherfirstname_decoded,
                            'guardian_name' => $guardian_name_decoded,
                            'guardian_surname' => $guardian_surname_decoded,
                            'guardian_fathername' => $guardian_fathername_decoded,
                            'guardian_mothername' => $guardian_mothername_decoded,
                            'lastschool_schoolname' => $gelStudent->lastschool_schoolname->value,
                            'lastschool_schoolyear' => $gelStudent->lastschool_schoolyear->value,
                            'lastschool_class' => $gelStudent->lastschool_class->value,
                            'currentclass' => $classId,
                            'regionaddress' => $regionaddress_decoded,
                            'regiontk' => $regiontk_decoded,
                            'regionarea' => $regionarea_decoded,
                            //'graduation_year' => $gelStudent->graduation_year->value,
                            'telnum' => $telnum_decoded,
                            'relationtostudent' => $relationtostudent_decoded,
                            //'birthdate' => substr($gelStudent->birthdate->value, 8, 10) . '/' . substr($gelStudent->birthdate->value, 6, 8) . '/' . substr($gelStudent->birthdate->value, 0, 4),
                            'birthdate' => date("d-m-Y", strtotime($gelStudent->birthdate->value)),
                           // 'checkstatus' => $checkstudentstatus -> directorconfirm ->value,
                            'lock_delete' => $lock_delete,
                            'created' => date('d/m/Y H:i', $gelStudent -> created ->value),
                            'choices' => $choices

                        );
                    }
                }
                return $this->respondWithStatus($list, Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'message' => t('Students not found!'),
                ], Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->respondWithStatus([
                'message' => t('User not found!'),
            ], Response::HTTP_FORBIDDEN);
        } 
    } catch (\Exception $e) {
        $this->logger->warning($e->getMessage());
        return $this->respondWithStatus([
            'message' => t('Unexpected Error'),
        ], Response::HTTP_FORBIDDEN);
    }
    }*/


   private function respondWithStatus($arr, $s)  {
        $res = new JsonResponse($arr);
        $res->setStatusCode($s);
        return $res;
    }

//πιθανότατα ΔΕΝ υπάρχει αναγακιότητα χρήσης της
/*
public function autoDistribution(Request $request)
{
  // GET method is checked
  if (!$request->isMethod('GET')) {
      return $this->respondWithStatus([
        "message" => t("Method Not Allowed")
      ], Response::HTTP_METHOD_NOT_ALLOWED);
  }

  $authToken = $request->headers->get('PHP_AUTH_USER');
  $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
  $user = reset($users);
  if ($user) {
      //check Role
      $selectionId = $user->init->value;
      $userRoles = $user->getRoles();
      $userRole = '';
      foreach ($userRoles as $tmpRole) {
          if ($tmpRole === 'eduadmin') {
              $userRole = $tmpRole;
          }
      }
      if ($userRole !== 'eduadmin') {
          return $this->respondWithStatus([
              'message' => "Not Valid Role",
              'error_code' => 4003,
          ], Response::HTTP_FORBIDDEN);
      }
      else
      {
        try {
            //check gel ministry settings
            $eggrafesConfigs = $this->entityTypeManager->getStorage('eggrafes_config')->loadByProperties(array('name' => 'eggrafes_config_gel'));
            $eggrafesConfig = reset($eggrafesConfigs);
            if (!$eggrafesConfig) {
                return $this->respondWithStatus([
                    "error_code" => 3001
                ], Response::HTTP_FORBIDDEN);
            }
            if (!$eggrafesConfig->lock_school_students_view->value) {
                return $this->respondWithStatus([
                    "error_code" => 1000
                ], Response::HTTP_OK);
            }
            if (!$eggrafesConfig->lock_results->value) {
                return $this->respondWithStatus([
                    "error_code" => 1001
                ], Response::HTTP_OK);
            }

            //εύρεση σχολείων ΓΕΛ της ΔΔΕ
            $sCon = $this->connection->select('gel_school', 'eSchool')
                ->fields('eSchool', array('id', 'name', 'unit_type_id','edu_admin_id', 'registry_no'))
                ->condition('eSchool.edu_admin_id', $selectionId , '=')
                ->condition('eSchool.unit_type_id', 4 , '=');

           $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
           $schoollist_ids = array();
           $schoollist_regno = array();

           foreach ($schools as $school) {
              array_push($schoollist_ids, $school->id);
              array_push($schoollist_regno, $school->registry_no);
            }

            //εύρεση μαθητών που έχουν κάνει αίτηση για να πάνε Β' / Γ' / Δ' Λυκείου, με σχολείο τελευταίας φοίτησης σε ΓΕΛ της ΔΔΕ
            $sConStud = $this->connection->select('gel_student', 'eStudent')
                  ->fields('eStudent', array('id','lastschool_registrynumber'))
                  ->condition('eStudent.nextclass', 1 , '!=')
                  ->condition('eStudent.nextclass', 4 , '!=')
                  //->condition('eStudent.lastschool_registrynumber', $schoollist_regno, 'IN')
                  ->condition('eStudent.lastschool_unittypeid', "4", '=')
                  ->condition('eStudent.delapp', 0 , '=');
            $students = $sConStud->execute()->fetchAll(\PDO::FETCH_OBJ);

            $schoolId = -1;
            foreach ($students as $student)   {
              //εύρεση id σχολείου τελευταίας φοίτησης με βάση το registry_no
              for ($k=0; $k < sizeof($schoollist_regno); $k++) {
                  if ($student->lastschool_registrynumber === $schoollist_regno[$k])  {
                      $schoolId =  $schoollist_ids[$k];
                      break;
                  }
              }

              if ($schoolId !== -1) {
                    //εισαγωγή αποτελεσμάτων
                    $timestamp = strtotime(date("Y-m-d"));
                    $this->connection->insert('gelstudenthighschool')->fields([
                        'id' => $this->globalCounterId++,
                        'uuid' => \Drupal::service('uuid')->generate(),
                        'langcode' => $this->language,
                        'user_id' => $this->currentuser,
                        //'user_id' => 1,
                        'student_id'=> $student->id,
                        'school_id'=> $schoolId,
                        'status' => 1,
                        'created' => $timestamp,
                        'changed' => $timestamp
                    ])->execute();
                }
            }

            return $this->respondWithStatus([
                    'message' => t("Success"),
                ], Response::HTTP_OK);
          }

          catch (\Exception $e) {
              $this->logger->error($e->getMessage());
              return $this->respondWithStatus([
                      "error_code" => 1002,
                      'message' => t("error in autoDistribution function"),
                  ], Response::HTTP_FORBIDDEN);
          }
      }

  } else {
      return $this->respondWithStatus([
          'message' => t('User not found!'),
      ], Response::HTTP_FORBIDDEN);
  }

}
*/



}
