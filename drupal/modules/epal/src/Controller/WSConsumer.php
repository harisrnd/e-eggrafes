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

    public function getStudentEpalInfo($didactic_year_id, $lastname, $firstname, $father_firstname, $mother_firstname, $birthdate, $registry_no, $registration_no)
    {
        $ts_start = microtime(true);

        try {
            $result = $this->client->getStudentEpalInfo($didactic_year_id, $lastname, $firstname, $father_firstname, $mother_firstname, $birthdate, $registry_no, $registration_no);
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

    public function testgetStudentEpalInfo($didactic_year_id, $lastname, $firstname, $father_firstname, $mother_firstname, $birthdate, $registry_no, $registration_no)
    {
          $obj = array(
          'message' => 'Επιτυχία',

          'data' => array(
              'id' => '137',
              'studentId' => 1666010,
              'lastname' => 'ΓΕΩΡΓΟΥΛΑΣ',
              'firstname' => 'ΚΩΝΣΤΑΝΤΙΝΟΣ',
              'custodianLastName' =>  'ΚΑΤΣΑΟΥΝΟΣ',
              //'custodianLastName' =>  preg_replace('/\s+/', '', ' ΚΑΤΣ ΑΟΥΝΟΣ '),
              //'custodianLastName' =>  preg_replace('/[-\s]/', '', ' ΚΑΤΣ - ΑΟΥΝΟΣ '),
              'custodianFirstName' => '',
              'birthDate' => '1997-01-04T00:00:00',
              'addressStreet' => 'ΕΛΛΗΣ 6',
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

}
