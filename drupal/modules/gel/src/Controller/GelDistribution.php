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
                $schools = $this->entityTypeManager
                    ->getStorage('gel_school')
                    ->loadByProperties(array('region_edu_admin_id' => $selectionId));
            } elseif ($userRole === 'eduadmin') {
                $schools = $this->entityTypeManager
                    ->getStorage('gel_school')
                    ->loadByProperties(array('edu_admin_id' => $selectionId, 'unit_type_id'=> 3));
            } else {
                $schools = [];
            }

            if ($schools) {
                $list = array();

                foreach ($schools as $object) {
                    $status = 1;
                    $list[] = array(
                        'id' => $object->id(),
                        'name' => $object->name->value,
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
                $schools = $this->entityTypeManager
                    ->getStorage('gel_school')
                    ->loadByProperties(array('region_edu_admin_id' => $selectionId));
            } elseif ($userRole === 'eduadmin') {
                $schools = $this->entityTypeManager
                    ->getStorage('gel_school')
                    ->loadByProperties(array('edu_admin_id' => $selectionId, 'unit_type_id'=> 4));
            } else {
                $schools = [];
            }

            if ($schools) {
                $list = array();

                foreach ($schools as $object) {
                    $status = 1;
                    $list[] = array(
                        'id' => $object->id(),
                        'name' => $object->name->value,
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
                    
                    $studentPerSchool = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(array('lastschool_registrynumber' => $regno, 'lastschool_unittypeid' => 3, 'lastschool_class' => 3));
                }
                if ($studentPerSchool) {
                    $list = array();
                    foreach ($studentPerSchool as $object) {
                        

                            $crypt = new Crypt();
                            try {
                                $name_decoded = $object->name->value;
                                
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

        if (intval($oldschool) === 999999)
        {
        $this->logger->warning($oldschool."1");
        $transaction = $this->connection->startTransaction();
        try {

   
            $student = array(
                'langcode' => 'el',
                'student_id' => $studentid,
                'school_id' => $schoolid
                
            );

            $entity_storage_student = $this->entityTypeManager->getStorage('gelstudenthighschool');
            $entity_object = $entity_storage_student->create($student);
            $entity_storage_student->save($entity_object);
     
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
      elseif (intval($schoolid === 0))
      {
            $this->logger->warning($schoolid."delete");
            $this->connection->delete('gelstudenthighschool')
                            ->condition('student_id', $studentid, '=')
                            ->execute();
            return $this->respondWithStatus([
                "error_code" => 0
            ], Response::HTTP_OK);
                  
               
          

      }
      else
      {
            $this->logger->warning($oldschool."2");
            $this->logger->warning($schoolid."5");
          $schools = $this->entityTypeManager->getStorage('gelstudenthighschool')->loadByProperties(array('student_id' => $studentid));
            $school = reset($schools);
                    
          if ($school) {
              $school->set('school_id', intval($schoolid));
              $school->save();

          }
          return $this->respondWithStatus([
                "error_code" => 0
            ], Response::HTTP_OK);
      }
      
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

private function respondWithStatus($arr, $s)
    {
        $res = new JsonResponse($arr);
        $res->setStatusCode($s);

        return $res;
    }

}
