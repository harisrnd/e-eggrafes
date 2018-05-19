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


    public function __construct(EntityTypeManagerInterface $entityTypeManager, LoggerChannelFactoryInterface $loggerChannel)
    {
        $config = $this->config('epal.settings');
        foreach (['ws_endpoint', 'ws_username', 'ws_password', 'verbose', 'NO_SAFE_CURL'] as $setting) {
            $this->settings[$setting] = $config->get($setting);
        }

        $this->entityTypeManager = $entityTypeManager;
        $this->logger = $loggerChannel->get('epal-school');
        $this->client = new Client($this->settings, $this->logger);
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('entity_type.manager'), $container->get('logger.factory')
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
              'custodianLastName' =>  'αβγδεέζηήθιίϊΐκλμνξοόπρστυϋφχψωώ',
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
