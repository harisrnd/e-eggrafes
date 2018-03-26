<?php

namespace Drupal\epal\Controller;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Database\Connection;

use Drupal\epal\Crypt;

class SmallClassDistribution extends ControllerBase
{
    const SUCCESS = 0;
    const ERROR_DB = -1;
    const NO_CLASS_LIMIT_DOWN = -2;
    const SMALL_CLASS = 1;
    const NON_SMALL_CLASS = 2;
    const IS_FIRST_PERIOD = false;
    const IS_SECOND_PERIOD = true;


    protected $entityTypeManager;
    protected $logger;
    protected $connection;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        Connection $connection,
        LoggerChannelFactoryInterface $loggerChannel
    ) {
        $this->entityTypeManager = $entityTypeManager;
        $this->connection = $connection;
        $this->logger = $loggerChannel->get('epal-school');
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('entity_type.manager'),
            $container->get('database'),
            $container->get('logger.factory')
        );
    }




    public function findStatus($id, $classId, $sector, $specialit)
    {
        $schoolid = $id;
        $SchoolCats = $this->entityTypeManager->getStorage('eepal_school')->loadByProperties(array('id' => $schoolid));
        $SchoolCat = reset($SchoolCats);
        if ($SchoolCat) {
            $categ = $SchoolCat->metathesis_region->value;
            $operation_shift = $SchoolCat -> operation_shift -> value;
        } else {
            $categ = '-';
            $operation_shift ='-';
        }

        if ($classId == 1){

            $studentPerSchool = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('epal_id' => $schoolid, 'specialization_id' => -1, 'currentclass' => 1));

            $size = sizeof($studentPerSchool);
                        return $size;

        }
        elseif ($classId == 2)
        {
            $studentPerSchool = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('epal_id' => $schoolid, 'specialization_id' => $sector, 'currentclass' => 2));
                    $size = sizeof($studentPerSchool);
                        return $size;
        }
        elseif ($classId == 3)
        {
                $studentPerSchool = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('epal_id' => $schoolid, 'specialization_id' => $specialit, 'currentclass' => 3));
                $size = sizeof($studentPerSchool);
                    return $size;
        }
        else
        {
            if ($operation_shift == 'ΕΣΠΕΡΙΝΟ')
            {
                $studentPerSchool = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('epal_id' => $schoolid, 'specialization_id' => $specialit, 'currentclass' => 4));
                    $size = sizeof($studentPerSchool);
                        return $size;

            }
        }

    }


    protected function getLimit($name, $categ)
    {
        static $limits = array();

        $key = "{$name}_{$categ}";
        if (isset($limits[$key])) {
            $limit = $limits[$key];
        } else {
            $limit_down = $this->entityTypeManager->getStorage('epal_class_limits')->loadByProperties(array('name' => $name, 'category' => $categ));
            $limitdown = reset($limit_down);
            if ($limitdown) {
                $limit = $limitdown->limit_down->value;
            } else {
                $limit = -1;
            }
            $limits[$key] = $limit;
        }

        return $limit;
    }



    private function respondWithStatus($arr, $s)
    {
        $res = new JsonResponse($arr);
        $res->setStatusCode($s);

        return $res;
    }


