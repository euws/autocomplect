<?php

namespace Application\Controller\Factory;

use Application\Controller\LogoutController;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class LogoutControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $sessionContainer = $container->get('ClientSession');

        return new LogoutController($sessionContainer);
    }
}