<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Functional;

use Facile\ZFLinkHeadersModule\Options;
use Facile\ZFLinkHeadersModule\OptionsInterface;
use PHPUnit\Framework\ExpectationFailedException;
use Zend\Http\Header\HeaderInterface;
use Zend\Http\Headers;
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
        // Links
        $this->assertResponseHeaderContains('Link', '</style/foo.css>; rel="preload"');
        $this->assertResponseHeaderContains('Link', '</style/bar.css>; rel="prefetch"; as="style"; type="text/stylesheet"');
        // Stylesheets
        $this->assertNotResponseHeaderContains('Link', '</style/stylesheet.css>; rel="preload"; as="style"; type="text/css"; media="screen"');
        // Scripts
        $this->assertNotResponseHeaderContains('Link', '</script/foo.js>; rel="preload"; as="script"; type="text/javascript"');
        $this->assertNotResponseHeaderContains('Link', '</script/bar.js>; rel="preload"; as="script"; type="text/text"');
    }

    /**
     * @coversNothing
     */
    public function testIndexActionWithStylesheetModeDefault()
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getApplication()->getServiceManager();
        $serviceManager->setAllowOverride(true);
        /** @var Options $options */
        $options = $serviceManager->get(OptionsInterface::class);
        $options->setStylesheetEnabled(true);

        $this->dispatch('/');
        // Links
        $this->assertResponseHeaderContains('Link', '</style/foo.css>; rel="preload"');
        $this->assertResponseHeaderContains('Link', '</style/bar.css>; rel="prefetch"; as="style"; type="text/stylesheet"');
        // Stylesheets
        $this->assertResponseHeaderContains('Link', '</style/stylesheet.css>; rel="preload"; as="style"; type="text/css"; media="screen"');
        // Scripts
        $this->assertNotResponseHeaderContains('Link', '</script/foo.js>; rel="preload"; as="script"; type="text/javascript"');
        $this->assertNotResponseHeaderContains('Link', '</script/bar.js>; rel="preload"; as="script"; type="text/text"');
    }

    /**
     * @coversNothing
     */
    public function testIndexActionWithStylesheetModePrefetch()
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getApplication()->getServiceManager();
        $serviceManager->setAllowOverride(true);
        /** @var Options $options */
        $options = $serviceManager->get(OptionsInterface::class);
        $options->setStylesheetEnabled(true);
        $options->setStylesheetMode('prefetch');

        $this->dispatch('/');
        // Links
        $this->assertResponseHeaderContains('Link', '</style/foo.css>; rel="preload"');
        $this->assertResponseHeaderContains('Link', '</style/bar.css>; rel="prefetch"; as="style"; type="text/stylesheet"');
        // Stylesheets
        $this->assertResponseHeaderContains('Link', '</style/stylesheet.css>; rel="prefetch"; as="style"; type="text/css"; media="screen"');
        // Scripts
        $this->assertNotResponseHeaderContains('Link', '</script/foo.js>; rel="preload"; as="script"; type="text/javascript"');
        $this->assertNotResponseHeaderContains('Link', '</script/bar.js>; rel="preload"; as="script"; type="text/text"');
    }

    /**
     * @coversNothing
     */
    public function testIndexActionWithJavascriptModeDefault()
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getApplication()->getServiceManager();
        $serviceManager->setAllowOverride(true);
        /** @var Options $options */
        $options = $serviceManager->get(OptionsInterface::class);
        $options->setScriptEnabled(true);

        $this->dispatch('/');
        // Links
        $this->assertResponseHeaderContains('Link', '</style/foo.css>; rel="preload"');
        $this->assertResponseHeaderContains('Link', '</style/bar.css>; rel="prefetch"; as="style"; type="text/stylesheet"');
        // Stylesheets
        $this->assertNotResponseHeaderContains('Link', '</style/stylesheet.css>; rel="preload"; as="style"; type="text/css"; media="screen"');
        // Scripts
        $this->assertResponseHeaderContains('Link', '</script/foo.js>; rel="preload"; as="script"; type="text/javascript"');
        $this->assertResponseHeaderContains('Link', '</script/bar.js>; rel="preload"; as="script"; type="text/text"');
    }

    /**
     * @coversNothing
     */
    public function testIndexActionWithJavascriptModePrefetch()
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getApplication()->getServiceManager();
        $serviceManager->setAllowOverride(true);
        /** @var Options $options */
        $options = $serviceManager->get(OptionsInterface::class);
        $options->setScriptEnabled(true);
        $options->setScriptMode('prefetch');

        $this->dispatch('/');
        // Links
        $this->assertResponseHeaderContains('Link', '</style/foo.css>; rel="preload"');
        $this->assertResponseHeaderContains('Link', '</style/bar.css>; rel="prefetch"; as="style"; type="text/stylesheet"');
        // Stylesheets
        $this->assertNotResponseHeaderContains('Link', '</style/stylesheet.css>; rel="prefetch"; as="style"; type="text/css"; media="screen"');
        // Scripts
        $this->assertResponseHeaderContains('Link', '</script/foo.js>; rel="prefetch"; as="script"; type="text/javascript"');
        $this->assertResponseHeaderContains('Link', '</script/bar.js>; rel="prefetch"; as="script"; type="text/text"');
    }

    /**
     * @coversNothing
     */
    public function testIndexActionWithAllEnabledAndNoPush()
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getApplication()->getServiceManager();
        $serviceManager->setAllowOverride(true);
        /** @var Options $options */
        $options = $serviceManager->get(OptionsInterface::class);
        $options->setScriptEnabled(true);
        $options->setStylesheetEnabled(true);
        $options->setHttp2PushEnabled(false);

        $this->dispatch('/');
        // Links
        $this->assertResponseHeaderContains('Link', '</style/foo.css>; rel="preload"; nopush');
        $this->assertResponseHeaderContains('Link', '</style/bar.css>; rel="prefetch"; as="style"; type="text/stylesheet"; nopush');
        // Stylesheets
        $this->assertResponseHeaderContains('Link', '</style/stylesheet.css>; rel="preload"; as="style"; type="text/css"; media="screen"; nopush');
        // Scripts
        $this->assertResponseHeaderContains('Link', '</script/foo.js>; rel="preload"; as="script"; type="text/javascript"; nopush');
        $this->assertResponseHeaderContains('Link', '</script/bar.js>; rel="preload"; as="script"; type="text/text"; nopush');
    }

    /**
     * Assert response header exists and contains the given string
     *
     * @param  string $header
     * @param  string $match
     */
    public function assertResponseHeaderContains($header, $match)
    {
        $responseHeader = $this->getResponseHeader($header);
        if (! $responseHeader) {
            throw new ExpectationFailedException($this->createFailureMessage(sprintf(
                'Failed asserting response header, header "%s" doesn\'t exist',
                $header
            )));
        }

        $response = $this->getResponse();
        /** @var Headers|HeaderInterface[] $headers */
        $headers = $response->getHeaders();

        $headerMatched = false;
        $currentHeader = null;

        foreach ($headers as $currentHeader) {
            if ($match === $currentHeader->getFieldValue()) {
                $headerMatched = true;
                break;
            }
        }

        if (! $headerMatched) {
            throw new ExpectationFailedException($this->createFailureMessage(sprintf(
                'Failed asserting response header "%s" exists and contains "%s"',
                $header,
                $match
            )));
        }

        $this->assertEquals($match, $currentHeader ? $currentHeader->getFieldValue() : null);
    }

    /**
     * Assert response header exists and contains the given string
     *
     * @param  string $header
     * @param  string $match
     */
    public function assertNotResponseHeaderContains($header, $match)
    {
        $responseHeader = $this->getResponseHeader($header);
        if (! $responseHeader) {
            throw new ExpectationFailedException($this->createFailureMessage(sprintf(
                'Failed asserting response header, header "%s" doesn\'t exist',
                $header
            )));
        }

        $response = $this->getResponse();
        /** @var Headers|HeaderInterface[] $headers */
        $headers = $response->getHeaders();

        $currentHeader = null;

        foreach ($headers as $currentHeader) {
            if ($match === $currentHeader->getFieldValue()) {
                throw new ExpectationFailedException($this->createFailureMessage(sprintf(
                    'Failed asserting response header "%s" DOES NOT CONTAIN "%s"',
                    $header,
                    $match
                )));
            }
        }

        $this->assertNotEquals($match, $currentHeader ? $currentHeader->getFieldValue() : null);
    }
}
