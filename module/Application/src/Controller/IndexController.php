<?php
namespace Application\Controller;

use Application\Service\ImageManager;
use Application\Service\RequestService;
use GuzzleHttp\Exception\RequestException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class IndexController extends AbstractActionController
{
    private $session;

    private $requestService;

    private $imageManager;

    public function __construct(Container $session,
                                RequestService $requestService,
                                ImageManager $imageManager)
    {
        $this->session = $session;
        $this->requestService = $requestService;
        $this->imageManager = $imageManager;
    }

    public function indexAction()
    {
        if(!isset($this->session->userId))
            return $this->redirect()->toRoute('login');

        $query = ['uauto' => '1'];
        $body = '['.json_encode(["iduser" => $this->session->userId]).']';

        try{
            $response = $this->requestService->makeRequest($query, $body);
        } catch (RequestException $e){
            return new ViewModel(['message' => 'Ошибка сервера']);
        }

        $statusCode = $response->getStatusCode();

        switch ($statusCode){
            case 200:
                $body = $response->getBody();
                $remainingBytes = $body->getContents();
                $data = json_decode($remainingBytes, true);
                $autos = [];

                foreach ($data as $auto){
                    $autos[] = $this->imageManager->generateImageSrc($auto);
                }

                return new ViewModel(['autos' => $autos]);
            case 404:
                return new ViewModel(['message' => 'У пользователя пока нет авто']);
                break;
            default:
                return new ViewModel(['message' => 'Ошибка сервера']);
        }

    }
}
