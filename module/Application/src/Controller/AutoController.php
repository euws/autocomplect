<?php

namespace Application\Controller;

use Application\Service\ImageManager;
use Application\Service\RequestService;
use GuzzleHttp\Exception\RequestException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class AutoController extends AbstractActionController
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


        $autoId = $this->params()->fromRoute('autoId');

        $query = ['auto' => '1'];
        $body = '['.json_encode($autoId).']';

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
                $data = json_decode($remainingBytes, true)[0];
                $auto = $this->imageManager->generateImageSrc($data);

                return new ViewModel(['auto' => $auto]);
            case 404:
                return new ViewModel(['message' => 'У пользователя пока нет авто']);
                break;
            default:
                return new ViewModel(['message' => 'Ошибка сервера']);
        }

    }
}