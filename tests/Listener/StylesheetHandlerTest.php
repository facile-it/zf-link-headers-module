<?php

namespace Facile\ZFLinkHeadersModule\Listener;

use Facile\ZFLinkHeadersModule\OptionsInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Zend\Http\Header\GenericMultiHeader;
use Zend\Http\Header\HeaderInterface;
use Zend\Http\Headers;
use Zend\Http\PhpEnvironment;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\View\Helper\HeadLink;
use Zend\View\Helper\Placeholder\Container\AbstractContainer;

class StylesheetHandlerTest extends TestCase
{
    public function testInvokeWithAnotherResponse()
    {
        $headLink = $this->prophesize(HeadLink::class);
        $options = $this->prophesize(OptionsInterface::class);
        $event = $this->prophesize(MvcEvent::class);
        $response = $this->prophesize(Response::class);

        $headLink->getContainer()->shouldNotBeCalled();
        $response->getHeaders()->shouldNotBeCalled();
        $event->getResponse()->willReturn($response->reveal());
        $options->isStylesheetEnabled()->willReturn(true);

        $injector = new StylesheetHandler($headLink->reveal(), $options->reveal());

        $injector($event->reveal());
    }

    public function testInvokeWithDisabled()
    {
        $headLink = $this->prophesize(HeadLink::class);
        $options = $this->prophesize(OptionsInterface::class);
        $event = $this->prophesize(MvcEvent::class);
        $response = $this->prophesize(PhpEnvironment\Response::class);

        $headLink->getContainer()->shouldNotBeCalled();
        $response->getHeaders()->shouldNotBeCalled();
        $event->getResponse()->willReturn($response->reveal());
        $options->isStylesheetEnabled()->willReturn(false);

        $injector = new StylesheetHandler($headLink->reveal(), $options->reveal());

        $injector($event->reveal());
    }

    public function testInvokeWithExistingHeader()
    {
        $links = [
            $this->createStdClass([
                'href' => '/foo.css',
                'rel' => OptionsInterface::MODE_PRELOAD,
            ]),
            $this->createStdClass([
                'href' => '/bar.css',
                'rel' => 'stylesheet',
                'type' => 'text/style',
                'media' => '(min-width: 100px)',
            ]),
        ];

        $expected = [
            '</existing-link>; as="style"',
            '</bar.css>; rel="preload"; as="style"; type="text/style"; media="(min-width: 100px)"; nopush',
        ];

        $headLink = $this->prophesize(HeadLink::class);
        $options = $this->prophesize(OptionsInterface::class);
        $event = $this->prophesize(MvcEvent::class);
        $responseHeaders = $this->prophesize(Headers::class);
        $headContainer = $this->prophesize(AbstractContainer::class);
        $response = $this->prophesize(PhpEnvironment\Response::class);
        $existingHeader = $this->prophesize(HeaderInterface::class);

        $options->getStylesheetMode()->willReturn('preload');
        $options->isHttp2PushEnabled()->willReturn(false);
        $options->isStylesheetEnabled()->willReturn(true);

        $headContainer->getIterator()->willReturn(new \ArrayIterator($links));

        $headLink->getContainer()->willReturn($headContainer->reveal());
        $response->getHeaders()->willReturn($responseHeaders->reveal());
        $event->getResponse()->willReturn($response->reveal());

        $responseHeaders->get('Link')->willReturn($existingHeader->reveal());
        $responseHeaders->removeHeader($existingHeader->reveal())->shouldBeCalled();
        $existingHeader->getFieldValue()->willReturn('</existing-link>; as="style"');

        $responseHeaders->addHeader(Argument::allOf(
            Argument::type(GenericMultiHeader::class),
            Argument::which('getFieldName', 'Link'),
            Argument::which('getFieldValue', \implode(', ', $expected))
        ))
            ->shouldBeCalledTimes(1);

        $injector = new StylesheetHandler($headLink->reveal(), $options->reveal());

        $injector($event->reveal());
    }

    public function testInvokeWithNoPush()
    {
        $links = [
            $this->createStdClass([
                'href' => '/foo.css',
                'rel' => OptionsInterface::MODE_PRELOAD,
            ]),
            $this->createStdClass([
                'href' => '/bar.css',
                'rel' => 'stylesheet',
                'type' => 'text/style',
                'media' => '(min-width: 100px)',
            ]),
        ];

        $expected = [
            '</bar.css>; rel="preload"; as="style"; type="text/style"; media="(min-width: 100px)"; nopush',
        ];

        $headLink = $this->prophesize(HeadLink::class);
        $options = $this->prophesize(OptionsInterface::class);
        $event = $this->prophesize(MvcEvent::class);
        $responseHeaders = $this->prophesize(Headers::class);
        $headContainer = $this->prophesize(AbstractContainer::class);
        $response = $this->prophesize(PhpEnvironment\Response::class);

        $options->getStylesheetMode()->willReturn('preload');
        $options->isHttp2PushEnabled()->willReturn(false);
        $options->isStylesheetEnabled()->willReturn(true);

        $headContainer->getIterator()->willReturn(new \ArrayIterator($links));

        $headLink->getContainer()->willReturn($headContainer->reveal());
        $response->getHeaders()->willReturn($responseHeaders->reveal());
        $event->getResponse()->willReturn($response->reveal());

        $responseHeaders->get('Link')->willReturn(false);
        $responseHeaders->removeHeader(Argument::any())->shouldNotBeCalled();

        $responseHeaders->addHeader(Argument::allOf(
            Argument::type(GenericMultiHeader::class),
            Argument::which('getFieldName', 'Link'),
            Argument::which('getFieldValue', \implode(', ', $expected))
        ))
            ->shouldBeCalledTimes(1);

        $injector = new StylesheetHandler($headLink->reveal(), $options->reveal());

        $injector($event->reveal());
    }

