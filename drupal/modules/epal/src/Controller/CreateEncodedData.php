<?php
/**
 * @file
 * Contains \Drupal\query_example\Controller\QueryExampleController.
 */

namespace Drupal\epal\Controller;

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

use Drupal\epal\Crypt;

class CreateEncodedData extends ControllerBase {

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
			//$connection = Database::getConnection();
			$this->connection = $connection;
			$this->logger = $loggerChannel->get('epal');
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





	public function createData() {

		$crypt = new Crypt();

		print_r(date("Y-m-d H:i:s", 1530118246));
		print_r(date("Y-m-d H:i:s", 1530205696));
		print_r(date("Y-m-d H:i:s", 1530212831));
		print_r(date("Y-m-d H:i:s", 1530220482));

		print_r("decodedname:  " . "<br>");
		$name_encoded = $crypt->decrypt("def5020043fd2d3ba4746f43be836efa09e9033a3d041c75104bd8700cb59c03b3fb525773b099e8dbd3e9567a9587c9bed063b5a6d4df0af4e8da0f299045ad363395186e484fae498ea9d86f426747bdc658156ca7bd571f");
		print_r("Decrypted:  " . $name_encoded);
		print_r("<br>");



		//ΕΠΑΛ
		/*
		$sCon = $this->connection
			 ->select('epal_student', 'eStudent')
			 ->fields('eStudent', array('id', 'guardian_surname','guardian_name','epaluser_id'))
			  ->condition('eStudent.delapp', 0, '=');
		$epalSurnames = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
		foreach ($epalSurnames as $epalSurname)	{
			if ($epalSurname->guardian_surname != null)
				$str1 = $crypt->decrypt($epalSurname->guardian_surname);
			else
				$str1 = "EMPTY";
			//$str1_n = $crypt->decrypt($epalSurname->guardian_name);
			$sCon = $this->connection
				 ->select('applicant_users', 'eApplicant')
				 ->fields('eApplicant', array('surname','name'))
				 ->condition('eApplicant.id', $epalSurname->epaluser_id, '=');
			$applicantSurnames = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
			$applicantSurname = reset($applicantSurnames);
			if ($applicantSurname->surname != null)
				$str2 = $crypt->decrypt($applicantSurname->surname);
			else
				$str2 = "EMPTY";
			//$str2_n = $crypt->decrypt($applicantSurname->name);

			if ($str1 != $str2
				//||  ($str1 == $str2 && $str1_n != $str2_n )
			)
			{
				print_r("id: "  . $epalSurname->epaluser_id .  "  epalSurname: "  . $str1 .  "  applicantName:  " . $str2 );
				print_r("\r");
			}
		}
		*/



		//ΓΕΛ
		/*
		$sCon = $this->connection
			 ->select('gel_student', 'eStudent')
			 ->fields('eStudent', array('id', 'guardian_surname','guardian_name', 'gel_userid'))
			 ->condition('eStudent.delapp', 0, '=');
		$gelSurnames = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
		foreach ($gelSurnames as $gelSurname)	{
			if ($gelSurname->guardian_surname != null)
				$str1 = $crypt->decrypt($gelSurname->guardian_surname);
			else
				$str1 = "EMPTY";
			//$str1_n = $crypt->decrypt($gelSurname->guardian_name);
			$sCon = $this->connection
				 ->select('applicant_users', 'eApplicant')
				 ->fields('eApplicant', array('surname','name'))
				 ->condition('eApplicant.id', $gelSurname->gel_userid, '=');
			$applicantSurnames = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
			$applicantSurname = reset($applicantSurnames);
			if ($applicantSurname->surname != null)
				$str2 = $crypt->decrypt($applicantSurname->surname);
			else
				$str2 = "EMPTY";
			//$str2_n = $crypt->decrypt($applicantSurname->name);

			//if ($str1 != $str2)  {
			if (  $str1 != $str2
		//||  ($str1 == $str2 && $str1_n != $str2_n )
			)  {
				print_r("id: "  . $gelSurname->id .  "  gel_userid: "  . $gelSurname->gel_userid .  "  gelSurname: "  . $str1 .  "  applicantName:  " . $str2 );
				print_r("\r");
			}
		}

		*/



		//ΕΛΕΓΧΟΣ ΕΠΙΒΕΒΑΙΩΣΗΣ
		/*
		$sCon = $this->connection
			 ->select('gel_student', 'eStudent')
			 ->fields('eStudent', array('id', 'guardian_surname', 'gel_userid'))
			 ->condition('eStudent.delapp', 0, '=');
		$gelSurnames = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
		foreach ($gelSurnames as $gelSurname)  {
			if ($gelSurname->id == 739 || $gelSurname->id == 1000 || $gelSurname->id == 1014 || $gelSurname->id == 2588 || $gelSurname->id == 3379 ||
					$gelSurname->id == 6919 || $gelSurname->id == 13884 || $gelSurname->id == 13988 || $gelSurname->id == 14255 || $gelSurname->id == 14482  ||
					$gelSurname->id == 19543 || $gelSurname->id == 19691 || $gelSurname->id == 23855 || $gelSurname->id == 25097 || $gelSurname->id == 25196 ||
					$gelSurname->id == 25773 || $gelSurname->id == 27097 || $gelSurname->id == 28375 || $gelSurname->id == 32468 || $gelSurname->id == 34116 ||
					$gelSurname->id == 38145 || $gelSurname->id == 46005 || $gelSurname->id == 47575 || $gelSurname->id == 50425 || $gelSurname->id == 52831 ||
					$gelSurname->id == 52916
			)  {
				$sCon = $this->connection
					 ->select('epal_student', 'eEpalStudent')
					 ->fields('eEpalStudent', array('guardian_surname','epaluser_id'))
					 ->condition('eEpalStudent.id', $gelSurname->id, '=');
				$epalSurnames = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
				foreach ($epalSurnames as $epalSurname)	{
						$name1 = $crypt->decrypt($gelSurname->guardian_surname);
						$name2 = $crypt->decrypt($epalSurname->guardian_surname);
						print_r("id: " . $gelSurname->id .   " gelGuardianSurname: "  . $name1 . "  epalGuardianSurname: "  . $name2);
						print_r("\r");
				}

			}

		}
		*/



		//για ΔΔΕ ΑΧΑΙΑΣ!
		/*
		$sCon = $this->connection
			 ->select('gel_school', 'eSchool')
			 ->fields('eSchool', array('registry_no', 'name'))
			 ->condition('eSchool.edu_admin_id', 17, '=');
		$epalSchools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);


		foreach ($epalSchools as $epalSchool)  {

				$sCon = $this->connection
					 ->select('epal_student', 'eStudent')
					 ->fields('eStudent', array('id', 'am'))
					 ->condition('eStudent.delapp', 0, '=')
					 ->condition('eStudent.lastschool_registrynumber',$epalSchool->registry_no , '=');
				$epalStudents = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
				foreach ($epalStudents as $epalStudent)  {
					if ($epalStudent->am != null)  {
						print_r("Am," . $crypt->decrypt($epalStudent->am) .   ",reg_no,"  . strval($epalSchool->registry_no) . ", schName,"  . $epalSchool->name );
						print_r("<br/>");
					}
				}

			}
			*/



		print_r("\rΤΕΛΟΣ!");



	}





}