public function findGroupsForMerging(Request $request,$firstid, $classId, $sector, $specialit)
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');

        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if ($user) {
            $selectionId = $user->init->value;
            $userRoles = $user->getRoles();
            $userRole = '';
            foreach ($userRoles as $tmpRole) {
                if ( ($tmpRole === 'regioneduadmin') || ($tmpRole === 'eduadmin')) {
                    $userRole = $tmpRole;
                }
            }

            if ($userRole === '') {
                return $this->respondWithStatus([
                    'error_code' => 4003,
                    "message" => t("1")
                ], Response::HTTP_FORBIDDEN);
            } elseif ($userRole === 'regioneduadmin') {
                $schools = $this->entityTypeManager
                    ->getStorage('eepal_school')
                    ->loadByProperties(array('region_edu_admin_id' => $selectionId));
            } elseif ($userRole === 'eduadmin') {
                $schools = $this->entityTypeManager
                    ->getStorage('eepal_school')
                    ->loadByProperties(array('edu_admin_id' => $selectionId));
            } else {
                $schools = [];
            }

            if ($schools) {
                $list = array();
                foreach ($schools as $object)
                {
                      $categ = $object->metathesis_region->value;
                      if ($classId == 1)
                      {

                            if ($firstid != $object->id())
                            {
                            $status = $this-> findStatus($object->id(),$classId, $sector, $specialit);
                            $stat = intval($status);
                                $list[] = array(
                                'id' => $object->id(),
                                'name' => $object->name->value,
                                'tmhma' => 'Ά Λυκείου',
                                'studentcount' => $stat,
                                    );
                            }



                      }
                       elseif ($classId == 2)
                       {


                      $courses =  $this->entityTypeManager->getStorage('eepal_sectors_in_epal')->loadByProperties(array('epal_id' => $object->id(), 'sector_id' => $sector));
                      if ($courses){
                      foreach ($courses as $key)
                        {
                            if ($firstid != $object->id())
                            {
                            $status = $this-> findStatus($object->id(),$classId, $sector, $specialit);
                            $stat = intval($status);
                                $list[] = array(
                                'id' => $object->id(),
                                'name' => $object->name->value,
                                'tmhma' => $key->sector_id-> entity->get('name')->value,
                                'studentcount' => $stat,
                                    );
                            }

                        }

                    }


                       }
                      else
                       {
                      $courses =  $this->entityTypeManager->getStorage('eepal_specialties_in_epal')->loadByProperties(array('epal_id' => $object->id(), 'specialty_id' => $specialit));
                      if ($courses){
                      foreach ($courses as $key)
                        {
                            if ($firstid != $object->id())
                            {
                            $status = $this-> findStatus($object->id(),$classId, $sector, $specialit);
                            $stat = intval($status);
                                $list[] = array(
                                'id' => $object->id(),
                                'name' => $object->name->value,
                                'tmhma' => $key->specialty_id-> entity->get('name')->value,
                                'studentcount' => $stat,
                                    );
                            }

                        }

                    }
                    }
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



    public function merging(Request $request, $classId, $sector, $speciality)
    {

        if (!$request->isMethod('POST')) {
            return $this->respondWithStatus([
                    "message" => t("Method Not Allowed")
                ], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if ($user) {

            $userRoles = $user->getRoles();
            $userRole = '';
            foreach ($userRoles as $tmpRole) {
                if ( ($tmpRole === 'regioneduadmin') || ($tmpRole === 'eduadmin')) {
                    $userRole = $tmpRole;
                }
            }
            $postData = null;
            if ($content = $request->getContent())
            {
                $postData = json_decode($content);

                $firstid = $postData->firstid;
                $secondid = $postData->secondid;
                if ($classId == 1){
                $recordsformerge = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('epal_id' => $secondid, 'specialization_id' => -1));
                }
                elseif ($classId == 2)
                {
                    $recordsformerge = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('epal_id' => $secondid, 'specialization_id' => $sector, 'currentclass' => 2));

                }
                else
                {
                    $recordsformerge = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('epal_id' => $secondid, 'specialization_id' => $speciality, 'currentclass' => $classId));

                }


                if ($recordsformerge)
                {
                    foreach ($recordsformerge as $recordsformerges)
                    {
                        $secondmerge = $recordsformerges -> getepalid() ;

                        if (($secondmerge == '0') || ($secondmerge == null))
                        {
                            $recordsformerges->set('initial_epal_id', $secondid);
                            $recordsformerges->set('merging_role', $userRole);
                        }

                        $recordsformerges->set('epal_id', $firstid);
                        $recordsformerges->save();
                    }



                } else {
                    return $this->respondWithStatus([
                        'error_code' => '1001',
                    ], Response::HTTP_FORBIDDEN);
                }
                return $this->respondWithStatus([
                    'error_code' => '0',
                ], Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'error_code' => '1002',
                ], Response::HTTP_BAD_REQUEST);
            }

        } else {
            return $this->respondWithStatus([
                    'error_code' => '1003',
                ], Response::HTTP_FORBIDDEN);
        }
    }


public function findMergingSchoolsforUndo(Request $request, $classId, $sector, $specialit)
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');

        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if ($user) {
            $selectionId = $user->init->value;
            $userRoles = $user->getRoles();
            $userRole = '';
            foreach ($userRoles as $tmpRole) {
                if ( ($tmpRole === 'regioneduadmin') || ($tmpRole === 'eduadmin')) {
                    $userRole = $tmpRole;
                }
            }

            if ($userRole === '') {
                return $this->respondWithStatus([
                    'error_code' => 4003,
                    "message" => t("1")
                ], Response::HTTP_FORBIDDEN);
            } elseif ($userRole === 'regioneduadmin') {
                $schools = $this->entityTypeManager
                    ->getStorage('eepal_school')
                    ->loadByProperties(array('region_edu_admin_id' => $selectionId));
            } elseif ($userRole === 'eduadmin') {
                $schools = $this->entityTypeManager
                    ->getStorage('eepal_school')
                    ->loadByProperties(array('edu_admin_id' => $selectionId));
            } else {
                $schools = [];
            }

            if ($schools) {
                $list = array();
                foreach ($schools as $object)
                {
                    $schoolid = $object -> id();
                    if ($classId == 1)
                    {

                        $mergedSchool = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('initial_epal_id' => $schoolid, 'specialization_id' => -1, 'currentclass' => 1));

                             $mergedSchools = reset($mergedSchool);
                                if ($mergedSchools)
                                {
                                    $indnew = $mergedSchools -> epal_id -> entity ->id();
                                    if ($schoolid != $indnew )
                                    {
                                     $list[] = array(
                                        'id' => $schoolid,
                                        'idnew' => $indnew,
                                        'name' => $object->name->value,
                                        'namenew' => $mergedSchools -> epal_id ->entity->get('name')->value,
                                            );
                                    }
                                }

                    }
                    elseif ($classId == 2)
                    {

                        $mergedSchool = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('initial_epal_id' => $schoolid, 'specialization_id' => $sector, 'currentclass' => 2));

                             $mergedSchools = reset($mergedSchool);
                                if ($mergedSchools)
                                {
                                $indnew = $mergedSchools -> epal_id -> entity ->id();
                                if ($schoolid != $indnew )
                                 {
                                 $list[] = array(
                                    'id' => $schoolid,
                                    'idnew' => $idnew,
                                    'name' => $object->name->value,
                                    'namenew' => $mergedSchools -> epal_id ->entity->get('name')->value,

                                );
                                }

                                }

                    }
                    elseif ($classId == 3)
                    {

                        $mergedSchool = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('initial_epal_id' => $schoolid, 'specialization_id' => $specialit, 'currentclass' => 3));

                             $mergedSchools = reset($mergedSchool);
                                if ($mergedSchools)
                                {
                                $indnew = $mergedSchools -> epal_id -> entity ->id();
                                if ($schoolid != $indnew )
                                {
                                  $list[] = array(
                                    'id' => $schoolid,
                                    'idnew' => $idnew,
                                    'name' => $object->name->value,
                                    'namenew' => $mergedSchools -> epal_id ->entity->get('name')->value,

                                        );
                                }
                                }

                    }
                    else
                    {

                        $mergedSchool = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('initial_epal_id' => $schoolid, 'specialization_id' => $specialit, 'currentclass' => 4));

                             $mergedSchools = reset($mergedSchool);
                                if ($mergedSchools)
                                {
                                $indnew = $mergedSchools -> epal_id -> entity ->id();
                                if ($schoolid != $indnew )
                                 {
                                $list[] = array(
                                    'id' => $schoolid,
                                    'idnew' => $indnew,
                                    'name' => $object->name->value,
                                    'namenew' => $mergedSchools -> epal_id ->entity->get('name')->value,

                                );
                             }
                                }

                    }




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



 public function findSmallGroups(Request $request, $classId, $sector, $specialit)
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');

        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if ($user) {
            $selectionId = $user->init->value;
            $userRoles = $user->getRoles();
            $userRole = '';
            foreach ($userRoles as $tmpRole) {
                if ( ($tmpRole === 'regioneduadmin') || ($tmpRole === 'eduadmin')) {
                    $userRole = $tmpRole;
                }
            }

            if ($userRole === '') {
                return $this->respondWithStatus([
                    'error_code' => 4003,
                    "message" => t("1")
                ], Response::HTTP_FORBIDDEN);
            } elseif ($userRole === 'regioneduadmin') {
                $schools = $this->entityTypeManager
                    ->getStorage('eepal_school')
                    ->loadByProperties(array('region_edu_admin_id' => $selectionId));
            } elseif ($userRole === 'eduadmin') {
                $schools = $this->entityTypeManager
                    ->getStorage('eepal_school')
                    ->loadByProperties(array('edu_admin_id' => $selectionId));
            } else {
                $schools = [];
            }

            if ($schools) {
                $list = array();
                foreach ($schools as $object)
                {
                      $categ = $object->metathesis_region->value;
                      if ($classId == 1)
                      {
                            $limit = $this->getLimit(1, $categ);
                            $status = $this-> findStatus($object->id(),$classId, $sector, $specialit);
                            $stat = intval($status);
                            $lim = intval($limit);
                            if ($stat <= $limit && $stat !=0)
                            {
                                $list[] = array(
                                'id' => $object->id(),
                                'name' => $object->name->value,
                                'tmhma' => 'Ά Λυκείου',
                                'studentcount' => $stat,
                                    );
                            }
                      }
                      elseif ($classId ==2)
                      {
                           $limit = $this->getLimit(2, $categ);
                           $courses =  $this->entityTypeManager->getStorage('eepal_sectors_in_epal')->loadByProperties(array('epal_id' => $object->id(), 'sector_id' => $sector));
                          if ($courses){
                          foreach ($courses as $key)
                            {
                                $status = $this-> findStatus($object->id(),$classId, $sector, $specialit);
                                $stat = intval($status);
                                $lim = intval($limit);
                                if ($stat < $limit && $stat !=0 )
                                {
                                    $list[] = array(
                                    'id' => $object->id(),
                                    'name' => $object->name->value,
                                    'tmhma' => $key->sector_id-> entity->get('name')->value,
                                    'studentcount' => $stat,
                                        );
                                }
                            }

                      }
                     }
                      elseif ($classId == 3 || $classId == 4)
                       {
                      $limit = $this->getLimit($classId, $categ);
                      $courses =  $this->entityTypeManager->getStorage('eepal_specialties_in_epal')->loadByProperties(array('epal_id' => $object->id(), 'specialty_id' => $specialit));
                      if ($courses){
                      foreach ($courses as $key)
                        {
                            $status = $this-> findStatus($object->id(),$classId, $sector, $specialit);
                            $stat = intval($status);
                            $lim = intval($limit);
                            if ($stat < $limit && $stat !=0)
                            {
                                $list[] = array(
                                'id' => $object->id(),
                                'name' => $object->name->value,
                                'tmhma' => $key->specialty_id-> entity->get('name')->value,
                                'studentcount' => $stat,
                                    );
                            }
                        }
                        }
                    }


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



  public function UndoMerge(Request $request, $classId, $sector, $speciality)
    {

        if (!$request->isMethod('POST')) {
            return $this->respondWithStatus([
                    "message" => t("Method Not Allowed")
                ], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if ($user) {


            $userRoles = $user->getRoles();
            $userRole = '';
            foreach ($userRoles as $tmpRole) {
                if ( ($tmpRole === 'regioneduadmin') || ($tmpRole === 'eduadmin')) {
                    $userRole = $tmpRole;
                }
            }

            $postData = null;
            if ($content = $request->getContent())
            {
                $postData = json_decode($content);

                $firstid = $postData->firstid;
                $secondid = $postData->secondid;

                if ($classId == 1){
                $recordsforundomerge = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('initial_epal_id' => $firstid, 'specialization_id' => -1));
                }
                elseif ($classId == 2)
                {
                    $recordsforundomerge = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('initial_epal_id' => $firstid, 'specialization_id' => $sector, 'currentclass' => 2));

                }
                else
                {
                    $recordsforundomerge = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('initial_epal_id' => $firstid, 'specialization_id' => $speciality, 'currentclass' => $classId));

                }


                if ($recordsforundomerge)
                {
                    $merging_role = reset($recordsforundomerge);
                    $role_forundomerge = $merging_role -> merging_role -> value;
                    if (( $role_forundomerge == 'regioneduadmin') && ($userRole == 'eduadmin'))
                    {
                        return $this->respondWithStatus([
                        'error_code' => '2500',
                        ], Response::HTTP_FORBIDDEN);
                    }
                    else
                    {
                    foreach ($recordsforundomerge as $recordsforundomerges)
                        {
                            $recordsforundomerges->set('initial_epal_id', 0);
                            $recordsforundomerges->set('epal_id', $firstid);
                            $recordsforundomerges->set('merging_role', null);
                            $recordsforundomerges->save();
                        }
                    }

                } else {
                    return $this->respondWithStatus([
                        'error_code' => '1001',
                    ], Response::HTTP_FORBIDDEN);
                }
                return $this->respondWithStatus([
                    'error_code' => '0' ,
                ], Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'error_code' => '1002',
                ], Response::HTTP_BAD_REQUEST);
            }

        } else {
            return $this->respondWithStatus([
                    'error_code' => '1003',
                ], Response::HTTP_FORBIDDEN);
        }
    }

    public function UndoMergeAll(Request $request)
    {
         if (!$request->isMethod('POST')) {
            return $this->respondWithStatus([
                    "message" => t("Method Not Allowed")
                ], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if ($user) {
            $selectionId = $user->init->value;
            $userRoles = $user->getRoles();
            $userRole = '';
            foreach ($userRoles as $tmpRole) {
                if ( ($tmpRole === 'regioneduadmin') || ($tmpRole === 'eduadmin')) {
                    $userRole = $tmpRole;
                }
            }
            if ($userRole === '') {
                return $this->respondWithStatus([
                    'error_code' => 4003,
                    "message" => t("1")
                ], Response::HTTP_FORBIDDEN);
            } elseif ($userRole === 'regioneduadmin') {
                $schools = $this->entityTypeManager
                    ->getStorage('eepal_school')
                    ->loadByProperties(array('region_edu_admin_id' => $selectionId));
            } elseif ($userRole === 'eduadmin') {
                $schools = $this->entityTypeManager
                    ->getStorage('eepal_school')
                    ->loadByProperties(array('edu_admin_id' => $selectionId));
            } else {
                $schools = [];
            }

            if ($schools) {
            foreach ($schools as $object) {

                $old_schoolid = $object->id() ;
                if ($userRole == 'regioneduadmin')
                $recordsforundomerge = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('initial_epal_id' => $old_schoolid ));
                else
                $recordsforundomerge = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('initial_epal_id' => $old_schoolid, 'merging_role' => 'eduadmin'));



                if ($recordsforundomerge)
                {

                    foreach ($recordsforundomerge as $recordsforundomerges)
                        {
                            $recordsforundomerges->set('initial_epal_id', 0);
                            $recordsforundomerges->set('epal_id', $old_schoolid);
                            $recordsforundomerges->set('merging_role', null);
                            $recordsforundomerges->save();
                        }
                }

               }
                return $this->respondWithStatus([
                    'error_code' => '0' ,
                ], Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'error_code' => '1002',
                ], Response::HTTP_BAD_REQUEST);
            }

        } else {
            return $this->respondWithStatus([
                    'error_code' => '1003',
                ], Response::HTTP_FORBIDDEN);
        }

    }


 public function ApproveClasses(Request $request)
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
                if ($tmpRole === 'regioneduadmin') {
                    $userRole = $tmpRole;
                }
            }
            if ($userRole === 'regioneduadmin') {
                if ($content = $request->getContent()) {
                    $postData = json_decode($content);
                    $taxi = $postData -> taxi;
                    $arr = $postData -> classid;
                    $type = $postData -> type;
                    $valnew = intval($arr);
                    $typen = intval($type);
                    if ($taxi === 1)
                    $classesForConfirm = $this->entityTypeManager->getStorage('eepal_school')->loadByProperties(['id' => $valnew]);
                    if ($taxi === 2)
                    $classesForConfirm = $this->entityTypeManager->getStorage('eepal_sectors_in_epal')->loadByProperties(['id' => $valnew]);
                    if ($taxi === 3 || $taxi === 4)
                    $classesForConfirm = $this->entityTypeManager->getStorage('eepal_specialties_in_epal')->loadByProperties(['id' => $valnew]);

                    $classConfirm = reset($classesForConfirm);
                    if ($classConfirm) {
                        if ($typen === 1) {
                            if ($taxi === 1)
                               $classConfirm->set('approved_a', 1);
                            if ($taxi === 2)
                               $classConfirm->set('approved_sector', 1);
                            if ($taxi === 3)
                               $classConfirm->set('approved_speciality', 1);
                            if ($taxi === 4)
                               $classConfirm->set('approved_speciality_d', 1);
                            $classConfirm->save();
                            return $this->respondWithStatus(['message' => t('saved')], Response::HTTP_OK);
                        } elseif ($typen === 2) {
                            if ($taxi === 1)
                               $classConfirm->set('approved_a', 0);
                            if ($taxi === 2)
                               $classConfirm->set('approved_sector', 0);
                            if ($taxi === 3)
                               $classConfirm->set('approved_speciality', 0);
                            if ($taxi === 4)
                               $classConfirm->set('approved_speciality_d', 0);
                            $classConfirm->save();
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
            return $this->respondWithStatus(['message' => t('EPAL user not found')], Response::HTTP_FORBIDDEN);
        }
    }


 public function FindSmallClassesApproved(Request $request)
 {

        $authToken = $request->headers->get('PHP_AUTH_USER');
        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if (!$user) {
            return $this->respondWithStatus([
                'message' => t("User not found"),
            ], Response::HTTP_FORBIDDEN);
        }

//        if (false === in_array('ministry', $user->getRoles())) {
//            return $this->respondWithStatus([
//                'message' => t("User Invalid Role"),
 //           ], Response::HTTP_FORBIDDEN);
 //       }

        $config_storage = $this->entityTypeManager->getStorage('eggrafes_config');
        $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config_epal'));
        $eggrafesConfig = reset($eggrafesConfigs);
        if (!$eggrafesConfig)
         {
             return $this->respondWithStatus([
                'message' => t("eggrafesConfig Enity not found"),
             ], Response::HTTP_FORBIDDEN);
        }
        else
        {
            $list = array();
            $lockSmallClasses = $eggrafesConfig->lock_small_classes->getString();
            if ($lockSmallClasses !== "1" )
            {
                 $list[] = array('res' => 0);
               return $this->respondWithStatus($list, Response::HTTP_OK);
            }
            else
            {
                 $list[] = array('res' => 1);
                return $this->respondWithStatus($list, Response::HTTP_OK);
            }
        }
 }



public function GetRegions(Request $request)
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if (!$user) {
            return $this->respondWithStatus([
                'message' => t("User not found"),
            ], Response::HTTP_FORBIDDEN);
        }

        $schools = $this->entityTypeManager->getStorage('eepal_school')->loadByProperties(array());
        if ($schools) {
                $list = array();
                foreach ($schools as $object)
                {
                      $SmallClassesAppr =  $object -> approved_a -> value ;
                       if ( $SmallClassesAppr == 1)
                            {
                            $prefid = intval($object->getperfecture());
                            $prefectionname = $this -> entityTypeManager ->getStorage('eepal_region') ->loadByProperties(array('id' => $prefid));
                            $prefname = reset($prefectionname);

                            $namepref = $prefname->name->value;
                            $list[] = array(
                                'epal_id' => $object->id(),
                                'epal_name' => $object->name->value,
                                'epal_special_case' => $object-> special_case ->value,
                                'region_id' => $object->getperfecture(),
                                'region_name' => $namepref,
                                                                     );
                          }
                }
                return $this->respondWithStatus($list, Response::HTTP_OK);
        }
        else
             return $this->respondWithStatus([
                    'message' => t("Schools not found"),
                ], Response::HTTP_FORBIDDEN);
}



public function GetSectorsperschool(Request $request, $courseActive )
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if (!$user) {
            return $this->respondWithStatus([
                'message' => t("User not found"),
            ], Response::HTTP_FORBIDDEN);
        }
        else
        {
        $schools = $this->entityTypeManager->getStorage('eepal_sectors_in_epal')->loadByProperties(array('sector_id' => $courseActive));
        if ($schools) {
                $list = array();
                foreach ($schools as $object)
                {
                    $id_new = $object->epal_id -> entity ->id();
                    $schooldata =  $this->entityTypeManager->getStorage('eepal_school')->loadByProperties(array('id' => $id_new));
                     $sdata = reset($schooldata);
                    if (!$sdata){
                         return $this->respondWithStatus([
                            'message' => t("School not found"),
                            ], Response::HTTP_FORBIDDEN);
                      }
                      else
                    {

                      $SmallClassesAppr =  $object -> approved_sector -> value ;
                        if ( $SmallClassesAppr == 1)
                            {
                            $prefid = intval($sdata->getperfecture());
                            $prefectionname = $this -> entityTypeManager ->getStorage('eepal_region') ->loadByProperties(array('id' => $prefid));
                            $prefname = reset($prefectionname);

                            $namepref = $prefname->name->value;
                            $list[] = array(
                                'epal_id' => $sdata->id(),
                                'epal_name' => $sdata->name->value,
                                'epal_special_case' => $sdata-> special_case ->value,
                                'region_id' => $sdata->getperfecture(),
                                'region_name' => $namepref,
                                                                     );
                          }


                 }
             }
             return $this->respondWithStatus($list, Response::HTTP_OK);
        }
        else
        {
             return $this->respondWithStatus([
                    'message' => t("Schools not found"),
                ], Response::HTTP_FORBIDDEN);
         }
        }

        }