    public function testInvokeWithPushAndPrefetch()
    {
        $links = [
            $this->createStdClass([
                'rel' => OptionsInterface::MODE_PRELOAD,
                'href' => '/foo.css',
            ]),
            $this->createStdClass([
                'rel' => 'stylesheet',
                'href' => '/bar.css',
                'type' => 'text/style',
                'media' => '(min-width: 100px)',
            ]),
            $this->createStdClass([
                'rel' => 'stylesheet',
                'href' => '/bar2.css',
            ]),
        ];

        $expected = [
            '</bar.css>; rel="prefetch"; as="style"; type="text/style"; media="(min-width: 100px)"',
            '</bar2.css>; rel="prefetch"; as="style"',
        ];

        $headLink = $this->prophesize(HeadLink::class);
        $options = $this->prophesize(OptionsInterface::class);
        $event = $this->prophesize(MvcEvent::class);
        $responseHeaders = $this->prophesize(Headers::class);
        $headContainer = $this->prophesize(AbstractContainer::class);
        $response = $this->prophesize(PhpEnvironment\Response::class);

        $options->getStylesheetMode()->willReturn('prefetch');
        $options->isHttp2PushEnabled()->willReturn(true);
        $options->isStylesheetEnabled()->willReturn(true);

        $headContainer->getIterator()->willReturn(new \ArrayIterator($links));

        $headLink->getContainer()->willReturn($headContainer->reveal());
        $response->getHeaders()->willReturn($responseHeaders->reveal());
        $event->getResponse()->willReturn($response->reveal());

        $responseHeaders->get('Link')->willReturn(false);
        $responseHeaders->removeHeader(Argument::any())->shouldNotBeCalled();

        $responseHeaders->addHeader(Argument::allOf(
            Argument::type(GenericMultiHeader::class),
            Argument::which('getFieldName', 'Link'),
            Argument::which('getFieldValue', \implode(', ', $expected))
        ))
            ->shouldBeCalledTimes(1);

        $injector = new StylesheetHandler($headLink->reveal(), $options->reveal());

        $injector($event->reveal());
    }

    public function testInvokeWithNoRelAttribute()
    {
        $links = [
            $this->createStdClass([
                'rel' => OptionsInterface::MODE_PRELOAD,
                'href' => '/foo.css',
            ]),
            $this->createStdClass([
                'href' => '/bar.css',
            ]),
        ];

        $headLink = $this->prophesize(HeadLink::class);
        $options = $this->prophesize(OptionsInterface::class);
        $event = $this->prophesize(MvcEvent::class);
        $responseHeaders = $this->prophesize(Headers::class);
        $headContainer = $this->prophesize(AbstractContainer::class);
        $response = $this->prophesize(PhpEnvironment\Response::class);

        $options->getStylesheetMode()->willReturn('preload');
        $options->isHttp2PushEnabled()->willReturn(true);
        $options->isStylesheetEnabled()->willReturn(true);

        $headContainer->getIterator()->willReturn(new \ArrayIterator($links));

        $headLink->getContainer()->willReturn($headContainer->reveal());
        $response->getHeaders()->willReturn($responseHeaders->reveal());
        $event->getResponse()->willReturn($response->reveal());

        $responseHeaders->addHeaderLine(Argument::any())->shouldNotBeCalled();

        $injector = new StylesheetHandler($headLink->reveal(), $options->reveal());

        $injector($event->reveal());
    }

    public function testInvokeWithNoHrefAttribute()
    {
        $links = [
            $this->createStdClass([
                'rel' => OptionsInterface::MODE_PRELOAD,
                'href' => '/foo.css',
            ]),
            $this->createStdClass([
                'rel' => OptionsInterface::MODE_PRELOAD,
            ]),
            $this->createStdClass([
                'rel' => 'stylesheet',
            ]),
        ];

        $headLink = $this->prophesize(HeadLink::class);
        $options = $this->prophesize(OptionsInterface::class);
        $event = $this->prophesize(MvcEvent::class);
        $responseHeaders = $this->prophesize(Headers::class);
        $headContainer = $this->prophesize(AbstractContainer::class);
        $response = $this->prophesize(PhpEnvironment\Response::class);

        $options->getStylesheetMode()->willReturn('preload');
        $options->isHttp2PushEnabled()->willReturn(true);
        $options->isStylesheetEnabled()->willReturn(true);

        $headContainer->getIterator()->willReturn(new \ArrayIterator($links));

        $headLink->getContainer()->willReturn($headContainer->reveal());
        $response->getHeaders()->willReturn($responseHeaders->reveal());
        $event->getResponse()->willReturn($response->reveal());

        $responseHeaders->addHeaderLine(Argument::any())->shouldNotBeCalled();

        $injector = new StylesheetHandler($headLink->reveal(), $options->reveal());

        $injector($event->reveal());
    }

    /**
     * @param array $properties
     * @return \stdClass
     */
    private function createStdClass(array $properties): \stdClass
    {
        $item = new \stdClass();

        foreach ($properties as $name => $value) {
            $item->{$name} = $value;
        }

        return $item;
    }
}
