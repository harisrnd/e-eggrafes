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
use Drupal\Core\TypedData\Plugin\DataType\TimeStamp;

use Drupal\Core\Language\LanguageManagerInterface;

use Drupal\gel\Crypt;

class SdeDistribution extends ControllerBase
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

    public function getSdeStudents(Request $request)
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
                    "message" => t("1")
                ], Response::HTTP_FORBIDDEN);
            } elseif ($userRole === 'eduadmin') {

                $sCon = $this->connection->select('gel_student', 'gStudent')
                ->fields('gStudent', array('lastschool_registrynumber','lastschool_unittypeid',  'lastschool_class' , 'delapp','nextclass','name','am','regionarea','regiontk','regionaddress','id','second_period'))
                ->condition('gStudent.lastschool_unittypeid', 40 , '=')
                ->condition('gStudent.delapp', 0, '=');
                $sCon -> orderBy('gStudent.regionarea', 'DESC');
                $studentPerSchool =  $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

            }

            if ($studentPerSchool) {

                $i = 0;
                foreach ($studentPerSchool as $object) {
                    
                    $i++;
                    $crypt = new Crypt();
                    try {
                        $name_decoded = $object->name;
                        $regionaddress_decoded = $crypt->decrypt($object->regionaddress);
                        if ($object->regiontk !== null)
                            $regiontk_decoded = $crypt->decrypt($object->regiontk);
                        else
                            $regiontk_decoded = "";
                        if ($object->regionarea !== null)
                            $regionarea_decoded = ", ".$crypt->decrypt($object->regionarea);
                        else
                            $regionarea_decoded = null;
                        if ($object ->nextclass >= "4")
                        {
                            $school_type = "ΕΣΠΕΡΙΝΟ";
                        }
                        else{
                            $school_type = "ΗΜΕΡΗΣΙΟ";
                        }


                    } catch (\Exception $e) {
                        $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                        return $this->respondWithStatus([
                        "message" => t("An unexpected error occured during DECODING data in getStudentPerSchool Method ")
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }

                    $list[] = array(
                        'idnew' => $i,
                        'id' => $object ->id,
                        'regionaddress' => $regionaddress_decoded,
                        'regionarea' => $regionarea_decoded,
                        'regiontk'=>$regiontk_decoded,
                        'school_type'=>$school_type,
                        'oldschool' => $this->getSchoolperStudent($object->id),

                    );
                }

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
    public function SaveSdeStudentHighSchhool(Request $request, $studentid, $schoolid, $undoselection)
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
   
            $sCon=$this->connection->select('gel_student','eStudent')
            ->fields('eStudent', array('nextclass'))
            ->condition('eStudent.delapp', 0, '=')
            ->condition('eStudent.id', $value, '=');
            $nclass = $sCon->execute()->fetchField();

            

           $transaction = $this->connection->startTransaction();
           try {
   
   
               $this->connection->delete('gelstudenthighschool')
                               ->condition('id', $value, '=')
                               ->execute();
   
            //    if ($nextclass === '1')
            //        $nexttaxi = 'Α';
            //    elseif ($nextclass === '2')
            //        $nexttaxi = 'Β';
            //     elseif ($nextclass === '3')
            //        $nexttaxi = 'Γ';
            //     elseif ($nextclass === '4')
            //        $nexttaxi = 'Α';
            //     elseif ($nextclass === '6')
            //        $nexttaxi = 'Β';
            //     elseif ($nextclass === '7')
            //        $nexttaxi = 'Γ';
            //    else
            //         $nexttaxi = '';

                if ($nclass === '1' || $nclass==='Α')
                    $nexttaxi = 'Α';
                elseif ($nclass === '2' || $nclass==='Β')
                    $nexttaxi = 'Β';
                 elseif ($nclass === '3' || $nclass==='Γ')
                    $nexttaxi = 'Γ';
                 elseif ($nclass === '4' || $nclass==='Α')
                    $nexttaxi = 'Α';
                 elseif ($nclass === '5' || $nclass==='Β')
                    $nexttaxi = 'Β';
                 elseif ($nclass === '6' || $nclass==='Γ')
                    $nexttaxi = 'Γ';
                elseif ($nclass === '7' || $nclass==='Δ')
                    $nexttaxi = 'Δ';
                else
                     $nexttaxi = '';
   
               if (intval($undoselection) === 1)
               {
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
   
   
   public function getSchoolperStudent($id){

   
           $sCon = $this->connection->select('gelstudenthighschool', 'eStudent')
                   ->fields('eStudent', array('school_id'))
                   ->condition('eStudent.student_id', $id, '=');
               $res1 =  intval($sCon->execute()->fetchField());
   
   
                $sCon1 = $this->connection->select('gel_school', 'gels')
                   ->fields('gels', array('name'))
                   ->condition('gels.id', $res1, '=');
                return $sCon1->execute()->fetchField();
   
   
   
   
   
    }






    public function getIdiwtStudents(Request $request)
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
                    "message" => t("1")
                ], Response::HTTP_FORBIDDEN);
            } elseif ($userRole === 'eduadmin') {

                $sCon = $this->connection->select('gel_student', 'gStudent');
                $sCon->leftJoin('gel_school', 'gSchool', 'gSchool.registry_no = gStudent.lastschool_registrynumber');
                $sCon->fields('gStudent', array('lastschool_registrynumber','lastschool_unittypeid',  'lastschool_class' , 'delapp','nextclass','name','am','regionarea','regiontk','regionaddress','id','second_period'))
                     ->fields('gSchool', array('id', 'edu_admin_id', 'registry_no','extra_unitid'))     
                     ->condition('gStudent.delapp', 0, '=')
                     ->condition('gSchool.extra_unitid',300,'=');
                $studentPerSchool =  $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

            }

            if ($studentPerSchool) {

                $i = 0;
                foreach ($studentPerSchool as $object) {
                    
                    $i++;
                    $crypt = new Crypt();
                    try {
                        $name_decoded = $object->name;
                        $regionaddress_decoded = $crypt->decrypt($object->regionaddress);
                        if ($object->regiontk !== null)
                            $regiontk_decoded = $crypt->decrypt($object->regiontk);
                        else
                            $regiontk_decoded = "";
                        if ($object->regionarea !== null)
                            $regionarea_decoded = ", ".$crypt->decrypt($object->regionarea);
                        else
                            $regionarea_decoded = null;
                        if ($object ->nextclass >= "4")
                        {
                            $school_type = "ΕΣΠΕΡΙΝΟ";
                        }
                        else{
                            $school_type = "ΗΜΕΡΗΣΙΟ";
                        }


                    } catch (\Exception $e) {
                        $this->logger->warning(__METHOD__ . ' Decrypt error: ' . $e->getMessage());
                        return $this->respondWithStatus([
                        "message" => t("An unexpected error occured during DECODING data in getStudentPerSchool Method ")
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }

                    $list[] = array(
                        'idnew' => $i,
                        'id' => $object ->id,
                        'regionaddress' => $regionaddress_decoded,
                        'regionarea' => $regionarea_decoded,
                        'regiontk'=>$regiontk_decoded,
                        'school_type'=>$school_type,
                        'oldschool' => $this->getSchoolperStudent($object->id),

                    );
                }

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
    public function SaveIdiwtStudentHighSchhool(Request $request, $studentid, $schoolid, $undoselection)
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

         $sCon=$this->connection->select('gel_student','eStudent')
         ->fields('eStudent', array('nextclass'))
         ->condition('eStudent.delapp', 0, '=')
         ->condition('eStudent.id', $value, '=');
         $nclass = $sCon->execute()->fetchField();

         

        $transaction = $this->connection->startTransaction();
        try {


            $this->connection->delete('gelstudenthighschool')
                            ->condition('id', $value, '=')
                            ->execute();

         //    if ($nextclass === '1')
         //        $nexttaxi = 'Α';
         //    elseif ($nextclass === '2')
         //        $nexttaxi = 'Β';
         //     elseif ($nextclass === '3')
         //        $nexttaxi = 'Γ';
         //     elseif ($nextclass === '4')
         //        $nexttaxi = 'Α';
         //     elseif ($nextclass === '6')
         //        $nexttaxi = 'Β';
         //     elseif ($nextclass === '7')
         //        $nexttaxi = 'Γ';
         //    else
         //         $nexttaxi = '';

             if ($nclass === '1' || $nclass==='Α')
                 $nexttaxi = 'Α';
             elseif ($nclass === '2' || $nclass==='Β')
                 $nexttaxi = 'Β';
              elseif ($nclass === '3' || $nclass==='Γ')
                 $nexttaxi = 'Γ';
              elseif ($nclass === '4' || $nclass==='Α')
                 $nexttaxi = 'Α';
              elseif ($nclass === '5' || $nclass==='Β')
                 $nexttaxi = 'Β';
              elseif ($nclass === '6' || $nclass==='Γ')
                 $nexttaxi = 'Γ';
             elseif ($nclass === '7' || $nclass==='Δ')
                 $nexttaxi = 'Δ';
             else
                  $nexttaxi = '';

            if (intval($undoselection) === 1)
            {
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


    private function respondWithStatus($arr, $s)  {
        $res = new JsonResponse($arr);
        $res->setStatusCode($s);
        return $res;
    }



}