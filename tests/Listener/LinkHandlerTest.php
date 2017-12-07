<?php

namespace Facile\ZFLinkHeadersModule\Listener;

use Facile\ZFLinkHeadersModule\OptionsInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Zend\Http\Header\GenericMultiHeader;
use Zend\Http\Headers;
use Zend\Http\PhpEnvironment;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\View\Helper\HeadLink;
use Zend\View\Helper\Placeholder\Container\AbstractContainer;

class LinkHandlerTest extends TestCase
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

        $injector = new LinkHandler($headLink->reveal(), $options->reveal());

        $injector($event->reveal());
    }

    public function testInvokeWithNoPush()
    {
        $links = [
            $this->createStdClass([
                'rel' => OptionsInterface::MODE_PRELOAD,
                'href' => '/foo.css',
            ]),
            $this->createStdClass([
                'rel' => OptionsInterface::MODE_PREFETCH,
                'href' => '/bar.css',
                'as' => 'style',
                'crossorigin' => 'same-origin',
                'type' => 'text/style',
                'media' => '(min-width: 100px)',
                'nopush' => 'nopush',
            ]),
            $this->createStdClass([
                'rel' => OptionsInterface::MODE_DNS_PREFETCH,
                'href' => '/foo-dns-prefetch.css',
            ]),
            $this->createStdClass([
                'rel' => OptionsInterface::MODE_PRECONNECT,
                'href' => '/foo-preconnect.css',
            ]),
            $this->createStdClass([
                'rel' => OptionsInterface::MODE_PRERENDER,
                'href' => '/foo-prerender.css',
            ]),
        ];

        $headLink = $this->prophesize(HeadLink::class);
        $options = $this->prophesize(OptionsInterface::class);
        $event = $this->prophesize(MvcEvent::class);
        $responseHeaders = $this->prophesize(Headers::class);
        $headContainer = $this->prophesize(AbstractContainer::class);
        $response = $this->prophesize(PhpEnvironment\Response::class);

        $options->isHttp2PushEnabled()->willReturn(false);

        $headContainer->getIterator()->willReturn(new \ArrayIterator($links));

        $headLink->getContainer()->willReturn($headContainer->reveal());
        $response->getHeaders()->willReturn($responseHeaders->reveal());
        $event->getResponse()->willReturn($response->reveal());

        $responseHeaders->addHeader(Argument::allOf(
            Argument::type(GenericMultiHeader::class),
            Argument::which('getFieldName', 'Link'),
            Argument::which('getFieldValue', '</foo.css>; rel="preload"; nopush')
        ))
            ->shouldBeCalled();
        $responseHeaders->addHeader(Argument::allOf(
            Argument::type(GenericMultiHeader::class),
            Argument::which('getFieldName', 'Link'),
            Argument::which('getFieldValue', '</bar.css>; rel="prefetch"; as="style"; crossorigin="same-origin"; type="text/style"; media="(min-width: 100px)"; nopush')
        ))
            ->shouldBeCalled();
        $responseHeaders->addHeader(Argument::allOf(
            Argument::type(GenericMultiHeader::class),
            Argument::which('getFieldName', 'Link'),
            Argument::which('getFieldValue', '</foo-dns-prefetch.css>; rel="dns-prefetch"; nopush')
        ))
            ->shouldBeCalled();
        $responseHeaders->addHeader(Argument::allOf(
            Argument::type(GenericMultiHeader::class),
            Argument::which('getFieldName', 'Link'),
            Argument::which('getFieldValue', '</foo-preconnect.css>; rel="preconnect"; nopush')
        ))
            ->shouldBeCalled();
        $responseHeaders->addHeader(Argument::allOf(
            Argument::type(GenericMultiHeader::class),
            Argument::which('getFieldName', 'Link'),
            Argument::which('getFieldValue', '</foo-prerender.css>; rel="prerender"; nopush')
        ))
            ->shouldBeCalled();

        $injector = new LinkHandler($headLink->reveal(), $options->reveal());

        $injector($event->reveal());
    }

    public function testInvokeWithPush()
    {
        $links = [
            $this->createStdClass([
                'rel' => OptionsInterface::MODE_PRELOAD,
                'href' => '/foo.css',
            ]),
        ];

        $headLink = $this->prophesize(HeadLink::class);
        $options = $this->prophesize(OptionsInterface::class);
        $event = $this->prophesize(MvcEvent::class);
        $responseHeaders = $this->prophesize(Headers::class);
        $headContainer = $this->prophesize(AbstractContainer::class);
        $response = $this->prophesize(PhpEnvironment\Response::class);

        $options->isHttp2PushEnabled()->willReturn(true);

        $headContainer->getIterator()->willReturn(new \ArrayIterator($links));

        $headLink->getContainer()->willReturn($headContainer->reveal());
        $response->getHeaders()->willReturn($responseHeaders->reveal());
        $event->getResponse()->willReturn($response->reveal());

        $responseHeaders->addHeader(Argument::allOf(
            Argument::type(GenericMultiHeader::class),
            Argument::which('getFieldName', 'Link'),
            Argument::which('getFieldValue', '</foo.css>; rel="preload"')
        ))
            ->shouldBeCalled();

        $injector = new LinkHandler($headLink->reveal(), $options->reveal());

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

        $options->isHttp2PushEnabled()->willReturn(true);

        $headContainer->getIterator()->willReturn(new \ArrayIterator($links));

        $headLink->getContainer()->willReturn($headContainer->reveal());
        $response->getHeaders()->willReturn($responseHeaders->reveal());
        $event->getResponse()->willReturn($response->reveal());

        $responseHeaders->addHeader(Argument::allOf(
            Argument::type(GenericMultiHeader::class),
            Argument::which('getFieldName', 'Link'),
            Argument::which('getFieldValue', '</foo.css>; rel="preload"')
        ))
            ->shouldBeCalled();

        $injector = new LinkHandler($headLink->reveal(), $options->reveal());

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

        $options->isHttp2PushEnabled()->willReturn(true);

        $headContainer->getIterator()->willReturn(new \ArrayIterator($links));

        $headLink->getContainer()->willReturn($headContainer->reveal());
        $response->getHeaders()->willReturn($responseHeaders->reveal());
        $event->getResponse()->willReturn($response->reveal());

        $responseHeaders->addHeader(Argument::allOf(
            Argument::type(GenericMultiHeader::class),
            Argument::which('getFieldName', 'Link'),
            Argument::which('getFieldValue', '</foo.css>; rel="preload"')
        ))
            ->shouldBeCalled();

        $injector = new LinkHandler($headLink->reveal(), $options->reveal());

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
