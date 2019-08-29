<?php

namespace Application\Controller;

use Application\Service\ImageManager;
use Application\Service\RequestService;
use GuzzleHttp\Exception\RequestException;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class DetailsController extends AbstractActionController
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

        $request = $this->getRequest();
        $autoId = $this->params()->fromRoute('autoId');

        if ($request->isPost()) {
            $details = $request->getPost()->toArray();
            $details['autoId'] = $autoId;

            $query = ['posorder' => 'post'];
            $body = '['.json_encode($details).']';

            $responseAjax = new Response();

            try{
                $response = $this->requestService->makeRequest($query, $body);
            } catch (RequestException $e){
                $responseAjax->setStatusCode(500);
                return $responseAjax;
            }

            $statusCode = $response->getStatusCode();

            switch ($statusCode) {
                case 200:
                    $responseAjax->setStatusCode(200);
                    break;
                default:
                    $responseAjax->setStatusCode(500);
            }

            return $responseAjax;
        }

        $queryDetails = ['posorder' => '1'];
        $queryAuto = ['auto' => '1'];
        $body = '['.json_encode($autoId).']';

        try{
            $responseDetails = $this->requestService->makeRequest($queryDetails, $body);
            $responseAuto = $this->requestService->makeRequest($queryAuto, $body);
        } catch (RequestException $e){
            return new ViewModel(['message' => 'Ошибка сервера']);
        }

        $statusCode = $responseDetails->getStatusCode();

        switch ($statusCode){
            case 200:
                $body = $responseDetails->getBody();
                $remainingBytes = $body->getContents();
                $data = json_decode($remainingBytes, true);
                $groupPart = '';
                $dataSorted = [];

                foreach ($data as $detail){
                    if ($detail['grouppart'] != $groupPart){
                        $dataSorted[$detail['grouppart']] = [];
                        $dataSorted[$detail['grouppart']]['id'] = uniqid();
                        $dataSorted[$detail['grouppart']]['details'] = [];
                        $groupPart = $detail['grouppart'];
                        foreach ($data as $value){
                            if ($value['grouppart'] == $groupPart){
                                $dataSorted[$detail['grouppart']]['details'][] = $value;
                            }
                        }
                    }
                }

                $statusCode = $responseAuto->getStatusCode();

                if ($statusCode == 200){
                    $body = $responseAuto->getBody();
                    $remainingBytes = $body->getContents();
                    $data = json_decode($remainingBytes, true)[0];
                    $auto = $this->imageManager->generateImageSrc($data);
                } else{
                    $auto = [];
                }

                return new ViewModel(['data' => $dataSorted, 'auto' => $auto]);
            case 404:
                return new ViewModel(['message' => 'У пользователя пока нет авто']);
                break;
            default:
                return new ViewModel(['message' => 'Ошибка сервера']);
        }

    }
}