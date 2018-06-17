<?php

namespace Drupal\epal\Controller;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Database\Connection;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\epal\Client;

class WSConsumer extends ControllerBase
{
    protected $entityTypeManager;
    protected $logger;
    protected $client;
    protected $settings;
    protected $connection;

    public function __construct(EntityTypeManagerInterface $entityTypeManager, LoggerChannelFactoryInterface $loggerChannel, Connection $connection)
    {
        $config = $this->config('epal.settings');
        foreach (['ws_endpoint', 'ws_username', 'ws_password', 'verbose', 'NO_SAFE_CURL'] as $setting) {
            $this->settings[$setting] = $config->get($setting);
        }

        $this->entityTypeManager = $entityTypeManager;
        $this->logger = $loggerChannel->get('epal-school');
        $this->client = new Client($this->settings, $this->logger);
        $this->connection = $connection;

    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('entity_type.manager'), $container->get('logger.factory'),   $container->get('database')
        );
    }

    public function getPing(Request $request)
    {
        return (new JsonResponse(['message' => 'Ping!!!']))
            ->setStatusCode(Response::HTTP_OK);
    }

    public function getAllDidactiYear()
    {
        $ts_start = microtime(true);

        try {
            $catalog = $this->client->getAllDidactiYear();
        } catch (\Exception $e) {
            return (new JsonResponse(['message' => $e->getMessage()]))
                ->setStatusCode(($code = $e->getCode()) == 0 ? Response::HTTP_INTERNAL_SERVER_ERROR : $code);
        }

        $duration = microtime(true) - $ts_start;
        $this->logger->info(__METHOD__ . " :: timed [{$duration}]");

        return (new JsonResponse([
                'message' => 'Επιτυχία',
                'data' => $catalog
            ]))
            ->setStatusCode(Response::HTTP_OK);
    }

    public function getStudentEpalInfo($didactic_year, $lastname, $firstname, $father_firstname, $mother_firstname, $birthdate, $registry_no, $registration_no)
    {
        $testmode = false;
        $didactic_year_id=$this->getdidacticyear($didactic_year);

        if ($testmode)  {
          $obj = array(
          'message' => 'Επιτυχία',

          'data' => array(
              'id' => '15800',
              'studentId' => 266345444,
              'lastname' => 'ΓΕ',
              'firstname' => 'ΚΩ',
              'custodianLastName' =>  'KAΤΣΑΟυΝΟΣ',
              'custodianFirstName' => '',
              'birthDate' => '1997-01-04T00:00:00',
              'addressStreet' => 'ΣΚΣ. //Δ Δ&&',
              'addressPostCode' => '22222',
              'addressArea' => 'Ν. / ,&^% ΣΜΥΡΝΗ',
              'unitTypeDescription' => 'Ημερήσιο ΕΠΑΛ',
              'levelName' => 'Γ',
              'sectionName' => 'Τεχνικός Μηχανοσυνθέτης Αεροσκαφών'
            )
          //'data' => "null"
        );
          return (new JsonResponse($obj))
            ->setStatusCode(Response::HTTP_OK);
        }


        //formal code
        //$ts_start = microtime(true);

        try {
            $result = $this->client->getStudentEpalInfo($didactic_year_id, $lastname, $firstname, $father_firstname, $mother_firstname, $birthdate, $registry_no, $registration_no);
        } catch (\Exception $e) {
            return (new JsonResponse(['message' => $e->getMessage()]))
                ->setStatusCode(($code = $e->getCode()) == 0 ? Response::HTTP_INTERNAL_SERVER_ERROR : $code);
                //->setStatusCode(($code = $e->getCode()) == 0 ? Response::HTTP_OK : $code);
        }

        //$duration = microtime(true) - $ts_start;
        //$this->logger->info(__METHOD__ . " :: timed [{$duration}]");

        return (new JsonResponse([
                'message' => 'Επιτυχία',
                'data' => json_decode($result)
            ]))
            ->setStatusCode(Response::HTTP_OK);
    }


    public function getStudentEpalPromotion()
    {
        $ts_start = microtime(true);

        //get Ids
        $id = 0;
        //...

        try {
            $result = $this->client->getStudentEpalPromotion($id);
        } catch (\Exception $e) {
            return (new JsonResponse(['message' => $e->getMessage()]))
                ->setStatusCode(($code = $e->getCode()) == 0 ? Response::HTTP_INTERNAL_SERVER_ERROR : $code);
        }

        $duration = microtime(true) - $ts_start;
        $this->logger->info(__METHOD__ . " :: timed [{$duration}]");

        return (new JsonResponse([
                'message' => 'Επιτυχία',
                'data' => $result
            ]))
            ->setStatusCode(Response::HTTP_OK);
    }

    public function transitionToBPeriod() {

      //μετάπτωση όλων των αιτήσεων για ΕΠΑΛ που οι μαθητές δεν προάχθηκαν σε δεύτερη περίοδο
      $sCon = $this->connection
  			 ->select('epal_student', 'eStudent')
  			 ->fields('eStudent', array('id', 'myschool_promoted', 'lastschool_unittypeid','myschool_currentlevelname'))
  			 ->condition('eStudent.delapp', 0, '=');
  		$epalStudents = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
      $cnt_epal = 0;
  		foreach ($epalStudents as $epalStudent)  {
      if ( ($epalStudent->myschool_promoted == "6" || $epalStudent->myschool_promoted == "7")
                  //μετάπτωση σε Β' περίοδο όσων απορρίφθηκαν, εκτός των αιτήσεων Γυμνασίου
            && (  ($epalStudent->lastschool_unittypeid != 3)  ||
                  //εξαίρεση: ΓΥΜ ΜΕ ΛΤ
                  ($epalStudent->lastschool_unittypeid == 3 && $epalStudent->myschool_currentlevelname != "Γ")  )
        )
    {
          try {
              $query = $this->connection->update('epal_student');
              $query->fields(['second_period' => "1"]);
              $query->condition('id', $epalStudent->id);
              $query->execute();
              ++$cnt_epal;

              //διαγραφή ενδεχόμενου αποτελέσματος σε πίνακα αποτελεσμάτων ΕΠΑΛ
              $this->connection->delete('epal_student_class')
                  ->condition('student_id', $epalStudent->id, '=')
                  ->execute();


          } catch (\Exception $e) {
              $this->logger->error($e->getMessage());
              //return self::ERROR_DB;
          }
        }
      }

      //μετάπτωση όλων των αιτήσεων για ΓΕΛ που οι μαθητές δεν προάχθηκαν σε δεύτερη περίοδο
      $sCon = $this->connection
         ->select('gel_student', 'eStudent')
         ->fields('eStudent', array('id', 'myschool_promoted'))
         ->condition('eStudent.delapp', 0, '=');
      $gelStudents = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
      $cnt_gel = 0;
      foreach ($gelStudents as $gelStudent)  {
      if ( $gelStudent->myschool_promoted == "6" || $gelStudent->myschool_promoted == "7")  {
          try {
              $query = $this->connection->update('gel_student');
              $query->fields(['second_period' => "1"]);
              $query->condition('id', $gelStudent->id);
              $query->execute();
              ++$cnt_gel;

              //διαγραφή ενδεχόμενου αποτελέσματος σε πίνακα αποτελεσμάτων ΓΕΛ (gelstudenthighschool)
              $this->connection->delete('gelstudenthighschool')
                  ->condition('student_id', $gelStudent->id, '=')
                  ->execute();

          } catch (\Exception $e) {
              $this->logger->error($e->getMessage());
          }
        }
      }




      return (new JsonResponse([
              'num_epal' => $cnt_epal,
              'num_gel' => $cnt_gel,
            ]))
            ->setStatusCode(Response::HTTP_OK);

    }

    /*
    public function testgetStudentEpalInfo($didactic_year_id, $lastname, $firstname, $father_firstname, $mother_firstname, $birthdate, $registry_no, $registration_no)
    {
          $obj = array(
          'message' => 'Επιτυχία',

          'data' => array(
              'id' => '158',
              'studentId' => 2666027,
              'lastname' => 'ΓΕΩΡΓΟΥΛΑΣ',
              'firstname' => 'ΚΩΝΣΤΑΝΤΙΣτοιχείαΝΟΣ',
              'custodianLastName' =>  'ΚΑΤΣΑΟΥΝΟΣ',
              //'custodianLastName' =>  preg_replace('/\s+/', '', ' ΚΑΤΣ ΑΟΥΝΟΣ '),
              //'custodianLastName' =>  preg_replace('/[-\s]/', '', ' ΚΑΤΣ - ΑΟΥΝΟΣ '),
              'custodianFirstName' => '',
              'birthDate' => '1997-01-04T00:00:00',
              'addressStreet' => 'ΕΛΛΗΣ 8',
              'addressPostCode' => '30100',
              'addressArea' => 'ΑΓΡΙΝΙΟ',
              'unitTypeDescription' => 'Ημερήσιο ΕΠΑΛ',
              'levelName' => 'Γ',
              'sectionName' => 'Τεχνικός Μηχανοσυνθέτης Αεροσκαφών'
        )
          //'data' => "null"
    );

    return (new JsonResponse($obj))
        ->setStatusCode(Response::HTTP_OK);

    }
    */

    /*
    public function getStudentEpalCertification($id)
    {
        $ts_start = microtime(true);

        try {
            $result = $this->client->getStudentEpalCertification($id);
        } catch (\Exception $e) {
            return (new JsonResponse(['message' => $e->getMessage()]))
                ->setStatusCode(($code = $e->getCode()) == 0 ? Response::HTTP_INTERNAL_SERVER_ERROR : $code);
        }

        $duration = microtime(true) - $ts_start;
        $this->logger->info(__METHOD__ . " :: timed [{$duration}]");

        return (new JsonResponse([
                'message' => 'Επιτυχία',
                'data' => $result
            ]))
            ->setStatusCode(Response::HTTP_OK);
    }
    */

    private function generateRandomString($length)
    {
        $characters = ['Α','Β','Γ','Δ','Ε','Ζ','Η','Θ','Ι','Κ','Λ','Μ','Ν','Ξ','Ο','Π','Ρ','Σ','Τ','Υ','Φ','Χ','Ψ','Ω'];
        $charactersLength = count($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function getdidacticyear($didactic_year)
    {
        switch ($didactic_year){
            case "2013-2014":
                $didactic_year_id="18";
                break;
            case "2014-2015":
                $didactic_year_id="22";
                break;
            case "2015-2016":
                $didactic_year_id="23";
                break;
            case "2016-2017":
                $didactic_year_id="24";
                break;
            case "2017-2018":
                $didactic_year_id="25";
                break;
       }
        return $didactic_year_id;
    }

}