public function getCoursesPerSchoolSmallClasses(Request $request, $courseActive )
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if (!$user) {
            return $this->respondWithStatus([
                'message' => t("User not found"),
            ], Response::HTTP_FORBIDDEN);
        }
        else
        {
        $schools = $this->entityTypeManager->getStorage('eepal_specialties_in_epal')->loadByProperties(array('specialty_id' => $courseActive));
        if ($schools) {
                $list = array();
                foreach ($schools as $object)
                {
                    $id_new = $object->epal_id -> entity ->id();
                    $this->logger->notice($id_new);
                    $schooldata =  $this->entityTypeManager->getStorage('eepal_school')->loadByProperties(array('id' => $id_new));
                     $sdata = reset($schooldata);
                    if (!$sdata){
                         return $this->respondWithStatus([
                            'message' => t("School not found"),
                            ], Response::HTTP_FORBIDDEN);
                      }
                      else
                    {

                      $SmallClassesAppr =  $object -> approved_speciality -> value ;
                      if ($SmallClassesAppr == 1)
                            {
                            $prefid = intval($sdata->getperfecture());
                            $prefectionname = $this -> entityTypeManager ->getStorage('eepal_region') ->loadByProperties(array('id' => $prefid));
                            $prefname = reset($prefectionname);

                            $namepref = $prefname->name->value;
                            $list[] = array(
                                'epal_id' => $sdata->id(),
                                'epal_name' => $sdata->name->value,
                                'epal_special_case' => $sdata-> special_case ->value,
                                'region_id' => $sdata->getperfecture(),
                                'region_name' => $namepref,
                                                                     );
                          }


                 }
             }
              return $this->respondWithStatus($list, Response::HTTP_OK);
        }
        else
        {
             return $this->respondWithStatus([
                    'message' => t("Schools not found"),
                ], Response::HTTP_FORBIDDEN);
         }
        }

        }


