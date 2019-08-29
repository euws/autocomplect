<?php

namespace Application\Service\Factory;

use Application\Service\ImageManager;
use Application\Service\RequestService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ImageManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $requestService = $container->get(RequestService::class);
        $service = new ImageManager($requestService);

        return $service;
    }
}