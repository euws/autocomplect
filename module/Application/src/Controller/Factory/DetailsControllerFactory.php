<?php
namespace Application\Controller\Factory;

use Application\Controller\DetailsController;
use Application\Service\ImageManager;
use Application\Service\RequestService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class DetailsControllerFactory  implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $sessionContainer = $container->get('ClientSession');
        $requestService = $container->get(RequestService::class);
        $imagesManager = $container->get(ImageManager::class);

        return new DetailsController($sessionContainer, $requestService, $imagesManager);
    }
}