public function getCoursesPerSchoolSmallClassesNight(Request $request, $courseActive )
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');
        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if (!$user) {
            return $this->respondWithStatus([
                'message' => t("User not found"),
            ], Response::HTTP_FORBIDDEN);
        }
        else
        {
        $schools = $this->entityTypeManager->getStorage('eepal_specialties_in_epal')->loadByProperties(array('specialty_id' => $courseActive));
        if ($schools) {
                $list = array();
                foreach ($schools as $object)
                {
                    $id_new = $object->epal_id -> entity ->id();
                    $this->logger->notice($id_new);
                    $schooldata =  $this->entityTypeManager->getStorage('eepal_school')->loadByProperties(array('id' => $id_new));
                     $sdata = reset($schooldata);
                    if (!$sdata){
                         return $this->respondWithStatus([
                            'message' => t("School not found"),
                            ], Response::HTTP_FORBIDDEN);
                      }
                      else
                    {
                      $SmallClassesAppr =  $object -> approved_sector -> value ;
                            if ($SmallClassesAppr == 1)
                            {
                            $prefid = intval($sdata->getperfecture());
                            $prefectionname = $this -> entityTypeManager ->getStorage('eepal_region') ->loadByProperties(array('id' => $prefid));
                            $prefname = reset($prefectionname);

                            $namepref = $prefname->name->value;
                            $list[] = array(
                                'epal_id' => $sdata->id(),
                                'epal_name' => $sdata->name->value,
                                'epal_special_case' => $sdata-> special_case ->value,
                                'region_id' => $sdata->getperfecture(),
                                'region_name' => $namepref,
                                                                     );
                          }

                return $this->respondWithStatus($list, Response::HTTP_OK);
                 }
             }
        }
        else
        {
             return $this->respondWithStatus([
                    'message' => t("Schools not found"),
                ], Response::HTTP_FORBIDDEN);
         }
        }

        }


