<?php

namespace Facile\ZFLinkHeadersModule\Listener;

use Facile\ZFLinkHeadersModule\OptionsInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\View\Helper\HeadScript;
use Zend\View\HelperPluginManager;

class ScriptHandlerFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $viewHelperManager = $this->prophesize(HelperPluginManager::class);
        $headScript = $this->prophesize(HeadScript::class);
        $options = $this->prophesize(OptionsInterface::class);

        $container->get(OptionsInterface::class)->willReturn($options->reveal());
        $container->get('ViewHelperManager')->willReturn($viewHelperManager->reveal());
        $viewHelperManager->get(HeadScript::class)->willReturn($headScript->reveal());

        $factory = new ScriptHandlerFactory();

        $result = $factory($container->reveal());

        $this->assertInstanceOf(ScriptHandler::class, $result);
    }
}
