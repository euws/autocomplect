<?php

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Service\RequestService;
use GuzzleHttp\Client;

class RequestServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $client = new Client();
        $service = new RequestService($client);

        return $service;
    }

}