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


class CustomViews extends ControllerBase {

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

    private function respondWithStatus($arr, $s) {
    					$res = new JsonResponse($arr);
    					$res->setStatusCode($s);
    					return $res;
    }

		public function getSchoolList(Request $request, $schsearch)  {
      try {
          $sCon = $this->connection->select('school_list', 'eSchool')
              ->fields('eSchool', array('name', 'registry_no', 'unit_type_id'));
              //->condition('eSchool.name', '%' . db_like($schsearch) . '%', 'LIKE');
					$words = preg_split('/[\s]+/', $schsearch);
					foreach ($words as $word)
							$sCon->condition('eSchool.name', '%' . db_like($word) . '%', 'LIKE');

					$schools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
          //$school = reset($schools);
					$list = array();
					foreach ($schools as $object) {
							$list[] = array(
									'registry_no' => $object->registry_no,
									'name' => $object->name,
									'unit_type_id' => $object->unit_type_id,
							);
					}
					return $this->respondWithStatus($list, Response::HTTP_OK);

      } catch (\Exception $e) {
          $this->logger->error($e->getMessage());
					return $this->respondWithStatus([
									'message' => t("error in getSchoolList function"),
							], Response::HTTP_FORBIDDEN);
      }

    }




}
