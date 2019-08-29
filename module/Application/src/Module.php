<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Http\Response;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;

class Module
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        $sessionManager = $event->getApplication()->getServiceManager()->get('Zend\Session\SessionManager');

        $this->forgetInvalidSession($sessionManager);
    }

    protected function forgetInvalidSession($sessionManager) {
        try {
            $sessionManager->start();
            return;
        } catch (\Exception $e) {
        }

        session_unset();
    }

    public function init(ModuleManager $manager)
    {
        $eventManager = $manager->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        //$sharedEventManager->attach('Zend\Mvc\Application', MvcEvent::EVENT_BOOTSTRAP,
        //    [$this, 'onRoute'], 100);
    }

    public function onRoute(MvcEvent $event)
    {
        if (php_sapi_name() == "cli") {
            return;
        }

        $uri = $event->getRequest()->getUri();
        $scheme = $uri->getScheme();

        if ($scheme != 'https'){
            $uri->setScheme('https');
            $response=$event->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $uri);
            $response->setStatusCode(301);
            $response->sendHeaders();

            return $response;
        }
    }
}
