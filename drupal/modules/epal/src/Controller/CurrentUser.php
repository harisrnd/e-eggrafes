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
use Drupal\epal\Crypt;

class CurrentUser extends ControllerBase
{
    const CHILDREN_LIMIT = 100;

    protected $entityTypeManager;
    protected $logger;
    protected $connection;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        Connection $connection,
        LoggerChannelFactoryInterface $loggerChannel
        )
    {
        $this->entityTypeManager = $entityTypeManager;
        $this->connection = $connection;
        $this->logger = $loggerChannel->get('epal');
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('entity_type.manager'),
            $container->get('database'),
            $container->get('logger.factory')
        );
    }

    public function getLoginInfo(Request $request)
    {

        $authToken = $request->headers->get('PHP_AUTH_USER');
//        echo("authtoken in controller=" . $authToken);
        $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
        $user = reset($users);
        if (!$user) {
            return $this->respondWithStatus([
                    'message' => t("User not found"),
                ], Response::HTTP_FORBIDDEN);
        }

        $epalConfigs = $this->entityTypeManager->getStorage('epal_config')->loadByProperties(array('name' => 'epal_config'));
        $epalConfig = reset($epalConfigs);
        if (!$epalConfig) {
            return $this->respondWithStatus([
                    'message' => t("Configuration not found"),
                ], Response::HTTP_FORBIDDEN);
        }

        $userRoles = $user->getRoles();
        foreach ($userRoles as $userRole) {
            if (($userRole === 'epal') || ($userRole === 'regioneduadmin') || ($userRole === 'eduadmin')) {
                return $this->respondWithStatus([
                        'cu_name' => $user->mail->value,
                        'cu_surname' => '',
                        'cu_fathername' => '',
                        'cu_mothername' => '',
                        'cu_email' => '',
                        'minedu_username' => '',
                        'minedu_userpassword' => '',
                        'lock_capacity' => $epalConfig->lock_school_capacity->value,
                        'lock_students' => $epalConfig->lock_school_students_view->value,
                        'lock_application' => $epalConfig->lock_application->value,
                        'disclaimer_checked' => "0",
                        'title' => $user->init->value
                    ], Response::HTTP_OK);
            } else if ($userRole === 'applicant') {
                break;
            }

        }

        $epalUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
        $epalUser = reset($epalUsers);
        if ($epalUser) {

            $crypt = new Crypt();
            try  {
              $name_decoded = $crypt->decrypt($epalUser->name->value);
              $surname_decoded = $crypt->decrypt($epalUser->surname->value);
              $fathername_decoded = $crypt->decrypt($epalUser->fathername->value);
              $mothername_decoded = $crypt->decrypt($epalUser->mothername->value);
            }
            catch (\Exception $e) {
                unset($crypt);
                $this->logger->warning($e->getMessage());
                return $this->respondWithStatus([
                    "error_code" => 5001
                  ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            unset($crypt);

            $userName = $name_decoded;
            $userSurname = $surname_decoded ;
            $userFathername = $fathername_decoded;
            $userMothername =$mothername_decoded;
            $userEmail = $user->mail->value;

            $numAppSelf = $this->getNumApps($epalUser->user_id->target_id, "Μαθητής");
            $numAppChildren = $this->getNumApps($epalUser->user_id->target_id, "Γονέας/Κηδεμόνας");
            if ($numAppSelf === -1 || $numAppChildren === -1)
              return $this->respondWithStatus([
                  'message' => t("num of children not found"),
              ], Response::HTTP_INTERNAL_SERVER_ERROR);

            return $this->respondWithStatus([
                    'cu_name' => mb_substr($userName,0,4,'UTF-8') !== "####" ? $userName : '',
                    'cu_surname' => mb_substr($userSurname,0,4,'UTF-8') !== "####" ? $userSurname : '',
                    'cu_fathername' => mb_substr($userFathername,0,4,'UTF-8') !== "####" ? $userFathername : '',
                    'cu_mothername' => mb_substr($userMothername,0,4,'UTF-8') !== "####" ? $userMothername : '',
                    'cu_email' => mb_substr($user->mail->value,0,4,'UTF-8') !== "####" ? $user->mail->value : '',
                    'minedu_username' => '',
                    'minedu_userpassword' => '',
                    'lock_capacity' => $epalConfig->lock_school_capacity->value,
                    'lock_students' => $epalConfig->lock_school_students_view->value,
                    'lock_application' => $epalConfig->lock_application->value,
                    'disclaimer_checked' => "0",
                    'verificationCodeVerified' => $epalUser->verificationcodeverified->value,
                    'numapp_self' => $numAppSelf,
                    'numapp_children' => $numAppChildren,
                    'numchildren'=> $epalUser->numchildren->value
                ], Response::HTTP_OK);
        } else {
            return $this->respondWithStatus([
                    'message' => t("EPAL user not found"),
                ], Response::HTTP_FORBIDDEN);
        }
    }

    public function getEpalUserData(Request $request)
    {
        $authToken = $request->headers->get('PHP_AUTH_USER');

        $epalUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
        $epalUser = reset($epalUsers);
        if ($epalUser) {
            $user = $this->entityTypeManager->getStorage('user')->load($epalUser->user_id->target_id);
            if ($user) {
                $representRole = $epalUser->representative->value;
                $crypt = new Crypt();
                try  {
                  $userName = $crypt->decrypt($epalUser->name->value);
                  $userSurname = $crypt->decrypt($epalUser->surname->value);
                  $userFathername = $crypt->decrypt($epalUser->fathername->value);
                  $userMothername = $crypt->decrypt($epalUser->mothername->value);
                }
                catch (\Exception $e) {
                    unset($crypt);
                    $this->logger->warning($e->getMessage());
                    return $this->respondWithStatus([
                        "error_code" => 5001
                      ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                unset($crypt);

                $numAppSelf = $this->getNumApps($epalUser->user_id->target_id, "Μαθητής");
                $numAppChildren = $this->getNumApps($epalUser->user_id->target_id, "Γονέας/Κηδεμόνας");
                if ($numAppSelf === -1 || $numAppChildren === -1)
                  return $this->respondWithStatus([
                      'message' => t("num of children not found"),
                  ], Response::HTTP_INTERNAL_SERVER_ERROR);

                $userEmail = $user->mail->value;
                return $this->respondWithStatus([
                    'userName' => mb_substr($userName,0,4,'UTF-8') !== "####" ? $userName : '',
                    'userSurname' => mb_substr($userSurname,0,4,'UTF-8') !== "####" ? $userSurname : '',
                    'userFathername' => mb_substr($userFathername,0,4,'UTF-8') !== "####" ? $userFathername : '',
                    'userMothername' => mb_substr($userMothername,0,4,'UTF-8') !== "####" ? $userMothername : '',
                    'userEmail' => mb_substr($user->mail->value,0,4,'UTF-8') !== "####" ? $user->mail->value : '',
                    'verificationCodeVerified' => $epalUser->verificationcodeverified->value,
                    //'test' => $epalUser->user_id->target_id,
                    'representRole' => $representRole,
                    'numAppSelf' => $numAppSelf,
                    'numAppChildren' => $numAppChildren,
                    'numChildren'=> $epalUser->numchildren->value
                ], Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'message' => t("user not found"),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        } else {
            return $this->respondWithStatus([
                    'message' => t("EPAL user not found"),
                ], Response::HTTP_FORBIDDEN);
        }
    }


    public function getNumApps($userId, $applicantType)  {
      try {
          $sCon = $this->connection->select('epal_student', 'eStudent')
              ->fields('eStudent', array('relationtostudent'))
              ->condition('eStudent.user_id', $userId, '=')
              ->condition('eStudent.delapp', 0, '=')
              ->condition('eStudent.relationtostudent', $applicantType , '=');
          //$results = $sCon->execute()->fetchAll(\PDO::FETCH_OBJ);
          //$row = reset($results);

          return $sCon->countQuery()->execute()->fetchField();
      } catch (\Exception $e) {
          $this->logger->error($e->getMessage());
          return -1;
      }

    }

    public function sendVerificationCode(Request $request)
    {

        if (!$request->isMethod('POST')) {
			return $this->respondWithStatus([
					"message" => t("Method Not Allowed")
				], Response::HTTP_METHOD_NOT_ALLOWED);
    	}
        $authToken = $request->headers->get('PHP_AUTH_USER');

        $trx = $this->connection->startTransaction();
        try {
        $epalUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
        $epalUser = reset($epalUsers);
        if ($epalUser) {
            $user = $this->entityTypeManager->getStorage('user')->load($epalUser->user_id->target_id);
            if ($user) {
                $postData = null;
                if ($content = $request->getContent()) {
                    $postData = json_decode($content);
                    $verificationCode = uniqid();
                    $epalUser->set('verificationcode', $verificationCode);
                    $epalUser->set('verificationcodeverified', FALSE);
                    $epalUser->save();
                    $user->set('mail', $postData->userEmail);
                    $user->save();
                    $this->sendEmailWithVerificationCode($postData->userEmail, $verificationCode, $user);
                    return $this->respondWithStatus([
                        'userEmail' => $postData->userEmail,
                        'verCode' => $verificationCode,
                    ], Response::HTTP_OK);
                }
                else {
                    return $this->respondWithStatus([
                        'message' => t("post with no data"),
                    ], Response::HTTP_BAD_REQUEST);
                }

            } else {
                return $this->respondWithStatus([
                    'message' => t("user not found"),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        } else {
            return $this->respondWithStatus([
                    'message' => t("EPAL user not found"),
                ], Response::HTTP_FORBIDDEN);
        }
        } catch (\Exception $ee) {
            $this->logger->warning($ee->getMessage());
            $trx->rollback();
            return false;
        }

    }

    private function sendEmailWithVerificationCode($email, $vc, $user) {
        $mailManager = \Drupal::service('plugin.manager.mail');

        $module = 'epal';
        $key = 'send_verification_code';
        $to = $email;
        $params['message'] = 'Κωδικός επαλήθευσης=' . $vc;
        $langcode = $user->getPreferredLangcode();
        $send = true;

        $mail_sent = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

        if ($mail_sent) {
            $this->logger->info("Mail Sent successfully.");
        }
        else {
            $this->logger->info("There is error in sending mail.");
        }
        return;
    }


    public function verifyVerificationCode(Request $request)
    {

        if (!$request->isMethod('POST')) {
			return $this->respondWithStatus([
					"message" => t("Method Not Allowed")
				], Response::HTTP_METHOD_NOT_ALLOWED);
    	}
        $authToken = $request->headers->get('PHP_AUTH_USER');

        $epalUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
        $epalUser = reset($epalUsers);
        if ($epalUser) {

            $user = $this->entityTypeManager->getStorage('user')->load($epalUser->user_id->target_id);
            if ($user) {
                $postData = null;
                if ($content = $request->getContent()) {
                    $postData = json_decode($content);
                    if ($epalUser->verificationcode->value !== $postData->verificationCode) {
                        return $this->respondWithStatus([
                            'userEmail' => $user->mail->value,
                            'verificationCodeVerified' => false
                        ], Response::HTTP_OK);
                    } else {
                        $epalUser->set('verificationcodeverified', true);
                        $epalUser->save();
                        return $this->respondWithStatus([
                            'userEmail' => $user->mail->value,
                            'verificationCodeVerified' => true
                        ], Response::HTTP_OK);
                    }
                }
            } else {
                return $this->respondWithStatus([
                    'message' => t("user not found"),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        } else {
            return $this->respondWithStatus([
                    'message' => t("EPAL user not found"),
                ], Response::HTTP_FORBIDDEN);
        }
    }

    public function saveUserProfile(Request $request)
    {

        if (!$request->isMethod('POST')) {
			return $this->respondWithStatus([
					"message" => t("Method Not Allowed")
				], Response::HTTP_METHOD_NOT_ALLOWED);
    	}
        $authToken = $request->headers->get('PHP_AUTH_USER');

        $epalUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
        $epalUser = reset($epalUsers);
        if ($epalUser) {
            $postData = null;
            if ($content = $request->getContent()) {
                $postData = json_decode($content);
                /*
                if (isset($postData->userProfile->representRole))
                  $representRole = $postData->userProfile->representRole;
                if (isset($postData->userProfile->userChildren))
                  $userChildren = $postData->userProfile->userChildren;
               */

                //validate representRole
                /*
                if ( $epalUser->name->value !== "" &&  intval($epalUser->representative->value) !== $representRole )
                      return $this->respondWithStatus([
                          "error_code" => 5001
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                */
                $crypt = new Crypt();
                try  {
                  $name_encoded = $crypt->encrypt($postData->userProfile->userName);
                  $surname_encoded = $crypt->encrypt($postData->userProfile->userSurname);
                  $fathername_encoded = $crypt->encrypt($postData->userProfile->userMothername);
                  $mothername_encoded = $crypt->encrypt($postData->userProfile->userFathername);
                }
                catch (\Exception $e) {
                    unset($crypt);
                    $this->logger->warning($e->getMessage());
                    return $this->respondWithStatus([
                        "error_code" => 5001
                      ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                unset($crypt);

                /*
                $epalUser->set('name', $postData->userProfile->userName);
                $epalUser->set('surname', $postData->userProfile->userSurname);
                $epalUser->set('mothername', $postData->userProfile->userMothername);
                $epalUser->set('fathername', $postData->userProfile->userFathername);
                */
                $epalUser->set('name', $name_encoded);
                $epalUser->set('surname', $surname_encoded);
                $epalUser->set('mothername', $fathername_encoded);
                $epalUser->set('fathername', $mothername_encoded);

                $representRole = "0";
                if (isset($postData->userProfile->representRole)) {
                  $representRole = $postData->userProfile->representRole;
                  $epalUser->set('representative', $representRole);
                }
                if ($representRole === "1")
                  $epalUser->set('numchildren', self::CHILDREN_LIMIT);
                else if (isset($postData->userProfile->userChildren))
                  $epalUser->set('numchildren', $postData->userProfile->userChildren);

                $epalUser->save();
                $user = $this->entityTypeManager->getStorage('user')->load($epalUser->user_id->target_id);
                if ($user) {
                    $user->set('mail', $postData->userProfile->userEmail);
                    $user->save();
                } else {
                    return $this->respondWithStatus([
                        'error_code' => '1001',
                    ], Response::HTTP_FORBIDDEN);
                }
                return $this->respondWithStatus([
                    'error_code' => '0',
                ], Response::HTTP_OK);
            } else {
                return $this->respondWithStatus([
                    'error_code' => '1002',
                ], Response::HTTP_BAD_REQUEST);
            }

        } else {
            return $this->respondWithStatus([
                    'error_code' => '1003',
                ], Response::HTTP_FORBIDDEN);
        }
    }

    private function respondWithStatus($arr, $s) {
        $res = new JsonResponse($arr);
        $res->setStatusCode($s);
        return $res;
    }
}
