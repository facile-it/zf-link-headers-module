<?php

namespace Facile\ZFLinkHeadersModule;

use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{
    public function testIsStylesheetEnabled()
    {
        $options = new Options();

        $this->assertFalse($options->isStylesheetEnabled());
        $options->setStylesheetEnabled(true);
        $this->assertTrue($options->isStylesheetEnabled());
    }

    public function testSetStylesheetEnabled()
    {
        $options = new Options();

        $options->setStylesheetEnabled(false);
        $this->assertFalse($options->isStylesheetEnabled());

        $options->setStylesheetEnabled(true);
        $this->assertTrue($options->isStylesheetEnabled());
    }

    public function testGetStylesheetMode()
    {
        $options = new Options();

        $this->assertSame('preload', $options->getStylesheetMode());
        $options->setStylesheetMode('prefetch');
        $this->assertSame('prefetch', $options->getStylesheetMode());
    }

    public function testSetStylesheetMode()
    {
        $options = new Options();

        $options->setStylesheetMode('preload');
        $this->assertSame('preload', $options->getStylesheetMode());
        $options->setStylesheetMode('prefetch');
        $this->assertSame('prefetch', $options->getStylesheetMode());
    }

    public function testIsScriptEnabled()
    {
        $options = new Options();

        $this->assertFalse($options->isScriptEnabled());
        $options->setScriptEnabled(true);
        $this->assertTrue($options->isScriptEnabled());
    }

    public function testSetScriptEnabled()
    {
        $options = new Options();

        $options->setScriptEnabled(false);
        $this->assertFalse($options->isScriptEnabled());

        $options->setScriptEnabled(true);
        $this->assertTrue($options->isScriptEnabled());
    }

    public function testGetScriptMode()
    {
        $options = new Options();

        $this->assertSame('preload', $options->getScriptMode());
        $options->setScriptMode('prefetch');
        $this->assertSame('prefetch', $options->getScriptMode());
    }

    public function testSetScriptMode()
    {
        $options = new Options();

        $options->setScriptMode('preload');
        $this->assertSame('preload', $options->getScriptMode());
        $options->setScriptMode('prefetch');
        $this->assertSame('prefetch', $options->getScriptMode());
    }

    public function testIsHttp2PushDisabled()
    {
        $options = new Options();

        $this->assertFalse($options->isHttp2PushEnabled());
        $options->setHttp2PushEnabled(true);
        $this->assertTrue($options->isHttp2PushEnabled());
    }

    public function testSetHttp2PushDisabled()
    {
        $options = new Options();

        $options->setHttp2PushEnabled(false);
        $this->assertFalse($options->isHttp2PushEnabled());

        $options->setHttp2PushEnabled(true);
        $this->assertTrue($options->isHttp2PushEnabled());
    }
}
