<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Functional;

use Facile\ZFLinkHeadersModule\Options;
use Facile\ZFLinkHeadersModule\OptionsInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class FunctionalTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../config/application.config.php'
        );

        parent::setUp();
    }

    /**
     * @coversNothing
     */
    public function testIndexActionWithDefaultOptions()
    {
        $this->dispatch('/');

        $expected = [
            '</style/foo.css>; rel="preload"',
            '</style/bar.css>; rel="prefetch"; as="style"; type="text/stylesheet"',
        ];
        $this->assertResponseHeaderContains('Link', \implode(', ', $expected));
    }

    /**
     * @coversNothing
     */
    public function testIndexActionWithStylesheetModeDefault()
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getApplication()->getServiceManager();
        /** @var Options $options */
        $options = $serviceManager->get(OptionsInterface::class);
        $options->setStylesheetEnabled(true);

        $this->dispatch('/');

        $expected = [
            '</style/foo.css>; rel="preload"',
            '</style/bar.css>; rel="prefetch"; as="style"; type="text/stylesheet"',
            '</style/stylesheet.css>; rel="preload"; as="style"; type="text/css"; media="screen"',
        ];
        $this->assertResponseHeaderContains('Link', \implode(', ', $expected));
    }

    /**
     * @coversNothing
     */
    public function testIndexActionWithStylesheetModePrefetch()
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getApplication()->getServiceManager();
        /** @var Options $options */
        $options = $serviceManager->get(OptionsInterface::class);
        $options->setStylesheetEnabled(true);
        $options->setStylesheetMode('prefetch');

        $this->dispatch('/');

        $expected = [
            '</style/foo.css>; rel="preload"',
            '</style/bar.css>; rel="prefetch"; as="style"; type="text/stylesheet"',
            '</style/stylesheet.css>; rel="prefetch"; as="style"; type="text/css"; media="screen"',
        ];
        $this->assertResponseHeaderContains('Link', \implode(', ', $expected));
    }

    /**
     * @coversNothing
     */
    public function testIndexActionWithJavascriptModeDefault()
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getApplication()->getServiceManager();
        /** @var Options $options */
        $options = $serviceManager->get(OptionsInterface::class);
        $options->setScriptEnabled(true);

        $this->dispatch('/');

        $expected = [
            '</style/foo.css>; rel="preload"',
            '</style/bar.css>; rel="prefetch"; as="style"; type="text/stylesheet"',
            '</script/foo.js>; rel="preload"; as="script"; type="text/javascript"',
            '</script/bar.js>; rel="preload"; as="script"; type="text/text"',
        ];
        $this->assertResponseHeaderContains('Link', \implode(', ', $expected));
    }

    /**
     * @coversNothing
     */
    public function testIndexActionWithJavascriptModePrefetch()
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getApplication()->getServiceManager();
        /** @var Options $options */
        $options = $serviceManager->get(OptionsInterface::class);
        $options->setScriptEnabled(true);
        $options->setScriptMode('prefetch');

        $this->dispatch('/');

        $expected = [
            '</style/foo.css>; rel="preload"',
            '</style/bar.css>; rel="prefetch"; as="style"; type="text/stylesheet"',
            '</script/foo.js>; rel="prefetch"; as="script"; type="text/javascript"',
            '</script/bar.js>; rel="prefetch"; as="script"; type="text/text"',
        ];
        $this->assertResponseHeaderContains('Link', \implode(', ', $expected));
    }

    /**
     * @coversNothing
     */
    public function testIndexActionWithAllEnabledAndNoPush()
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getApplication()->getServiceManager();
        /** @var Options $options */
        $options = $serviceManager->get(OptionsInterface::class);
        $options->setScriptEnabled(true);
        $options->setStylesheetEnabled(true);
        $options->setHttp2PushEnabled(false);

        $this->dispatch('/');

        $expected = [
            '</style/foo.css>; rel="preload"; nopush',
            '</style/bar.css>; rel="prefetch"; as="style"; type="text/stylesheet"; nopush',
            '</style/stylesheet.css>; rel="preload"; as="style"; type="text/css"; media="screen"; nopush',
            '</script/foo.js>; rel="preload"; as="script"; type="text/javascript"; nopush',
            '</script/bar.js>; rel="preload"; as="script"; type="text/text"; nopush',
        ];
        $this->assertResponseHeaderContains('Link', \implode(', ', $expected));
    }
}