public function OffLineCalculationSmallClasses(Request $request)
    {

       try {
             if (!$request->isMethod('GET')) {
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

            $selectionId = $user->init->value;
            $userRoles = $user->getRoles();
            $userRole = '';
            foreach ($userRoles as $tmpRole)
            {
                if  ($tmpRole === 'ministry')
                {
                    $userRole = $tmpRole;
                }
            }

            if ($userRole === '')
            {
                return $this->respondWithStatus([
                    'error_code' => 4003,
                    "message" => t("1")
                ], Response::HTTP_FORBIDDEN);
            }
            elseif ($userRole === 'ministry')
            {

                $transaction = $this->connection->startTransaction();

                 try
                 {

                    if ($this->findSmallClasses() === self::ERROR_DB) {
                        $transaction->rollback();
                        return $this->respondWithStatus([
                            "message" => t("Unexpected Error3")
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }

                 }
                 catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                    $transaction->rollback();
                    return $this->respondWithStatus([
                        "message" => t("Unexpected Error!!")
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                    drupal_flush_all_caches();
                    return $this->respondWithStatus([
                        'message' => "SmallClasses approvement successfully",
                    ], Response::HTTP_OK);



            }
            else
            {
                return $this->respondWithStatus([
                    'message' => t('No schools found!'),
                ], Response::HTTP_FORBIDDEN);
            }

    }
     //end try

        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                        "message" => t("An unexpected problem occured during retrieveSettings Method ")
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }



}



public function findStatusNew($id, $classId, $sector, $specialit)
    {


        $this->logger->warning('id.'.$specialit.'id'.$id);

        $schoolid = $id;
        $SchoolCats = $this->entityTypeManager->getStorage('eepal_school')->loadByProperties(array('id' => $schoolid));
        $SchoolCat = reset($SchoolCats);
        if ($SchoolCat) {
            $categ = $SchoolCat->metathesis_region->value;
            $operation_shift = $SchoolCat -> operation_shift -> value;
        } else {
            $categ = '-';
            $operation_shift ='-';
        }

        if ($classId == 1)
        {

            $studentPerSchool = $this->entityTypeManager->getStorage('epal_student_class')->loadByProperties(array('epal_id' => $schoolid, 'specialization_id' => -1, 'currentclass' => 1));

            $size = sizeof($studentPerSchool);
                        return $size;

        }
        elseif ($classId == 2)
        {
        $sCon = $this->connection->select('eepal_sectors_in_epal_field_data', 'eSchool');
        $sCon->leftJoin('epal_student_class', 'eStudent',
            'eStudent.epal_id = ' . $schoolid . ' ' .
            'AND eStudent.specialization_id = ' . $sector . ' ' .
            'AND eStudent.currentclass = 2');
        $sCon->fields('eSchool', array('sector_id'))
            ->fields('eStudent', array('specialization_id'))
            ->groupBy('specialization_id')
            ->groupBy('sector_id')
            ->condition('eSchool.epal_id', $schoolid, '=')
            ->condition('eStudent.specialization_id', $sector, '=');
        $sCon->addExpression('count(eStudent.id)', 'eStudent_count');

        $results = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);


            foreach ($results as $result) {
                $size = $result->eStudent_count;
                return $size;
            }
        }
        elseif ($classId == 3)
        {
                $sCon = $this->connection->select('eepal_specialties_in_epal_field_data', 'eSchool');
                $sCon->leftJoin('epal_student_class', 'eStudent',
                'eStudent.epal_id = ' . $schoolid . ' ' .
                 'AND eStudent.specialization_id = ' . $specialit . ' ' .
                 'AND eStudent.currentclass = 3');
                 $sCon->fields('eSchool', array('specialty_id'))
                ->fields('eStudent', array('specialization_id'))
                ->groupBy('specialization_id')
                ->groupBy('specialty_id')
                ->condition('eSchool.epal_id', $schoolid, '=');
                $sCon->addExpression('count(eStudent.id)', 'eStudent_count');

                $results = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);


            foreach ($results as $result) {
                $size = $result->eStudent_count ;
                return $size;
                }

        }
        else
        {
            if ($operation_shift == 'ΕΣΠΕΡΙΝΟ')
            {
                $sCon = $this->connection->select('eepal_specialties_in_epal_field_data', 'eSchool');
            $sCon->leftJoin('epal_student_class', 'eStudent',
                'eStudent.epal_id = ' . $schoolid . ' ' .
                'AND eStudent.specialization_id = ' . $specialit . ' '.
                'AND eStudent.currentclass = 4');
            $sCon->fields('eSchool', array('specialty_id'))
                ->fields('eStudent', array('specialization_id'))
                ->groupBy('specialization_id')
                ->groupBy('specialty_id')
                ->condition('eSchool.epal_id', $schoolid, '=')
                ->condition('eStudent.specialization_id', $specialit, '=');
            $sCon->addExpression('count(eStudent.id)', 'eStudent_count');

            $results = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

            foreach ($results as $result) {
                $size = $result->eStudent_count ;
                return $size;
            }

            }
        }

    }




  private function findSmallClasses()
    {

        //Για κάθε σχολείο βρες τα ολιγομελή τμήματα
        $sCon = $this->connection->select('eepal_school_field_data', 'eSchool')
            ->fields('eSchool', array('id', 'metathesis_region','operation_shift'));
        $eepalSchools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

        foreach ($eepalSchools as $eepalSchool) {

            // Α' τάξη
            if ($this->isSmallClass($eepalSchool->id, "1", "-1", $eepalSchool->metathesis_region) === self::SMALL_CLASS) {
                $this->logger->error("ypologizwnnnnn");
                if ($this->setSmallClassforA($eepalSchool->id) === self::ERROR_DB) {
                    return self::ERROR_DB;
                }
            }

            // Β' τάξη
            $sCon = $this->connection->select('eepal_sectors_in_epal_field_data', 'eSchool')
                ->fields('eSchool', array('epal_id', 'sector_id'))
                ->condition('eSchool.epal_id', $eepalSchool->id, '=');
            $eepalSectorsInEpal = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            foreach ($eepalSectorsInEpal as $eepalSecInEp) {
                if ($this->isSmallClass($eepalSchool->id, "2", $eepalSecInEp->sector_id, $eepalSchool->metathesis_region) === self::SMALL_CLASS) {
                    if ($this->setSmallClassforSector($eepalSchool->id, $eepalSecInEp->sector_id) === self::ERROR_DB) {
                        return self::ERROR_DB;
                    }
                }
            }

            // Γ' τάξη
            $sCon = $this->connection->select('eepal_specialties_in_epal_field_data', 'eSchool')
                ->fields('eSchool', array('epal_id', 'specialty_id'))
                ->condition('eSchool.epal_id', $eepalSchool->id, '=');
            $eepalSpecialtiesInEpal = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            foreach ($eepalSpecialtiesInEpal as $eepalSpecialInEp) {
                if ($this->isSmallClass($eepalSchool->id, "3", $eepalSpecialInEp->specialty_id, $eepalSchool->metathesis_region) === self::SMALL_CLASS) {
                    if ($this->setSmallClassforSpecility($eepalSchool->id, $eepalSpecialInEp->specialty_id) === self::ERROR_DB) {
                        return self::ERROR_DB;
                    }
                }
            }

            // Δ' τάξη
            if ($eepalSchool->operation_shift === "ΕΣΠΕΡΙΝΟ") {
                $sCon = $this->connection->select('eepal_specialties_in_epal_field_data', 'eSchool')
                    ->fields('eSchool', array('epal_id', 'specialty_id'))
                    ->condition('eSchool.epal_id', $eepalSchool->id, '=');
                $eepalSpecialtiesInEpal = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                foreach ($eepalSpecialtiesInEpal as $eepalSpecialInEp) {
                    if ($this->isSmallClass($eepalSchool->id, "4", $eepalSpecialInEp->specialty_id, $eepalSchool->metathesis_region) === self::SMALL_CLASS) {
                        if ($this->setSmallClassforSpecilityD($eepalSchool->id, $eepalSpecialInEp->specialty_id) === self::ERROR_DB) {
                            return self::ERROR_DB;
                        }
                    }
                }
            } //end if ΕΣΠΕΡΙΝΟ
        } //end for each school/department

        return self::SUCCESS;
    }   //end function


    private function isSmallClass($schoolId, $classId, $sectorOrcourseId, $regionId)
    {

        $limitDown = $this->retrieveLimitDown($classId, $regionId);

        if ($limitDown === self::NO_CLASS_LIMIT_DOWN) {
            return self::NO_CLASS_LIMIT_DOWN;
        } elseif ($limitDown === self::ERROR_DB) {
            return self::ERROR_DB;
        }

        $numStudents = $this->countStudents($schoolId, $classId, $sectorOrcourseId);

        if ($numStudents === self::ERROR_DB) {
            return self::ERROR_DB;
        }

        //Αν $numStudents == 0, γύρισε false, ώστε να μη γίνει περιττή κλήση στην markStudentsInSmallClass
        if ($numStudents < $limitDown) {
            return self::SMALL_CLASS;
        } else {
            return self::NON_SMALL_CLASS;
        }
    }

    private function retrieveLimitDown($classId, $regionId)
    {

        try {
            $sCon = $this->connection->select('epal_class_limits', 'eClassLimit')
                ->fields('eClassLimit', array('limit_down'))
                ->condition('eClassLimit.name', $classId, '=')
                ->condition('eClassLimit.category', $regionId, '=');
            $classLimits = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            if (sizeof($classLimits) === 1) {
                $classLimit = reset($classLimits);
                return $classLimit->limit_down;
            } else {
                return self::NO_CLASS_LIMIT_DOWN;
            }
        } //end try
        catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return self::ERROR_DB;
        }
    } //end function

    private function countStudents($schoolId, $classId, $sectorOrcourseId)
    {
        try {
            $sCon = $this->connection->select('epal_student_class', 'eStudent')
                ->fields('eStudent', array('id'))
                ->condition('eStudent.epal_id', $schoolId, '=')
                ->condition('eStudent.currentclass', $classId, '=')
                ->condition('eStudent.specialization_id', $sectorOrcourseId, '=');
            return $sCon->countQuery()->execute()->fetchField();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return self::ERROR_DB;
        }
    }

    private function setSmallClassforA($schoolId)
    {
        $this->logger->error($schoolId. "A");
        try {
            $query = $this->connection->update('eepal_school_field_data');
            $query->fields(['approved_a' => 0]);
            $query->condition('id', $schoolId);
            $query->execute();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return self::ERROR_DB;
        }
        return self::SUCCESS;
    }


    private function setSmallClassforSector($schoolId, $sectorid)
    {
          $this->logger->error($schoolId.$sectorid. "B");
          try {
            $query = $this->connection->update('eepal_sectors_in_epal_field_data');
            $query->fields(['approved_sector' => 0]);
            $query->condition('epal_id', $schoolId);
            $query->condition('sector_id', $sectorid);
            $query->execute();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return self::ERROR_DB;
        }
        return self::SUCCESS;
    }


 private function setSmallClassforSpecility($schoolId, $specialityid)
    {
        $this->logger->error($schoolId.$specialityid. "C");
        try {
            $query = $this->connection->update('eepal_specialties_in_epal_field_data');
            $query->fields(['approved_speciality' => 0]);
            $query->condition('epal_id', $schoolId);
            $query->condition('specialty_id', $specialityid);
            $query->execute();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return self::ERROR_DB;
        }
        return self::SUCCESS;
    }


    private function setSmallClassforSpecilityD($schoolId, $specialityid)
    {
        $this->logger->error($schoolId.$specialityid. "D");
        try {
            $query = $this->connection->update('eepal_specialties_in_epal_field_data');
            $query->fields(['approved_speciality_d' => 0]);
            $query->condition('epal_id', $schoolId);
            $query->condition('specialty_id', $specialityid);
            $query->execute();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return self::ERROR_DB;
        }
        return self::SUCCESS;
    }

}
