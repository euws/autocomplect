<?php

namespace Application\Controller\Factory;

use Application\Controller\RegistrationController;
use Application\Service\RequestService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class RegistrationControllerFactory  implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $sessionContainer = $container->get('ClientSession');
        $requestService = $container->get(RequestService::class);

        return new RegistrationController($sessionContainer, $requestService );
    }
}