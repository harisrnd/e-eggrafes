<?php
/**
 * @file
 * Contains \Drupal\query_example\Controller\QueryExampleController.
 */

namespace Drupal\deploysystem\Controller;

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


class MinisterSettings extends ControllerBase {

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
			$connection = Database::getConnection();
			$this->connection = $connection;
			$this->logger = $loggerChannel->get('deploysystem');
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


	public function retrieveSettings(Request $request) {

		try {
			 if (!$request->isMethod('GET')) {
					return $this->respondWithStatus([
						 "message" => t("Method Not Allowed")
							], Response::HTTP_METHOD_NOT_ALLOWED);
			 }

			 //user validation
			 $authToken = $request->headers->get('PHP_AUTH_USER');
			 $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
			 $user = reset($users);
			 if (!$user) {
					 return $this->respondWithStatus([
									 'message' => t("User not found"),
							 ], Response::HTTP_FORBIDDEN);
			 }

			 //user role validation
			 $roles = $user->getRoles();
			 $validRole = false;
			 foreach ($roles as $role)
				 if ($role === "ministry") {
					 $validRole = true;
					 break;
				 }
			 if (!$validRole) {
					 return $this->respondWithStatus([
									 'message' => t("User Invalid Role"),
							 ], Response::HTTP_FORBIDDEN);
			 }

			 //minister settings retrieve
			 $config_storage = $this->entityTypeManager->getStorage('eggrafes_config');
	 		 $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config'));
	 		 $eggrafesConfig = reset($eggrafesConfigs);
	 		 if (!$eggrafesConfig) {
	 				return $this->respondWithStatus([
	 								'message' => t("eggrafesConfig Enity not found"),
	 						], Response::HTTP_FORBIDDEN);
	 		 }
	 		 else {
	 				$capacityDisabled = $eggrafesConfig->lock_school_capacity->getString();
	 				$directorViewDisabled = $eggrafesConfig->lock_school_students_view->getString();
	 				$applicantsLoginDisabled = $eggrafesConfig->lock_application->getString();
					$applicantsAppModifyDisabled = $eggrafesConfig->lock_modify->getString();
					$applicantsAppDeleteDisabled = $eggrafesConfig->lock_delete->getString();
					$applicantsResultsDisabled = $eggrafesConfig->lock_results->getString();
					$secondPeriodEnabled = $eggrafesConfig->activate_second_period->getString();
					$dateStart = $eggrafesConfig->date_start_b_period->getString();
					$smallClassApproved = $eggrafesConfig->lock_small_classes->getString();
	 		 }
	 		 $config_storage->resetCache();

			return $this->respondWithStatus([
					//'message' => t("post successful"),
					'capacityDisabled' => $capacityDisabled,
					'directorViewDisabled' => $directorViewDisabled,
					'applicantsLoginDisabled' => $applicantsLoginDisabled,
					'applicantsAppModifyDisabled' => $applicantsAppModifyDisabled,
					'applicantsAppDeleteDisabled' => $applicantsAppDeleteDisabled,
					'applicantsResultsDisabled' => $applicantsResultsDisabled,
					'secondPeriodEnabled' => $secondPeriodEnabled,
					'dateStart' => $dateStart,
					'smallClassApproved' => $smallClassApproved
			], Response::HTTP_OK);

		}	//end try

		catch (\Exception $e) {
			$this->logger->warning($e->getMessage());
			return $this->respondWithStatus([
						"message" => t("An unexpected problem occured during retrieveSettings Method ")
					], Response::HTTP_INTERNAL_SERVER_ERROR);
		}



}


public function storeSettings(Request $request, $capacityDisabled, $directorViewDisabled, $applicantsLoginDisabled, $applicantsAppModifyDisabled,
		$applicantsAppDeleteDisabled, $applicantsResultsDisabled,
		 $secondPeriodEnabled, $dateStart, $smallClassApproved ) {

	try {
		if (!$request->isMethod('GET')) {
				return $this->respondWithStatus([
					 "message" => t("Method Not Allowed")
						], Response::HTTP_METHOD_NOT_ALLOWED);
		 }

		 //user validation
		 $authToken = $request->headers->get('PHP_AUTH_USER');
		 $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
		 $user = reset($users);
		 if (!$user) {
				 return $this->respondWithStatus([
								 'message' => t("User not found"),
						 ], Response::HTTP_FORBIDDEN);
		 }

		 //user role validation
		 $roles = $user->getRoles();
		 $validRole = false;
		 foreach ($roles as $role)
			 if ($role === "ministry") {
				 $validRole = true;
				 break;
			 }
		 if (!$validRole) {
				 return $this->respondWithStatus([
								 'message' => t("User Invalid Role"),
						 ], Response::HTTP_FORBIDDEN);
		 }

		 $config_storage = $this->entityTypeManager->getStorage('eggrafes_config');
 		 $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config'));
 		 $eggrafesConfig = reset($eggrafesConfigs);
 		 if (!$eggrafesConfig) {
 				return $this->respondWithStatus([
 								'message' => t("eggrafesConfig Enity not found"),
 						], Response::HTTP_FORBIDDEN);
 		 }
 		 else {

 				  $eggrafesConfig->set('lock_school_capacity', $capacityDisabled);
					$eggrafesConfig->set('lock_school_students_view', $directorViewDisabled);
					$eggrafesConfig->set('lock_application', $applicantsLoginDisabled);
					$eggrafesConfig->set('lock_modify', $applicantsAppModifyDisabled);
					$eggrafesConfig->set('lock_delete', $applicantsAppDeleteDisabled);
					$eggrafesConfig->set('lock_results', $applicantsResultsDisabled);
					$eggrafesConfig->set('activate_second_period', $secondPeriodEnabled);
					$eggrafesConfig->set('date_start_b_period', $dateStart);
					$eggrafesConfig->set('lock_small_classes', $smallClassApproved);
					$eggrafesConfig->save();
 		 }
 		 $config_storage->resetCache();


		return $this->respondWithStatus([
				//'message' => t("post successful"),
				'capacityDisabled' => $capacityDisabled,
				'directorViewDisabled' => $directorViewDisabled,
				'applicantsLoginDisabled' => $applicantsLoginDisabled,
				'applicantsAppModifyDisabled' => $applicantsAppModifyDisabled,
				'applicantsAppDeleteDisabled' => $applicantsAppDeleteDisabled,
				'applicantsResultsDisabled' => $applicantsResultsDisabled,
				'secondPeriodEnabled' => $secondPeriodEnabled,
				'dateStart' => $dateStart,
				'smallClassApproved' =>$smallClassApproved,
		], Response::HTTP_OK);

	}	//end try

	catch (\Exception $e) {
		$this->logger->warning($e->getMessage());
		return $this->respondWithStatus([
					"message" => t("An unexpected problem occured during storeSettings Method ")
				], Response::HTTP_INTERNAL_SERVER_ERROR);
	}



}


	private function respondWithStatus($arr, $s) {
					$res = new JsonResponse($arr);
					$res->setStatusCode($s);
					return $res;
			}




}
