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
use Drupal\Core\Database\Database;
use Drupal\Core\Database\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

//use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\TypedData\Plugin\DataType\TimeStamp;

use Drupal\Core\Language\LanguageManagerInterface;

use Drupal\epal\Crypt;

class ReportsCreator extends ControllerBase
{

    const ERR_DB = -1;
    const NO_CLASS_LIM_DOWN = -2;
    const SMALL_CLS = 1;
    const NON_SMALL_CLS = 2;

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
            $this->logger = $loggerChannel->get('epal');
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

    public function makeReportUsers(Request $request)
    {

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
            foreach ($roles as $role) {
                if ($role === "ministry") {
                    $validRole = true;
                    break;
                }
            }
            if (!$validRole) {
                return $this->respondWithStatus([
                    'message' => t("User Invalid Role"),
                ], Response::HTTP_FORBIDDEN);
            }

            $list = array();

            //υπολογισμός αριθμού αιτήσεων
            $sCon = $this->connection
                ->select('epal_student', 'eStudent')
                ->fields('eStudent', array('id'))
                ->condition('eStudent.delapp', 0, '=');
            $numApplications = $sCon->countQuery()->execute()->fetchField();
            array_push($list, (object) array('name' => "Αριθμός Αιτήσεων (συνολικά)", 'numStudents' => $numApplications));

            //υπολογισμός αριθμού αιτήσεων ανά τάξη
            $classes = [1 => 'Α', 2 => 'Β', 3 => 'Γ', 4 => 'Δ'];
            foreach ($classes as $i => $label) {
                $sCon = $this->connection
                    ->select('epal_student', 'eStudent')
                    ->fields('eStudent', array('id'))
                    ->condition('eStudent.currentclass', strval($i), '=')
                    ->condition('eStudent.delapp', 0, '=');
                $numApplications = $sCon->countQuery()->execute()->fetchField();
                array_push($list, (object) array('name' => "Αριθμός Αιτήσεων για {$label} Τάξη", 'numStudents' => $numApplications));
            }

            //υπολογισμός αριθμού αιτήσεων για δεύτερη περίοδο
            $sCon = $this->connection
                ->select('epal_student', 'eStudent')
                ->fields('eStudent', array('id'))
                ->condition('eStudent.second_period', 1, '=')
                ->condition('eStudent.delapp', 0, '=');
            $numApplications = $sCon->countQuery()->execute()->fetchField();
            array_push($list, (object) array('name' => "Αριθμός Αιτήσεων B' περιόδου", 'numStudents' => $numApplications));


            //υπολογισμός αριθμού αιτήσεων για τρίτη περίοδο
            $datelimit = '31-8-2017';
            $datelimitInt = strtotime($datelimit);

            $sCon = $this->connection
                ->select('epal_student', 'eStudent')
                ->fields('eStudent', array('id'))
                ->condition('eStudent.second_period', 1, '=')
                ->condition('created', $datelimitInt, '>=')
                ->condition('eStudent.delapp', 0, '=');
            $numApplications = $sCon->countQuery()->execute()->fetchField();
            array_push($list, (object) array('name' => "Αριθμός Αιτήσεων περιόδου Σεπτεμβρίου", 'numStudents' => $numApplications));


            //υπολογισμός αριθμού χρηστών
            $sCon = $this->connection
                ->select('applicant_users', 'eUser')
                ->fields('eUser', array('id'));
            $numUsers = $sCon->countQuery()->execute()->fetchField();
            array_push($list, (object) array('name' => "Αριθμός Εγγεγραμένων Χρηστών με ρόλο Αιτούντα", 'numStudents' => $numUsers));

            return $this->respondWithStatus($list, Response::HTTP_OK);
        } //end try

        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                "message" => t("An unexpected problem occured during report")
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function makeGeneralReport(Request $request)
    {

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
            foreach ($roles as $role) {
                if ($role === "ministry") {
                    $validRole = true;
                    break;
                }
            }
            if (!$validRole) {
                return $this->respondWithStatus([
                    'message' => t("User Invalid Role"),
                ], Response::HTTP_FORBIDDEN);
            }

            //υπολογισμός αριθμού δηλώσεων
            $sCon = $this->connection
                ->select('epal_student', 'eStudent')
                ->fields('eStudent', array('id'))
                ->condition('eStudent.delapp', 0, '=');
            $numTotal = $sCon->countQuery()->execute()->fetchField();

            //υπολογισμός αριθμού δηλώσεων που ικανοποιήθηκαν στην i προτίμηση
            $numData = array();
            for ($i=0; $i < 3; $i++) {
                $sCon = $this->connection
                    ->select('epal_student_class', 'eStudent')
                    ->fields('eStudent', array('id', 'distribution_id'))
                    ->condition('eStudent.distribution_id', $i+1, '=')
                    ->condition('eStudent.finalized', 1, '=');
                array_push($numData, $sCon->countQuery()->execute()->fetchField());
            }

            // υπολογισμός αριθμού δηλώσεων που ΔΕΝ ικανοποιήθηκαν
            /*
			$sCon = $this->connection
				->select('epal_student_class', 'eStudent')
				->fields('eStudent', array('student_id'));
			$epalStudents = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
			$studentIds = array();
			foreach ($epalStudents as $epalStudent)
				array_push($studentIds, $epalStudent->student_id);
			$sCon = $this->connection
				->select('epal_student', 'eStudent')
				->fields('eStudent', array('id'))
				->condition('eStudent.id', $studentIds, 'NOT IN');
			$numNoAllocated = $sCon->countQuery()->execute()->fetchField();
			*/
            $sCon = $this->connection->select('epal_student', 'epalStudent')
                ->condition('epalStudent.delapp', 0, '=');
            $sCon->leftJoin('epal_student_class', 'eStudent', 'eStudent.student_id = epalStudent.id');
            $sCon->fields('eStudent', array('student_id'))
                ->fields('epalStudent', array('id'))
                ->isNull('eStudent.student_id');
            $numNoAllocated = $sCon->countQuery()->execute()->fetchField();

            //υπολογισμός αριθμού δηλώσεων που τοποθετήθηκαν προσωρινά σε ολιγομελή τμήματα
            $numInSmallClasses = 0;
            $sCon = $this->connection
                ->select('epal_student_class', 'eStudent')
                ->fields('eStudent', array('id'))
                ->condition('eStudent.finalized', 0, '=');
            $numInSmallClasses = $sCon->countQuery()->execute()->fetchField();

            $list = array(
                array('name' => "Αριθμός Δηλώσεων Προτίμησης", 'numStudents' => $numTotal),
                array('name' => "Αριθμός μαθητών που τοποθετήθηκαν στην πρώτη τους προτίμηση", 'numStudents' => $numData[0]),
                array('name' => "Αριθμός μαθητών που τοποθετήθηκαν στην δεύτερή τους προτίμηση", 'numStudents' => $numData[1]),
                array('name' => "Αριθμός μαθητών που τοποθετήθηκαν στην τρίτη τους προτίμηση", 'numStudents' => $numData[2]),
                array('name' => "Αριθμός μαθητών που δεν τοποθετήθηκαν σε καμμία τους προτίμηση", 'numStudents' => $numNoAllocated),
                array('name' => "Αριθμός μαθητών που τοποθετήθηκαν προσωρινά σε ολιγομελή τμήματα", 'numStudents' => $numInSmallClasses)
            );

            return $this->respondWithStatus($list, Response::HTTP_OK);
        } //end try

        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                "message" => t("An unexpected problem occured during report")
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function makeReportCompleteness(Request $request, $regionId, $adminId, $schId)
    {

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
            foreach ($roles as $role) {
                if ($role === "ministry" || $role === "regioneduadmin" || $role === "eduadmin") {
                    $validRole = $role;
                    break;
                }
            }
            if ($validRole === false) {
				return $this->respondWithStatus([
					'message' => t("User Invalid Role"),
				], Response::HTTP_FORBIDDEN);
            }

            if (!$this->canReportOn($user, $role, $regionId, $adminId, $schId)) {
				return $this->respondWithStatus([
					'message' => t('User access to area forbidden'),
				], Response::HTTP_FORBIDDEN);
            }

            $list = array();

            //βρες ανώτατο επιτρεπόμενο όριο μαθητών
            $limitUp = $this->retrieveUpLimit();

            //βρες όλα τα σχολεία που πληρούν τα κριτήρια / φίλτρα
            $sCon = $this->connection->select('eepal_school_field_data', 'eSchool');
			$sCon->join('eepal_region_field_data', 'eRegion', 'eRegion.id = eSchool.region_edu_admin_id');
			$sCon->join('eepal_admin_area_field_data', 'eAdmin', 'eAdmin.id = eSchool.edu_admin_id');
			$sCon->leftJoin('eepal_sectors_in_epal_field_data', 'sectors', 'sectors.epal_id = eSchool.id');
			$sCon->fields('eSchool', array('id', 'name', 'capacity_class_a', 'region_edu_admin_id', 'edu_admin_id', 'operation_shift'))
				->fields('eRegion', ['name'])
				->fields('eAdmin', ['name'])
                ->groupBy('id')
                ->groupBy('name')
                ->groupBy('capacity_class_a')
                ->groupBy('region_edu_admin_id')
                ->groupBy('edu_admin_id')
                ->groupBy('operation_shift')
                ->groupBy('eRegion_name')
                ->groupBy('eAdmin_name');
            $sCon->addExpression('sum(sectors.capacity_class_sector)', 'capacity_class_b');
            if ($regionId != 0) {
				$sCon->condition('eSchool.region_edu_admin_id', $regionId, '=');
            }
            if ($adminId != 0) {
				$sCon->condition('eSchool.edu_admin_id', $adminId, '=');
            }
            if ($schId != 0) {
                $sCon->condition('eSchool.id', $schId, '=');
            }
            $epalSchools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            foreach ($epalSchools as $epalSchool) {
                //εύρεση ονόματος ΠΔΕ που ανήκει το σχολείο
                $regionColumn = $epalSchool->eRegion_name;
                //εύρεση ονόματος ΔΙΔΕ που ανήκει το σχολείο
                $adminColumn = $epalSchool->eAdmin_name;

                //βρες μέγιστη χωρητικότητα για κάθε τάξη
                $capacity = array();
                //χωρητικότητα για Α' τάξη
                array_push($capacity, $epalSchool->capacity_class_a * $limitUp );

                //χωρητικότητα για Β' τάξη
                array_push($capacity, $epalSchool->capacity_class_b * $limitUp);

                //χωρητικότητα για Γ' τάξη
                $sCon = $this->connection
					->select('eepal_specialties_in_epal_field_data', 'eSchool')
                    ->fields('eSchool', array('id',  'capacity_class_specialty'))
                    ->condition('eSchool.epal_id', $epalSchool->id, '=');
                $specialtiesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                $numClassSpecialties = 0;
                foreach ($specialtiesInEpals as $specialtiesInEpal) {
                    $numClassSpecialties += $specialtiesInEpal->capacity_class_specialty;
                }
                array_push($capacity, $numClassSpecialties * $limitUp);

                //χωρητικότητα για Δ' τάξη
                if ($epalSchool->operation_shift === "ΕΣΠΕΡΙΝΟ") {
                    $sCon = $this->connection
                        ->select('eepal_specialties_in_epal_field_data', 'eSchool')
                        ->fields('eSchool', array('id',  'capacity_class_specialty_d'))
                        ->condition('eSchool.epal_id', $epalSchool->id, '=');
                    $specialtiesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                    $numClassSpecialtiesNight = 0;
                    foreach ($specialtiesInEpals as $specialtiesInEpal) {
                        $numClassSpecialtiesNight += $specialtiesInEpal->capacity_class_specialty_d;
                    }
					array_push($capacity, $numClassSpecialtiesNight * $limitUp);
                } else {
                    array_push($capacity, -1);
                }

                //χωρητικότητα για όλο το σχολείο
                $capacityTotal = array_reduce($capacity, function ($sum, $v) {
                        return $sum += ($v > 0) ? intval($v) : 0;
                    }, 0);

                //βρες αριθμό μαθητών γισ κάθε τάξη
                $num = array();
                $perc = array();
                $stat_complete = true;
                for ($classId = 1; $classId <= 4; $classId++) {
                    $sCon = $this->connection
						->select('epal_student_class', 'eStudent')
                        ->fields('eStudent', array('id', 'epal_id', 'currentclass'))
                        ->condition('eStudent.epal_id', $epalSchool->id, '=')
                        ->condition('eStudent.currentclass', $classId, '=');
                    array_push( $num, $sCon->countQuery()->execute()->fetchField());
                    //βρες ποσοστά συμπλήρωσης
                    if (isset($capacity[$classId-1]) && $capacity[$classId-1] > 0) {
                        $perc_str = number_format($num[$classId-1] / $capacity[$classId-1] * 100, 2);
                    } elseif (isset($capacity[$classId-1]) && $capacity[$classId-1] == -1) {
                        $perc_str = '-';
                    } else {
                        $perc_str = '-';
                        $stat_complete = false;
                    }
                    array_push($perc, $perc_str);
                }

                if ($stat_complete === true && $capacityTotal > 0) {
                    $percTotal = number_format(array_sum($num) / $capacityTotal * 100, 2);
                } else {
                    $percTotal = '-';
                }

                //αποστολή αποτελεσμάτων / στατιστικών
				array_push($list, (object) array(
					'name' => $epalSchool->name,
					'region' => $regionColumn,
					'admin' => $adminColumn,
					'percTotal' => $percTotal,
					'percA' => $perc[0],
					'percB' => $perc[1],
					'percC' => $perc[2],
					'percD' => $perc[3]
				));
            }

			return $this->respondWithStatus($list, Response::HTTP_OK);
        } //end try

        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                "message" => t("An unexpected problem occured during report")
			], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    private function isSmallClass($schoolId, $numStud, $classId, $sectorOrcourseId, $regionId)
    {

        $limitDown = $this->retrieveLimitDown($classId, $regionId);

        if ($limitDown === self::NO_CLASS_LIM_DOWN) {
            return self::NO_CLASS_LIM_DOWN;
        } elseif ($limitDown === self::ERR_DB) {
            return self::ERR_DB;
        }

        $numStudents = (int) $numStud;
        if (($numStudents < $limitDown) /*&& ($numStudents > 0)*/) {
            return self::SMALL_CLS;
        } else {
            return self::NON_SMALL_CLS;
        }
    }


    /**
     * Fetch epal_class_limits data in memory; will save time.
     *
     */
    private function retrieveLimitDown($classId, $regionId)
    {
        static $data = [];

        if (count($data) === 0) {
            try {
                $sCon = $this->connection
                    ->select('epal_class_limits', 'eClassLimit')
                    ->fields('eClassLimit', ['name', 'category', 'limit_down']);
                $classLimits = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                if ($classLimits !== FALSE) {
                    foreach ($classLimits as $limit) {
                        $data["$limit->name"]["$limit->category"] = $limit->limit_down;
                    }
                } else {
                    return self::NO_CLASS_LIM_DOWN;
                }
            } catch (\Exception $e) {
                $this->logger->warning($e->getMessage());
                return self::ERR_DB;
            }
        }

        if (isset($data["$classId"]["$regionId"])) {
            return $data["$classId"]["$regionId"];
        } else {
            return self::NO_CLASS_LIM_DOWN;
        }
    }

    public function retrieveUserRegistryNo(Request $request)
    {

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
            foreach ($roles as $role) {
                if ($role === "regioneduadmin" || $role === "eduadmin") {
                    $validRole = true;
                    break;
                }
            }
            if (!$validRole) {
				return $this->respondWithStatus([
					'message' => t("User Invalid Role"),
				], Response::HTTP_FORBIDDEN);
            }

            return $this->respondWithStatus([
				'message' => t("retrieve ID successful"),
				'id' => $user->init->value,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
				"message" => t("An unexpected problem occured in retrievePDEId Method")
			], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function makeReportNoCapacity(Request $request, $capacityEnabled)
    {

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
            foreach ($roles as $role) {
                if ($role === "ministry") {
                    $validRole = true;
                    break;
                }
            }
            if (!$validRole) {
				return $this->respondWithStatus([
					'message' => t("User Invalid Role"),
				], Response::HTTP_FORBIDDEN);
            }

            $list = array();

            //βρες όλα τα σχολεία
            $sCon = $this->connection
				->select('eepal_school_field_data', 'eSchool')
                ->fields('eSchool', array('id', 'name', 'capacity_class_a', 'region_edu_admin_id', 'edu_admin_id','operation_shift'));

            //if ($capacityEnabled === "0")
            //	$sCon->condition('eSchool.capacity_class_a', 0, '=');

            $epalSchools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

            foreach ($epalSchools as $epalSchool) {       //για κάθε σχολείο

                $schoolNameColumn = array();
                $regionColumn = array();
                $adminColumn = array();
                $schoolSectionColumn = array();
                $capacityColumn = array();

                //εύρεση ονόματος ΠΔΕ που ανήκει το σχολείο
                $sCon = $this->connection
					->select('eepal_region_field_data', 'eRegion')
                    ->fields('eRegion', array('id','name'))
                    ->condition('eRegion.id', $epalSchool->region_edu_admin_id, '=');
                $epalRegions = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                $epalRegion = reset($epalRegions);

                //εύρεση ονόματος ΔΙΔΕ που ανήκει το σχολείο
                $sCon = $this->connection
					->select('eepal_admin_area_field_data', 'eAdmin')
                    ->fields('eAdmin', array('id','name'))
                    ->condition('eAdmin.id', $epalSchool->edu_admin_id, '=');
                $epalAdmins = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                $epalAdmin = reset($epalAdmins);

                //εύρεση αριθμού τμημάτων (χωρητικότητα) για κάθε τμήμα της Α' τάξης
                //$epalSchool->capacity_class_a === "0" ||
                if ( ($capacityEnabled === '0' && ( !isset($epalSchool->capacity_class_a)))  ||  ($capacityEnabled === "1")) {
					array_push($regionColumn, $epalRegion->name);
					array_push($adminColumn, $epalAdmin->name);
					array_push($schoolNameColumn, $epalSchool->name);
					array_push($schoolSectionColumn, 'Α\' τάξη');
					array_push($capacityColumn, $epalSchool->capacity_class_a);
                }

                //εύρεση αριθμού τμημάτων (χωρητικότητα) για κάθε τομέα της Β' τάξης
                //ΠΡΟΣΟΧΗ: χειρισμ΄ός τιμών: 0 (ΟΧΙ??) και null

                $sCon = $this->connection
					->select('eepal_sectors_in_epal_field_data', 'eSchool')
                    ->fields('eSchool', array('sector_id','capacity_class_sector'))
                    ->condition('eSchool.epal_id', $epalSchool->id, '=');
                //$db_or = db_or();
                //$db_or->condition('eSchool.capacity_class_sector', 0, '=');
                //$db_or->condition('eSchool.capacity_class_sector', null, 'is');
                //$sCon->condition($db_or) ;
                if ($capacityEnabled === "0") {
                    $sCon->condition( db_or()
						//->condition('eSchool.capacity_class_sector', 0, '=')
						->condition('eSchool.capacity_class_sector', 0, '=')  ) ;
                }
                $sectorsInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

                foreach ($sectorsInEpals as $sectorsInEpal) {
                    $sCon = $this->connection
						->select('eepal_sectors_field_data', 'eSectors')
                        ->fields('eSectors', array('name'))
                        ->condition('eSectors.id', $sectorsInEpal->sector_id, '=');

                    $sectorsNamesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                    foreach ($sectorsNamesInEpals as $sectorsNamesInEpal) {
                        array_push($regionColumn, $epalRegion->name);
                        array_push($adminColumn, $epalAdmin->name);
                        array_push($schoolNameColumn, $epalSchool->name);
                        array_push($schoolSectionColumn, 'Β\' τάξη / ' . $sectorsNamesInEpal->name );
                        array_push($capacityColumn, $sectorsInEpal->capacity_class_sector);
                    }   //end foreach sectorsNamesInEpals
                }   //end foreach sectorsInEpal

                //εύρεση αριθμού τμημάτων (χωρητικότητα) για κάθε ειδικότητα της Γ' τάξης
                $sCon = $this->connection
					->select('eepal_specialties_in_epal_field_data', 'eSchool')
                    ->fields('eSchool', array('specialty_id', 'capacity_class_specialty'))
                    ->condition('eSchool.epal_id', $epalSchool->id, '=');

                if ($capacityEnabled === "0") {
					$sCon->condition( db_or()
						//->condition('eSchool.capacity_class_specialty', 0, '=')
						->condition('eSchool.capacity_class_specialty', 0, '=')  ) ;
                }

                $specialtiesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

                foreach ($specialtiesInEpals as $specialtiesInEpal) {
					$sCon = $this->connection
						->select('eepal_specialty_field_data', 'eSpecialties')
                        ->fields('eSpecialties', array('name'))
                        ->condition('eSpecialties.id', $specialtiesInEpal->specialty_id, '=');

					$specialtiesNamesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                    foreach ($specialtiesNamesInEpals as $specialtiesNamesInEpal) {
						array_push($regionColumn, $epalRegion->name);
						array_push($adminColumn, $epalAdmin->name);
						array_push($schoolNameColumn, $epalSchool->name);
						array_push($schoolSectionColumn, 'Γ\' τάξη / ' . $specialtiesNamesInEpal->name );
						array_push($capacityColumn, $specialtiesInEpal->capacity_class_specialty);
                    }   //end foreach $specialtiesNamesInEpal
                } //end foreach $specialtiesInEpals

                //εύρεση αριθμού τμημάτων (χωρητικότητα) για κάθε ειδικότητα της Δ' τάξης
                $sCon = $this->connection
					->select('eepal_specialties_in_epal_field_data', 'eSchool')
                    ->fields('eSchool', array('specialty_id', 'capacity_class_specialty_d'))
                    ->condition('eSchool.epal_id', $epalSchool->id, '=');

                if ($capacityEnabled === "0") {
					$sCon->condition( db_or()
						//->condition('eSchool.capacity_class_specialty_d', 0, '=')
						->condition('eSchool.capacity_class_specialty_d', 0, '=')  ) ;
                }

                $specialtiesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

                foreach ($specialtiesInEpals as $specialtiesInEpal) {
					$sCon = $this->connection
						->select('eepal_specialty_field_data', 'eSpecialties')
                        ->fields('eSpecialties', array('name'))
                        ->condition('eSpecialties.id', $specialtiesInEpal->specialty_id, '=');

					$specialtiesNamesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                    foreach ($specialtiesNamesInEpals as $specialtiesNamesInEpal) {
                        if ($epalSchool->operation_shift === "ΕΣΠΕΡΙΝΟ") {
                            array_push($regionColumn, $epalRegion->name);
                            array_push($adminColumn, $epalAdmin->name);
                            array_push($schoolNameColumn, $epalSchool->name);
                            array_push($schoolSectionColumn, 'Δ\' τάξη / ' . $specialtiesNamesInEpal->name );
                            array_push($capacityColumn, $specialtiesInEpal->capacity_class_specialty_d);
                        }
                    }   //end foreach $specialtiesNamesInEpal
                } //end foreach $specialtiesInEpals

                //εισαγωγή εγγραφών στο tableschema
                for ($j = 0; $j < sizeof($schoolNameColumn); $j++) {
					array_push($list, (object) array(
						'name' => $schoolNameColumn[$j],
						'region' => $regionColumn[$j],
						'admin' => $adminColumn[$j],
						'section' => str_replace(",", " ", $schoolSectionColumn[$j]),
						'capacity' => $capacityColumn[$j],
					));
                }
            } //end foreach school

            return $this->respondWithStatus($list, Response::HTTP_OK);
        } //end try

        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
				"message" => t("An unexpected problem occured during makeReportNoCapacity Method")
			], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function retrieveUpLimit()
    {

        //βρες ανώτατο επιτρεπόμενο όριο μαθητών
        //$limitup = 1;
        try {
            $sCon = $this->connection
				->select('epal_class_limits', 'eSchool')
                ->fields('eSchool', array('name', 'limit_up'))
                ->condition('eSchool.name', '1', '=');
            $epalLimitUps = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            $epalLimitUp = reset($epalLimitUps);
            //$limitup = $epalLimitUp->limit_up;
            //return $limitup;
            return $epalLimitUp->limit_up;
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return -1;
        }
    }


    public function retrieveDownLimit($category,$classId)
    {

        try {
            $sCon = $this->connection->select('epal_class_limits', 'eSchool');
            $sCon->fields('eSchool', array('name', 'limit_down'));
            $sCon->condition('eSchool.name', $classId, '=');
            $sCon->condition('eSchool.category', $category, '=');
            $epalLimitDowns = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            $epalLimitDown = reset($epalLimitDowns);

            return $epalLimitDown->limit_down;
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return -1;
        }
    }

    /**
     * Check if $user, under $role, can issue the report on the
     * designated region, admin area and school.
     *
     * @return boolean
     */
    protected function canReportOn($user, $role, $regionId, $adminId, $schId)
    {
        if ($role === 'ministry') {
            $can = true;
        } elseif ($role === 'regioneduadmin') {
            $can = (
                ($user->init->value == $regionId)
                && (($adminId == 0) || $this->isAdminUnderRegion($adminId, $regionId))
            );
        } elseif ($role === 'eduadmin') {
            $can = (
                ($user->init->value == $adminId)
                && (($regionId == 0) || $this->isAdminUnderRegion($adminId, $regionId))
                && (($schId == 0) || $this->isSchoolUnderAdmin($schId, $adminId))
            );
        } else {
            $can = false;
        }
        return $can;
    }

    protected function isSchoolUnderAdmin($schId, $adminId)
    {
        $map = $this->entityTypeManager
            ->getStorage('eepal_school')
            ->loadByProperties([
                'id' => $schId,
                'edu_admin_id' => $adminId,
            ]);
        $existing_map = reset($map);
        if (!$existing_map) {
            return false;
        } else {
            return true;
        }
    }

    protected function isAdminUnderRegion($adminId, $regionId)
    {
        $map = $this->entityTypeManager
            ->getStorage('eepal_admin_area')
            ->loadByProperties([
                'id' => $adminId,
                'region_to_belong' => $regionId,
            ]);
        $existing_map = reset($map);
        if (!$existing_map) {
            return false;
        } else {
            return true;
        }
    }

    private function respondWithStatus($arr, $s)
    {
        $res = new JsonResponse($arr);
        $res->setStatusCode($s);
        return $res;
    }


    public function makeReportMergedClasses(Request $request, $regionId, $adminId, $schId, $classId, $sectorId, $courseId, $finalized)
    {
        try {
            $list = array();

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
            foreach ($roles as $role) {
                if ($role === "ministry" || $role === "regioneduadmin" || $role === "eduadmin") {
                    $validRole = $role;
                    break;
                }
            }
            if ($validRole === false) {
                return $this->respondWithStatus([
                    'message' => t("User Invalid Role"),
                ], Response::HTTP_FORBIDDEN);
            }
            if (!$this->canReportOn($user, $role, $regionId, $adminId, $schId)) {
                return $this->respondWithStatus([
                    'message' => t('User access to area forbidden'),
                ], Response::HTTP_FORBIDDEN);
            }


            if ( ($classId==3 || $classId==4) && $sectorId!=0 && $courseId==0 ){
                $sCon = \Drupal::database()->select('eepal_specialty_field_data', 'eSectors');
                $sCon->fields('eSectors', array('sector_id', 'id', 'name', ));
                $sCon->condition('eSectors.sector_id', $sectorId, '=');
                $specialties_in_sector = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            }
            $sector_specialties=array();
            foreach ($specialties_in_sector as $specialty){
                array_push($sector_specialties,$specialty->id);
            }



            $sCon = \Drupal::database()->select('epal_student_class', 'eStudent');
            $sCon->leftjoin('eepal_school_field_data','eSchool_initial','eSchool_initial.id=eStudent.initial_epal_id');
            $sCon->join('eepal_region_field_data', 'eRegion_initial', 'eRegion_initial.id = eSchool_initial.region_edu_admin_id');
            $sCon->join('eepal_admin_area_field_data', 'eAdmin_initial', 'eAdmin_initial.id = eSchool_initial.edu_admin_id');
            $sCon->join('eepal_school_field_data','eSchool_epal','eSchool_epal.id=eStudent.epal_id');
            $sCon->join('eepal_region_field_data', 'eRegion_epal', 'eRegion_epal.id = eSchool_epal.region_edu_admin_id');
            $sCon->join('eepal_admin_area_field_data', 'eAdmin_epal', 'eAdmin_epal.id = eSchool_epal.edu_admin_id');
            $sCon->fields('eStudent', array('initial_epal_id', 'epal_id', 'currentclass', 'specialization_id'));
            $sCon->addField('eRegion_initial', 'name','regionAName');
            $sCon->addField('eAdmin_initial', 'name','adminAName');
            $sCon->addField('eSchool_initial', 'name','schoolAName');
            $sCon->addField('eRegion_epal', 'name','regionBName');
            $sCon->addField('eAdmin_epal', 'name','adminBName');
            $sCon->addField('eSchool_epal', 'name','schoolBName');
            if ($classId != 0) {
                $sCon->condition('eStudent.currentclass', $classId, '=');
            }
            if ($sectorId != 0) {
                if ($classId==2){
                    $sCon->condition('eStudent.specialization_id', $sectorId, '=');
                }
                if (($classId==3 || $classId==4) && $courseId==0){
                    $sCon->condition('eStudent.specialization_id', $sector_specialties, 'IN');
                }

            }
            if ($courseId!=0){
                if ($classId==3 || $classId==4){
                    $sCon->condition('eStudent.specialization_id', $courseId, '=');
                }
            }
            if ($regionId != 0) {
                $sCon->condition('eSchool_initial.region_edu_admin_id', $regionId, '=');
            }
            if ($adminId != 0) {
                $sCon->condition('eSchool_initial.edu_admin_id', $adminId, '=');
            }
            if ($schId != 0) {
                $sCon->condition('eSchool_initial.id', $schId, '=');
            }
            $sCon->condition('eStudent.initial_epal_id', 0, '!=');
            $sCon->groupBy('initial_epal_id');
            $sCon->groupBy('epal_id');
            $sCon->groupBy('currentclass');
            $sCon->groupBy('specialization_id');
            $sCon->groupBy('eRegion_initial.name');
            $sCon->groupBy('eAdmin_initial.name');
            $sCon->groupBy('eSchool_initial.name');
            $sCon->groupBy('eRegion_epal.name');
            $sCon->groupBy('eAdmin_epal.name');
            $sCon->groupBy('eSchool_epal.name');
            $sCon->addExpression('count(eStudent.id)', 'eStudent_count');
            $mergedSchools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);




            $schoolANameColumn = array();
            $regionAColumn = array();
            $adminAColumn = array();
            $schoolSectionColumn = array();
            $numAColumn = array();
            $schoolBNameColumn = array();
            $regionBColumn = array();
            $adminBColumn = array();
            $numBColumn = array();

            foreach ($mergedSchools as $mergedSchool) {

                array_push($regionAColumn, $mergedSchool->regionAName);
                array_push($adminAColumn, $mergedSchool->adminAName);
                array_push($schoolANameColumn, $mergedSchool->schoolAName);
                if ($mergedSchool->currentclass==1){
                    array_push($schoolSectionColumn, 'Α Tάξη');
                }
                else if ($mergedSchool->currentclass==2){
                    $sCon = \Drupal::database()->select('eepal_sectors_field_data', 'eSectors');
                    $sCon->addField('eSectors', 'name');
                    $sCon->condition('eSectors.id', $mergedSchool->specialization_id, '=');
                    $sector_name = $sCon->execute()->fetchField();
                    array_push($schoolSectionColumn, 'Β τάξη / ' . $sector_name);
                }
                else{
                    $sCon = \Drupal::database()->select('eepal_specialty_field_data', 'eSpecialties');
                    $sCon->join('eepal_sectors_field_data', 'eSectors','eSectors.id=eSpecialties.sector_id');
                    $sCon->addField('eSectors', 'name','sectorName');
                    $sCon->addField('eSpecialties', 'name','specialtyName');
                    $sCon->condition('eSpecialties.id', $mergedSchool->specialization_id, '=');
                    $sector_specialty = $sCon->execute()->fetchAssoc();
                    if ($mergedSchool->currentclass==3){
                        array_push($schoolSectionColumn, 'Γ Tάξη / ' . $sector_specialty['sectorName'].'/'.$sector_specialty['specialtyName']);//->specialization_id);
                    }
                    else if ($mergedSchool->currentclass==4){
                        array_push($schoolSectionColumn, 'Δ Tάξη / ' . $mergedSchool->specialization_id);
                    }
                }

                array_push($numAColumn, $mergedSchool->eStudent_count);
                array_push($regionBColumn, $mergedSchool->regionBName);
                array_push($adminBColumn, $mergedSchool->adminBName);
                array_push($schoolBNameColumn, $mergedSchool->schoolBName);

                $sCon = \Drupal::database()->select('epal_student_class', 'eStudent');
                $sCon->condition('eStudent.epal_id', $mergedSchool->epal_id, '=');
                $sCon->condition('eStudent.currentclass', $mergedSchool->currentclass, '=');
                $sCon->condition('eStudent.specialization_id', $mergedSchool->specialization_id, '=');
                $sCon->addExpression('count(eStudent.id)', 'eStudent_extra_count');
                $sCon->groupBy('eStudent.epal_id');
                $sCon->groupBy('eStudent.currentclass');
                $sCon->groupBy('eStudent.specialization_id');
                $eStudent_extra_count = $sCon->execute()->fetchAssoc();
                array_push($numBColumn, $eStudent_extra_count['eStudent_extra_count']);

            }

            for ($j = 0; $j < sizeof($schoolANameColumn); $j++) {

                array_push($list, (object) array(
                    'nameΑ' => $schoolANameColumn[$j],
                    'regionΑ' => $regionAColumn[$j],
                    'adminΑ' => $adminAColumn[$j],
                    'sectionΑ' => str_replace(",", "", $schoolSectionColumn[$j]),
                    'numΑ' => $numAColumn[$j],
                    'nameΒ' => $schoolBNameColumn[$j],
                    'regionΒ' => $regionBColumn[$j],
                    'adminΒ' => $adminBColumn[$j],
                    'numΒ' => $numBColumn[$j],
                ));
            }

            return $this->respondWithStatus($list, Response::HTTP_OK);

        }
        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                "message" => t("An unexpected problem occured in makeReportCompleteness Method")
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function makeReportApplications(Request $request, $regionId, $adminId, $schId, $classId, $sectorId, $courseId, $finalized)
    {

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
            foreach ($roles as $role) {
                if ($role === "ministry") {
                    $validRole = true;
                    break;
                }
            }
            if (!$validRole) {
                return $this->respondWithStatus([
                    'message' => t("User Invalid Role"),
                ], Response::HTTP_FORBIDDEN);
            }

            $list = array();

            if ( ($classId==3 || $classId==4) && $sectorId!=0 && $courseId==0 ){
                $sCon = \Drupal::database()->select('eepal_specialty_field_data', 'eSectors');
                $sCon->fields('eSectors', array('sector_id', 'id', 'name', ));
                $sCon->condition('eSectors.sector_id', $sectorId, '=');
                $specialties_in_sector = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            }
            $sector_specialties=array();
            foreach ($specialties_in_sector as $specialty){
                array_push($sector_specialties,$specialty->id);
            }


            $sCon = \Drupal::database()->select('epal_student', 'eStudent');
            $sCon->join('epal_student_epal_chosen','eSchool_chosen','eSchool_chosen.student_id=eStudent.id');
            $sCon->leftjoin('epal_student_sector_field', 'eSector', 'eSector.student_id = eStudent.id AND eStudent.currentclass=2');
            $sCon->leftjoin('epal_student_course_field', 'eCourse', 'eCourse.student_id = eStudent.id  AND eStudent.currentclass>2');
            $sCon->join('eepal_school_field_data','eSchool','eSchool.id=eSchool_chosen.epal_id');
            $sCon->join('eepal_region_field_data', 'eRegion', 'eRegion.id = eSchool.region_edu_admin_id');
            $sCon->join('eepal_admin_area_field_data', 'eAdmin', 'eAdmin.id = eSchool.edu_admin_id');
            $sCon->leftjoin('eepal_sectors_field_data','eSectorTitle','eSectorTitle.id=eSector.sectorfield_id');
            $sCon->leftjoin('eepal_specialty_field_data','eCourseTitle','eCourseTitle.id=eCourse.coursefield_id');
            $sCon->addField('eRegion', 'name','regionName');
            $sCon->addField('eAdmin', 'name','adminName');
            $sCon->addField('eSchool', 'name','schoolName');
            $sCon->addField('eStudent', 'currentclass');
            $sCon->addField('eSchool_chosen','epal_id');
            $sCon->addField('eCourse', 'coursefield_id');
            $sCon->addField('eSector', 'sectorfield_id');
            $sCon->addField('eSectorTitle', 'name','sectorname');
            $sCon->addField('eCourseTitle', 'name','specialtyname');


            if ($classId != 0) {
                $sCon->condition('eStudent.currentclass', $classId, '=');
            }
            if ($sectorId != 0) {
                if ($classId==2){
                    $sCon->condition('eSector.sectorfield_id', $sectorId, '=');
                }
                if (($classId==3 || $classId==4) && $courseId==0){
                    $sCon->condition('eCourse.coursefield_id', $sector_specialties, 'IN');
                }

            }
            if ($courseId!=0){
                if ($classId==3 || $classId==4){
                    $sCon->condition('eCourse.coursefield_id', $courseId, '=');
                }
            }

            if ($regionId != 0) {
                $sCon->condition('eSchool.region_edu_admin_id', $regionId, '=');
            }
            if ($adminId != 0) {
                $sCon->condition('eSchool.edu_admin_id', $adminId, '=');
            }
            if ($schId != 0) {
                $sCon->condition('eSchool_chosen.epal_id', $schId, '=');
            }
            $sCon->condition('eStudent.delapp', 0, '=');
            $sCon->condition('eSchool_chosen.choice_no', 1, '=');
            $sCon->groupBy('eStudent.currentclass');
            $sCon->groupBy('sectorfield_id');
            $sCon->groupBy('coursefield_id');
            $sCon->groupBy('eRegion.name');
            $sCon->groupBy('eAdmin.name');
            $sCon->groupBy('eSchool.name');
            $sCon->groupBy('eSchool_chosen.epal_id');
            $sCon->groupBy('sectorname');
            $sCon->groupBy('specialtyname');
            $sCon->addExpression('count(eStudent.id)', 'eStudent_count');
            $applications = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);



            $schoolNameColumn = array();
            $regionColumn = array();
            $adminColumn = array();
            $schoolSectionColumn = array();
            $numColumn = array();


            foreach ($applications as $application) {

                array_push($regionColumn, $application->regionName);
                array_push($adminColumn, $application->adminName);
                array_push($schoolNameColumn, $application->schoolName);
                if ($application->currentclass==1){
                    array_push($schoolSectionColumn, 'Α Tάξη');
                }else if($application->currentclass==2){
                    array_push($schoolSectionColumn, 'B Tάξη / '.$application->sectorname);
                }else{
                    $sCon = \Drupal::database()->select('eepal_specialty_field_data', 'eSpecialties');
                    $sCon->join('eepal_sectors_field_data', 'eSectors','eSectors.id=eSpecialties.sector_id');
                    $sCon->addField('eSectors', 'name','sectorName');
                    $sCon->addField('eSpecialties', 'name','specialtyName');
                    $sCon->condition('eSpecialties.id', $application->coursefield_id, '=');
                    $sector_specialty = $sCon->execute()->fetchAssoc();
                    if($application->currentclass==3){
                        array_push($schoolSectionColumn, 'Γ Tάξη / '.$sector_specialty['sectorName'].' / '.$sector_specialty['specialtyName']);
                    }else if($application->currentclass==4){
                        array_push($schoolSectionColumn, 'Δ Tάξη / '.$sector_specialty['sectorName'].' / '.$sector_specialty['specialtyName']);
                    }
                }
                array_push($numColumn,$application->eStudent_count);

            }

            for ($j = 0; $j < sizeof($schoolNameColumn); $j++) {

                array_push($list, (object) array(
                    'name' => $schoolNameColumn[$j],
                    'region' => $regionColumn[$j],
                    'admin' => $adminColumn[$j],
                    'section' => str_replace(",", "", $schoolSectionColumn[$j]),
                    'num' => $numColumn[$j],
                ));
            }

            return $this->respondWithStatus($list, Response::HTTP_OK);

        }
        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                "message" => t("An unexpected problem occured in makeReportApplications Method")
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function makeReportUserApplications(Request $request)
    {

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
            foreach ($roles as $role) {
                if ($role === "ministry") {
                    $validRole = true;
                    break;
                }
            }
            if (!$validRole) {
                return $this->respondWithStatus([
                    'message' => t("User Invalid Role"),
                ], Response::HTTP_FORBIDDEN);
            }

            $list = array();
            $studentIdColumn = array();
            $numAppsColumn = array();

            $sCon = \Drupal::database()->select('epal_student', 'eStudent');
            $sCon->addField('eStudent','user_id','studentId');
            $sCon->condition('eStudent.delapp', 0 , '=');
            $sCon->groupBy('eStudent.user_id');
            $sCon->addExpression('count(eStudent.user_id)', 'apps_count');
            $sCon->having('count(eStudent.user_id)>5');
            $sCon->orderBy('apps_count', 'DESC');
            $applications = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);


            foreach ($applications as $application) {

                array_push($studentIdColumn, $application->studentId);
                array_push($numAppsColumn, $application->apps_count);
            }

             for ($j = 0; $j < sizeof($studentIdColumn); $j++) {

                 array_push($list, (object) array(
                    'studentId' => $studentIdColumn[$j],
                    'numapps' => $numAppsColumn[$j],
                ));
            }

            return $this->respondWithStatus($list, Response::HTTP_OK);

        }
        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                "message" => t("An unexpected problem occured in makeReportUserApplications Method")
                   ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function makeReportGelStudents(Request $request,$regionId, $adminId, $schId, $classId, $sectorId, $courseId, $finalized)
    {
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
            foreach ($roles as $role) {
                if ($role === "ministry") {
                    $validRole = true;
                    break;
                }
            }
            if (!$validRole) {
                return $this->respondWithStatus([
                    'message' => t("User Invalid Role"),
                ], Response::HTTP_FORBIDDEN);
            }

            $list = array();

            if ( ($classId==3 || $classId==4) && $sectorId!=0 && $courseId==0 ){
                $sCon = \Drupal::database()->select('eepal_specialty_field_data', 'eSectors');
                $sCon->fields('eSectors', array('sector_id', 'id', 'name', ));
                $sCon->condition('eSectors.sector_id', $sectorId, '=');
                $specialties_in_sector = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            }
            $sector_specialties=array();
            foreach ($specialties_in_sector as $specialty){
                array_push($sector_specialties,$specialty->id);
            }


            $sCon = \Drupal::database()->select('epal_student', 'eStudent');
            $sCon->join('epal_student_epal_chosen','eSchool_chosen','eSchool_chosen.student_id=eStudent.id');
            $sCon->leftjoin('epal_student_sector_field', 'eSector', 'eSector.student_id = eStudent.id AND eStudent.currentclass=2');
            $sCon->leftjoin('epal_student_course_field', 'eCourse', 'eCourse.student_id = eStudent.id  AND eStudent.currentclass>2');
            $sCon->join('eepal_school_field_data','eSchool','eSchool.id=eSchool_chosen.epal_id');
            $sCon->join('eepal_region_field_data', 'eRegion', 'eRegion.id = eSchool.region_edu_admin_id');
            $sCon->join('eepal_admin_area_field_data', 'eAdmin', 'eAdmin.id = eSchool.edu_admin_id');
            $sCon->leftjoin('eepal_sectors_field_data','eSectorTitle','eSectorTitle.id=eSector.sectorfield_id');
            $sCon->leftjoin('eepal_specialty_field_data','eCourseTitle','eCourseTitle.id=eCourse.coursefield_id');
            $sCon->addField('eRegion', 'name','regionName');
            $sCon->addField('eAdmin', 'name','adminName');
            $sCon->addField('eSchool', 'name','schoolName');
            $sCon->addField('eStudent', 'currentclass');
            $sCon->addField('eSchool_chosen','epal_id');
            $sCon->addField('eCourse', 'coursefield_id');
            $sCon->addField('eSector', 'sectorfield_id');
            $sCon->addField('eSectorTitle', 'name','sectorname');
            $sCon->addField('eCourseTitle', 'name','specialtyname');
            $sCon->condition('eStudent.lastschool_unittypeid', 4, '=');

            if ($classId != 0) {
                $sCon->condition('eStudent.currentclass', $classId, '=');
            }
            if ($sectorId != 0) {
                if ($classId==2){
                    $sCon->condition('eSector.sectorfield_id', $sectorId, '=');
                }
                if (($classId==3 || $classId==4) && $courseId==0){
                    $sCon->condition('eCourse.coursefield_id', $sector_specialties, 'IN');
                }

            }
            if ($courseId!=0){
                if ($classId==3 || $classId==4){
                    $sCon->condition('eCourse.coursefield_id', $courseId, '=');
                }
            }

            if ($regionId != 0) {
                $sCon->condition('eSchool.region_edu_admin_id', $regionId, '=');
            }
            if ($adminId != 0) {
                $sCon->condition('eSchool.edu_admin_id', $adminId, '=');
            }
            if ($schId != 0) {
                $sCon->condition('eSchool_chosen.epal_id', $schId, '=');
            }
            $sCon->condition('eStudent.delapp', 0, '=');
            $sCon->condition('eSchool_chosen.choice_no', 1, '=');
            $sCon->groupBy('eStudent.currentclass');
            $sCon->groupBy('sectorfield_id');
            $sCon->groupBy('coursefield_id');
            $sCon->groupBy('eRegion.name');
            $sCon->groupBy('eAdmin.name');
            $sCon->groupBy('eSchool.name');
            $sCon->groupBy('eSchool_chosen.epal_id');
            $sCon->groupBy('sectorname');
            $sCon->groupBy('specialtyname');
            $sCon->addExpression('count(eStudent.id)', 'eStudent_count');
            $applications = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);



            $schoolNameColumn = array();
            $regionColumn = array();
            $adminColumn = array();
            $schoolSectionColumn = array();
            $numColumn = array();


            foreach ($applications as $application) {

                array_push($regionColumn, $application->regionName);
                array_push($adminColumn, $application->adminName);
                array_push($schoolNameColumn, $application->schoolName);
                if ($application->currentclass==1){
                    array_push($schoolSectionColumn, 'Α Tάξη');
                }else if($application->currentclass==2){
                    array_push($schoolSectionColumn, 'B Tάξη / '.$application->sectorname);
                }else{
                    $sCon = \Drupal::database()->select('eepal_specialty_field_data', 'eSpecialties');
                    $sCon->join('eepal_sectors_field_data', 'eSectors','eSectors.id=eSpecialties.sector_id');
                    $sCon->addField('eSectors', 'name','sectorName');
                    $sCon->addField('eSpecialties', 'name','specialtyName');
                    $sCon->condition('eSpecialties.id', $application->coursefield_id, '=');
                    $sector_specialty = $sCon->execute()->fetchAssoc();
                    if($application->currentclass==3){
                        array_push($schoolSectionColumn, 'Γ Tάξη / '.$sector_specialty['sectorName'].' / '.$sector_specialty['specialtyName']);
                    }else if($application->currentclass==4){
                        array_push($schoolSectionColumn, 'Δ Tάξη / '.$sector_specialty['sectorName'].' / '.$sector_specialty['specialtyName']);
                    }
                }
                array_push($numColumn,$application->eStudent_count);

            }

            for ($j = 0; $j < sizeof($schoolNameColumn); $j++) {

                array_push($list, (object) array(
                    'name' => $schoolNameColumn[$j],
                    'region' => $regionColumn[$j],
                    'admin' => $adminColumn[$j],
                    'section' => str_replace(",", "", $schoolSectionColumn[$j]),
                    'num' => $numColumn[$j],
                ));
            }

            return $this->respondWithStatus($list, Response::HTTP_OK);
        }
        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                "message" => t("An unexpected problem occured in makeReportgelStudents Method")
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function makeReportStudentsNum(Request $request, $regionId, $adminId, $schId, $classId, $sectorId, $courseId, $finalized)
    {
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
            foreach ($roles as $role) {
                if ($role === "ministry" || $role === "regioneduadmin" || $role === "eduadmin") {
                    $validRole = $role;
                    break;
                }
            }
            if ($validRole === false) {
				return $this->respondWithStatus([
					'message' => t("User Invalid Role"),
				], Response::HTTP_FORBIDDEN);
            }

            if (!$this->canReportOn($user, $role, $regionId, $adminId, $schId)) {
				return $this->respondWithStatus([
					'message' => t('User access to area forbidden'),
				], Response::HTTP_FORBIDDEN);
            }

            $limitup = $this->retrieveUpLimit();

            $list = array();

            // βρες όλα τα σχολεία που πληρούν τα κριτήρια / φίλτρα
            $sCon = $this->connection->select('eepal_school_field_data', 'eSchool');
			$sCon->join('eepal_region_field_data', 'eRegion', 'eRegion.id = eSchool.region_edu_admin_id');
			$sCon->join('eepal_admin_area_field_data', 'eAdmin', 'eAdmin.id = eSchool.edu_admin_id');
            $sCon->fields('eSchool', array('id', 'name', 'capacity_class_a', 'region_edu_admin_id', 'edu_admin_id','operation_shift', 'metathesis_region'))
				->fields('eRegion', ['name'])
				->fields('eAdmin', ['name']);
            if ($regionId != 0) {
                $sCon->condition('eSchool.region_edu_admin_id', $regionId, '=');
            }
            if ($adminId != 0) {
                $sCon->condition('eSchool.edu_admin_id', $adminId, '=');
            }
            if ($schId != 0) {
                $sCon->condition('eSchool.id', $schId, '=');
            }
            $epalSchools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

            foreach ($epalSchools as $epalSchool) { // για κάθε σχολείο

                $schoolNameColumn = array();
                $regionColumn = array();
                $adminColumn = array();
                $schoolSectionColumn = array();
                $numColumn = array();
                $capacityColumn = array();
                $percColumn = array();
                $limitDownColumn = array();
                $numNotConfirmedColumn = array();

                $smallClass = array();

                $numClassSec = 0;
                $numClassCour = 0;
                $numClassCour_D = 0;

                // εύρεση αριθμού μαθητών για κάθε τομέα της Β' τάξης
                if ($classId === "0" || $classId === "2") {
                    $sCon = $this->connection->select('eepal_sectors_in_epal_field_data', 'eSchool');
			        $sCon->join('eepal_sectors_field_data', 'eSectors', 'eSectors.id = eSchool.sector_id');
			        $sCon->leftJoin('epal_student_class', 'eStudent',
                        'eStudent.epal_id = eSchool.epal_id ' .
                        'AND eStudent.specialization_id = eSchool.sector_id ' .
                        //'AND eStudent.currentclass = 2  AND eStudent.directorconfirm = 1');
                        'AND eStudent.currentclass = 2');
                    $sCon->fields('eSchool', array('sector_id','capacity_class_sector'))
                        ->fields('eSectors', ['name'])
                        ->groupBy('sector_id')
                        ->groupBy('capacity_class_sector')
                        ->groupBy('eSectors.name')
                        ->condition('eSchool.epal_id', $epalSchool->id, '=');
                    //$sCon->addExpression('count(eStudent.id)', 'eStudent_count');
                    $sCon->addExpression('sum(case when eStudent.directorconfirm = 1 then 1 else 0 end)','eStudent_count'); //synolo pou exoun ginei confirm
                    $sCon->addExpression('count(eStudent.id)', 'eStudent_count_not_confirmed');
                    if ($sectorId != "0") {
                        $sCon->condition('eSchool.sector_id', $sectorId, '=');
                    }

                    $sectorsInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

                    foreach ($sectorsInEpals as $sectorsInEpal) {
                        array_push($regionColumn, $epalSchool->eRegion_name);
                        array_push($adminColumn, $epalSchool->eAdmin_name);
                        array_push($schoolNameColumn, $epalSchool->name);
                        array_push($schoolSectionColumn, 'Β τάξη / ' . $sectorsInEpal->name);

                        $numStud = $sectorsInEpal->eStudent_count;
                        $smCl = $this->isSmallClass($epalSchool->id, $numStud, "2", $sectorsInEpal->sector_id, $epalSchool->metathesis_region);
                        array_push($smallClass, $smCl);

                        array_push($numColumn, $numStud);
                        array_push($numNotConfirmedColumn,$sectorsInEpal->eStudent_count_not_confirmed);
                        $capacityColumnValue = $sectorsInEpal->capacity_class_sector * $limitup;
                        array_push($capacityColumn, $capacityColumnValue);
                        array_push($limitDownColumn,$this->retrieveDownLimit($epalSchool->metathesis_region,"2"));
                        array_push($percColumn, $capacityColumnValue > 0 ? number_format($numStud / $capacityColumnValue * 100, 2) : 0);

                        $numClassSec += $sectorsInEpal->capacity_class_sector;
                    }
                } // end εύρεση αριθμού μαθητών για κάθε τομέα της Β' τάξης

                // εύρεση αριθμού μαθητών για κάθε ειδικότητα της Γ' τάξης
                if ($classId === "0" || $classId === "3") {
                    $sCon = $this->connection->select('eepal_specialties_in_epal_field_data', 'eSchool');
			        $sCon->join('eepal_specialty_field_data', 'eSpecialties', 'eSpecialties.id = eSchool.specialty_id');
			        $sCon->leftJoin('epal_student_class', 'eStudent',
                        'eStudent.epal_id = eSchool.epal_id ' .
                        'AND eStudent.specialization_id = eSchool.specialty_id ' .
                        //'AND eStudent.currentclass = 3 AND eStudent.directorconfirm = 1');
                        'AND eStudent.currentclass = 3');
                    $sCon->fields('eSchool', array('specialty_id', 'capacity_class_specialty'))
                        ->fields('eSpecialties', ['name'])
                        ->groupBy('specialty_id')
                        ->groupBy('capacity_class_specialty')
                        ->groupBy('eSpecialties.name')
                        ->condition('eSchool.epal_id', $epalSchool->id, '=');
                    //$sCon->addExpression('count(eStudent.id)', 'eStudent_count');
                    $sCon->addExpression('sum(case when eStudent.directorconfirm = 1 then 1 else 0 end)','eStudent_count');
                    $sCon->addExpression('count(eStudent.id)', 'eStudent_count_not_confirmed');
                    if ($courseId !== "0") {
                        $sCon->condition('eSchool.specialty_id', $courseId, '=');
                    } else if ($sectorId !== "0") {
                        $sCon->condition('eSpecialties.sector_id', $sectorId, '=');
                    }
                    $specialtiesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

                    foreach ($specialtiesInEpals as $specialtiesInEpal) {
                        array_push($regionColumn, $epalSchool->eRegion_name);
                        array_push($adminColumn, $epalSchool->eAdmin_name);
                        array_push($schoolNameColumn, $epalSchool->name);
                        array_push($schoolSectionColumn, 'Γ τάξη / ' . $specialtiesInEpal->name );

                        $numStud = $specialtiesInEpal->eStudent_count;
                        $smCl = $this->isSmallClass($epalSchool->id, $numStud, "3", $specialtiesInEpal->specialty_id, $epalSchool->metathesis_region);
                        array_push($smallClass, $smCl);

                        array_push($numColumn, $numStud);
                        array_push($numNotConfirmedColumn,$specialtiesInEpal->eStudent_count_not_confirmed);
                        $capacityColumnValue = $specialtiesInEpal->capacity_class_specialty * $limitup;
                        array_push($capacityColumn, $capacityColumnValue);
                        array_push($limitDownColumn,$this->retrieveDownLimit($epalSchool->metathesis_region,"3"));
                        array_push($percColumn, $capacityColumnValue > 0 ? number_format($numStud / $capacityColumnValue * 100, 2) : 0);

                        $numClassCour += $specialtiesInEpal->capacity_class_specialty;
                    }
                } // end εύρεση αριθμού μαθητών για κάθε ειδικότητα της Γ' τάξης

                // εύρεση αριθμού μαθητών για κάθε ειδικότητα της Δ' τάξης
                if ($epalSchool->operation_shift === "ΕΣΠΕΡΙΝΟ") {
                    if ($classId === "0" || $classId === "4") {
                        $sCon = $this->connection->select('eepal_specialties_in_epal_field_data', 'eSchool');
                        $sCon->join('eepal_specialty_field_data', 'eSpecialties', 'eSpecialties.id = eSchool.specialty_id');
                        $sCon->leftJoin('epal_student_class', 'eStudent',
                            'eStudent.epal_id = eSchool.epal_id ' .
                            'AND eStudent.specialization_id = eSchool.specialty_id ' .
                            //'AND eStudent.currentclass = 4 AND eStudent.directorconfirm = 1');
                            'AND eStudent.currentclass = 4');
                        $sCon->fields('eSchool', array('specialty_id', 'capacity_class_specialty_d'))
                            ->fields('eSpecialties', ['name'])
                            ->groupBy('specialty_id')
                            ->groupBy('capacity_class_specialty_d')
                            ->groupBy('eSpecialties.name')
                            ->condition('eSchool.epal_id', $epalSchool->id, '=');
                        //$sCon->addExpression('count(eStudent.id)', 'eStudent_count');
                        $sCon->addExpression('sum(case when eStudent.directorconfirm = 1 then 1 else 0 end)','eStudent_count');
                        $sCon->addExpression('count(eStudent.id)', 'eStudent_count_not_confirmed');
                        if ($courseId !== "0") {
                            $sCon->condition('eSchool.specialty_id', $courseId, '=');
                        } else if ($sectorId !== "0") {
                            $sCon->condition('eSpecialties.sector_id', $sectorId, '=');
                        }

                        $specialtiesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

                        foreach ($specialtiesInEpals as $specialtiesInEpal) {
                            array_push($regionColumn, $epalSchool->eRegion_name);
                            array_push($adminColumn, $epalSchool->eAdmin_name);
                            array_push($schoolNameColumn, $epalSchool->name);
                            array_push($schoolSectionColumn, 'Δ τάξη / ' . $specialtiesInEpal->name );

                            $numStud = $specialtiesInEpal->eStudent_count;
                            $smCl = $this->isSmallClass($epalSchool->id, $numStud, "4", $specialtiesInEpal->specialty_id, $epalSchool->metathesis_region);
                            array_push($smallClass, $smCl);

                            array_push($numColumn, $numStud);
                            array_push($numNotConfirmedColumn,$specialtiesInEpal->eStudent_count_not_confirmed);
                            $capacityColumnValue = $specialtiesInEpal->capacity_class_specialty_d * $limitup;
                            array_push($capacityColumn, $capacityColumnValue);
                            array_push($limitDownColumn,$this->retrieveDownLimit($epalSchool->metathesis_region,"4"));
                            array_push($percColumn, $capacityColumnValue > 0 ? number_format($numStud / $capacityColumnValue * 100, 2) : 0);

                            $numClassCour_D += $specialtiesInEpal->capacity_class_specialty_d;
                        }
                    }
                } //end "ΕΣΠΕΡΙΝΟ" εύρεση αριθμού μαθητών για κάθε ειδικότητα της Δ' τάξης

                // εύρεση αριθμού μαθητών για κάθε τάξη
                $numClasses = array();
                array_push($numClasses, $epalSchool->capacity_class_a);
                array_push($numClasses, $numClassSec);
                array_push($numClasses, $numClassCour);
                array_push($numClasses, $numClassCour_D);

                if ($sectorId === "0" && $courseId === "0") {
                    $clidstart = 1;
                    $clidend = 4;

                    if ($classId !== "0") {
                        $clidstart = $classId;
                        $clidend = $classId;
                        if ($classId === "1") {
                            array_push($schoolSectionColumn, 'Α τάξη');
                        } elseif ($classId === "2") {
                            array_push($schoolSectionColumn, 'Β τάξη');
                        } elseif ($classId === "3") {
                            array_push($schoolSectionColumn, 'Γ τάξη');
                        } elseif ($classId === "4") {
                            array_push($schoolSectionColumn, 'Δ τάξη');
                        }
                    } else {
                        array_push($schoolSectionColumn, 'Α τάξη');
                        array_push($schoolSectionColumn, 'Β τάξη');
                        array_push($schoolSectionColumn, 'Γ τάξη');
                        array_push($schoolSectionColumn, 'Δ τάξη');
                    }

                    for ($clId = $clidstart; $clId <= $clidend; $clId++) {
                        $sCon = $this->connection->select('epal_student_class', 'eStudent');
                        //$sCon->fields('eStudent', array('id', 'epal_id', 'currentclass'));
                        $sCon->condition('eStudent.epal_id', $epalSchool->id, '=');
                        //$sCon->condition('eStudent.directorconfirm', 1, '=');
                        $sCon->condition('eStudent.currentclass', $clId, '=');
                        $sCon->addExpression('sum(case when eStudent.directorconfirm = 1 then 1 else 0 end)','eStudent_count');
                        $sCon->addExpression('count(eStudent.id)', 'eStudent_count_not_confirmed');
                        $values_per_Classes = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

                        $value_of_Class=reset($values_per_Classes);
                        $numStud=$value_of_Class->eStudent_count;

                        //$numStud =  $sCon->countQuery()->execute()->fetchField();

                        $smCl = $this->isSmallClass($epalSchool->id, $numStud, "1", "-1", $epalSchool->metathesis_region);
                        array_push($smallClass, $smCl);

                        array_push($schoolNameColumn, $epalSchool->name);
                        array_push($regionColumn, $epalSchool->eRegion_name);
                        array_push($adminColumn, $epalSchool->eAdmin_name);
                        array_push($numColumn, $numStud);
                        array_push($numNotConfirmedColumn,$value_of_Class->eStudent_count_not_confirmed);
                        $capacityColumnValue = ($numClasses[$clId-1] * $limitup);
                        array_push($capacityColumn, $capacityColumnValue);
                        array_push($limitDownColumn,$this->retrieveDownLimit($epalSchool->metathesis_region,$clId));
                        array_push($percColumn, $capacityColumnValue > 0 ? number_format($numStud / $capacityColumnValue * 100, 2) : 0);
                    }
                } // end εύρεση αριθμού μαθητών για κάθε τάξη

                for ($j = 0; $j < sizeof($schoolNameColumn); $j++) {
                    // αν έγινε αίτημα για εμφάνιση ολιγομελών και είναι το τρέχον τμήμα ολιγομελές
                    if (($finalized === "1") ||
                        ($finalized === "0" && $smallClass[$j] === self::SMALL_CLS
                            && $schoolSectionColumn[$j] !== "Β τάξη" && $schoolSectionColumn[$j] !== "Γ τάξη"
                            && $schoolSectionColumn[$j] !== "Δ τάξη")) {
                            array_push($list, (object) array(
                                'name' => $schoolNameColumn[$j],
                                'region' => $regionColumn[$j],
                                'admin' => $adminColumn[$j],
                                'section' => str_replace(",", "", $schoolSectionColumn[$j]),
                                'num' => $numColumn[$j],
                                'capacity' => $capacityColumn[$j],
                                'percentage' => $percColumn[$j],
                                'limit_down' => $limitDownColumn[$j],
                                'num_not_confirmed' => $numNotConfirmedColumn[$j],
                            ));
                    }
                }
            } //end loop για κάθε σχολείο

            return $this->respondWithStatus($list, Response::HTTP_OK);
        }
        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                "message" => t("An unexpected problem occured in makeReportStudentsNum Method")
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function makeReportSmallClasses(Request $request, $regionId, $adminId, $schId, $classId, $sectorId, $courseId, $finalized)
    {
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
            foreach ($roles as $role) {
                if ($role === "ministry" || $role === "regioneduadmin" || $role === "eduadmin") {
                    $validRole = $role;
                    break;
                }
            }
            if ($validRole === false) {
				return $this->respondWithStatus([
					'message' => t("User Invalid Role"),
				], Response::HTTP_FORBIDDEN);
            }

            if (!$this->canReportOn($user, $role, $regionId, $adminId, $schId)) {
				return $this->respondWithStatus([
					'message' => t('User access to area forbidden'),
				], Response::HTTP_FORBIDDEN);
            }

            $limitup = $this->retrieveUpLimit();

            $list = array();

            // βρες όλα τα σχολεία που πληρούν τα κριτήρια / φίλτρα
            $sCon = $this->connection->select('eepal_school_field_data', 'eSchool');
			$sCon->join('eepal_region_field_data', 'eRegion', 'eRegion.id = eSchool.region_edu_admin_id');
			$sCon->join('eepal_admin_area_field_data', 'eAdmin', 'eAdmin.id = eSchool.edu_admin_id');
            $sCon->fields('eSchool', array('id', 'name', 'capacity_class_a', 'region_edu_admin_id', 'edu_admin_id','operation_shift', 'metathesis_region'))
				->fields('eRegion', ['name'])
				->fields('eAdmin', ['name']);
            if ($regionId != 0) {
                $sCon->condition('eSchool.region_edu_admin_id', $regionId, '=');
            }
            if ($adminId != 0) {
                $sCon->condition('eSchool.edu_admin_id', $adminId, '=');
            }
            if ($schId != 0) {
                $sCon->condition('eSchool.id', $schId, '=');
            }
            $epalSchools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

            foreach ($epalSchools as $epalSchool) { // για κάθε σχολείο

                $schoolNameColumn = array();
                $regionColumn = array();
                $adminColumn = array();
                $schoolSectionColumn = array();
                $numColumn = array();
                $capacityColumn = array();
                $percColumn = array();
                $limitDownColumn = array();
                $numNotConfirmedColumn = array();

                $smallClass = array();

                $numClassSec = 0;
                $numClassCour = 0;
                $numClassCour_D = 0;

                // εύρεση αριθμού μαθητών για κάθε τομέα της Β' τάξης
                if ($classId === "0" || $classId === "2") {
                    $sCon = $this->connection->select('eepal_sectors_in_epal_field_data', 'eSchool');
			        $sCon->join('eepal_sectors_field_data', 'eSectors', 'eSectors.id = eSchool.sector_id');
			        $sCon->leftJoin('epal_student_class', 'eStudent',
                        'eStudent.epal_id = eSchool.epal_id ' .
                        'AND eStudent.specialization_id = eSchool.sector_id ' .
                        //'AND eStudent.currentclass = 2  AND eStudent.directorconfirm = 1');
                        'AND eStudent.currentclass = 2');
                    $sCon->fields('eSchool', array('sector_id','capacity_class_sector'))
                        ->fields('eSectors', ['name'])
                        ->groupBy('sector_id')
                        ->groupBy('capacity_class_sector')
                        ->groupBy('eSectors.name')
                        ->condition('eSchool.epal_id', $epalSchool->id, '=');
                    //$sCon->addExpression('count(eStudent.id)', 'eStudent_count');
                    $sCon->addExpression('sum(case when eStudent.directorconfirm = 1 then 1 else 0 end)','eStudent_count'); //synolo pou exoun ginei confirm
                    $sCon->addExpression('count(eStudent.id)', 'eStudent_count_not_confirmed');
                    if ($sectorId != "0") {
                        $sCon->condition('eSchool.sector_id', $sectorId, '=');
                    }

                    $sectorsInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

                    foreach ($sectorsInEpals as $sectorsInEpal) {
                        array_push($regionColumn, $epalSchool->eRegion_name);
                        array_push($adminColumn, $epalSchool->eAdmin_name);
                        array_push($schoolNameColumn, $epalSchool->name);
                        array_push($schoolSectionColumn, 'Β τάξη / ' . $sectorsInEpal->name);

                        $numStud = $sectorsInEpal->eStudent_count;
                        $smCl = $this->isSmallClass($epalSchool->id, $numStud, "2", $sectorsInEpal->sector_id, $epalSchool->metathesis_region);
                        array_push($smallClass, $smCl);

                        array_push($numColumn, $numStud);
                        array_push($numNotConfirmedColumn,$sectorsInEpal->eStudent_count_not_confirmed);
                        $capacityColumnValue = $sectorsInEpal->capacity_class_sector * $limitup;
                        array_push($capacityColumn, $capacityColumnValue);
                        array_push($limitDownColumn,$this->retrieveDownLimit($epalSchool->metathesis_region,"2"));
                        array_push($percColumn, $capacityColumnValue > 0 ? number_format($numStud / $capacityColumnValue * 100, 2) : 0);

                        $numClassSec += $sectorsInEpal->capacity_class_sector;
                    }
                } // end εύρεση αριθμού μαθητών για κάθε τομέα της Β' τάξης

                // εύρεση αριθμού μαθητών για κάθε ειδικότητα της Γ' τάξης
                if ($classId === "0" || $classId === "3") {
                    $sCon = $this->connection->select('eepal_specialties_in_epal_field_data', 'eSchool');
			        $sCon->join('eepal_specialty_field_data', 'eSpecialties', 'eSpecialties.id = eSchool.specialty_id');
			        $sCon->leftJoin('epal_student_class', 'eStudent',
                        'eStudent.epal_id = eSchool.epal_id ' .
                        'AND eStudent.specialization_id = eSchool.specialty_id ' .
                        //'AND eStudent.currentclass = 3 AND eStudent.directorconfirm = 1');
                        'AND eStudent.currentclass = 3');
                    $sCon->fields('eSchool', array('specialty_id', 'capacity_class_specialty'))
                        ->fields('eSpecialties', ['name'])
                        ->groupBy('specialty_id')
                        ->groupBy('capacity_class_specialty')
                        ->groupBy('eSpecialties.name')
                        ->condition('eSchool.epal_id', $epalSchool->id, '=');
                    //$sCon->addExpression('count(eStudent.id)', 'eStudent_count');
                    $sCon->addExpression('sum(case when eStudent.directorconfirm = 1 then 1 else 0 end)','eStudent_count');
                    $sCon->addExpression('count(eStudent.id)', 'eStudent_count_not_confirmed');
                    if ($courseId !== "0") {
                        $sCon->condition('eSchool.specialty_id', $courseId, '=');
                    } else if ($sectorId !== "0") {
                        $sCon->condition('eSpecialties.sector_id', $sectorId, '=');
                    }
                    $specialtiesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

                    foreach ($specialtiesInEpals as $specialtiesInEpal) {
                        array_push($regionColumn, $epalSchool->eRegion_name);
                        array_push($adminColumn, $epalSchool->eAdmin_name);
                        array_push($schoolNameColumn, $epalSchool->name);
                        array_push($schoolSectionColumn, 'Γ τάξη / ' . $specialtiesInEpal->name );

                        $numStud = $specialtiesInEpal->eStudent_count;
                        $smCl = $this->isSmallClass($epalSchool->id, $numStud, "3", $specialtiesInEpal->specialty_id, $epalSchool->metathesis_region);
                        array_push($smallClass, $smCl);

                        array_push($numColumn, $numStud);
                        array_push($numNotConfirmedColumn,$specialtiesInEpal->eStudent_count_not_confirmed);
                        $capacityColumnValue = $specialtiesInEpal->capacity_class_specialty * $limitup;
                        array_push($capacityColumn, $capacityColumnValue);
                        array_push($limitDownColumn,$this->retrieveDownLimit($epalSchool->metathesis_region,"3"));
                        array_push($percColumn, $capacityColumnValue > 0 ? number_format($numStud / $capacityColumnValue * 100, 2) : 0);

                        $numClassCour += $specialtiesInEpal->capacity_class_specialty;
                    }
                } // end εύρεση αριθμού μαθητών για κάθε ειδικότητα της Γ' τάξης

                // εύρεση αριθμού μαθητών για κάθε ειδικότητα της Δ' τάξης
                if ($epalSchool->operation_shift === "ΕΣΠΕΡΙΝΟ") {
                    if ($classId === "0" || $classId === "4") {
                        $sCon = $this->connection->select('eepal_specialties_in_epal_field_data', 'eSchool');
                        $sCon->join('eepal_specialty_field_data', 'eSpecialties', 'eSpecialties.id = eSchool.specialty_id');
                        $sCon->leftJoin('epal_student_class', 'eStudent',
                            'eStudent.epal_id = eSchool.epal_id ' .
                            'AND eStudent.specialization_id = eSchool.specialty_id ' .
                            //'AND eStudent.currentclass = 4 AND eStudent.directorconfirm = 1');
                            'AND eStudent.currentclass = 4');
                        $sCon->fields('eSchool', array('specialty_id', 'capacity_class_specialty_d'))
                            ->fields('eSpecialties', ['name'])
                            ->groupBy('specialty_id')
                            ->groupBy('capacity_class_specialty_d')
                            ->groupBy('eSpecialties.name')
                            ->condition('eSchool.epal_id', $epalSchool->id, '=');
                        //$sCon->addExpression('count(eStudent.id)', 'eStudent_count');
                        $sCon->addExpression('sum(case when eStudent.directorconfirm = 1 then 1 else 0 end)','eStudent_count');
                        $sCon->addExpression('count(eStudent.id)', 'eStudent_count_not_confirmed');
                        if ($courseId !== "0") {
                            $sCon->condition('eSchool.specialty_id', $courseId, '=');
                        } else if ($sectorId !== "0") {
                            $sCon->condition('eSpecialties.sector_id', $sectorId, '=');
                        }

                        $specialtiesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

                        foreach ($specialtiesInEpals as $specialtiesInEpal) {
                            array_push($regionColumn, $epalSchool->eRegion_name);
                            array_push($adminColumn, $epalSchool->eAdmin_name);
                            array_push($schoolNameColumn, $epalSchool->name);
                            array_push($schoolSectionColumn, 'Δ τάξη / ' . $specialtiesInEpal->name );

                            $numStud = $specialtiesInEpal->eStudent_count;
                            $smCl = $this->isSmallClass($epalSchool->id, $numStud, "4", $specialtiesInEpal->specialty_id, $epalSchool->metathesis_region);
                            array_push($smallClass, $smCl);

                            array_push($numColumn, $numStud);
                            array_push($numNotConfirmedColumn,$specialtiesInEpal->eStudent_count_not_confirmed);
                            $capacityColumnValue = $specialtiesInEpal->capacity_class_specialty_d * $limitup;
                            array_push($capacityColumn, $capacityColumnValue);
                            array_push($limitDownColumn,$this->retrieveDownLimit($epalSchool->metathesis_region,"4"));
                            array_push($percColumn, $capacityColumnValue > 0 ? number_format($numStud / $capacityColumnValue * 100, 2) : 0);

                            $numClassCour_D += $specialtiesInEpal->capacity_class_specialty_d;
                        }
                    }
                } //end "ΕΣΠΕΡΙΝΟ" εύρεση αριθμού μαθητών για κάθε ειδικότητα της Δ' τάξης

                // εύρεση αριθμού μαθητών για κάθε τάξη
                $numClasses = array();
                array_push($numClasses, $epalSchool->capacity_class_a);
                array_push($numClasses, $numClassSec);
                array_push($numClasses, $numClassCour);
                array_push($numClasses, $numClassCour_D);

                if ($sectorId === "0" && $courseId === "0") {
                    $clidstart = 1;
                    $clidend = 4;

                    if ($classId !== "0") {
                        $clidstart = $classId;
                        $clidend = $classId;
                        if ($classId === "1") {
                            array_push($schoolSectionColumn, 'Α τάξη');
                        } elseif ($classId === "2") {
                            array_push($schoolSectionColumn, 'Β τάξη');
                        } elseif ($classId === "3") {
                            array_push($schoolSectionColumn, 'Γ τάξη');
                        } elseif ($classId === "4") {
                            array_push($schoolSectionColumn, 'Δ τάξη');
                        }
                    } else {
                        array_push($schoolSectionColumn, 'Α τάξη');
                        array_push($schoolSectionColumn, 'Β τάξη');
                        array_push($schoolSectionColumn, 'Γ τάξη');
                        array_push($schoolSectionColumn, 'Δ τάξη');
                    }

                    for ($clId = $clidstart; $clId <= $clidend; $clId++) {
                        $sCon = $this->connection->select('epal_student_class', 'eStudent');
                        //$sCon->fields('eStudent', array('id', 'epal_id', 'currentclass'));
                        $sCon->condition('eStudent.epal_id', $epalSchool->id, '=');
                        //$sCon->condition('eStudent.directorconfirm', 1, '=');
                        $sCon->condition('eStudent.currentclass', $clId, '=');
                        $sCon->addExpression('sum(case when eStudent.directorconfirm = 1 then 1 else 0 end)','eStudent_count');
                        $sCon->addExpression('count(eStudent.id)', 'eStudent_count_not_confirmed');
                        $values_per_Classes = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);

                        $value_of_Class=reset($values_per_Classes);
                        $numStud=$value_of_Class->eStudent_count;

                        //$numStud =  $sCon->countQuery()->execute()->fetchField();

                        $smCl = $this->isSmallClass($epalSchool->id, $numStud, "1", "-1", $epalSchool->metathesis_region);
                        array_push($smallClass, $smCl);

                        array_push($schoolNameColumn, $epalSchool->name);
                        array_push($regionColumn, $epalSchool->eRegion_name);
                        array_push($adminColumn, $epalSchool->eAdmin_name);
                        array_push($numColumn, $numStud);
                        array_push($numNotConfirmedColumn,$value_of_Class->eStudent_count_not_confirmed);
                        $capacityColumnValue = ($numClasses[$clId-1] * $limitup);
                        array_push($capacityColumn, $capacityColumnValue);
                        array_push($limitDownColumn,$this->retrieveDownLimit($epalSchool->metathesis_region,$clId));
                        array_push($percColumn, $capacityColumnValue > 0 ? number_format($numStud / $capacityColumnValue * 100, 2) : 0);
                    }
                } // end εύρεση αριθμού μαθητών για κάθε τάξη

                for ($j = 0; $j < sizeof($schoolNameColumn); $j++) {
                    // αν έγινε αίτημα για εμφάνιση ολιγομελών και είναι το τρέχον τμήμα ολιγομελές
                    if (($finalized === "1") ||
                        ($finalized === "0" && $smallClass[$j] === self::SMALL_CLS
                            && $schoolSectionColumn[$j] !== "Β τάξη" && $schoolSectionColumn[$j] !== "Γ τάξη"
                            && $schoolSectionColumn[$j] !== "Δ τάξη")) {
                            array_push($list, (object) array(
                                'name' => $schoolNameColumn[$j],
                                'region' => $regionColumn[$j],
                                'admin' => $adminColumn[$j],
                                'section' => str_replace(",", "", $schoolSectionColumn[$j]),
                                'num' => $numColumn[$j],
                                'capacity' => $capacityColumn[$j],
                                'percentage' => $percColumn[$j],
                                'limit_down' => $limitDownColumn[$j],
                                'num_not_confirmed' => $numNotConfirmedColumn[$j],
                            ));
                    }
                }
            } //end loop για κάθε σχολείο

            return $this->respondWithStatus($list, Response::HTTP_OK);
        }
        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                "message" => t("An unexpected problem occured in makeReportCompleteness Method")
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function makeReportEpalCapacity(Request $request)
    {
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
            $schoolid = $user->init->value;
            $this->logger->warning("School Id:" . $schoolid);

            //user role validation
            $roles = $user->getRoles();
            $validRole = false;
            foreach ($roles as $role) {
                if ($role === "epal") {
                    $validRole = true;
                    break;
                }
            }
            if (!$validRole) {
				          return $this->respondWithStatus([
					               'message' => t("User Invalid Role"),
				                   ], Response::HTTP_FORBIDDEN);
            }

            $list = array();
            //βρες το σχολείο
            $sCon = $this->connection
                ->select('eepal_school_field_data', 'eSchool')
                ->fields('eSchool', array('id', 'name', 'capacity_class_a', 'operation_shift','registry_no'))
                ->condition('eSchool.id', $schoolid, '=');
            $epalSchools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            $epalSchool = reset($epalSchools);

            $schoolSectionColumn = array();
            $capacityColumn = array();

            //εύρεση αριθμού τμημάτων (χωρητικότητα) για κάθε τμήμα της Α' τάξης
					   array_push($schoolSectionColumn, 'Α\' τάξη');
					   array_push($capacityColumn, $epalSchool->capacity_class_a);

            //εύρεση αριθμού τμημάτων (χωρητικότητα) για κάθε τομέα της Β' τάξης
            $sCon = $this->connection
					     ->select('eepal_sectors_in_epal_field_data', 'eSchool')
               ->fields('eSchool', array('sector_id','capacity_class_sector'))
                ->condition('eSchool.epal_id', $epalSchool->id, '=');
            $sectorsInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            foreach ($sectorsInEpals as $sectorsInEpal) {
                $sCon = $this->connection
						          ->select('eepal_sectors_field_data', 'eSectors')
                      ->fields('eSectors', array('name'))
                      ->condition('eSectors.id', $sectorsInEpal->sector_id, '=');
                $sectorsNamesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                foreach ($sectorsNamesInEpals as $sectorsNamesInEpal) {
                      //array_push($schoolNameColumn, $epalSchool->name);
                      array_push($schoolSectionColumn, 'Β\' τάξη / ' . $sectorsNamesInEpal->name );
                      array_push($capacityColumn, $sectorsInEpal->capacity_class_sector);
                  }   //end foreach sectorsNamesInEpals
            }   //end foreach sectorsInEpal

            //εύρεση αριθμού τμημάτων (χωρητικότητα) για κάθε ειδικότητα της Γ' τάξης
            $sCon = $this->connection
					       ->select('eepal_specialties_in_epal_field_data', 'eSchool')
                 ->fields('eSchool', array('specialty_id', 'capacity_class_specialty'))
                  ->condition('eSchool.epal_id', $epalSchool->id, '=');
            $specialtiesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            foreach ($specialtiesInEpals as $specialtiesInEpal) {
					         $sCon = $this->connection
						            ->select('eepal_specialty_field_data', 'eSpecialties')
                        ->fields('eSpecialties', array('name'))
                        ->condition('eSpecialties.id', $specialtiesInEpal->specialty_id, '=');
					          $specialtiesNamesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                    foreach ($specialtiesNamesInEpals as $specialtiesNamesInEpal) {
						             array_push($schoolSectionColumn, 'Γ\' τάξη / ' . $specialtiesNamesInEpal->name );
						             array_push($capacityColumn, $specialtiesInEpal->capacity_class_specialty);
                    }   //end foreach $specialtiesNamesInEpal
              } //end foreach $specialtiesInEpals

              //εύρεση αριθμού τμημάτων (χωρητικότητα) για κάθε ειδικότητα της Δ' τάξης
              $sCon = $this->connection
					          ->select('eepal_specialties_in_epal_field_data', 'eSchool')
                    ->fields('eSchool', array('specialty_id', 'capacity_class_specialty_d'))
                    ->condition('eSchool.epal_id', $epalSchool->id, '=');
              $specialtiesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
              foreach ($specialtiesInEpals as $specialtiesInEpal) {
					           $sCon = $this->connection
						            ->select('eepal_specialty_field_data', 'eSpecialties')
                        ->fields('eSpecialties', array('name'))
                        ->condition('eSpecialties.id', $specialtiesInEpal->specialty_id, '=');
					          $specialtiesNamesInEpals = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
                    foreach ($specialtiesNamesInEpals as $specialtiesNamesInEpal) {
                        if ($epalSchool->operation_shift === "ΕΣΠΕΡΙΝΟ") {
                            array_push($schoolSectionColumn, 'Δ\' τάξη / ' . $specialtiesNamesInEpal->name );
                            array_push($capacityColumn, $specialtiesInEpal->capacity_class_specialty_d);
                        }
                    }   //end foreach $specialtiesNamesInEpal
                } //end foreach $specialtiesInEpals

              //εισαγωγή εγγραφών στο tableschema
              for ($j = 0; $j < sizeof($schoolSectionColumn); $j++) {
					           array_push($list, (object) array(
                              'section' => str_replace(",", "", $schoolSectionColumn[$j]),
						                  'capacity' => $capacityColumn[$j],
					                   ));
                }
          //  } //end foreach school

            return $this->respondWithStatus($list, Response::HTTP_OK);
        } //end try

        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
				          "message" => t("An unexpected problem occured during makeReportEpalCapacity Method")
			             ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function makeReportEpalApplications(Request $request)
    {
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
          $schoolid = $user->init->value;
          $this->logger->warning("School Id:" . $schoolid);

          //user role validation
          $roles = $user->getRoles();
          $validRole = false;
          foreach ($roles as $role) {
              if ($role === "epal") {
                  $validRole = true;
                  break;
              }
          }
          if (!$validRole) {
                return $this->respondWithStatus([
                       'message' => t("User Invalid Role"),
                         ], Response::HTTP_FORBIDDEN);
          }

          $crypt = new Crypt();
          $list = array();
          //βρες το σχολείο
          $sCon = $this->connection
              ->select('eepal_school_field_data', 'eSchool')
              ->fields('eSchool', array('id', 'name', 'capacity_class_a', 'operation_shift','registry_no'))
              ->condition('eSchool.id', $schoolid, '=');
          $epalSchools = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
          $epalSchool = reset($epalSchools);

          $schoolSectionColumn = array();
          $surnameColumn = array();
          $firstnameColumn = array();
          $addressColumn = array();
          $telColumn = array();
          $confirmColumn = array();

          //β' τρόπος
          /*
          $ecQuery = $this->connection->select('epal_student', 'eStudent')
                                                                  ->fields('eStudent', array('name','studentsurname','regionaddress', 'regiontk', 'regionarea','telnum'))
                                                                  ->fields('eClass', array('student_id','directorconfirm'));
          $ecQuery->addJoin('left outer', 'epal_student_class', 'eClass', 'eStudent.id=eClass.student_id');

          $ecQuery->condition('eClass.epal_id', $schoolid, '=');
          $ecQuery->condition('eClass.currentclass', 1, '=');

          $epalStudents = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
          foreach ($epalStudents as $epalStudent) {
            array_push($schoolSectionColumn, 'Α\' τάξη');
            //array_push($firstnameColumn, $crypt->decrypt($epalStudent->name));

            array_push($surnameColumn, $crypt->decrypt($epalStudent->studentsurname));
            array_push($addressColumn, $crypt->decrypt($epalStudent->regionaddress) . ", ΤΚ " . $crypt->decrypt($epalStudent->regiontk) . ", " . $crypt->decrypt($epalStudent->regionarea) );
            array_push($telColumn, $crypt->decrypt($epalStudent->telnum));
            if ($epalStudent->directorconfirm )
              array_push($confirmColumn, 'ΝΑΙ');
            else
              array_push($confirmColumn, 'ΟΧΙ');

          }
          */



          //α' τρόπος

          $sCon = $this->connection
             ->select('epal_student_class', 'eClass')
             ->fields('eClass', array('student_id','directorconfirm'))
             ->condition('eClass.epal_id', $schoolid, '=')
             ->condition('eClass.currentclass', 1, '=');
          $epalStudentsClasses = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
          foreach ($epalStudentsClasses as $epalStudentClass) {
            $sCon = $this->connection
               ->select('epal_student', 'eStudent')
               ->fields('eStudent', array('name','studentsurname','regionaddress', 'regiontk', 'regionarea','telnum'))
               ->condition('eStudent.id', $epalStudentClass->student_id, '=');
            $epalStudents = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
            foreach ($epalStudents as $epalStudent) {
              array_push($schoolSectionColumn, 'Α\' τάξη');
              array_push($firstnameColumn, $crypt->decrypt($epalStudent->name));
              array_push($surnameColumn, $crypt->decrypt($epalStudent->studentsurname));
              array_push($addressColumn, $crypt->decrypt($epalStudent->regionaddress) . ", ΤΚ " . $crypt->decrypt($epalStudent->regiontk) . ", " . $crypt->decrypt($epalStudent->regionarea) );
              array_push($telColumn, $crypt->decrypt($epalStudent->telnum));
              if ($epalStudentClass->directorconfirm )
                array_push($confirmColumn, 'ΝΑΙ');
              else
                array_push($confirmColumn, 'ΟΧΙ');
            }
          }


          //εισαγωγή εγγραφών στο tableschema
          for ($j = 0; $j < sizeof($schoolSectionColumn); $j++) {
                 array_push($list, (object) array(
                           'section' => str_replace(",", "", $schoolSectionColumn[$j]),
                           'name' => $firstnameColumn[$j],
                           'surname' => $surnameColumn[$j],
                           'address' => $addressColumn[$j],
                           'tel' => $telColumn[$j],
                           'confirm' => $confirmColumn[$j],
                         ));
            }

          unset($crypt);

          return $this->respondWithStatus($list, Response::HTTP_OK);
      } //end try

      catch (\Exception $e) {
          $this->logger->warning($e->getMessage());
          return $this->respondWithStatus([
                "message" => t("An unexpected problem occured during makeReportEpalCapacity Method")
                 ], Response::HTTP_INTERNAL_SERVER_ERROR);
      }
    }



}
