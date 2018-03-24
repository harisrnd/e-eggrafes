<?php

namespace Drupal\oauthost\Controller;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use OAuth;
use OAuthException;
use Drupal\user\Entity\User;
use Drupal\Core\Database\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\oauthost\Crypt;

class OAuthLogin extends ControllerBase
{
    protected $entity_query;
    protected $entityTypeManager;
    protected $logger;
    protected $connection;

    protected $consumer_key = '';
    protected $consumer_secret = '';
    protected $request_token_url;
    protected $user_authorization_url;
    protected $access_token_url;
    protected $signature_method;
    protected $api_url;
    protected $callback_url;
    protected $logout_url;
    protected $redirect_url;

    public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    QueryFactory $entity_query,
    Connection $connection,
    LoggerChannelFactoryInterface $loggerChannel)
    {
        $this->entityTypeManager = $entityTypeManager;
        $this->entity_query = $entity_query;
        $this->connection = $connection;
        $this->logger = $loggerChannel->get('oauthost');
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

    public function loginGo(Request $request)
    {
        //τεστ
        //$schoolToken = "fe76062d85fc06f12b93d53873da4314";
        //return new RedirectResponseWithCookieExt("http://eduslim2.minedu.gov.gr/e-eggrafes/dist/#/?auth_token=" . $schoolToken.'&auth_role=student', 302, []);
        //τέλος

        $configRowName = 'oauthost_taxisnet_config';
        $configRowId = $request->query->get('config');
        if ($configRowId)
            $configRowName = $configRowName . '_' . $configRowId;
        $ostauthConfigs = $this->entityTypeManager->getStorage('oauthost_config')->loadByProperties(array('name' => $configRowName));
        $ostauthConfig = reset($ostauthConfigs);
        if ($ostauthConfig) {

            $crypt = new Crypt();

            try  {
              $consumer_key_decoded = $crypt->decrypt($ostauthConfig->consumer_key->value);
              $consumer_secret_decoded = $crypt->decrypt($ostauthConfig->consumer_secret->value);
            }
            catch (\Exception $e) {
              unset($crypt);
              $this->logger->notice('schoolToken decoding false');
              $response = new Response();
              $response->setContent('internal_server_error');
              $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
              $response->headers->set('Content-Type', 'application/json');
              return $response;
            }
            unset($crypt);


            //$this->consumer_key = $ostauthConfig->consumer_key->value;
            //$this->consumer_secret = $ostauthConfig->consumer_secret->value;
            $this->consumer_key = $consumer_key_decoded;
            $this->consumer_secret = $consumer_secret_decoded;

            $this->request_token_url = $ostauthConfig->request_token_url->value;
            $this->user_authorization_url = $ostauthConfig->user_authorization_url->value;
            $this->access_token_url = $ostauthConfig->access_token_url->value;
            $this->signature_method = $ostauthConfig->signature_method->value;
            $this->api_url = $ostauthConfig->api_url->value;
            $this->callback_url = $ostauthConfig->callback_url->value;
            $this->logout_url = $ostauthConfig->logout_url->value;
            $this->redirect_url = $ostauthConfig->redirect_url->value;
        } else {
            $response = new Response();
            $response->setContent('forbidden: no valid ostauthConfig');
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        try {
            $oauth = new OAuth($this->consumer_key, $this->consumer_secret, OAUTH_SIG_METHOD_PLAINTEXT, OAUTH_AUTH_TYPE_URI);
            //$oauth->disableSSLChecks();
            //$oauth->enableDebug();

            $uniqid = uniqid('sid');
            $requestToken = $oauth->getRequestToken($this->request_token_url, $this->callback_url . '?sid_ost=' . $uniqid);
              // store auth token

              // $this->logger->warning($request->headers->get('referer'));
            $oauthostSession = $this->entityTypeManager()->getStorage('oauthost_session')->create(array(
              // 'langcode' => $language_interface->getId(),
              'langcode' => 'el',
              'user_id' => \Drupal::currentUser()->id(),
              'name' => $uniqid,
              'referer' => $request->headers->get('referer'),
              'configrowname' => $configRowName,
              'authtoken' => '---',
              'request_token' => $requestToken['oauth_token'],
              'request_token_secret' => $requestToken['oauth_token_secret'],
              'status' => 1
          ));

            $oauthostSession->save();
            //$this->logger->warning('redirected to:'.$this->user_authorization_url);
            header('Location: '.$this->user_authorization_url.'?oauth_token='.$requestToken['oauth_token']);
            exit;
        } catch (OAuthException $e) {
            $this->logger->warning($e->getMessage());
            $response = new Response();
            $response->setContent('forbidden: LoginGo: OAuthException');
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

}
