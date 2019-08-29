<?php
namespace Application\Controller\Factory;

use Application\Service\Factory\RequestServiceFactory;
use Application\Service\RequestService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Controller\LoginController;
use Interop\Container\ContainerInterface;

class LoginControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $sessionContainer = $container->get('ClientSession');
        $requestService = $container->get(RequestService::class);

        return new LoginController($sessionContainer, $requestService );
    }
}