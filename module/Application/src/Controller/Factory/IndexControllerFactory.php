<?php
namespace Application\Controller\Factory;

use Application\Service\ImageManager;
use Application\Service\RequestService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Controller\IndexController;
use Interop\Container\ContainerInterface;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $sessionContainer = $container->get('ClientSession');
        $requestService = $container->get(RequestService::class);
        $imagesManager = $container->get(ImageManager::class);

        return new IndexController($sessionContainer, $requestService, $imagesManager);
    }
}