<?php

namespace Facile\ZFLinkHeadersModule\Listener;

use Facile\ZFLinkHeadersModule\OptionsInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\View\Helper\HeadLink;
use Zend\View\HelperPluginManager;

class LinkHandlerFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $viewHelperManager = $this->prophesize(HelperPluginManager::class);
        $headLink = $this->prophesize(HeadLink::class);
        $options = $this->prophesize(OptionsInterface::class);

        $container->get(OptionsInterface::class)->willReturn($options->reveal());
        $container->get('ViewHelperManager')->willReturn($viewHelperManager->reveal());
        $viewHelperManager->get(HeadLink::class)->willReturn($headLink->reveal());

        $factory = new LinkHandlerFactory();

        $result = $factory($container->reveal());

        $this->assertInstanceOf(LinkHandler::class, $result);
    }
}
