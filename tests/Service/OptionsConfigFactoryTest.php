<?php

namespace Facile\ZFLinkHeadersModule\Service;

use Facile\ZFLinkHeadersModule\Options;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class OptionsConfigFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $config = [
            'facile' => [
                'zf_link_headers_module' => [
                    'stylesheet_enabled' => true,
                    'stylesheet_mode' => 'preload',
                    'http2_push_enabled' => false,
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);

        $container->get('config')->willReturn($config);

        $factory = new OptionsConfigFactory();

        $result = $factory($container->reveal());

        $this->assertInstanceOf(Options::class, $result);
        $this->assertSame('preload', $result->getStylesheetMode());
        $this->assertSame(true, $result->isStylesheetEnabled());
        $this->assertSame(false, $result->isHttp2PushEnabled());
    }

    public function testInvokeWithDifferentParams()
    {
        $config = [
            'facile' => [
                'zf_link_headers_module' => [
                    'stylesheet_enabled' => false,
                    'stylesheet_mode' => 'prefetch',
                    'http2_push_enabled' => true,
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);

        $container->get('config')->willReturn($config);

        $factory = new OptionsConfigFactory();

        $result = $factory($container->reveal());

        $this->assertInstanceOf(Options::class, $result);
        $this->assertSame('prefetch', $result->getStylesheetMode());
        $this->assertSame(false, $result->isStylesheetEnabled());
        $this->assertSame(true, $result->isHttp2PushEnabled());
    }
}
