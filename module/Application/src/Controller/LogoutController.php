<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;


class LogoutController extends AbstractActionController
{
    private $session;

    public function __construct(Container $session)
    {
        $this->session = $session;
    }

    public function indexAction()
    {
        $sessionManage = $this->session->getManager();
        $sessionManage->destroy();

        return $this->redirect()->toRoute('login');
    }

}