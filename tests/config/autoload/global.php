<?php

use Facile\ZFLinkHeadersModule;

return [
    'routes' => [
        'home' => [
            'type' => 'literal',
            'options' => [
                'route' => '/',
                'defaults' => [
                    'controller' => ZFLinkHeadersModule\Functional\IndexController::class,
                    'action' => 'index',
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            ZFLinkHeadersModule\Functional\IndexController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'not_found_template' => 'index',
        'exception_template' => 'index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../../view/layout.phtml',
            'index' => __DIR__ . '/../../view/index.phtml',
            'error' => __DIR__ . '/../../view/error.phtml',
        ],
    ],
];
