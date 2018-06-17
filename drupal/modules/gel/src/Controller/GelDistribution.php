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
                    "message" => t("1")
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
                 $this->logger->error("no role");
                return $this->respondWithStatus([
                    'error_code' => 4003,
                    "message" => t("1")
                ], Response::HTTP_FORBIDDEN);
            } elseif ($userRole === 'regioneduadmin') {
                 $sCon = $this->connection->select('gel_school', 'eSchool')
                              ->fields('eSchool', array('id', 'name', 'unit_type_id','edu_admin_id','extra_unitid'))
                              ->condition('eSchool.edu_admin_id', $selectionId , '=')
                              ->condition(db_or()->condition('eSchool.unit_type_id', 4 , '=') ->condition('eSchool.extra_unitid',200,'='));
                 $sCon -> orderBy('eSchool.name', 'ASC');

                 $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            } elseif ($userRole === 'eduadmin') {
                  $sCon = $this->connection->select('gel_school', 'eSchool')
                   ->fields('eSchool', array('id', 'name', 'unit_type_id','edu_admin_id','extra_unitid'))
                   ->condition('eSchool.edu_admin_id', $selectionId , '=')
                    ->condition(db_or()->condition('eSchool.unit_type_id', 4 , '=')->condition('eSchool.extra_unitid',200,'='));
                 $sCon -> orderBy('eSchool.name', 'ASC');
                 $this->logger->error($sCon."test");
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
                              ->fields('eSchool', array('id', 'name', 'unit_type_id','edu_admin_id','extra_unitid'))
                              ->condition('eSchool.edu_admin_id', $selectionId , '=')
                              >condition(db_or()->condition('eSchool.unit_type_id', 4 , '=') ->condition('eSchool.extra_unitid',200,'='));
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


public function getStudentsPerSchool(Request $request, $schoolid, $type,$addressfilter,$amfilter)
    {


        if ($type === '1')
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

            /*    $studentPerSchool = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(array('lastschool_registrynumber' => $regno, 'lastschool_unittypeid' => 3, 'lastschool_class' => "3", 'delapp' => '0'));
*/

            $sCon = $this->connection->select('gel_student', 'gStudent')
                ->fields('gStudent', array('lastschool_registrynumber','lastschool_unittypeid',  'lastschool_class' , 'delapp','nextclass','name','am','regionarea','regiontk','regionaddress','id'))
                ->condition('gStudent.lastschool_registrynumber', $regno, '=')
                ->condition('gStudent.lastschool_unittypeid', 3 , '=')
                ->condition('gStudent.lastschool_class', "3", '=')
                ->condition(db_or()->condition('nextclass', "1")->condition('nextclass', "4"))
                ->condition('gStudent.delapp', 0, '=');
            $studentPerSchool =  $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

                }
                if ($studentPerSchool) {

                    $list = array();
                    $i = 0;
                    foreach ($studentPerSchool as $object) {

                            $crypt = new Crypt();
                            try {
                                $name_decoded = $object->name;
                                if ($object->am != "")
                                  $am_decoded = $crypt ->decrypt($object->am);
                                else
                                  $am_decoded ="Παλιός απόφοιτος";
                                $regionaddress_decoded = $crypt->decrypt($object->regionaddress);
                                if ($object->regiontk !== null)
                                   $regiontk_decoded = $crypt->decrypt($object->regiontk);
                                else
                                    $regiontk_decoded = "";
                                if ($object->regionarea !== null)
                                $regionarea_decoded = $crypt->decrypt($object->regionarea);
                                else
                                    $regionarea_decoded = null;
                                if ($object ->nextclass === "4")
                                {
                                  $school_type = "Αίτηση για Εσπερινό";
                                }
                                else{
                                  $school_type = "Αίτηση για Ημερήσιο";
                                }


                            } catch (\Exception $e) {
                                $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                                return $this->respondWithStatus([
                                "message" => t("An unexpected error occured during DECODING data in getStudentPerSchool Method ")
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }




            if ($addressfilter === '99999' && $amfilter === '0')
                            {
                            $i++;
                            $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                           }
                           if ($addressfilter !== '99999' && $amfilter !== '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);
                            $pos1 = strpos($am_decoded,$amfilter);
                            if  ($pos >=0 && $pos !== false && $pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }
                            }


                            if ($addressfilter !== '99999' && $amfilter === '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);

                            if  ($pos >=0 && $pos !== false )
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }



                           }

                            if ($addressfilter === '99999' && $amfilter !== '0')
                            {

                            $pos1 = strpos($am_decoded,$amfilter);

                            if  ($pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }

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
        elseif ($type == 2)
        {


        try {


            $authToken = $request->headers->get('PHP_AUTH_USER');
            $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
            $user = reset($users);
            if ($user) {
               $selectionId = $user->init->value;
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
                } elseif ($userRole === 'eduadmin')
                {



/*            $sCon = $this->connection->select('gel_student', 'gStudent')
                ->fields('gStudent', array('lastschool_registrynumber','lastschool_unittypeid',  'lastschool_class' , 'delapp','nextclass','name','am','regionarea','regiontk','regionaddress','id'))
                ->condition('gStudent.lastschool_unittypeid', 5, '=')
                ->condition(db_or()->condition('nextclass', "2")->condition('nextclass', "5"))
                ->condition('gStudent.delapp', 0, '=');
*/
                $sCon = $this->connection->select('gel_student', 'gStudent');
                $sCon->leftJoin('eepal_school_field_data', 'eSchool', 'eSchool.registry_no = gStudent.lastschool_registrynumber');
                $sCon->fields('gStudent', array('id','lastschool_registrynumber','nextclass', 'delapp','name','studentsurname' ,'fatherfirstname' ,'motherfirstname' ,'regionaddress' ,'regiontk' ,'regionarea','telnum' ,'guardian_name' ,'guardian_surname','guardian_fathername ','guardian_mothername', 'birthdate', 'lastschool_schoolname','lastschool_class','lastschool_schoolyear','directorconfirm', 'created' ,'nextclass','am'))
                  ->fields('eSchool', array('id','registry_no','edu_admin_id'))
                ->condition('eSchool.edu_admin_id', $selectionId, '=')
                ->condition('gStudent.lastschool_unittypeid', 5, '=')
                ->condition(db_or()->condition('nextclass', "2")->condition('nextclass', "6"))
                ->condition('gStudent.delapp', 0, '=');




            $studentPerSchool =  $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);



                $sCon = $this->connection->select('gel_student', 'gStudent');
                $sCon->leftJoin('gel_school', 'eSchool', 'eSchool.registry_no = gStudent.lastschool_registrynumber');
                $sCon->fields('gStudent', array('id','lastschool_registrynumber','nextclass', 'delapp','name','studentsurname' ,'fatherfirstname' ,'motherfirstname' ,'regionaddress' ,'regiontk' ,'regionarea','telnum' ,'guardian_name' ,'guardian_surname','guardian_fathername ','guardian_mothername', 'birthdate', 'lastschool_schoolname','lastschool_class','lastschool_schoolyear','directorconfirm', 'created' ,'am'))
                ->fields('eSchool', array('id','registry_no','edu_admin_id','extra_unitid'))
                ->condition('eSchool.edu_admin_id', $selectionId, '=')
                ->condition('eSchool.extra_unitid',400,'=')
                ->condition('gStudent.lastschool_unittypeid', 4 , '=')
                ->condition('nextclass', "2",'=')
                ->condition('gStudent.delapp', 0, '=');
            $studentPerSchoolfromesp =  $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

$this->logger->warning($sCon."fromesp");

               $sCon = $this->connection->select('gel_student', 'gStudent');
                $sCon->leftJoin('gel_school', 'eSchool', 'eSchool.registry_no = gStudent.lastschool_registrynumber');
                $sCon->fields('gStudent', array('id','lastschool_registrynumber','nextclass', 'delapp','name','studentsurname' ,'fatherfirstname' ,'motherfirstname' ,'regionaddress' ,'regiontk' ,'regionarea','telnum' ,'guardian_name' ,'guardian_surname','guardian_fathername ','guardian_mothername', 'birthdate', 'lastschool_schoolname','lastschool_class','lastschool_schoolyear','directorconfirm', 'created' ,'am'))
                ->fields('eSchool', array('id','registry_no','edu_admin_id','extra_unitid'))
                ->condition('eSchool.edu_admin_id', $selectionId, '=')
                ->condition('eSchool.extra_unitid',300,'=')
                ->condition('gStudent.lastschool_unittypeid', 4 , '=')
                ->condition('lastschool_class', "1",'=')
                ->condition('nextclass', "2",'=')
                ->condition('gStudent.delapp', 0, '=');
            $studentPerSchoolfromidiwt =  $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
               $this->logger->warning($sCon."fromidiwt");

                }
                if ($studentPerSchool)
                {

                    $list = array();
                    $i = 0;
                    foreach ($studentPerSchool as $object) {

                            $schoolIdNew = $object->lastschool_registrynumber;




                            $crypt = new Crypt();
                            try {
                                $name_decoded = $object->name;
                                if ($object->am != "")
                                  $am_decoded = $crypt ->decrypt($object->am);
                                else
                                  $am_decoded ="Παλιός απόφοιτος";
                                $regionaddress_decoded = $crypt->decrypt($object->regionaddress);
                                if ($object->regiontk !== null)
                                   $regiontk_decoded = $crypt->decrypt($object->regiontk);
                                else
                                    $regiontk_decoded = "";
                                if ($object->regionarea !== null)
                                $regionarea_decoded = $crypt->decrypt($object->regionarea);
                                else
                                    $regionarea_decoded = null;
                                if ($object ->nextclass === "6")
                                {
                                  $school_type = "Αίτηση για Εσπερινό";
                                }
                                else{
                                  $school_type = "Αίτηση για Ημερήσιο";
                                }


                            } catch (\Exception $e) {
                                $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                                return $this->respondWithStatus([
                                "message" => t("An unexpected error occured during DECODING data in getStudentPerSchool Method ")
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }




            if ($addressfilter === '99999' && $amfilter === '0')
                            {
                            $i++;
                            $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                           }
                           if ($addressfilter !== '99999' && $amfilter !== '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);
                            $pos1 = strpos($am_decoded,$amfilter);
                            if  ($pos >=0 && $pos !== false && $pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }
                            }


                            if ($addressfilter !== '99999' && $amfilter === '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);

                            if  ($pos >=0 && $pos !== false )
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }



                           }

                            if ($addressfilter === '99999' && $amfilter !== '0')
                            {

                            $pos1 = strpos($am_decoded,$amfilter);

                            if  ($pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }

                           }


                    }

                }


                if ($studentPerSchoolfromidiwt)
                {

                    $i = 0;
                    foreach ($studentPerSchoolfromidiwt as $object) {

                            $schoolIdNew = $object->lastschool_registrynumber;




                            $crypt = new Crypt();
                            try {
                                $name_decoded = $object->name;
                                if ($object->am != "")
                                  $am_decoded = $crypt ->decrypt($object->am);
                                else
                                  $am_decoded ="Παλιός απόφοιτος";
                                $regionaddress_decoded = $crypt->decrypt($object->regionaddress);
                                if ($object->regiontk !== null)
                                   $regiontk_decoded = $crypt->decrypt($object->regiontk);
                                else
                                    $regiontk_decoded = "";
                                if ($object->regionarea !== null)
                                $regionarea_decoded = $crypt->decrypt($object->regionarea);
                                else
                                    $regionarea_decoded = null;
                                if ($object ->nextclass === "6")
                                {
                                  $school_type = "Αίτηση για Εσπερινό";
                                }
                                else{
                                  $school_type = "Αίτηση για Ημερήσιο";
                                }


                            } catch (\Exception $e) {
                                $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                                return $this->respondWithStatus([
                                "message" => t("An unexpected error occured during DECODING data in getStudentPerSchool Method ")
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }




            if ($addressfilter === '99999' && $amfilter === '0')
                            {
                            $i++;
                            $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                           }
                           if ($addressfilter !== '99999' && $amfilter !== '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);
                            $pos1 = strpos($am_decoded,$amfilter);
                            if  ($pos >=0 && $pos !== false && $pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }
                            }


                            if ($addressfilter !== '99999' && $amfilter === '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);

                            if  ($pos >=0 && $pos !== false )
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }



                           }

                            if ($addressfilter === '99999' && $amfilter !== '0')
                            {

                            $pos1 = strpos($am_decoded,$amfilter);

                            if  ($pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }

                           }


                    }

                }


                if ($studentPerSchoolfromesp)
                {


                    foreach ($studentPerSchoolfromesp as $object) {

                            $schoolIdNew = $object->lastschool_registrynumber;



                            $crypt = new Crypt();
                            try {
                                $name_decoded = $object->name;
                                if ($object->am != "")
                                  $am_decoded = $crypt ->decrypt($object->am);
                                else
                                  $am_decoded ="Παλιός απόφοιτος";
                                $regionaddress_decoded = $crypt->decrypt($object->regionaddress);
                                if ($object->regiontk !== null)
                                   $regiontk_decoded = $crypt->decrypt($object->regiontk);
                                else
                                    $regiontk_decoded = "";
                                if ($object->regionarea !== null)
                                $regionarea_decoded = $crypt->decrypt($object->regionarea);
                                else

                                  $school_type = "Αίτηση για Ημερήσιο";


                            } catch (\Exception $e) {
                                $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                                return $this->respondWithStatus([
                                "message" => t("An unexpected error occured during DECODING data in getstudentPerSchoolfromesp Method ")
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }




            if ($addressfilter === '99999' && $amfilter === '0')
                            {
                            $i++;
                            $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                           }
                           if ($addressfilter !== '99999' && $amfilter !== '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);
                            $pos1 = strpos($am_decoded,$amfilter);
                            if  ($pos >=0 && $pos !== false && $pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }
                            }


                            if ($addressfilter !== '99999' && $amfilter === '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);

                            if  ($pos >=0 && $pos !== false )
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }



                           }

                            if ($addressfilter === '99999' && $amfilter !== '0')
                            {

                            $pos1 = strpos($am_decoded,$amfilter);

                            if  ($pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }


                        }

                    }

                }



                if ($studentPerSchool || $studentPerSchoolfromesp || $studentPerSchoolfromidiwt) {
                    return $this->respondWithStatus($list, Response::HTTP_OK);
                }
                else {
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
        else
        {


             $this->logger->warning("mphke");




        try {


            $authToken = $request->headers->get('PHP_AUTH_USER');
            $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
            $user = reset($users);
            if ($user) {
               $selectionId = $user->init->value;
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
                } elseif ($userRole === 'eduadmin')
                {


              /*  $studentPerSchooltoesp = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(array('lastschool_unittypeid' => 4, 'delapp' => '0', 'nextclass' =>'6')); */

                $sCon = $this->connection->select('gel_student', 'gStudent');
                $sCon->leftJoin('gel_school', 'eSchool', 'eSchool.registry_no = gStudent.lastschool_registrynumber');
                $sCon->fields('gStudent', array('id','lastschool_registrynumber','nextclass', 'delapp','name','studentsurname' ,'fatherfirstname' ,'motherfirstname' ,'regionaddress' ,'regiontk' ,'regionarea','telnum' ,'guardian_name' ,'guardian_surname','guardian_fathername ','guardian_mothername', 'birthdate', 'lastschool_schoolname','lastschool_class','lastschool_schoolyear','directorconfirm', 'created','am' ))
                  ->fields('eSchool', array('id','registry_no','edu_admin_id','extra_unitid'))
                ->condition('eSchool.edu_admin_id', $selectionId, '=')
                 ->condition('eSchool.extra_unitid',400,'=')
               ->condition('gStudent.lastschool_unittypeid', 4 , '=')
                ->condition('lastschool_class', "2",'=')
                ->condition('nextclass', "7",'=')
                ->condition('gStudent.delapp', 0, '=');
                $studentPerSchooltoesp =  $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);



               $sCon = $this->connection->select('gel_student', 'gStudent');
                $sCon->leftJoin('gel_school', 'eSchool', 'eSchool.registry_no = gStudent.lastschool_registrynumber');
                $sCon->fields('gStudent', array('id','lastschool_registrynumber','nextclass', 'delapp','name','studentsurname' ,'fatherfirstname' ,'motherfirstname' ,'regionaddress' ,'regiontk' ,'regionarea','telnum' ,'guardian_name' ,'guardian_surname','guardian_fathername ','guardian_mothername', 'birthdate', 'lastschool_schoolname','lastschool_class','lastschool_schoolyear','directorconfirm', 'created' ,'am'))
                  ->fields('eSchool', array('id','registry_no','edu_admin_id','extra_unitid'))
                ->condition('eSchool.edu_admin_id', $selectionId, '=')
                 ->condition('eSchool.extra_unitid',400,'=')
                ->condition('gStudent.lastschool_unittypeid', 4 , '=')
                ->condition('nextclass', "3",'=')
                ->condition('gStudent.delapp', 0, '=');
                $studentPerSchoolfromesp =  $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);



            $sCon = $this->connection->select('gel_student', 'gStudent');
                $sCon->leftJoin('gel_school', 'eSchool', 'eSchool.registry_no = gStudent.lastschool_registrynumber');
                $sCon->fields('gStudent', array('id','lastschool_registrynumber','nextclass', 'delapp','name','studentsurname' ,'fatherfirstname' ,'motherfirstname' ,'regionaddress' ,'regiontk' ,'regionarea','telnum' ,'guardian_name' ,'guardian_surname','guardian_fathername ','guardian_mothername', 'birthdate', 'lastschool_schoolname','lastschool_class','lastschool_schoolyear','directorconfirm', 'created' ,'am'))
                  ->fields('eSchool', array('id','registry_no','edu_admin_id','extra_unitid'))
                ->condition('eSchool.edu_admin_id', $selectionId, '=')
                 ->condition('eSchool.extra_unitid',300,'=')
                ->condition('gStudent.lastschool_unittypeid', 4 , '=')
                ->condition('lastschool_class', "2",'=')
                ->condition('nextclass', "3",'=')
                ->condition('gStudent.delapp', 0, '=');
                $studentPerSchoolfromidiwt =  $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                }


                if ($studentPerSchoolfromidiwt)
                {

                    $list = array();
                    $i = 0;
                    foreach ($studentPerSchooltoesp as $object) {

                            $schoolIdNew = $object->lastschool_registrynumber;



                            $crypt = new Crypt();
                            try {
                                $name_decoded = $object->name;
                                if ($object->am != "")
                                  $am_decoded = $crypt ->decrypt($object->am);
                                else
                                  $am_decoded ="Παλιός απόφοιτος";
                                $regionaddress_decoded = $crypt->decrypt($object->regionaddress);
                                if ($object->regiontk !== null)
                                   $regiontk_decoded = $crypt->decrypt($object->regiontk);
                                else
                                    $regiontk_decoded = "";
                                if ($object->regionarea !== null)
                                $regionarea_decoded = $crypt->decrypt($object->regionarea);
                                else
                                    $regionarea_decoded = null;
                                if ($object ->nextclass === "7")
                                {
                                  $school_type = "Αίτηση για Εσπερινό";
                                }
                                else{
                                  $school_type = "Αίτηση για Ημερήσιο";
                                }


                            } catch (\Exception $e) {
                                $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                                return $this->respondWithStatus([
                                "message" => t("An unexpected error occured during DECODING data in getStudentPerSchool Method ")
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }




            if ($addressfilter === '99999' && $amfilter === '0')
                            {
                                $this->logger->warning("ok....");
                            $i++;
                            $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                           }
                           if ($addressfilter !== '99999' && $amfilter !== '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);
                            $pos1 = strpos($am_decoded,$amfilter);
                            if  ($pos >=0 && $pos !== false && $pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }
                            }


                            if ($addressfilter !== '99999' && $amfilter === '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);

                            if  ($pos >=0 && $pos !== false )
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }



                           }

                            if ($addressfilter === '99999' && $amfilter !== '0')
                            {

                            $pos1 = strpos($am_decoded,$amfilter);

                            if  ($pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }

                           }


                    }

                }

                if ($studentPerSchoolfromidiwt)
                {

                    $list = array();
                    $i = 0;
                    foreach ($studentPerSchoolfromidiwt as $object) {

                            $schoolIdNew = $object->lastschool_registrynumber;



                            $crypt = new Crypt();
                            try {
                                $name_decoded = $object->name;
                                if ($object->am != "")
                                  $am_decoded = $crypt ->decrypt($object->am);
                                else
                                  $am_decoded ="Παλιός απόφοιτος";
                                $regionaddress_decoded = $crypt->decrypt($object->regionaddress);
                                if ($object->regiontk !== null)
                                   $regiontk_decoded = $crypt->decrypt($object->regiontk);
                                else
                                    $regiontk_decoded = "";
                                if ($object->regionarea !== null)
                                $regionarea_decoded = $crypt->decrypt($object->regionarea);
                                else
                                    $regionarea_decoded = null;
                                if ($object ->nextclass === "7")
                                {
                                  $school_type = "Αίτηση για Εσπερινό";
                                }
                                else{
                                  $school_type = "Αίτηση για Ημερήσιο";
                                }


                            } catch (\Exception $e) {
                                $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                                return $this->respondWithStatus([
                                "message" => t("An unexpected error occured during DECODING data in getStudentPerSchool Method ")
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }




            if ($addressfilter === '99999' && $amfilter === '0')
                            {
                                $this->logger->warning("ok....");
                            $i++;
                            $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                           }
                           if ($addressfilter !== '99999' && $amfilter !== '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);
                            $pos1 = strpos($am_decoded,$amfilter);
                            if  ($pos >=0 && $pos !== false && $pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }
                            }


                            if ($addressfilter !== '99999' && $amfilter === '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);

                            if  ($pos >=0 && $pos !== false )
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }



                           }

                            if ($addressfilter === '99999' && $amfilter !== '0')
                            {

                            $pos1 = strpos($am_decoded,$amfilter);

                            if  ($pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }

                           }


                    }

                }

                if ($studentPerSchoolfromesp)
                {


                    foreach ($studentPerSchoolfromesp as $object) {

                            $schoolIdNew = $object->lastschool_registrynumber;




                            $crypt = new Crypt();
                            try {
                                $name_decoded = $object->name;
                                if ($object->am != "")
                                  $am_decoded = $crypt ->decrypt($object->am);
                                else
                                  $am_decoded ="Παλιός απόφοιτος";
                                $regionaddress_decoded = $crypt->decrypt($object->regionaddress);
                                if ($object->regiontk !== null)
                                   $regiontk_decoded = $crypt->decrypt($object->regiontk);
                                else
                                    $regiontk_decoded = "";
                                if ($object->regionarea !== null)
                                $regionarea_decoded = $crypt->decrypt($object->regionarea);
                                else
                                    $regionarea_decoded = null;
                                  $school_type = "Αίτηση για Ημερήσιο";


                            } catch (\Exception $e) {
                                $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                                return $this->respondWithStatus([
                                "message" => t("An unexpected error occured during DECODING data in getstudentPerSchoolfromesp Method ")
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }




            if ($addressfilter === '99999' && $amfilter === '0')
                            {
                            $i++;
                            $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                           }
                           if ($addressfilter !== '99999' && $amfilter !== '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);
                            $pos1 = strpos($am_decoded,$amfilter);
                            if  ($pos >=0 && $pos !== false && $pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }
                            }


                            if ($addressfilter !== '99999' && $amfilter === '0')
                            {
                            $pos = strpos($regionaddress_decoded,$addressfilter);

                            if  ($pos >=0 && $pos !== false )
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }



                           }

                            if ($addressfilter === '99999' && $amfilter !== '0')
                            {

                            $pos1 = strpos($am_decoded,$amfilter);

                            if  ($pos1 >=0 && $pos1 !== false)
                            {

                                $i++;
                                $list[] = array(
                                'idnew' => $i,
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'am' => $am_decoded,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                'school_type' => $school_type,
                                'oldschool' => $this -> gethighschoolperstudent($object->id),
                            );
                            }


                        }

                    }

                }



                if ($studentPerSchooltoesp || $studentPerSchoolfromesp || $studentPerSchoolfromidiwt) {
                    return $this->respondWithStatus($list, Response::HTTP_OK);
                }
                else {
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
        return $this->respondWithStatus($list, Response::HTTP_OK);

    }

 public function SaveHighSchoolSelection(Request $request, $studentid, $schoolid, $oldschool, $nextclass, $undoselection)
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
        $eggrafesConfigs = $this->entityTypeManager->getStorage('eggrafes_config')->loadByProperties(array('name' => 'eggrafes_config_gel'));
            $eggrafesConfig = reset($eggrafesConfigs);
        if (!$eggrafesConfig) {
                return $this->respondWithStatus([
                        "error_code" => 3001
                    ], Response::HTTP_FORBIDDEN);
            }
        else
        {
             $second_period = $eggrafesConfig -> activate_second_period -> value ;
        }

        $chunks = explode(",", $studentid);
       // $chunks = preg_split(',', $studentid);

        foreach ($chunks as $studId =>$value )
        {

        $transaction = $this->connection->startTransaction();
        try {


            $this->connection->delete('gelstudenthighschool')
                            ->condition('id', $value, '=')
                            ->execute();

            if ($nextclass === '1')
                $nexttaxi = 'Α';
            elseif ($nextclass === '2')
                $nexttaxi = 'Β';
             elseif ($nextclass === '3')
                $nexttaxi = 'Γ';
             elseif ($nextclass === '4')
                $nexttaxi = 'Α';
             elseif ($nextclass === '6')
                $nexttaxi = 'Β';
             elseif ($nextclass === '7')
                $nexttaxi = 'Γ';
            else
                 $nexttaxi = '';

            if (intval($undoselection) === 1)
            {
                 $this->logger->warning("mphkeedv");
                $schoolid = NULL;
                $nexttaxi = NULL;


            }

            $student = array(
                'langcode' => 'el',
                'id' => $value,
                'student_id' => $value,
                'school_id' => $schoolid,
                'taxi' => $nexttaxi,
                'dide' => $user->init->value,
                'second_period' => $second_period,

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


                $sCon = $this->connection->select('gel_student', 'gStudent');
                $sCon->leftJoin('gel_school', 'gSchool', 'gSchool.registry_no = gStudent.lastschool_registrynumber');
                $sCon->fields('gStudent', array('id','lastschool_registrynumber','nextclass', 'delapp','name','studentsurname' ,'fatherfirstname' ,'motherfirstname' ,'regionaddress' ,'regiontk' ,'regionarea','telnum' ,'guardian_name' ,'guardian_surname','guardian_fathername ','guardian_mothername', 'birthdate', 'lastschool_schoolname','lastschool_class','lastschool_schoolyear','directorconfirm', 'created' ))
                  ->fields('gSchool', array('id','registry_no'))
                  ->condition('gSchool.id', $schoolid , '=')
                  ->condition('gStudent.nextclass', '2' , '=')
                  ->condition('gStudent.delapp', '0' , '=');
                $existingstudentPerSchool = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

                    $list[] = array(
                        'class' => 2,
                        'taxi' => 'Β Λυκείου',
                        'globalindex' => $i,
                        'size' => sizeof($studentPerSchool) + sizeof($existingstudentPerSchool),
                        'sizenew' => sizeof($existingstudentPerSchool),
                       );



                     $studentPerSchool = $this->entityTypeManager->getStorage('gelstudenthighschool')->loadByProperties(array('school_id' => $schoolid , 'taxi' => 'Γ'));


                     $sCon = $this->connection->select('gel_student', 'gStudent');
                $sCon->leftJoin('gel_school', 'gSchool', 'gSchool.registry_no = gStudent.lastschool_registrynumber');
                $sCon->fields('gStudent', array('id','lastschool_registrynumber','nextclass', 'delapp','name','studentsurname' ,'fatherfirstname' ,'motherfirstname' ,'regionaddress' ,'regiontk' ,'regionarea','telnum' ,'guardian_name' ,'guardian_surname','guardian_fathername ','guardian_mothername', 'birthdate', 'lastschool_schoolname','lastschool_class','lastschool_schoolyear','directorconfirm', 'created' ))
                  ->fields('gSchool', array('id','registry_no'))
                  ->condition('gSchool.id', $schoolid , '=')
                  ->condition('gStudent.nextclass', '3' , '=')
                  ->condition('gStudent.delapp', '0' , '=');
                $existingstudentPerSchool = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);




                    $list[] = array(
                        'class' => 3,
                        'taxi' => 'Γ Λυκείου',
                        'globalindex' => $i,
                        'size' => sizeof($studentPerSchool) + sizeof($existingstudentPerSchool),
                       );
                    if ($operation_shift != 'ΗΜΕΡΗΣΙΟ'){
                     $studentPerSchool = $this->entityTypeManager->getStorage('gelstudenthighschool')->loadByProperties(array('school_id' => $schoolid, 'taxi' => 'Δ' ));


                     $sCon = $this->connection->select('gel_student', 'gStudent');
                $sCon->leftJoin('gel_school', 'gSchool', 'gSchool.registry_no = gStudent.lastschool_registrynumber');
                $sCon->fields('gStudent', array('id','lastschool_registrynumber','nextclass', 'delapp','name','studentsurname' ,'fatherfirstname' ,'motherfirstname' ,'regionaddress' ,'regiontk' ,'regionarea','telnum' ,'guardian_name' ,'guardian_surname','guardian_fathername ','guardian_mothername', 'birthdate', 'lastschool_schoolname','lastschool_class','lastschool_schoolyear','directorconfirm', 'created' ))
                  ->fields('gSchool', array('id','registry_no'))
                  ->condition('gSchool.id', $schoolid , '=')
                  ->condition('gStudent.nextclass', '4' , '=')
                  ->condition('gStudent.delapp', '0' , '=');
                $existingstudentPerSchool = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);



                    $list[] = array(
                        'class' => 4,
                        'taxi' => 'Δ Λυκείου',
                        'globalindex' => $i,
                        'size' => sizeof($studentPerSchool) + sizeof($existingstudentPerSchool),
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
            $classIdNew = 'Α';
        }
        elseif ($classId == 2)
        {
            $classIdNew = 'Β';
        }
        elseif ($classId == 3)
        {
            $classIdNew = 'Γ';
        }
        else
        {
            $classIdNew = 'Δ';

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

                $studentPerSchool = $this->entityTypeManager->getStorage('gelstudenthighschool')->loadByProperties(array('school_id' => $gelId, 'taxi' => $classIdNew));

                if ($classIdNew === "Α")
                {
                    $existingstudents =array();
                }
                else
                {
                $sCon = $this->connection->select('gel_student', 'gStudent');
                $sCon->leftJoin('gel_school', 'gSchool', 'gSchool.registry_no = gStudent.lastschool_registrynumber');
                $sCon->fields('gStudent', array('id','lastschool_registrynumber','nextclass', 'delapp','name','studentsurname' ,'fatherfirstname' ,'motherfirstname' ,'regionaddress' ,'regiontk' ,'regionarea','telnum' ,'guardian_name' ,'guardian_surname','guardian_fathername ','guardian_mothername', 'birthdate', 'lastschool_schoolname','lastschool_class','lastschool_schoolyear','directorconfirm', 'created' ))
                  ->fields('gSchool', array('id','registry_no'))
                  ->condition('gSchool.id', $gelId , '=')
                  ->condition('gStudent.nextclass', $classId , '=')
                  ->condition('gStudent.delapp', '0' , '=');
                $existingstudents = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                }

                 $this->logger->warning($sCon."existingstudents");


                }
                if ($studentPerSchool || $existingstudents) {
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
                                if ($gelStudent->name->value !== null)
                                {
                                    $name_decoded = $crypt->decrypt($gelStudent->name->value);
                                }
                                if ($gelStudent->studentsurname->value !== null)
                                {
                                $studentsurname_decoded = $crypt->decrypt($gelStudent->studentsurname->value);
                                }
                                if ($gelStudent->fatherfirstname->value !== null)
                                {
                                $fatherfirstname_decoded = $crypt->decrypt($gelStudent->fatherfirstname->value);
                                }
                                if ($gelStudent->motherfirstname->value !== null)
                                {
                                $motherfirstname_decoded = $crypt->decrypt($gelStudent->motherfirstname->value);
                                }
                                if ($gelStudent->regionaddress->value !== null)
                                {
                                $regionaddress_decoded = $crypt->decrypt($gelStudent->regionaddress->value);
                                }
                                if ($gelStudent->regiontk->value !== null)
                                {
                                $regiontk_decoded = $crypt->decrypt($gelStudent->regiontk->value);
                                }
                                if ($gelStudent->regionarea->value !== null)
                                {
                                $regionarea_decoded = $crypt->decrypt($gelStudent->regionarea->value);
                                }
                                if ($gelStudent->telnum->value !== null)
                                {
                                $telnum_decoded = $crypt->decrypt($gelStudent->telnum->value);
                                }
                                if ($gelStudent->guardian_name->value !== null)
                                {
                                $guardian_name_decoded = $crypt->decrypt($gelStudent->guardian_name->value);
                                }
                                if ($gelStudent->guardian_surname->value !== null)
                                {
                                $guardian_surname_decoded = $crypt->decrypt($gelStudent->guardian_surname->value);
                                }
                                if ($gelStudent->guardian_fathername->value !== null)
                                {
                                $guardian_fathername_decoded = $crypt->decrypt($gelStudent->guardian_fathername->value);
                                }
                                if ($gelStudent->guardian_mothername->value !== null)
                                {
                                $guardian_mothername_decoded = $crypt->decrypt($gelStudent->guardian_mothername->value);
                                }
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
                                'checkstatus' => $gelStudent -> directorconfirm ->value,
                                'lock_delete' => $lock_delete,
                                'created' => date('d/m/Y H:i', $gelStudent -> created ->value),
                                'choices' => $choices

                            );
                        }
                    }


                    foreach ($existingstudents as $object) {


                        $studentId = $object->id ;
                        $this->logger->warning($studentId."Aaaaa");
                        $gelStudents = $this->entityTypeManager->getStorage('gelstudenthighschool')->loadByProperties(array('student_id' => $studentId));
                        $gelStudent = reset($gelStudents);
                        if (!$gelStudents) {

                            $studentIdNew = $studentId;
                            $choices = "";
                            $studentchoices = $this->entityTypeManager->getStorage('gel_student_choices')->loadByProperties(array('student_id' => $studentId));

                            foreach ($studentchoices as $objects) {

                                    $choices = $choices."  ".($objects -> choice_id ->entity->get('name')->value)."/" ;
                                }

                            $crypt = new Crypt();
                            try {
                                if ($object->name!== null)
                                {
                                $name_decoded = $crypt->decrypt($object->name);
                                }
                                if ($object->studentsurname!== null)
                                {
                                $studentsurname_decoded = $crypt->decrypt($object->studentsurname );
                                }
                                 if ($object->fatherfirstname!== null)
                                  {
                                $fatherfirstname_decoded = $crypt->decrypt($object->fatherfirstname );
                                }
                                 if ($object->motherfirstname!== null) {
                                $motherfirstname_decoded = $crypt->decrypt($object->motherfirstname );
                                } if ($object->regionaddress!== null) {
                                $regionaddress_decoded = $crypt->decrypt($object->regionaddress );
                                } if ($object->regiontk!== null)
                                {
                                $regiontk_decoded = $crypt->decrypt($object->regiontk );
                                }
                                if ($object->regionarea!== null)
                                {
                                $regionarea_decoded = $crypt->decrypt($object->regionarea );
                                }
                                if ($object->telnum!== null) {
                                $telnum_decoded = $crypt->decrypt($object->telnum );
                                } if ($object->guardian_name!== null)
                                {
                                $guardian_name_decoded = $crypt->decrypt($object->guardian_name );
                                }
                                 if ($object->guardian_surname!== null)
                                  {
                                $guardian_surname_decoded = $crypt->decrypt($object->guardian_surname );
                                } if ($object->guardian_fathername!== null) {
                                $guardian_fathername_decoded = $crypt->decrypt($object->guardian_fathername );
                                } if ($object->guardian_mothername!== null) {
                                $guardian_mothername_decoded = $crypt->decrypt($object->guardian_mothername );
                                }
                            } catch (\Exception $e) {
                                $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                                return $this->respondWithStatus([
                                "message" => t("An unexpected error occured during DECODING data in getStudentPerSchool Method ")
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }

                            $list[] = array(
                                'id' => $object->id,
                                'name' => $name_decoded,
                                'studentsurname' => $studentsurname_decoded,
                                'fatherfirstname' => $fatherfirstname_decoded,
                                'motherfirstname' => $motherfirstname_decoded,
                                'guardian_name' => $guardian_name_decoded,
                                'guardian_surname' => $guardian_surname_decoded,
                                'guardian_fathername' => $guardian_fathername_decoded,
                                'guardian_mothername' => $guardian_mothername_decoded,
                                'lastschool_schoolname' => $object->lastschool_schoolname ,
                                'lastschool_schoolyear' => $object->lastschool_schoolyear ,
                                'lastschool_class' => $object->lastschool_class ,
                                'currentclass' => $classId,
                                'regionaddress' => $regionaddress_decoded,
                                'regiontk' => $regiontk_decoded,
                                'regionarea' => $regionarea_decoded,
                                //'graduation_year' => $object->graduation_year ,
                                'telnum' => $telnum_decoded,
                                'relationtostudent' => $relationtostudent_decoded,
                                //'birthdate' => substr($object->birthdate , 8, 10) . '/' . substr($object->birthdate , 6, 8) . '/' . substr($object->birthdate , 0, 4),
                                'birthdate' => date("d-m-Y", strtotime($object->birthdate )),
                                'checkstatus' => $object -> directorconfirm  ,
                                'lock_delete' => $lock_delete,
                                'created' => date('d/m/Y H:i', $object -> created  ),
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
                              ->fields('eSchool', array('id', 'name', 'unit_type_id','edu_admin_id','extra_unitid'))
                              ->condition('eSchool.edu_admin_id', $selectionId , '=')
                              ->condition(db_or()->condition('eSchool.unit_type_id', 4 , '=') ->condition('eSchool.extra_unitid',200,'='));
                 $sCon -> orderBy('eSchool.name', 'ASC');
                 $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            } elseif ($userRole === 'eduadmin') {

                 $sCon = $this->connection->select('gel_school', 'eSchool')
                              ->fields('eSchool', array('id', 'name', 'unit_type_id','edu_admin_id','extra_unitid'))
                              ->condition('eSchool.edu_admin_id', $selectionId , '=')
                              ->condition(db_or()->condition('eSchool.unit_type_id', 4 , '=') ->condition('eSchool.extra_unitid',200,'='));
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
            $CoursesA = $this->entityTypeManager->getStorage('gel_school')
                ->loadByProperties(array('id' => $schoolid));
            $CourseA = reset($CoursesA);
            if ($CourseA) {
                $reg_num = $CourseA->get('registry_no')->value;

                $studentPerSchool = $this->entityTypeManager->getStorage('gelstudenthighschool')
                    ->loadByProperties(array('school_id' => $schoolid, 'taxi' => 'Α'));


                    $list[] = array(
                        'id' => '1',
                        'name' => 'Α Λυκείου',
                        'size' => sizeof($studentPerSchool),
                        'categ' => $categ,
                        'classes' => 1,

                    );

                if ( $operation_shift != 'ΗΜΕΡΗΣΙΟ')
                  $taxi = 5;
                        else
                     $taxi = 2;
                 $studentPerSchool = $this->entityTypeManager->getStorage('gel_student')
                    ->loadByProperties(array('lastschool_registrynumber' => $reg_num, 'nextclass' => $taxi,'delapp' => '0'));
                 $studentPerSchoolNew = $this->entityTypeManager->getStorage('gelstudenthighschool')
                    ->loadByProperties(array('school_id' => $schoolid, 'taxi' => 'Β'));



                    $list[] = array(
                        'id' => '2-5',
                        'name' => 'Β Λυκείου',
                        'size' => sizeof($studentPerSchool)+ sizeof($studentPerSchoolNew),
                        'categ' => $categ,
                        'classes' => 1,

                    );


                if ( $operation_shift != 'ΗΜΕΡΗΣΙΟ')
                    $taxi = 6;
                        else
                     $taxi = 3;
                $studentPerSchool = $this->entityTypeManager->getStorage('gel_student')
                    ->loadByProperties(array('lastschool_registrynumber' => $reg_num, 'nextclass' => $taxi,delapp => '0'));
                $studentPerSchoolNew = $this->entityTypeManager->getStorage('gelstudenthighschool')
                    ->loadByProperties(array('school_id' => $schoolid, 'taxi' => 'Γ'));

                    $list[] = array(
                        'id' => '3-6',
                        'name' => 'Γ Λυκείου',
                        'size' => sizeof($studentPerSchool)+ sizeof($studentPerSchoolNew),
                        'categ' => $categ,
                        'classes' => 1,

                    );


                if ( $operation_shift != 'ΗΜΕΡΗΣΙΟ')
                {
                  $studentPerSchool = $this->entityTypeManager->getStorage('gel_student')
                    ->loadByProperties(array('lastschool_registrynumber' => $reg_num, 'nextclass' => 7));



                    $list[] = array(
                        'id' => '4',
                        'name' => 'Δ Λυκείου',
                        'size' => sizeof($studentPerSchool),
                        'categ' => $categ,
                        'classes' => 1,

                    );

                }

        $taxi = "Α";
        $selectionPerSchool = $this->entityTypeManager->getStorage('gel_choices')->loadByProperties(array());
        foreach ($selectionPerSchool as $object) {

          $choicenew = $object -> id();

          $sCon = $this->connection->select('gel_student_choices', 'gClassChoice');
          $sCon->leftJoin('gelstudenthighschool', 'gSchool',
    'gSchool.student_id = gClassChoice.student_id');
           $sCon->fields('gSchool', array( 'taxi', 'school_id'))
                ->fields('gClassChoice', array('choice_id'))
             ->condition('gClassChoice.choice_id', $choicenew)
             ->condition('gSchool.taxi', $taxi )
             ->condition('gSchool.school_id', $schoolid)
             ->groupBy('gClassChoice.choice_id')
             ->groupBy('gSchool.taxi')
             ->groupBy('gSchool.school_id')
             ;
        $sCon->addExpression('count(gClassChoice.student_id)', 'student_count');
        $results = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
        foreach ($results as $key ) {
                  $list[] = array(
                        'id' => 'ΕΠ',
                        'name' => 'Α Λυκείου-'.$object -> name ->value,
                        'size' => $key->student_count,
                        'categ' => $categ,
                        'classes' => 1,

                    );
                    }

            }

        if ( $operation_shift != 'ΗΜΕΡΗΣΙΟ')
                  $taxi = 5;
         else
                     $taxi = 2;
        $selectionPerSchool = $this->entityTypeManager->getStorage('gel_choices')->loadByProperties(array());
        foreach ($selectionPerSchool as $object) {

          $choicenew = $object -> id();

          $sCon = $this->connection->select('gel_student_choices', 'gClassChoice');
          $sCon->leftJoin('gel_student', 'gSchool',
           'gSchool.id = gClassChoice.student_id');
           $sCon->fields('gSchool', array( 'nextclass', 'lastschool_registrynumber  '))
                ->fields('gClassChoice', array('choice_id'))
             ->condition('gClassChoice.choice_id', $choicenew)
             ->condition('gSchool.nextclass', $taxi )
             ->condition('gSchool.lastschool_registrynumber ', $reg_num)
             -> condition('gSchool.delapp','0')
             ->groupBy('gClassChoice.choice_id')
             ->groupBy('gSchool.nextclass')
             ->groupBy('gSchool.lastschool_registrynumber ')
             ;
        $sCon->addExpression('count(gClassChoice.student_id)', 'student_count');
        $results = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
        foreach ($results as $key ) {
                  $list[] = array(
                        'name' => 'B Λυκείου-'.$object -> name ->value,
                        'size' => $key->student_count,
                        'categ' => $categ,
                        'classes' => 2-5,

                    );
                    }

            }

        if ( $operation_shift != 'ΗΜΕΡΗΣΙΟ')
                  $taxi = 6;
        else
                     $taxi = 3;
        $selectionPerSchool = $this->entityTypeManager->getStorage('gel_choices')->loadByProperties(array());
        foreach ($selectionPerSchool as $object) {

          $choicenew = $object -> id();

          $sCon = $this->connection->select('gel_student_choices', 'gClassChoice');
          $sCon->leftJoin('gel_student', 'gSchool',
           'gSchool.id = gClassChoice.student_id');
           $sCon->fields('gSchool', array( 'nextclass', 'lastschool_registrynumber  '))
                ->fields('gClassChoice', array('choice_id'))
             ->condition('gClassChoice.choice_id', $choicenew)
             ->condition('gSchool.nextclass', $taxi )
             ->condition('gSchool.lastschool_registrynumber ', $reg_num)
             -> condition('gSchool.delapp','0')
             ->groupBy('gClassChoice.choice_id')
             ->groupBy('gSchool.nextclass')
             ->groupBy('gSchool.lastschool_registrynumber ')
             ;
        $sCon->addExpression('count(gClassChoice.student_id)', 'student_count');
        $results = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
        foreach ($results as $key ) {
                  $list[] = array(

                        'name' => 'Γ Λυκείου-'.$object -> name ->value,
                        'size' => $key->student_count,
                        'categ' => $categ,
                        'classes' => 3,

                    );
                    }

            }



            }


        if ( $operation_shift != 'ΗΜΕΡΗΣΙΟ')
                {
            $taxi = 7;
        $selectionPerSchool = $this->entityTypeManager->getStorage('gel_choices')->loadByProperties(array());
        foreach ($selectionPerSchool as $object) {

          $choicenew = $object -> id();

          $sCon = $this->connection->select('gel_student_choices', 'gClassChoice');
          $sCon->leftJoin('gel_student', 'gSchool',
           'gSchool.id = gClassChoice.student_id');
           $sCon->fields('gSchool', array( 'nextclass', 'lastschool_registrynumber  '))
                ->fields('gClassChoice', array('choice_id'))
             ->condition('gClassChoice.choice_id', $choicenew)
             ->condition('gSchool.nextclass', $taxi )
             ->condition('gSchool.lastschool_registrynumber ', $reg_num)
             -> condition('gSchool.delapp','0')
             ->groupBy('gClassChoice.choice_id')
             ->groupBy('gSchool.nextclass')
             ->groupBy('gSchool.lastschool_registrynumber ')
             ;
        $sCon->addExpression('count(gClassChoice.student_id)', 'student_count');
        $results = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
        foreach ($results as $key ) {
                  $list[] = array(

                        'name' => 'Δ Λυκείου-'.$object -> name ->value,
                        'size' => $key->student_count,
                        'categ' => $categ,
                        'classes' => 3,

                    );
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



public function FindStudentsPerSchoolGym(Request $request)
{
    /*
    try{

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

            if (sizeof($studentPerSchool_gel)==0 &&sizeof($studentPerSchool_epal)==0) {
                return $this->respondWithStatus([
                    'message' => t('Students not found!'),
                ], Response::HTTP_NOT_FOUND);
            }
            else{
                if ($studentPerSchool_epal) {
                    foreach ($studentPerSchool_epal as $epalStudent) {

                        $studentId=intval($epalStudent->id->value);

                        $assignedEpals = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(['student_id'=> $studentId]);
                        if (sizeof($assignedEpals)>0){
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
                }



                if ($studentPerSchool_gel) {
                    foreach ($studentPerSchool_gel as $gelStudent) {

                        $studentId=intval($gelStudent->id->value);

                        $assignedGels = $this->entityTypeManager->getStorage('gelstudenthighschool')->loadByProperties(['student_id'=> $studentId]);
                        if (sizeof($assignedGels)>0){
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


                    }
                }

                return $this->respondWithStatus($list, Response::HTTP_OK);
            }



        }
    } catch (\Exception $e) {
        $this->logger->warning($e->getMessage());
        return $this->respondWithStatus([
            'message' => t('Unexpected Error'),
        ], Response::HTTP_FORBIDDEN);
   }
   */

   return $this->respondWithStatus([
       'message' => t('Unexpected Error'),
   ], Response::HTTP_FORBIDDEN);
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


public function ConfirmStudents(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return $this->respondWithStatus(['message' => t('Method Not Allowed')], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $authToken = $request->headers->get('PHP_AUTH_USER');
        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if ($user) {
            $userRoles = $user->getRoles();
            $userRole = '';
            foreach ($userRoles as $tmpRole) {
                if ($tmpRole === 'gel') {
                    $userRole = $tmpRole;
                }
            }
            if ($userRole === 'gel') {
                if ($content = $request->getContent()) {
                    $postData = json_decode($content);
                    $arr = $postData->students;
                    $type = $postData->type;
                    $valnew = intval($arr);
                    $typen = intval($type);
                    $studentForConfirm = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(array('id' => $valnew, 'delapp' => '0' ));
                    $studentConfirm = reset($studentForConfirm);
                    if ($studentConfirm) {
                        if ($typen === 1) {
                            $studentConfirm->set('directorconfirm', 1);
                            $studentConfirm->save();
                            return $this->respondWithStatus(['message' => t('saved')], Response::HTTP_OK);
                        } elseif ($typen === 2) {
                            $studentConfirm->set('directorconfirm', 0);
                            $studentConfirm->save();
                            return $this->respondWithStatus(['message' => t('saved')], Response::HTTP_OK);
                        } elseif ($typen === 3) {
                            unset($studentConfirm->{directorconfirm});
                            $studentConfirm->save();
                            return $this->respondWithStatus(['message' => t('saved')], Response::HTTP_OK);
                        } else {
                            return $this->respondWithStatus(['message' => t('Bad request')], Response::HTTP_FORBIDDEN);
                        }
                    } else {
                        return $this->respondWithStatus(['message' => t('Student not found')], Response::HTTP_FORBIDDEN);
                    }
                } else {
                    return $this->respondWithStatus(['message' => t('post with no data')], Response::HTTP_BAD_REQUEST);
                }
            } else {
                return $this->respondWithStatus(['error_code' => 4003], Response::HTTP_FORBIDDEN);
            }
        } else {
            return $this->respondWithStatus(['message' => t('GEL user not found')], Response::HTTP_FORBIDDEN);
        }
    }


  public function Initialization(Request $request)
    {

        if (!$request->isMethod('POST')) {
            return $this->respondWithStatus([
                "message" => t("Method Not Allowed")
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


        if (false === in_array('eduadmin', $user->getRoles())) {
            return $this->respondWithStatus([
                'message' => t("User Invalid Role"),
            ], Response::HTTP_FORBIDDEN);
        }

        $dide_id = $user->init->value;
        $this->logger->warning($dide_id."pppp");
       $transaction = $this->connection->startTransaction();
        $this->logger->warning("$dide_id."."pppp");
        try {
            $this->logger->warning("3");
            //initialazation A class
           $sCon = $this->connection->select('gel_student', 'gStudent');
           $sCon->leftJoin('gel_school', 'gSchool', 'gSchool.registry_no = gStudent.lastschool_registrynumber');
            $sCon->fields('gStudent', array('id', 'lastschool_registrynumber','lastschool_unittypeid','lastschool_class','nextclass','second_period'))
                ->fields('gSchool', array('id', 'edu_admin_id', 'registry_no'))
                ->condition('gStudent.lastschool_unittypeid', 3 , '=')
                ->condition('gStudent.lastschool_class', "3" , '=')
                ->condition('gStudent.delapp', 0, '=')
                ->condition(db_or()->condition('gStudent.nextclass', "1")->condition('gStudent.nextclass', "4"))
                ->condition('gSchool.edu_admin_id', $dide_id , '=');

           $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
          $this->logger->warning($sCon."4");

           foreach ($schools as $school) {

            $student = array(
                'langcode' => 'el',
                'id' => $school ->id,
                'student_id' => $school ->id,
                'taxi' => $school-> nextclass,
                'dide' => $dide_id,
                'second_period' =>$school -> second_period,
            );
             $entity_storage_student = $this->entityTypeManager->getStorage('gelstudenthighschool');
            $entity_object = $entity_storage_student->create($student);
            $entity_storage_student->save($entity_object);
        }

                   $student = array();
             //initialazation B class from epal
            $sCon = $this->connection->select('gel_student', 'gStudent');
           $sCon->leftJoin('eepal_school_field_data', 'gSchool', 'gSchool.registry_no = gStudent.lastschool_registrynumber');
            $sCon->fields('gStudent', array('id', 'lastschool_registrynumber','lastschool_unittypeid','lastschool_class','nextclass','second_period'))
                ->fields('gSchool', array('id', 'edu_admin_id', 'registry_no'))
                ->condition('gStudent.lastschool_unittypeid', 5, '=')
                ->condition(db_or()->condition('gStudent.nextclass', "2")->condition('gStudent.nextclass', "6"))
                ->condition('gStudent.delapp', 0, '=')
                ->condition('gSchool.edu_admin_id', $dide_id , '=');

                $this->logger->warning("5");

           $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

           $student = array();
           foreach ($schools as $school) {

            $student = array(
                'langcode' => 'el',
                'id' => $school ->id,
                'student_id' => $school ->id,
                'taxi' => $school-> nextclass,
                'dide' => $dide_id,
                'second_period' =>$school -> second_period,
            );
             $entity_storage_student = $this->entityTypeManager->getStorage('gelstudenthighschool');
            $entity_object = $entity_storage_student->create($student);
            $entity_storage_student->save($entity_object);
        }
                   $student = array();
             //initialazation B class from esperina
          $sCon = $this->connection->select('gel_student', 'gStudent');
           $sCon->leftJoin('gel_school', 'gSchool', 'gSchool.registry_no = gStudent.lastschool_registrynumber');
            $sCon->fields('gStudent', array('id', 'lastschool_registrynumber','lastschool_unittypeid','lastschool_class','nextclass','second_period'))
                ->fields('gSchool', array('id', 'edu_admin_id', 'registry_no','extra_unitid'))

                ->condition('gStudent.lastschool_unittypeid', 4 , '=')
                ->condition('gStudent.nextclass', "2",'=')
                ->condition('gStudent.delapp', 0, '=')
                ->condition('gSchool.edu_admin_id', $dide_id , '=')
                ->condition('gSchool.extra_unitid',400,'=');

           $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
          $this->logger->warning("6");

           foreach ($schools as $school) {

            $student = array(
                'langcode' => 'el',
                'id' => $school ->id,
                'student_id' => $school ->id,
                'taxi' => $school-> nextclass,
                'dide' => $dide_id,
                'second_period' =>$school -> second_period,
            );
             $entity_storage_student = $this->entityTypeManager->getStorage('gelstudenthighschool');
            $entity_object = $entity_storage_student->create($student);
            $entity_storage_student->save($entity_object);

        }


       $student = array();
             //initialazation B class from idiwtika
          $sCon = $this->connection->select('gel_student', 'gStudent');
           $sCon->leftJoin('gel_school', 'gSchool', 'gSchool.registry_no = gStudent.lastschool_registrynumber');
            $sCon->fields('gStudent', array('id', 'lastschool_registrynumber','lastschool_unittypeid','lastschool_class','nextclass','second_period'))
                ->fields('gSchool', array('id', 'edu_admin_id', 'registry_no','extra_unitid'))

                ->condition('gStudent.lastschool_unittypeid', 4 , '=')
                ->condition('gStudent.lastschool_class', "1",'=')
                ->condition('gStudent.nextclass', "2",'=')
                ->condition('gStudent.delapp', 0, '=')
                ->condition('gSchool.edu_admin_id', $dide_id , '=')
                ->condition('gSchool.extra_unitid',300,'=');
                $this->logger->warning("6");
           $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);


           foreach ($schools as $school) {

            $student = array(
                'langcode' => 'el',
                'id' => $school ->id,
                'student_id' => $school ->id,
                'taxi' => $school-> nextclass,
                'dide' => $dide_id,
                'second_period' =>$school -> second_period,
            );
             $entity_storage_student = $this->entityTypeManager->getStorage('gelstudenthighschool');
            $entity_object = $entity_storage_student->create($student);
            $entity_storage_student->save($entity_object);

        }


             $student = array();
             //initialazation C class from esperina
            $sCon = $this->connection->select('gel_student', 'gStudent');
            $sCon->leftJoin('gel_school', 'gSchool', 'gSchool.registry_no = gStudent.lastschool_registrynumber');
            $sCon->fields('gStudent', array('id', 'lastschool_registrynumber','lastschool_unittypeid','lastschool_class','nextclass','second_period'))
                ->fields('gSchool', array('id', 'edu_admin_id', 'registry_no','extra_unitid'))
                ->condition('gStudent.lastschool_unittypeid', 4 , '=')
                ->condition('gStudent.nextclass', "3",'=')
                ->condition('gStudent.delapp', 0, '=')
                ->condition('gSchool.edu_admin_id', $dide_id , '=')
                ->condition('gSchool.extra_unitid',400,'=');

           $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
          $this->logger->warning("7");

           foreach ($schools as $school) {

            $student = array(
                'langcode' => 'el',
                'id' => $school ->id,
                'student_id' => $school ->id,
                'taxi' => $school-> nextclass,
                'dide' => $dide_id,
                'second_period' =>$school -> second_period,
            );

             $entity_storage_student = $this->entityTypeManager->getStorage('gelstudenthighschool');
            $entity_object = $entity_storage_student->create($student);
            $entity_storage_student->save($entity_object);
        }
                   $student = array();
            //initialazation C class to esperina
            $sCon = $this->connection->select('gel_student', 'gStudent');
           $sCon->leftJoin('gel_school', 'gSchool', 'gSchool.registry_no = gStudent.lastschool_registrynumber');
            $sCon->fields('gStudent', array('id', 'lastschool_registrynumber','lastschool_unittypeid','lastschool_class','nextclass','second_period'))
                ->fields('gSchool', array('id', 'edu_admin_id', 'registry_no','extra_unitid'))
                ->condition('gStudent.lastschool_unittypeid', 4 , '=')
                ->condition('gStudent.lastschool_class', "2",'=')
                ->condition('gStudent.nextclass', "7",'=')
                ->condition('gStudent.delapp', 0, '=')
                ->condition('gSchool.edu_admin_id', $dide_id , '=')
                 ->condition('gSchool.extra_unitid',400,'=');

        $this->logger->warning("8");
           $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);


           foreach ($schools as $school) {

            $student = array(
                'langcode' => 'el',
                'id' => $school ->id,
                'student_id' => $school ->id,
                'taxi' => $school-> nextclass,
                'dide' => $dide_id,
                'second_period' =>$school -> second_period,
            );

            $entity_storage_student = $this->entityTypeManager->getStorage('gelstudenthighschool');
            $entity_object = $entity_storage_student->create($student);
            $entity_storage_student->save($entity_object);
        }

    $this->logger->warning("9prin");
               $student = array();
             //initialazation C class from idiwt
         $sCon = $this->connection->select('gel_student', 'gStudent');
           $sCon->leftJoin('gel_school', 'gSchool', 'gSchool.registry_no = gStudent.lastschool_registrynumber');
            $sCon->fields('gStudent', array('id', 'lastschool_registrynumber','lastschool_unittypeid','lastschool_class','nextclass','second_period'))
                ->fields('gSchool', array('id', 'edu_admin_id', 'registry_no','extra_unitid'))
                ->condition('gStudent.lastschool_unittypeid', 4 , '=')
                ->condition('gStudent.nextclass', "3",'=')
                ->condition('gStudent.delapp', 0, '=')
                ->condition('gSchool.edu_admin_id', $dide_id , '=')
                ->condition('gSchool.extra_unitid',300,'=');

           $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
          $this->logger->warning("9");

           foreach ($schools as $school) {

            $student = array(
                'langcode' => 'el',
                'id' => $school ->id,
                'student_id' => $school ->id,
                'taxi' => $school-> nextclass,
                'dide' => $dide_id,
                'second_period' =>$school -> second_period,
            );
             $entity_storage_student = $this->entityTypeManager->getStorage('gelstudenthighschool');
            $entity_object = $entity_storage_student->create($student);
            $entity_storage_student->save($entity_object);

        }


    }
    catch (\Exception $e)

        {
             $this->logger->warning($e->getMessage());
            $transaction->rollback();


            return $this->respondWithStatus([
                "error_code" => 5001
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

     return $this->respondWithStatus('ok', Response::HTTP_OK);


    }



  public function Initialized(Request $request)
    {


        $authToken = $request->headers->get('PHP_AUTH_USER');
        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if (!$user) {
            return $this->respondWithStatus([
                'message' => t("User not found"),
            ], Response::HTTP_FORBIDDEN);
        }


        if (false === in_array('eduadmin', $user->getRoles())) {
            return $this->respondWithStatus([
                'message' => t("User Invalid Role"),
            ], Response::HTTP_FORBIDDEN);
        }

        $dide_id = $user->init->value;

        try {
           $student = array();
           $sCon = $this->connection->select('gelstudenthighschool', 'gStudent');
            $sCon->fields('gStudent', array('dide'))
                ->condition('gStudent.dide', $dide_id , '=');
           $schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);


          if ($schools)
              $student = array('answer' => true);
           else
              $student = array('answer' => false);

          return $this->respondWithStatus($student, Response::HTTP_OK);
        }

    catch (\Exception $e)

        {


            return $this->respondWithStatus([
                "error_code" => 5001
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }




    }





}
