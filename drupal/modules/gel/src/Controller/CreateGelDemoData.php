<?php
/**
 * @file
 * Contains \Drupal\query_example\Controller\QueryExampleController.
 */

namespace Drupal\gel\Controller;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Database\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

use Drupal\gel\Crypt;



class CreateGelDemoData extends ControllerBase {

	protected $entity_query;
  protected $entityTypeManager;
  protected $logger;
  protected $connection;

	public function __construct(
		EntityTypeManagerInterface $entityTypeManager,
		QueryFactory $entity_query,
		Connection $connection,
		LoggerChannelFactoryInterface $loggerChannel)
		{
			$this->entityTypeManager = $entityTypeManager;
			$this->entity_query = $entity_query;
			$this->connection = $connection;
			$this->logger = $loggerChannel->get('gel');
    }

	public static function create(ContainerInterface $container)
    {
        return new static(
          $container->get('entity.manager'),
          $container->get('entity.query'),
          $container->get('database'),
          $container->get('logger.factory')
      );
    }


	public function make_seed() {
		  list($usec, $sec) = explode(' ', microtime());
		  return $sec + $usec * 1000000;
	}

	public function UniqueRandNum($min, $max, $quantity) {
    $numbers = range($min, $max);
    shuffle($numbers);
    return array_slice($numbers, 0, $quantity);
	}



	public function createData() {

		$crypt = new Crypt();

    $transaction = $this->connection->startTransaction();

		try {
			//insert demo records in entity: gel_student
			$entity_manager = \Drupal::entityTypeManager();

			$geluserid = \Drupal::currentUser()->id();
			//$geluserid = 25;

			$schoolIdsList = array();
			$sCon = $this->connection->select('gel_school', 'eSchool')
						 ->fields('eSchool', array('name', 'registry_no', 'unit_type_id','edu_admin_id'))
						 //περιοχή Γ' Αθήνας'
						 ->condition('eSchool.edu_admin_id', 9, '=');
						 //
						 //->condition('eSchool.unit_type_id', 3, '=');
			$gelSchools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
			foreach ($gelSchools as $gelSchool)	{
				array_push($schoolIdsList, $gelSchool->id);
			}


			for ($i = 1; $i <= 5000; $i++) {

			  $curclass = rand(1,7);
				$lastclass = rand(1,4);
				$am = rand(1,100000);
				$myschool_id = rand(1,100000);

				$lastschool_id = rand(0, sizeof($schoolIdsList));
				$lastschool_schoolname = $gelSchools[$lastschool_id]->name;
				$lastschool_unittypeid = $gelSchools[$lastschool_id]->unit_type_id;
				$lastschool_registrynumber = $gelSchools[$lastschool_id]->registry_no;
				//$lastschool_schoolname = "8ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΙΛΙΟΥ";
				//$lastschool_unittypeid = 3;
				//$lastschool_registrynumber = "0501067";



				$student = array(
					'gel_userid' => $geluserid,
					'am' => $crypt->encrypt(strval($am)),
					'myschool_id' =>$myschool_id,

					'name' => $crypt->encrypt("όνομα" . $i),
					'studentsurname' => $crypt->encrypt("επώνυμο" . $i),
					'birthdate' => '01/01/1970',
					'fatherfirstname' => $crypt->encrypt("όνομα_πατέρα" . $i),
					'motherfirstname' => $crypt->encrypt("όνομα_μητέρας" . $i),
					'regionaddress' => $crypt->encrypt("διεύθυνση" . $i),
					'regionarea' => $crypt->encrypt("περιοχή" . $i),
					'regiontk' => $crypt->encrypt("ΤΚ" . $i),
					'nextclass' => $curclass,
					'relationtostudent' => 'Γονέας/Κηδεμόνας',
					'telnum' => $crypt->encrypt('6944123456'),
					'guardian_name' => $crypt->encrypt('όνομα_κηδεμόνα'),
					'guardian_surname' => $crypt->encrypt('επώνυμο_κηδεμόνα'),
					'guardian_fathername' => $crypt->encrypt('όνομα_πατέρα_κηδεμόνα'),
					'guardian_mothername' => $crypt->encrypt('όνομα_μητέρας_κηδεμόνα'),
					'lastschool_class' => $lastclass,
					'lastschool_schoolyear' => "2017-2018",
					'lastschool_schoolname' => $lastschool_schoolname,
					'lastschool_unittypeid' => $lastschool_unittypeid,
					'lastschool_registrynumber' => $lastschool_registrynumber,
					'agreement' => 1,
					'myschool_currentsection' => "ΟΝΟΜΑ ΤΟΜΕΑ / ΟΜΑΔΑΣ ΠΡΟΣΑΝΑΤΟΛΙΣΜΟΥ"
        );

				$entity_storage_student = $entity_manager->getStorage('gel_student');
				$entity_object = $entity_storage_student->create($student);
				$entity_storage_student->save($entity_object);


				//insert records in entity: 	gel_student_choices
				//...
				$created_student_id = $entity_object->id();

				//$entity_storage_student->resetCache();

				if ($curclass === 3 || $curclass === 7 || $curclass === 2 || $curclass === 6 ) 	{
						$choice_id_OP = 0;
						if ($curclass === 3 || $curclass === 7 )
							$choice_id_OP = rand(15,17);
						else if ($curclass === 2 || $curclass === 6)
							$choice_id_OP = rand(15,16);

						$studentChoicesOP = array(
							'name' => '',
							'student_id' =>$created_student_id,
							'choice_id' => strval($choice_id_OP),
		        );
						$entity_storage_choice = $entity_manager->getStorage('gel_student_choices');
						$entity_object = $entity_storage_choice->create($studentChoicesOP);
						$entity_storage_choice->save($entity_object);
				}
				else if ($curclass === 1 || $curclass === 4 || $curclass === 3  )  {
						//$choice_id_EPIL;
						if ($curclass === 1 || $curclass === 4)
							 //$choice_id_EPIL = rand(4,7);
							 $choice_id_EPIL = $this->UniqueRandNum(4,7,2);
						else if ($curclass === 3)
							//$choice_id_EPIL = rand(8,13);
							 $choice_id_EPIL = $this->UniqueRandNum(8,13,2);

						for ($j=1; $j <= 2; $j++)	{
								$studentChoicesEPIL = array(
									'name' => '',
									'student_id' =>$created_student_id,
									'choice_id' => strval($choice_id_EPIL[$j-1]),
									'order_id' => $j,
								);
								$entity_storage_choice = $entity_manager->getStorage('gel_student_choices');
								$entity_object = $entity_storage_choice->create($studentChoicesEPIL);
								$entity_storage_choice->save($entity_object);
					} //end for
				} //end if

		} //end for

	} //end try

	catch (\Exception $e) {
			$this->logger->warning($e->getMessage());

			$returnmsg = "Αποτυχία καταχώρησης demo data!";
			$response = new JsonResponse([$returnmsg]);
    //  $transaction->rollback();
			return $response;
	}


  $response = new JsonResponse(['hello' => 'world']);
  $response->headers->set('X-AUTH-TOKEN', 'HELLOTOKEN');
  return $response;


}


}
