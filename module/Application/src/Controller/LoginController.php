<?php
namespace Application\Controller;

use Application\Form\LoginForm;
use Application\Service\RequestService;
use GuzzleHttp\Exception\GuzzleException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class LoginController extends AbstractActionController
{
    private $session;

    private $requestService;

    public function __construct(Container $session, RequestService $requestService)
    {
        $this->session = $session;
        $this->requestService = $requestService;
    }

    public function indexAction()
    {
        if(isset($this->session->userId))
            return $this->redirect()->toRoute('home');
        else{

            $form = new LoginForm();
            $request = $this->getRequest();

            if (!$request->isPost()) {

                return new ViewModel(['form' => $form]);
            }

            $form->setData($request->getPost());

            if (!$form->isValid()) {

               return new ViewModel(['form' => $form]);
            }

            $query = ['login' => '1'];
            $body = '['.json_encode([
                        "login" => $form->get('email')->getValue(),
                        "pass" => $form->get('password')->getValue()
                    ]).']';

            try{
                $response = $this->requestService->makeRequest($query, $body);
            } catch (GuzzleException $e){
                $form->setMessages(array(
                    'email' => array(
                        'Пользователь с данным логином или поролем не существует'
                    )
                ));

                return new ViewModel(['form' => $form]);
            }

            $statusCode = $response->getStatusCode();

            switch ($statusCode){
                case 200:
                    $body = $response->getBody();
                    $remainingBytes = $body->getContents();
                    $data = json_decode($remainingBytes);

                    if (!isset($data[0])){
                        $form->setMessages(array(
                            'email' => array(
                                'Пользователь с данным логином или поролем не существует'
                            )
                        ));

                        return new ViewModel(['form' => $form]);
                    }

                    $userId = $data[0]->iduser;
                    $userName = $data[0]->username;
                    $this->session->userId = $userId;
                    $this->session->userName = $userName;

                    return $this->redirect()->toRoute('home');

                case 404:
                    $form->setMessages(array(
                        'email' => array(
                            'Пользователь с данным логином или поролем не существует'
                        )
                    ));

                    return new ViewModel(['form' => $form]);

                default:
                    $form->setMessages(array(
                        'email' => array(
                            'Ошибка сервера'
                        )
                    ));

                    return new ViewModel(['form' => $form]);
            }
        }
    }

}