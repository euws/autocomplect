<?php

namespace Application\Controller;

use Application\Form\LoginForm;
use Application\Form\RegistrationForm;
use Application\Service\RequestService;
use GuzzleHttp\Exception\GuzzleException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class RegistrationController extends AbstractActionController
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
        if(isset($this->session->userId)){
            return $this->redirect()->toRoute('home');
        }
        else {

            $form = new RegistrationForm();
            $request = $this->getRequest();

            if (!$request->isPost()) {

                return new ViewModel(['form' => $form]);
            }

            $form->setData($request->getPost());

            if (!$form->isValid()) {

                return new ViewModel(['form' => $form]);
            }

            return;
        }
    }
}
