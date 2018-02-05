<?php
namespace Drupal\epal\Controller;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
//use Drupal\Core\Database\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MinistryLogin extends ControllerBase
{
    //protected $entity_query;
    protected $entityTypeManager;
    protected $logger;
    //protected $connection;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        //QueryFactory $entity_query,
        // $connection,
        LoggerChannelFactoryInterface $loggerChannel
    ) {
    
        $this->entityTypeManager = $entityTypeManager;
        //$this->entity_query = $entity_query;
        //$this->connection = $connection;
        $this->logger = $loggerChannel->get('epal');
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
          $container->get('entity.manager'),
          //$container->get('entity.query'),
          //$container->get('database'),
          $container->get('logger.factory')
        );
    }

    public function loginGo(Request $request)
    {

        try {
            if (!$request->isMethod('POST')) {
                return $this->respondWithStatus([
                    "message" => t("Method Not Allowed")
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }

            //user validation
            //Note:  $authToken =  $postData->username
            $authToken = $request->headers->get('PHP_AUTH_USER');
            $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
            $user = reset($users);
            if (!$user) {
                return $this->respondWithStatus([
                    'message' => t("User not found"),
                ], Response::HTTP_FORBIDDEN);
            }

            //user role validation
            //$user = \Drupal\user\Entity\User::load($user->id());
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
            $currentRoleName = "supervisor";

            $postData = null;
            if ($content = $request->getContent()) {
                $postData = json_decode($content);
                //return new RedirectResponse("/drupal-8.2.6/eepal/dist/"  . '?auth_token=' . $postData->username .'&auth_role=supervisor', 302, []);
                return $this->respondWithStatus([
                //'auth_token' => $postData->username,
                //'userpassword' => $postData->userpassword,
                //'auth_role' => $currentRoleName,
                ], Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'message' => t("post with no data"),
                ], Response::HTTP_BAD_REQUEST);
            }
        } //end try

        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                'message' => 'forbidden',
            ], Response::HTTP_FORBIDDEN);
        }
    }

    public function logoutGo(Request $request)
    {
        try {
            if (!$request->isMethod('POST')) {
                return $this->respondWithStatus([
                    "message" => t("Method Not Allowed")
                 ], Response::HTTP_METHOD_NOT_ALLOWED);
            }

            //user validation
            //Note:  $authToken =  $postData->username
            $authToken = $request->headers->get('PHP_AUTH_USER');
            $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
            $user = reset($users);
            if (!$user) {
                return $this->respondWithStatus([
                      'message' => t("User not found"),
                  ], Response::HTTP_FORBIDDEN);
            }

            //user role validation
            //$user = \Drupal\user\Entity\User::load($user->id());
            /*
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
            */

            session_unset();
            session_destroy();

            return $this->respondWithStatus([
                'message' => 'logout successful',
            ], Response::HTTP_OK);
        } //end try

        catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            return $this->respondWithStatus([
                'message' => t("forbidden"),
            ], Response::HTTP_FORBIDDEN);
        }
    }

    private function respondWithStatus($arr, $s)
    {
        return (new JsonResponse($arr))
            ->setStatusCode($s);
    }
}
