<?php

namespace Facile\ZFLinkHeadersModule;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;

class ModuleTest extends TestCase
{
    public function testOnBootstrapAllEnabled()
    {
        $event = $this->prophesize(MvcEvent::class);
        $app = $this->prophesize(Application::class);
        $container = $this->prophesize(ContainerInterface::class);
        $eventManager = $this->prophesize(EventManagerInterface::class);
        $options = $this->prophesize(OptionsInterface::class);

        $linkHandler = $this->prophesize(Listener\LinkHandlerInterface::class);
        $stylesheetHandler = $this->prophesize(Listener\LinkHandlerInterface::class);
        $scriptHandler = $this->prophesize(Listener\LinkHandlerInterface::class);

        $event->getApplication()->willReturn($app->reveal());
        $app->getEventManager()->willReturn($eventManager->reveal());
        $app->getServiceManager()->willReturn($container->reveal());

        $container->get(OptionsInterface::class)->willReturn($options->reveal());
        $container->get(Listener\LinkHandler::class)->willReturn($linkHandler->reveal());
        $container->get(Listener\StylesheetHandler::class)->willReturn($stylesheetHandler->reveal());
        $container->get(Listener\ScriptHandler::class)->willReturn($scriptHandler->reveal());

        $eventManager->attach(MvcEvent::EVENT_FINISH, $linkHandler->reveal())
            ->shouldBeCalled();

        $options->isStylesheetEnabled()->willReturn(true);
        $options->isScriptEnabled()->willReturn(true);

        $eventManager->attach(MvcEvent::EVENT_FINISH, $stylesheetHandler->reveal())
            ->shouldBeCalled();

        $eventManager->attach(MvcEvent::EVENT_FINISH, $scriptHandler->reveal())
            ->shouldBeCalled();

        $module = new Module();
        $module->onBootstrap($event->reveal());
    }

    public function testOnBootstrapWithNoMvcEvent()
    {
        $event = $this->prophesize(EventInterface::class);

        $event->getName()->shouldNotBeCalled();

        $module = new Module();
        $module->onBootstrap($event->reveal());
    }

    public function testGetConfig()
    {
        $module = new Module();
        $result = $module->getConfig();

        $this->assertInternalType('array', $result);
    }
}
