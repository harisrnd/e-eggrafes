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
	 		 $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config_epal'));
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
					$wsIdentEnabled = $eggrafesConfig->ws_ident->getString();
					$gsisIdentEnabled = $eggrafesConfig->gsis_ident->getString();
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
					'smallClassApproved' => $smallClassApproved,
					'wsIdentEnabled' => $wsIdentEnabled,
					'gsisIdentEnabled' => $gsisIdentEnabled
			], Response::HTTP_OK);

		}	//end try

		catch (\Exception $e) {
			$this->logger->warning($e->getMessage());
			return $this->respondWithStatus([
						"message" => t("An unexpected problem occured during retrieveSettings Method ")
					], Response::HTTP_INTERNAL_SERVER_ERROR);
		}



}

//refers to EPAL
public function storeSettings(Request $request, $capacityDisabled, $directorViewDisabled, $applicantsLoginDisabled, $applicantsAppModifyDisabled,
		$applicantsAppDeleteDisabled, $applicantsResultsDisabled, $secondPeriodEnabled,
		$dateStart, $smallClass, $ws, $gsis ) {

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
 		 $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config_epal'));
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
					$eggrafesConfig->set('lock_small_classes', $smallClass);
					$eggrafesConfig->set('ws_ident', $ws );
					$eggrafesConfig->set('gsis_ident', $gsis );
					$eggrafesConfig->save();
 		 }

		 //αποθήκευση ίδιας τιμής σε ΓΕΛ, για τις ρυθμίσεις ws / gsis
		 $eggrafesGelConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config_gel'));
 		 $eggrafesGelConfig = reset($eggrafesGelConfigs);
 		 if (!$eggrafesGelConfig) {
 				return $this->respondWithStatus([
 								'message' => t("eggrafesGelConfig Enity not found"),
 						], Response::HTTP_FORBIDDEN);
 		 }
		 else {
				 $eggrafesGelConfig->set('ws_ident', $ws );
				 $eggrafesGelConfig->set('gsis_ident', $gsis );
				 $eggrafesGelConfig->save();
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
				'smallClassApproved' =>$smallClass,
				'$wsIdentEnabled' =>$ws,
				'$gsisIdentEnabled' =>$gsis
		], Response::HTTP_OK);

	}	//end try

	catch (\Exception $e) {
		$this->logger->warning($e->getMessage());
		return $this->respondWithStatus([
					"message" => t("An unexpected problem occured during storeSettings Method ")
				], Response::HTTP_INTERNAL_SERVER_ERROR);
	}



}



public function retrieveSettingsGel(Request $request) {

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
		 $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config_gel'));
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
				$wsIdentEnabled = $eggrafesConfig->ws_ident->getString();
				$gsisIdentEnabled = $eggrafesConfig->gsis_ident->getString();
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
				'smallClassApproved' => $smallClassApproved,
				'wsIdentEnabled' => $wsIdentEnabled,
				'gsisIdentEnabled' => $gsisIdentEnabled
		], Response::HTTP_OK);

	}	//end try

	catch (\Exception $e) {
		$this->logger->warning($e->getMessage());
		return $this->respondWithStatus([
					"message" => t("An unexpected problem occured during retrieveSettings Method ")
				], Response::HTTP_INTERNAL_SERVER_ERROR);
	}



}


public function storeSettingsGel(Request $request, $capacityDisabled, $directorViewDisabled, $applicantsLoginDisabled, $applicantsAppModifyDisabled,
	$applicantsAppDeleteDisabled, $applicantsResultsDisabled, $secondPeriodEnabled,
	$dateStart, $smallClassApproved, $ws, $gsis ) {

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
			 $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config_gel'));
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
						$eggrafesConfig->set('ws_ident', $ws );
						$eggrafesConfig->set('gsis_ident', $gsis );
						$eggrafesConfig->save();
			 }

			 //αποθήκευση ίδιας τιμής σε ΕΠΑΛ, για τις ρυθμίσεις ws / gsis
			 $eggrafesEpalConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config_epal'));
			 $eggrafesEpalConfig = reset($eggrafesEpalConfigs);
			 if (!$eggrafesEpalConfig) {
					return $this->respondWithStatus([
									'message' => t("eggrafesEpalConfig Enity not found"),
							], Response::HTTP_FORBIDDEN);
			 }
			 else {
					 $eggrafesEpalConfig->set('ws_ident', $ws );
					 $eggrafesEpalConfig->set('gsis_ident', $gsis );
					 $eggrafesEpalConfig->save();
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
					'$wsIdentEnabled' =>$ws,
					'$gsisIdentEnabled' =>$gsis
			], Response::HTTP_OK);

	}	//end try

	catch (\Exception $e) {
		$this->logger->warning($e->getMessage());
		return $this->respondWithStatus([
					"message" => t("An unexpected problem occured during storeSettings Method ")
				], Response::HTTP_INTERNAL_SERVER_ERROR);
	}



}



public function isWSIdentEnabled(Request $request)
{
			 $authToken = $request->headers->get('PHP_AUTH_USER');
			 $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
			 $user = reset($users);
			 if (!$user) {
					 return $this->respondWithStatus([
							 'message' => t("User not found"),
					 ], Response::HTTP_FORBIDDEN);
			 }

			 $config_storage = $this->entityTypeManager->getStorage('eggrafes_config');
			 $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config_epal'));
			 $eggrafesConfig = reset($eggrafesConfigs);
			 if (!$eggrafesConfig)
						return $this->respondWithStatus([
							 'message' => t("eggrafesConfig Enity not found"),
						], Response::HTTP_FORBIDDEN);
			 else
						return $this->respondWithStatus(array('res' => $eggrafesConfig->ws_ident->value), Response::HTTP_OK);

}

public function isGsisIdentEnabled(Request $request)
{
			 $authToken = $request->headers->get('PHP_AUTH_USER');
			 $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
			 $user = reset($users);
			 if (!$user) {
					 return $this->respondWithStatus([
							 'message' => t("User not found"),
					 ], Response::HTTP_FORBIDDEN);
			 }

			 $config_storage = $this->entityTypeManager->getStorage('eggrafes_config');
			 $eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config_epal'));
			 $eggrafesConfig = reset($eggrafesConfigs);
			 if (!$eggrafesConfig)
						return $this->respondWithStatus([
							 'message' => t("eggrafesConfig Enity not found"),
						], Response::HTTP_FORBIDDEN);
			 else
						return $this->respondWithStatus(array('res' => $eggrafesConfig->gsis_ident->value), Response::HTTP_OK);

}


private function respondWithStatus($arr, $s) {
					$res = new JsonResponse($arr);
					$res->setStatusCode($s);
					return $res;
			}




}
