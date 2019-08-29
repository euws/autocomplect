<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

use Application\Controller\AutoController;
use Application\Controller\Factory\AutoControllerFactory;
use Application\Controller\Factory\DetailsControllerFactory;
use Application\Controller\Factory\IndexControllerFactory;
use Application\Controller\Factory\LoginControllerFactory;
use Application\Controller\Factory\LogoutControllerFactory;
use Application\Controller\Factory\RegistrationControllerFactory;
use Application\Controller\LogoutController;
use Application\Service\Factory\ImageManagerFactory;
use Application\Service\Factory\RequestServiceFactory;
use Application\Service\ImageManager;
use Application\Service\RequestService;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'login' => [
                'type'    => Literal::class,
                'options' => [
                    'route' => '/login',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'registration' => [
                'type'    => Literal::class,
                'options' => [
                    'route' => '/registration',
                    'defaults' => [
                        'controller' => Controller\RegistrationController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'logout' => [
                'type'    => Literal::class,
                'options' => [
                    'route' => '/logout',
                    'defaults' => [
                        'controller' => Controller\LogoutController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'auto' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/auto[/:autoId]',
                    'constraints' => [
                        'autoId' => '[a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\AutoController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'details' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/details[/:autoId]',
                    'constraints' => [
                        'autoId' => '[a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\DetailsController::class,
                        'action' => 'index',
                    ],
                ],
            ]
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => IndexControllerFactory::class,
            Controller\LoginController::class => LoginControllerFactory::class,
            Controller\RegistrationController::class => RegistrationControllerFactory::class,
            Controller\LogoutController::class => LogoutControllerFactory::class,
            Controller\AutoController::class => AutoControllerFactory::class,
            Controller\DetailsController::class => DetailsControllerFactory::class,
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'session_containers' => [
        'ClientSession'
    ],

    'service_manager' => [
        'factories' => [
            RequestService::class => RequestServiceFactory::class,
            ImageManager::class => ImageManagerFactory::class
        ]
    ]

];