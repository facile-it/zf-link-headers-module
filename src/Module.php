<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule;

use Facile\ZFLinkHeadersModule\Listener\LinkHandler;
use Facile\ZFLinkHeadersModule\Listener\ScriptHandler;
use Facile\ZFLinkHeadersModule\Listener\StylesheetHandler;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

final class Module implements BootstrapListenerInterface, ConfigProviderInterface
{
    /**
     * @param EventInterface $e
     * @return array|void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function onBootstrap(EventInterface $e)
    {
        if (! $e instanceof MvcEvent) {
            return;
        }

        $app = $e->getApplication();
        $container = $app->getServiceManager();
        $eventManager = $app->getEventManager();

        $linkHandler = $container->get(LinkHandler::class);
        $eventManager->attach(MvcEvent::EVENT_FINISH, $linkHandler);

        $stylesheetHandler = $container->get(StylesheetHandler::class);
        $eventManager->attach(MvcEvent::EVENT_FINISH, $stylesheetHandler);

        $scriptHandler = $container->get(ScriptHandler::class);
        $eventManager->attach(MvcEvent::EVENT_FINISH, $scriptHandler);
    }

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return [
            'facile' => [
                'zf_link_headers_module' => [
                    'stylesheet_enabled' => false,
                    'stylesheet_mode' => 'preload',
                    'script_enabled' => false,
                    'script_mode' => 'preload',
                    'http2_push_enabled' => true,
                ],
            ],
            'service_manager' => [
                'factories' => [
                    OptionsInterface::class => Service\OptionsConfigFactory::class,
                    Listener\LinkHandler::class => Listener\LinkHandlerFactory::class,
                    Listener\StylesheetHandler::class => Listener\StylesheetHandlerFactory::class,
                    Listener\ScriptHandler::class => Listener\ScriptHandlerFactory::class,
                ],
            ],
        ];
    }
}
