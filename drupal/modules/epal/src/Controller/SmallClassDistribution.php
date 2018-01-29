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
            $operation_shift = $school -> operation_shift -> value;
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



    


}
